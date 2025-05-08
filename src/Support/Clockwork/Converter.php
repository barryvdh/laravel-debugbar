<?php

namespace Barryvdh\Debugbar\Support\Clockwork;

class Converter
{
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
            'log' => [],
            'postData' => [],
            'sessionData' => [],
            'timelineData' => [],
            'viewsData' => [],
            'controller' => null,
            'responseTime' => null,
            'responseStatus' => null,
            'responseDuration' => 0,
        ];

        if (isset($data['clockwork'])) {
            $output = array_merge($output, $data['clockwork']);
        }

        if (isset($data['memory']['peak_usage'])) {
            $output['memoryUsage'] = $data['memory']['peak_usage'];
        }

        if (isset($data['time']['measures'])) {
            $time = $data['time'];
            $output['time'] = $time['start'];
            $output['responseTime'] = $time['end'];
            $output['responseDuration'] = $time['duration'] * 1000;
            foreach ($time['measures'] as $measure) {
                $output['timelineData'][] = [
                    'data' => [],
                    'description' => $measure['label'],
                    'duration' => $measure['duration'] * 1000,
                    'end' => $measure['end'],
                    'start' => $measure['start'],
                    'relative_start' => $measure['start'] - $time['start'],
                ];
            }
        }

        if (isset($data['route'])) {
            $route = $data['route'];

            $controller = null;
            if (isset($route['controller'])) {
                $controller = $route['controller'];
            } elseif (isset($route['uses'])) {
                $controller = $route['uses'];
            }

            $output['controller'] = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '', (string) $controller) ?: null;

            list($method, $uri) = explode(' ', $route['uri'], 2);

            $output['routes'][] = [
                'action' => $output['controller'],
                'after' => isset($route['after']) ? $route['after'] : null,
                'before' => isset($route['before']) ? $route['before'] : null,
                'method' => $method,
                'name' => isset($route['as']) ? $route['as'] : null,
                'uri' => $uri,
            ];
        }

        if (isset($data['messages']['messages'])) {
            foreach ($data['messages']['messages'] as $message) {
                $output['log'][] = [
                    'message' => $message['message'],
                    'time' => $message['time'],
                    'level' => $message['label'],
                ];
            }
        }

        if (isset($data['queries']['statements'])) {
            $queries = $data['queries'];
            foreach ($queries['statements'] as $statement) {
                if ($statement['type'] === 'explain' || $statement['type'] === 'info') {
                    continue;
                }
                $output['databaseQueries'][] = [
                    'query' => $statement['sql'],
                    'bindings' => $statement['params'],
                    'duration' => $statement['duration'] * 1000,
                    'time' => $statement['start'] ?? null,
                    'connection' => $statement['connection']
                ];
            }

            $output['databaseDuration'] = $queries['accumulated_duration'] * 1000;
        }

        if (isset($data['models']['data'])) {
            $output['modelsActions'] = [];
            $output['modelsCreated'] = [];
            $output['modelsUpdated'] = [];
            $output['modelsDeleted'] = [];
            $output['modelsRetrieved'] = [];

            foreach ($data['models']['data'] as $model => $value) {
                foreach ($value as $event => $count) {
                    $eventKey = 'models' . ucfirst($event);
                    if (isset($output[$eventKey])) {
                        $output[$eventKey][$model] = $count;
                    }
                }
            }
        }

        if (isset($data['views']['templates'])) {
            foreach ($data['views']['templates'] as $view) {
                $output['viewsData'][] = [
                    'description' => 'Rendering a view',
                    'duration' => 0,
                    'end' => 0,
                    'start' => $view['start'] ?? 0,
                    'data' => [
                        'name' => $view['name'],
                        'data' => $view['params'],
                    ],
                ];
            }
        }

        if (isset($data['event']['measures'])) {
            foreach ($data['event']['measures'] as $event) {
                $event['data'] = [];
                $event['listeners'] = [];
                foreach ($event['params'] ?? [] as $key => $param) {
                    $event[is_numeric($key) ? 'data' : 'listeners'] = $param;
                }
                $output['events'][] = [
                    'event' => ['event' => $event['label']],
                    'data' => $event['data'],
                    'time' => $event['start'],
                    'duration' => $event['duration'] * 1000,
                    'listeners' => $event['listeners'],
                ];
            }
        }

        if (isset($data['symfonymailer_mails']['mails'])) {
            foreach ($data['symfonymailer_mails']['mails'] as $mail) {
                $output['emailsData'][] = [
                    'data' => [
                        'to' => implode(', ', $mail['to']),
                        'subject' => $mail['subject'],
                        'headers' => isset($mail['headers']) ? explode("\n", $mail['headers']) : null,
                    ],
                ];
            }
        }

        return $output;
    }
}
