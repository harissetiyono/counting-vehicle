<?php

namespace App\Http\Controllers;

use App\ANPRCamera;
use Illuminate\Http\Request;

class ANPRCameraController extends Controller
{
    public function index(Request $request)
    {
        $camera = ANPRCamera::all();
        return $camera;
    }
}
