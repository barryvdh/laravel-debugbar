<?php namespace Barryvdh\Debugbar\Support\Clockwork;

class Converter {

    /**
     * Convert the phpdebugbar data to Clockwork format.
     *
     * @param  array $data
     * @return array
     */
    public function convert($data)
    {
        $meta = $data['__meta'];

        // Default output
        $output = [
            'id' => $meta['id'],
            'method' => $meta['method'],
            'uri' => $meta['uri'],
            'time' => $meta['utime'],
            'headers' => [],
            'cookies' => [],
            'emailsData' => [],
            'getData' => [],
            'headers' => [],
            'log' => [],
            'postData' => [],
            'sessionData' => [],
            'timelineData' => [],
            'viewsData' => [],
            'controller' => null,
            'responseTime' => null,
            'responseStatus' => null,
            'responseDuration' => 0,
            'log' => [],
        ];


        if (isset($data['request'])) {
            $request = $data['request'];

            $output['responseStatus'] = $request['status_code'];
            foreach($request as $key => $value){
                $output['headers'][$key] = [$value];
            }
        }

        if (isset($data['time'])) {
            $time = $data['time'];
            $output['responseTime'] = $time['end'];
            $output['responseDuration'] = $time['duration'] * 1000;
            foreach($time['measures'] as $measure) {
                $measure['duration'] = $measure['duration'] * 1000;
                $output['timelineData'][] = $measure;
            }
        }

        if (isset($data['route'])) {
            $route = $data['route'];

            if (isset($route['uses'])) {
                $output['controller'] = $route['uses'];
            }
        }

        if (isset($data['messages'])) {
            $messages = $data['messages'];
            $output['messages'] = $messages['messages'];
        }



        if (isset($data['queries'])) {
            $queries = $data['queries'];
            foreach($queries['statements'] as $statement){
                $output['databaseQueries'][] = [
                    'query' => $statement['sql'],
                    'bindings' => $statement['params'],
                    'time' => $statement['duration'] * 1000,
                    'connection' => $statement['connection']
                ];
            }

            $output['databaseDuration'] = $queries['accumulated_duration'] * 1000;

        }

        return $output;
    }

}
