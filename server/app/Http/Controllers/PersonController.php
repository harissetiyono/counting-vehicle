<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
class PersonController extends Controller
{
    public function index()
    {
        $person = Person::with(['religion', 'profession'])->get();
        return $person;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $datas = $request->all();
        try {
            Person::create($datas['person_data']);
            foreach ($datas['photos'] as $key => $value) {
                $photo = substr($value, strpos($value, ",")+1);
                $photo = base64_decode($photo);
                Storage::disk('local')->put('face_data/'.$datas['person_data']['nik'].'/'.$key.'.png', $photo);
            }
            $this->runEncoding($datas['person_data']['nik']);
            return response()->json(array('success' => true), 200);
        } catch (\Throwable $th) {
   
        }
    }

    public function show($id)
    {
        $person = Person::with(['religion', 'profession'])->findOrFail($id);
        $files = Storage::files('face_data/'.$id);
        $photos = [];
        if (count($files) > 0) {
            foreach ($files as $key => $value) {
                if ($value == 'face_data/'.$id.'/.DS_Store') {
                    continue;
                }
                $photos[] = base64_encode(Storage::get($value));
            }
        }
        return array('person_data' => $person, 'photos' => $photos);
    }

    public function edit($id)
    {
        $person = Person::findOrFail($id);
        $files = Storage::files('face_data/'.$id);
        $photos = [];
        if (count($files) > 0) {
            foreach ($files as $key => $value) {
                $photos[] = base64_encode(Storage::get($value));
            }
        }
        return array('person_data' => $person, 'photos' => $photos);
    }

    public function update(Request $request, $id)
    {
        $datas = $request->all();
        $id = $datas['person_data']['nik'];
        try {
            $person = Person::findOrfail($id);
            $person->update($datas['person_data']);
            Storage::deleteDirectory($id);
            foreach ($datas['photos'] as $key => $value) {
                $photo = substr($value, strpos($value, ",")+1);
                $photo = base64_decode($photo);
                Storage::disk('local')->put('face_data/'.$id.'/'.$key.'.png', $photo);
            }
            $this->runEncoding($id);

            return $person;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function destroy($id)
    {
        try {
            $person = Person::find($id);
            Storage::deleteDirectory('face_data/'.$id);
            $person->delete();
            return $person;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getFacePhoto($id)
    {
        $files = 'face_data/'.$id.'/0.png';
        $photo = base64_encode(Storage::get($files));
        return $photo;
    }

    public function runEncoding($id)
    {
        exec('cd && python3 /Users/haris/Documents/project_test/t_face_recognition/face_to_json.py -a update -i '.$id. ' 2>/dev/null >/dev/null &');
        return response()->json(array('success' => true), 200);
        // $process = new Process(['cd','&&','python3', env('PATH_GENERATE_FACE'), '-u', $id, '2>/dev/null >/dev/null &']);
        // $process->run();
        // return $process->getOutput();
    }

    public function runTrain()
    {
        exec('cd && python3 /Users/haris/Documents/project_test/t_face_recognition/face_to_json.py -a train 2>/dev/null >/dev/null &');
        return response()->json(array('success' => true), 200);
    }

    public function findByPhoto(Request $request)
    {
        $file = $request->file('file');
        $tolerance = $request->post('tolerance') / 100;
        $file_name = Str::random().'.jpg';
        Storage::putFileAs('public/face_find', $file, $file_name);
        // Storage::disk('local')->put('face_data/test123.jpg', $photo);
        
        shell_exec('python3 /Users/haris/Documents/project_test/t_face_recognition/search_photo.py -t '.$tolerance.' -f '.$file_name.' > /dev/null 2>/dev/null &');
        return response()->json(array('success' => true), 200);
        // $process = new Process(['python3', '/Users/haris/Documents/project_test/t_face_recognition/search_photo.py', '2>/dev/null', '>/dev/null', '&']);
        // $process->run();
        // return $process->getOutput();
    }
}
