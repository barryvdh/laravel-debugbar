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


        if (isset($data['time'])) {
            $time = $data['time'];
            $output['responseTime'] = $time['end'];
            $output['responseDuration'] = $time['duration'] * 1000;
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

        if (isset($data['request'])) {
            $request = $data['request'];

            $output['responseStatus'] = $request['status_code'];
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

   protected function unvar_dump($str) {
        $str = str_replace($str, '\n', "\n");
        if (strpos($str, "\n") === false) {
            //Add new lines:
            $regex = array(
                '#(\\[.*?\\]=>)#',
                '#(string\\(|int\\(|float\\(|array\\(|NULL|object\\(|})#',
            );
            $str = preg_replace($regex, "\n\\1", $str);
            $str = trim($str);
        }
        $regex = array(
            '#^\\040*NULL\\040*$#m',
            '#^\\s*array\\((.*?)\\)\\s*{\\s*$#m',
            '#^\\s*string\\((.*?)\\)\\s*(.*?)$#m',
            '#^\\s*int\\((.*?)\\)\\s*$#m',
            '#^\\s*bool\\(true\\)\\s*$#m',
            '#^\\s*bool\\(false\\)\\s*$#m',
            '#^\\s*float\\((.*?)\\)\\s*$#m',
            '#^\\s*\[(\\d+)\\]\\s*=>\\s*$#m',
            '#\\s*?\\r?\\n\\s*#m',
        );
        $replace = array(
            'N',
            'a:\\1:{',
            's:\\1:\\2',
            'i:\\1',
            'b:1',
            'b:0',
            'd:\\1',
            'i:\\1',
            ';'
        );
        $serialized = preg_replace($regex, $replace, $str);
        $func = create_function(
            '$match',
            'return "s:".strlen($match[1]).":\\"".$match[1]."\\"";'
        );
        $serialized = preg_replace_callback(
            '#\\s*\\["(.*?)"\\]\\s*=>#',
            $func,
            $serialized
        );
        $func = create_function(
            '$match',
            'return "O:".strlen($match[1]).":\\"".$match[1]."\\":".$match[2].":{";'
        );
        $serialized = preg_replace_callback(
            '#object\\((.*?)\\).*?\\((\\d+)\\)\\s*{\\s*;#',
            $func,
            $serialized
        );
        $serialized = preg_replace(
            array('#};#', '#{;#'),
            array('}', '{'),
            $serialized
        );

        return unserialize($serialized);
    }
}
