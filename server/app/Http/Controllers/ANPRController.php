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
        $itemsPerPage = $request->get('itemsPerPage');
        if (($plateNumber == 'null') || !isset($plateNumber)) {
            $anpr = ANPR::with(['camera'])->paginate($itemsPerPage);
        }else{
            $anpr = ANPR::with(['camera'])->where([['plateNumber','like', '%'.$plateNumber.'%'],['id_camera','8']])->paginate($itemsPerPage);
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
        //
    }

    public function destroy($id)
    {
        //
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

    public function showPlateHistory($plateNumber)
    {
        $anpr_history = ANPR::with(['camera'])
                        ->where('plateNumber', $plateNumber)
                        ->orderByDesc("created_at")
                        ->get();
        return $anpr_history;
    }

}
