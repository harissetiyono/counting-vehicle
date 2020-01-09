<?php

namespace App\Http\Controllers;

use App\Camera;
use App\Lane;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CameraController extends Controller
{
    public function index(Request $request)
    {
        $cv_system = $request->get('system');
        $page = $request->get('page');
        if($page){
          $camera = Camera::where('system', $cv_system)->paginate(8);
        }else{
          $camera = Camera::where('system', $cv_system)->get();
        }
        return response()->json($camera, 200);
    }

    public function store(Request $request)
    {
        $datas = $request->all();
        try {
          $id = Camera::create($datas['camera_data'])->id;
          if (isset($datas['line_path'])) {
            foreach ($datas['line_path'] as $key => $value) {
              $data = new Lane;
              $data->id_camera = $id;
              $data->path_line = $value['path'];
              $data->x_start_line = $value['point'][0]*2;
              $data->y_start_line = $value['point'][1]*2;
              $data->x_end_line = $value['point'][2]*2;
              $data->y_end_line = $value['point'][3]*2;
              $data->save();

              if ($value['path'] == 'IN') {
                $line_array['in'][] = [$value['point'][0]*2,$value['point'][1]*2,$value['point'][2]*2,$value['point'][3]*2 ];
              }else{
                $line_array['out'][] = [$value['point'][0]*2,$value['point'][1]*2,$value['point'][2]*2,$value['point'][3]*2];
              }
            }

            if(Storage::exists('public/line_json/'.$datas['camera_data']['port'])) {
              Storage::deleteDirectory('public/line_json/'.$datas['camera_data']['port']);
            }

            Storage::makeDirectory('public/line_json/'.$datas['camera_data']['port']);

            foreach ($line_array as $key => $value) {
              $this->generateLine(json_encode($value), $datas['camera_data']['port'], $key);
            }
          }

          return response()->json(array('success' => true), 200);
        } catch (\Throwable $th) {
          return response()->json(array('success' => false, 'message' => $th), 500);
        }
    }

    public function update(Request $request)
    {
      $datas = $request->all();
      $id = $datas['camera_data']['id'];
        try {
          $camera = Camera::findOrfail($id);
          $camera->update($datas['camera_data']);
          if (isset($datas['line_path'])) {
            $deletedRows = Lane::where('id_camera', $id)->delete();
            foreach ($datas['line_path'] as $key => $value) {
              $data = new Lane;
              $data->id_camera = $id;
              $data->path_line = $value['path'];
              $data->x_start_line = $value['point'][0]*2;
              $data->y_start_line = $value['point'][1]*2;
              $data->x_end_line = $value['point'][2]*2;
              $data->y_end_line = $value['point'][3]*2;
              $data->save();

              if ($value['path'] == 'IN') {
                $line_array['in'][] = [$value['point'][0]*2,$value['point'][1]*2,$value['point'][2]*2,$value['point'][3]*2 ];
              }else{
                $line_array['out'][] = [$value['point'][0]*2,$value['point'][1]*2,$value['point'][2]*2,$value['point'][3]*2];
              }

            }

            if(Storage::exists('public/line_json/'.$datas['camera_data']['port'])) {
              Storage::deleteDirectory('public/line_json/'.$datas['camera_data']['port']);
            }

            Storage::makeDirectory('public/line_json/'.$datas['camera_data']['port']);

            foreach ($line_array as $key => $value) {
              $this->generateLine(json_encode($value), $datas['camera_data']['port'], $key);
            }
          }

          return response()->json(array('success' => true), 200);
        } catch (\Throwable $th) {
          return response()->json(array('success' => false, 'message' => $th), 500);
        }

      return $camera;
    }

    public function destroy($id)
    {
      $camera = Camera::findOrfail($id);
      if ($camera->delete()) {
          return $camera;
      } else {
          return response()->json(425, ['delete' => 'Error deleting record']);
      }
    }

    public function camera($id)
    {
      $camera = Camera::where('id', $id)->first();
      return response()->json($camera, 200);
    }

    public function lines($id)
    {
      $lines = Lane::where('id_camera', $id)->get();

      foreach ($lines as $key => $value) {
        $lines_point[$key]['x'] = 0;
        $lines_point[$key]['y'] = 0;
        $lines_point[$key]['point'] = [$value['x_start_line']/2,$value['y_start_line']/2,$value['x_end_line']/2,$value['y_end_line']/2];
        $lines_point[$key]['closed'] = true;
        $lines_point[$key]['stroke'] = '#00a1ff';
        $lines_point[$key]['path'] = $value['path_line'];
      }
      return response()->json($lines_point, 200);
    }

    public function generateLine($json_line,$port, $path)
    {
      $process = new Process(['python', env('PATH_GENERATE_LINE'),$json_line,$port,$path,env('PATH_LOCATION_LINE')]);
      $process->run();
      return $process->getOutput();
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
      $action = $request->get('action');
      if (!isset($action)) {
        exec('cd /Users/haris/Development/alpr/research && protoc object_detection/protos/*.proto --python_out=. && export PYTHONPATH=$PYTHONPATH:`pwd`:`pwd`/slim && cd object_detection && python3 start.py -i '. $id .' 2>/dev/null >/dev/null &');
      }else{
        exec('cd /Users/haris/Development/alpr/research && protoc object_detection/protos/*.proto --python_out=. && export PYTHONPATH=$PYTHONPATH:`pwd`:`pwd`/slim && cd object_detection && python3 start.py -i '. $id . ' -a '. $action.' 2>/dev/null >/dev/null &');
      }
      

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
