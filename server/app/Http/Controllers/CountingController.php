<?php

namespace App\Http\Controllers;

use App\Counting;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use Carbon\Carbon;

class CountingController extends Controller
{
    public function __construct()
    {
      $this->client = ClientBuilder::create()->setHosts(["http://127.0.0.1"])->build();
    }

    public function trend(Request $request, $id = null)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $interval = $request->get('interval');
        $lane = $request->get('lane');

        if ($interval == null) { $interval = '1h'; }

        $params['index'] = 'counting';
        $params['type'] = '_doc';
        $params['body']['size'] = 0;
        $params['body']['aggs']['date']['date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['date_histogram']['fixed_interval'] = $interval;
        $params['body']['aggs']['date']['date_histogram']['min_doc_count'] = 1;
        $params['body']['aggs']['date']['date_histogram']['time_zone'] = 'Asia/Jakarta';
        $params['body']['aggs']['date']['aggs']['type']['terms']['field'] = "categories.keyword";
        $params['body']['aggs']['date']['aggs']['type']['terms']['order']['_count'] = 'desc';
        $params['body']['aggs']['date']['aggs']['type']['terms']['missing'] = '__missing__';

        if ($start_date != '' && $end_date != '' ) {
            $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
        }else{
            $start_date = Carbon::today();
            $end_date = Carbon::today()->endOfDay();
            $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
        }

        if ($lane != 'all') {
            $params['body']['query']['bool']['filter'][]['match']['lane'] = $lane;
        }

        if($id !== null){ $params['body']['query']['bool']['filter'][]['match']['id_camera'] = $id; }
        $response = $this->client->search($params);

        if (empty($response['aggregations']['date']['buckets'])) {
            $datas = array(
                'categories' => [],
                'datasets' => [],
              );
        }else{
            $date = array_column($response['aggregations']['date']['buckets'], 'key_as_string');
            foreach ($date as $key => $value) {
                $date_new[] = date("H:i", strtotime($value));
                $tmp = $response['aggregations']['date']['buckets'][$key]['type']['buckets'];
                foreach ($tmp as $key3 => $value3) {
                    if ($value3['key'] == 'mobil') {
                        $data[$key]['mobil'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'mobil') {
                    //Skipp
                    }
                    else{
                        $data[$key]['mobil'] =  0;
                    }
        
                    if ($value3['key'] == 'motor') {
                        $data[$key]['motor'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'motor') {
                    //Skipp
                    }
                    else{
                        $data[$key]['motor'] =  0;
                    }
        
                    if ($value3['key'] == 'truck') {
                        $data[$key]['truck'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'truck') {
                    //Skipp
                    }
                    else{
                        $data[$key]['truck'] =  0;
                    }
        
                    if ($value3['key'] == 'bus') {
                        $data[$key]['bus'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'bus') {
                    //Skipp
                    }
                    else{
                        $data[$key]['bus'] =  0;
                    }
                }

                $vehicle = ['mobil','motor','truck', 'bus'];

                foreach ($vehicle as $key2 => $value2) {
                    if (array_key_exists($value2, $data[$key]) == false){
                        $data_count = 0;
                    }else{
                        $data_count = $data[$key][$value2];
                    }
                    $trend[$key2]['name'] = $value2;
                    $trend[$key2]['data'][$date_new[$key]] = $data_count;
                }
            }

            $datas = array(
                'categories' => $date_new,
                'datasets' => array_values($trend),
            );
        }

        return $datas;
    }

    public function trend_week($id = null)
    {
        $start_date = Carbon::today()->subDays(7);
        $end_date = Carbon::today()->endOfDay();
        
        $params['index'] = 'counting';
        $params['type'] = '_doc';
        $params['body']['size'] = 0;
        $params['body']['aggs']['date']['date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['date_histogram']['fixed_interval'] = '1D';
        $params['body']['aggs']['date']['date_histogram']['min_doc_count'] = 1;
        $params['body']['aggs']['date']['date_histogram']['time_zone'] = 'Asia/Jakarta';
        $params['body']['aggs']['date']['aggs']['type']['terms']['field'] = "categories.keyword";
        $params['body']['aggs']['date']['aggs']['type']['terms']['order']['_count'] = 'desc';
        
        $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];

        if($id !== null){ $params['body']['query']['bool']['filter'][]['match']['id_camera'] = $id; }
        $response = $this->client->search($params);

        if (empty($response['aggregations']['date']['buckets'])) {
            $datas = array(
                'categories' => [],
                'datasets' => [],
              );
        }else{
            $date = array_column($response['aggregations']['date']['buckets'], 'key_as_string');
    
            foreach ($date as $key => $value) {
                $date_new[] = date("d-m-Y", strtotime($value));
                $tmp = $response['aggregations']['date']['buckets'][$key]['type']['buckets'];
                foreach ($tmp as $key3 => $value3) {
                    if ($value3['key'] == 'mobil') {
                        $data[$key]['mobil'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'mobil') {
                    //Skipp
                    }
                    else{
                        $data[$key]['mobil'] =  0;
                    }
        
                    if ($value3['key'] == 'motor') {
                        $data[$key]['motor'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'motor') {
                    //Skipp
                    }
                    else{
                        $data[$key]['motor'] =  0;
                    }
        
                    if ($value3['key'] == 'truck') {
                        $data[$key]['truck'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'truck') {
                    //Skipp
                    }
                    else{
                        $data[$key]['truck'] =  0;
                    }
        
                    if ($value3['key'] == 'bus') {
                        $data[$key]['bus'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'bus') {
                    //Skipp
                    }
                    else{
                        $data[$key]['bus'] =  0;
                    }
                }

                $vehicle = ['mobil','motor','truck', 'bus'];

                foreach ($vehicle as $key2 => $value2) {
                    if (array_key_exists($value2, $data[$key]) == false){
                        $data_count = 0;
                    }else{
                        $data_count = $data[$key][$value2];
                    }
                    $trend[$key2]['name'] = $value2;
                    $trend[$key2]['data'][] = $data_count;
                }
            }

            $datas = array(
                'categories' => $date_new,
                'datasets' => array_values($trend),
            );
        }

        return $datas;
    }

    public function trend_range(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $id = $request->get('id_camera');
        $type = $request->get('vehicle_type');
        $lane = $request->get('lane');

        if ($type == ''){
            $type = 'mobil,motor,truck,bus';
        }

        $vehicle = ['mobil','motor','truck', 'bus'];

        $params['index'] = 'counting';
        $params['type'] = '_doc';
        $params['body']['size'] = 0;
        $params['body']['query']['bool']['filter'][]['match']['categories'] = $type;
        $params['body']['aggs']['date']['auto_date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['auto_date_histogram']['buckets'] = 10;
        $params['body']['aggs']['date']['auto_date_histogram']['time_zone'] = 'Asia/Jakarta';
        $params['body']['aggs']['date']['aggs']['type']['terms']['field'] = "categories.keyword";
        $params['body']['aggs']['date']['aggs']['type']['terms']['order']['_count'] = 'desc';
        $params['body']['aggs']['date']['aggs']['type']['terms']['missing'] = '__missing__';

        if ($start_date != '' && $end_date != '' ) {
            $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
        }else{
            $start_date = Carbon::today();
            $end_date = Carbon::today()->endOfDay();
            $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
        }

        if ($lane != 'all') {
            $params['body']['query']['bool']['filter'][]['match']['lane'] = $lane;
        }

        if($id !== null){ $params['body']['query']['bool']['filter'][]['terms']['id_camera'] = explode(",", $id); }

        $response = $this->client->search($params);

        if ($start_date == $end_date) {
            $format_interval = 'H:00';
        }else{
            $format_interval = 'd-M';
        }

        if (empty($response['aggregations']['date']['buckets'])) {
            $datas = array(
                'categories' => [],
                'datasets' => [],
              );
        }else{
            $date = array_column($response['aggregations']['date']['buckets'], 'key_as_string');
            foreach ($date as $key => $value) {
                $date_new[] = date($format_interval, strtotime($value));
                $tmp = $response['aggregations']['date']['buckets'][$key]['type']['buckets'];
                foreach ($tmp as $key3 => $value3) {
                    if ($value3['key'] == 'mobil') {
                        $data[$key]['mobil'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'mobil') {
                    //Skipp
                    }
                    else{
                        $data[$key]['mobil'] =  0;
                    }
        
                    if ($value3['key'] == 'motor') {
                        $data[$key]['motor'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'motor') {
                    //Skipp
                    }
                    else{
                        $data[$key]['motor'] =  0;
                    }
        
                    if ($value3['key'] == 'truck') {
                        $data[$key]['truck'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'truck') {
                    //Skipp
                    }
                    else{
                        $data[$key]['truck'] =  0;
                    }
        
                    if ($value3['key'] == 'bus') {
                        $data[$key]['bus'] =  $value3['doc_count'];
                    }
                    else if ($value3['key'] != 'bus') {
                    //Skipp
                    }
                    else{
                        $data[$key]['bus'] =  0;
                    }
                }
                
                foreach ($vehicle as $key2 => $value2) {
                    try {
                        if (array_key_exists($value2, $data[$key]) == false){
                            $data_count = 0;
                        }else{
                            $data_count = $data[$key][$value2];
                        }
                    } catch (\Throwable $th) {
                        $data_count = 0;
                    }
                    $trend[$key2]['name'] = $value2;
                    $trend[$key2]['data'][] = $data_count;
                }
            }
            
            $datas = array(
                'categories' => $date_new,
                'datasets' => array_values($trend),
            );
        }

        return $datas;
    }

    public function pie(Request $request, $id = null)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $lane = $request->get('lane');
        
        $vehicle = ['mobil','motor','truck', 'bus'];
    
        foreach ($vehicle as $key => $value) {
            $params[$key]['index'] = 'counting';
            $params[$key]['type'] = '_doc';
            $params[$key]['body']['size'] = 0;
            $params[$key]['body']['query']['bool']['filter'][]['match']['categories'] = $value;

            if($id !== null){ $params[$key]['body']['query']['bool']['filter'][]['match']['id_camera'] = $id; }

            if ($start_date != '' && $end_date != '' ) {
                $params[$key]['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
            }else{
                $start_date = Carbon::today();
                $end_date = Carbon::today()->endOfDay();
                $params[$key]['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
            }

            if ($lane != 'all' ) {
                $params[$key]['body']['query']['bool']['filter'][]['match']['lane'] = $lane;
            }

            $response[$key] = $this->client->search($params[$key]);

            $data[$key] = $response[$key]['hits']['total']['value'];

            if ($data[$key] == 0) {
                unset($data[$key]);
                unset($vehicle[$key]);
            }
        }
        $vehicle = array_values($vehicle);

        $datas = array(
            'categories' => $vehicle,
            'datasets' => array_values($data),
        );

        return $datas;
    }

    public function pie_range(Request $request)
    {
        $id = $request->get('id_camera');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $type = $request->get('vehicle_type');
        $lane = $request->get('lane');

        if ($type != '') {
            $type = explode(",", $type);
        }else{
            $type = ['mobil','motor','truck', 'bus'];
        }
        
        $vehicle = ['mobil','motor','truck', 'bus'];
    
        foreach ($vehicle as $key => $value) {
            $params[$key]['index'] = 'counting';
            $params[$key]['type'] = '_doc';
            $params[$key]['body']['size'] = 0;
            $params[$key]['body']['query']['bool']['filter'][]['match']['categories'] = $value;

            if($id !== null){ $params[$key]['body']['query']['bool']['filter'][]['terms']['id_camera'] = explode(",", $id); }

            if ($start_date != '' && $end_date != '' ) {
                $params[$key]['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
            }else{
                $start_date = Carbon::today();
                $end_date = Carbon::today()->endOfDay();
                $params[$key]['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];
            }

            if ($lane != 'all' ) {
                $params[$key]['body']['query']['bool']['filter'][]['match']['lane'] = $lane;
            }

            $response[$key] = $this->client->search($params[$key]);
            $data[$key] = $response[$key]['hits']['total']['value'];

            if (in_array($value, $type)){
                $data[$key] = $response[$key]['hits']['total']['value'];
            }else{
                $data[$key] = 0;
            }
        }
        $vehicle = array_values($vehicle);

        $datas = array(
            'categories' => $vehicle,
            'datasets' => array_values($data),
        );

        return $datas;
    }

    public function heatmap()
    {
        $start_date = Carbon::today()->subDays(7);
        $end_date = Carbon::today()->endOfDay();

        $params['index'] = 'counting';
        $params['type'] = '_doc';
        $params['body']['size'] = 0;
        // $params['body']['query']['match_all'] = new \stdClass();
        $params['body']['aggs']['date']['date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['date_histogram']['fixed_interval'] = '1d';
        $params['body']['aggs']['date']['date_histogram']['min_doc_count'] = 1;
        $params['body']['aggs']['date']['date_histogram']['time_zone'] = 'Asia/Jakarta';
        $params['body']['aggs']['date']['aggs']['hours']['date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['aggs']['hours']['date_histogram']['calendar_interval'] = '1h';
        $params['body']['aggs']['date']['aggs']['hours']['date_histogram']['time_zone'] = 'Asia/Jakarta';

        $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];

        $response = $this->client->search($params);

        if (empty($response['aggregations']['date']['buckets'])) {
            $count = array(
                'name' => [],
                'data' => [],
              );
        }else{
            $date = array_column($response['aggregations']['date']['buckets'], 'key_as_string');
            foreach ($date as $key => $value) {
                $date_new[] = date("d-M-Y", strtotime($value));
                $tmp = $response['aggregations']['date']['buckets'][$key]['hours']['buckets'];
                foreach ($tmp as $key2 => $value) {
                    $time_parse = Carbon::parse($value['key_as_string'])->format('H:i');
                    $data_count[$key][$time_parse] = $value['doc_count'];
                }

                for ($i=0; $i <= 23; $i++) {
                    $time = Carbon::parse($date_new[$key], 7)->addHours($i)->format('H:i');
                    if (array_key_exists($time, $data_count[$key])){
                        $count[$i]['name'] = $time;
                        $count[$i]['data'][$key] = $data_count[$key][$time];
                    }else{
                        $count[$i]['name'] = $time;
                        $count[$i]['data'][$key] = 0;
                    }
                }
            }
        }
        $datas = array(
            'date' => $date_new,
            'datasets' => array_values($count),
        );
        return $datas;
    }

    public function heatmap_range(Request $request)
    {
        $id = $request->get('id_camera');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $type = $request->get('vehicle_type');
        $lane = $request->get('lane');

        $params['index'] = 'counting';
        $params['type'] = '_doc';
        $params['body']['size'] = 0;
        $params['body']['query']['bool']['filter'][]['terms']['id_camera'] = explode(",", $id);
        $params['body']['query']['bool']['filter'][]['match']['categories'] = $type;
        $params['body']['aggs']['date']['auto_date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['auto_date_histogram']['buckets'] = 10;
        $params['body']['aggs']['date']['auto_date_histogram']['time_zone'] = 'Asia/Jakarta';
        $params['body']['aggs']['date']['aggs']['hours']['date_histogram']['field'] = 'timestamp';
        $params['body']['aggs']['date']['aggs']['hours']['date_histogram']['calendar_interval'] = '1h';
        $params['body']['aggs']['date']['aggs']['hours']['date_histogram']['time_zone'] = 'Asia/Jakarta';

        $params['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];

        $response = $this->client->search($params);

        if (empty($response['aggregations']['date']['buckets'])) {
            $count = array(
                'name' => [],
                'data' => [],
              );
        }else{
            $date = array_column($response['aggregations']['date']['buckets'], 'key_as_string');
            foreach ($date as $key => $value) {
                $date_new[] = date("d-M-Y", strtotime($value));
                $tmp = $response['aggregations']['date']['buckets'][$key]['hours']['buckets'];
                foreach ($tmp as $key2 => $value) {
                    $time_parse = Carbon::parse($value['key_as_string'])->format('H:i');
                    $data_count[$key][$time_parse] = $value['doc_count'];
                }

                for ($i=0; $i <= 23; $i++) {
                    $time = Carbon::parse($date_new[$key], 7)->addHours($i)->format('H:i');
                    try {
                        if (array_key_exists($time, $data_count[$key])){
                            $count[$i]['name'] = $time;
                            $count[$i]['data'][$key] = $data_count[$key][$time];
                        }else{
                            $count[$i]['name'] = $time;
                            $count[$i]['data'][$key] = 0;
                        }
                    } catch (\Throwable $th) {
                        $count[$i]['name'] = $time;
                        $count[$i]['data'][$key] = 0;
                    }
                }
            }
        }
        $datas = array(
            'date' => $date_new,
            'datasets' => array_values($count),
        );
        return $datas;
    }

    public function liveCount($id=null)
    {
        $start_date = Carbon::today();
        $end_date = Carbon::today()->endOfDay();

        $vehicle = ['mobil','motor','truck', 'bus'];
        $lane = ['In','Out'];

        foreach ($vehicle as $key => $value) {
            foreach ($lane as $key2 => $value2) {
            $params[$value][$value2]['index'] = 'counting';
            $params[$value][$value2]['type'] = '_doc';
            $params[$value][$value2]['body']['size'] = 0;
            $params[$value][$value2]['body']['query']['bool']['filter'][]['match']['id_camera'] = $id;
            $params[$value][$value2]['body']['query']['bool']['filter'][]['match']['categories'] = $value;
            $params[$value][$value2]['body']['query']['bool']['filter'][]['match']['lane'] = $value2;
            $params[$value][$value2]['body']['query']['bool']['must'][]['range']['timestamp'] = ['gte' => $start_date, 'lte' => $end_date, 'format' => 'strict_date_optional_time'];

            $response[$value][$value2] = $this->client->search($params[$value][$value2]);

            $data[$value][] = $response[$value][$value2]['hits']['total']['value'];
            }
        }
        return $data;
    }
}