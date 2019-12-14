<?php

namespace App\Http\Controllers;

use App\Camera;
use App\Lane;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CameraController extends Controller
{
    public function index()
    {
        $camera = Camera::all();
        return response()->json($camera, 200);
    }

    public function store(Request $request)
    {
        $datas = $request->all();
        $lane_point = $datas['line_path']['point'];
        $lane_path = $datas['line_path']['path'];

        $x_start_line = $lane_point[0];
        $y_start_line = $lane_point[1];
        $x_end_line = $lane_point[2];
        $y_end_line = $lane_point[3];

        try {
          $id = Camera::create($datas['camera_data'])->id;

          $data = new Lane;
          $data->id_camera = $id;
          $data->path_line = $lane_path;
          $data->x_start_line = $x_start_line;
          $data->y_start_line = $y_start_line;
          $data->x_end_line = $x_end_line;
          $data->y_end_line = $y_end_line;
          $data->y_end_line = $y_end_line;
         
          if ($data->save()) {
            return response()->json(array('success' => true), 200);
          }
        } catch (\Throwable $th) {
            return response()->json(array('success' => false, 'message' => $th), 200);
        }
    }

    public function update(Request $request, $id)
    {
      $camera = Camera::findOrfail($id);
      $camera->update($request->all());

      return $camera;
    }

    public function destroy($id)
    {
      $camera = Camera::findOrfail($id);
      if ($camera->delete()) {
          return $this->index();
      } else {
          return response()->json(425, ['delete' => 'Error deleting record']);
      }
    }

    public function camera($id)
    {
      $camera = Camera::where('id', $id)->get();
      return response()->json($camera, 200);
    }

    public function generateImage(Request $request)
    {
      $url = $request->post('url');
      $port = $request->post('port');
      $process = new Process(['python3',env('PATH_GENERATE_IMG'),'-u', $url,'-p', $port, '-l', env('PATH_LOCATION_IMG')]);
      $process->run();
      if (!$process->isSuccessful()) {
          throw new ProcessFailedException($process);
          return response()->json(500, [
            'status' => 'Failed',
            'message' => 'Terjadi masalah dengan koneksi database'
          ]);
      }else{
        return response()->json([
              'status' => 'success',
              'message' => 'Script service berhasil dijalankan'
            ]);
      }
    }

    public function runService(Request $request)
    {
      $id = $request->post('id');
      exec('cd /Users/haris/Development/alpr/research && protoc object_detection/protos/*.proto --python_out=. && export PYTHONPATH=$PYTHONPATH:`pwd`:`pwd`/slim && nohup python3 /Users/haris/Development/alpr/research/object_detection/start.py -i '. $id .' 2>/dev/null >/dev/null &');

      // $test = exec('python3 /Users/haris/Development/alpr/research/object_detection/start.py -i 1 > /dev/null 2>&1 &');
      return response()->json(200, ['message' => 'Success']);
      
      // $process = new Process('cd /Users/haris/Development/alpr/research && protoc object_detection/protos/*.proto --python_out=. && export PYTHONPATH=$PYTHONPATH:`pwd`:`pwd`/slim && nohup python3 /Users/haris/Development/alpr/research/object_detection/start.py -i 1 > /Users/haris/Development/alpr/research/object_detection/log/9001.log');
      // $process = Process::fromShellCommandline('python3 /Users/haris/Development/alpr/research/object_detection/start.py -i 1 '.' &');
      // $process = new Process(['python3','/Users/haris/Development/alpr/research/object_detection/start.py','-i', $id]);
      // $process->setTimeout(10000);
      // $process->run();
      
      // executes after the command finishes
      // if (!$process->isSuccessful()) {
      //     throw new ProcessFailedException($process);
      //     return response()->json([
      //       'status' => 'Failed',
      //       'message' => 'Terjadi masalah dengan koneksi database'
      //     ]);
      // }
      
      // $response = trim(preg_replace('/\s+/', ' ', $process->getOutput()));

      // if ($response == "sukses") {
      //   return response()->json([
      //     'status' => 'success',
      //     'message' => 'Script service berhasil dijalankan'
      //   ]);
      // }else{
      //   return response()->json([
      //     'status' => 'Failed',
      //     'message' => 'Terjadi masalah dengan koneksi database'
      //   ]);
      // }
      
    }
}
