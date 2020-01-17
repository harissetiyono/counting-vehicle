<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ANPR;
use App\Filtering;

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
            $anpr = ANPR::with(['camera'])->paginate($itemsPerPage);
        }else{
            if (($plateNumber !== null)) {
                $where[] = ['plateNumber', 'like', '%'.$plateNumber.'%'];
            }
                $where[] = ['id_camera', $camera];
            if ($matchingResult == 'none') {
                $where[] = ['matchingResult', null];
            }else if($matchingResult !== 'none' && $matchingResult !== 'all'){
                $where[] = ['matchingResult', $matchingResult];
            }
            $anpr = ANPR::with(['camera'])->where($where)->paginate($itemsPerPage);
        }
        
        return $anpr;
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        
    }

    public function show($id)
    {
        $anpr = ANPR::where('id_camera', $id)->get();
        return $anpr;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
       
    }

    public function destroy($id)
    {
        
    }

    public function checkFilter($plateNumber)
    {
        $anpr = Filtering::where('plateNumber', $plateNumber)->firstOrFail();
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

}
