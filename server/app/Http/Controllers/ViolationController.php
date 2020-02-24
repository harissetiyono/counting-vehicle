<?php

namespace App\Http\Controllers;

use App\ANPR;
use App\ANPRVideoViolation;
use App\ANPRViolation;
use App\ANPRCamera;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $plateNumber = $request->get('plateNumber');
        $camera = $request->get('camera');
        $itemsPerPage = $request->get('itemsPerPage');
        $matchingResult = $request->get('matchingResult');
        $where = [];
        if ($plateNumber == null && $matchingResult == 'all' && $camera == 'undefined') {
            $anpr = ANPR::with(['camera', 'vehicleType', 'ANPRViolation'])->whereNotNull('violationStatus')->orderByDesc("created_at")->paginate($itemsPerPage);
        }else{
            if ($plateNumber !== null) {
                $where[] = ['plateNumber', 'like', '%'.$plateNumber.'%'];
            }

            if ($camera !== 'undefined') {
                $where[] = ['id_camera', $camera];
            }
            
            else if($matchingResult !== 'none' && $matchingResult !== 'all'){
                $where[] = ['matchingResult', $matchingResult];
            }
            
            $anpr = ANPR::with(['camera', 'vehicleType', 'ANPRViolation'])->where($where)->whereNotNull('violationStatus')->paginate($itemsPerPage);
        }
        
        return $anpr;
    }

    public function show($id)
    {
        $client = new Client();
        $anpr = ANPR::with(['ANPRCamera:id,name', 'ANPRViolation:id,violationName'])->where('id', $id)->first();
        $camera = ANPRCamera::where('id', $anpr['id_camera'])->first();
        $start_date = Carbon::parse($anpr['created_at'])->setTimezone('UTC')->subSeconds(2)->toIso8601String();
        $end_date = Carbon::parse($anpr['created_at'])->setTimezone('UTC')->addSeconds(6)->toIso8601String();
        $payload = '
        <?xml version: "1.0" encoding="utf-8"?>
        <CMSearchDescription>
            <searchID>C8C76A2B-1740-0001-39E0-1A1020A01021</searchID>
            <trackIDList><trackID>103</trackID></trackIDList>
            <timeSpanList>
                <timeSpan><startTime>'.$start_date.'Z</startTime>
                <endTime>'.$end_date.'Z</endTime></timeSpan>
            </timeSpanList>
            <contentTypeList>
                <contentType>metadata</contentType>
            </contentTypeList>
            <maxResults>4</maxResults>
            <searchResultPostion>0</searchResultPostion>
            <metadataList>
                <metadataDescriptor>//recordType.meta.std-cgi.com/CMR</metadataDescriptor>
            </metadataList>
        </CMSearchDescription>';
        $response = $client->request('POST','http://'.$camera['support_camera'].'/ISAPI/ContentMgmt/search', [
            'auth' => ['admin', 'res1_bjg1', 'digest'],
            'body' => $payload
        ])->getBody()->getContents();
        $responseXml = simplexml_load_string($response);
        
        if ($responseXml->numOfMatches == 0) {
            $image = null;
        }else{
            $array = $responseXml->matchList->searchMatchItem;
            foreach ($array as $key => $value) {
                $image_url = strip_tags($value->mediaSegmentDescriptor->playbackURI);
                $response_image = $client->request('GET',$image_url, [ 'auth' => [$camera['username'], $camera['password'], 'digest']])->getBody()->getContents();
                $image[] = base64_encode($response_image);
            }
        }
        
        $start_time = Carbon::parse($anpr['created_at'])->subSeconds(20);
        $end_time = Carbon::parse($anpr['created_at'])->addSeconds(30);
        $video = ANPRVideoViolation::whereBetween('end_time', [$start_time, $end_time])->first();
        $datas = array(
            'anpr_record' => $anpr,
            'image' => $image,
            'video' => $video,
        );
        return $datas;
    }

    public function update(Request $request, $id)
    {
        $violation = ANPR::find($id);
        $violation->plateNumber = $request->get('plateNumber');
        $violation->save();
        return response()->json(['success'], 200);
    }
}
