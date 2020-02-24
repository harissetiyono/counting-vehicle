<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ANPR;
use App\Filtering;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class ANPRController extends Controller
{
    public function index(Request $request)
    {
        $plateNumber = $request->get('plateNumber');
        $camera = $request->get('camera');
        $itemsPerPage = $request->get('itemsPerPage');
        $matchingResult = $request->get('matchingResult');
        $where = [];
        if ($plateNumber == null && $matchingResult == 'all' && $camera == 'undefined') {
            $anpr = ANPR::with(['camera', 'vehicleType'])->orderByDesc("created_at")->paginate($itemsPerPage);
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
            
            $anpr = ANPR::with(['camera', 'vehicleType'])->where($where)->paginate($itemsPerPage);
        }
        
        return $anpr;
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $datas = (array) $request->all();
        $validator = Validator::make($datas, [
            'plateNumber' => 'required',
            'picName' => 'required',
            'laneNo' => 'required',
            'vehicleType' => 'required',
            'id_camera' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $anpr = ANPR::create($datas);
        if ($anpr) {
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json']
            ]);
            $client->request('POST', 'http://127.0.0.1:3000/plate_recognition', ['body' => json_encode($anpr)]);
            return response('Success', 200)->header('Content-Type', 'text/plain');
        }else{
            return response('Error Saving on Database', 500)->header('Content-Type', 'text/plain');
        }
    }

    public function show($id)
    {
        $anpr = ANPR::where('id_camera', $id)->get();
        return $anpr;
    }

    public function edit($id)
    {
        
    }

    public function update(Request $request, $id)
    {
       
    }

    public function destroy($id)
    {
        
    }

    public function checkFilter($plateNumber)
    {
        $anpr = Filtering::where('plateNumber', $plateNumber)->first();
        return $anpr;
    }

    public function filtering()
    {
        $filtering = Filtering::all();
        return $filtering;
    }

    public function filterStore(Request $request)
    {
        $datas = $request->all();
        $filter = Filtering::create($datas);
        return $filter;
    }

    public function filterUpdate(Request $request, $id)
    {
        $datas = $request->all();
        $filter = Filtering::findOrfail($id);
        $filter->update($datas);
    }

    public function filterDestroy($id)
    {
        $filter = Filtering::destroy($id);
        if ($filter) {
            return response()->json(['success' => 'success'], 200);
        }else{
            return response()->json(['error' => 'invalid'], 401);
        }
    }

    public function showPlateHistory($plateNumber)
    {
        $anpr_history = ANPR::with(['camera'])
                        ->where('plateNumber', $plateNumber)
                        ->orderByDesc("created_at")
                        ->get();
        return $anpr_history;
    }

    public function sync()
    {
        $filtering = Filtering::all();
        $datas = array(
            'filtering' => $filtering,
        );
        return $datas;
    }

}
