<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Illuminate\Support\Str;

/**
 * Collector for Laravel's gate checks
 */
class GateCollector extends MessagesCollector
{
    /** @var int */
    protected $backtraceLimit = 15;

    /** @var array */
    protected $reflection = [];

    /** @var \Illuminate\Routing\Router */
    protected $router;

    /**
     * @param Gate $gate
     */
    public function __construct(Gate $gate, Router $router)
    {
        parent::__construct('gate');
        $this->router = $router;
        $this->setDataFormatter(new SimpleFormatter());
        $gate->after(function ($user, $ability, $result, $arguments = []) {
            $this->addCheck($user, $ability, $result, $arguments);
        });
    }

    /**
     * {@inheritDoc}
     */
    protected function customizeMessageHtml($messageHtml, $message)
    {
        $pos = strpos((string) $messageHtml, 'array:5');
        if ($pos !== false) {

            $name = $message['ability'] .' ' . $message['target'] ?? '';

            $messageHtml = substr_replace($messageHtml, $name, $pos, 7);
        }

        return parent::customizeMessageHtml($messageHtml, $message);
    }

    public function addCheck($user, $ability, $result, $arguments = [])
    {
        $userKey = 'user';
        $userId = null;

        if ($user) {
            $userKey = Str::snake(class_basename($user));
            $userId = $user instanceof Authenticatable ? $user->getAuthIdentifier() : $user->getKey();
        }

        $label = $result ? 'success' : 'error';

        if ($result instanceof Response) {
            $label = $result->allowed() ? 'success' : 'error';
        }

        $target = null;
        if (isset($arguments[0])) {
            if ($arguments[0] instanceof Model) {
                $model = $arguments[0];
                if ($model->getKeyName() && isset($model[$model->getKeyName()])) {
                    $target = get_class($model) . '(' . $model->getKeyName() . '=' . $model->getKey() . ')';
                } else {
                    $target = get_class($model);
                }
            } else if (is_string($arguments[0])) {
                $target = $arguments[0];
            }
        }

        $this->addMessage([
            'ability' => $ability,
            'target' => $target,
            'result' => $result,
            $userKey => $userId,
            'arguments' => $this->getDataFormatter()->formatVar($arguments),
        ], $label, false);
    }

    /**
     * @param array $stacktrace
     *
     * @return array
     */
    protected function getStackTraceItem($stacktrace)
    {
        foreach ($stacktrace as $i => $trace) {
            if (!isset($trace['file'])) {
                continue;
            }

            if (str_ends_with($trace['file'], 'Illuminate/Routing/ControllerDispatcher.php')) {
                $trace = $this->findControllerFromDispatcher($trace);
            } elseif (str_starts_with($trace['file'], storage_path())) {
                $hash = pathinfo($trace['file'], PATHINFO_FILENAME);

                if ($file = $this->findViewFromHash($hash)) {
                    $trace['file'] = $file;
                }
            }

            if ($this->fileIsInExcludedPath($trace['file'])) {
                continue;
            }

            return $trace;
        }

        return $stacktrace[0];
    }

    /**
     * Find the route action file
     *
     * @param  array $trace
     * @return array
     */
    protected function findControllerFromDispatcher($trace)
    {
        /** @var \Closure|string|array $action */
        $action = $this->router->current()->getAction('uses');

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);

            $reflection = new \ReflectionMethod($controller, $method);
            $trace['file'] = $reflection->getFileName();
            $trace['line'] = $reflection->getStartLine();
        } elseif ($action instanceof \Closure) {
            $reflection = new \ReflectionFunction($action);
            $trace['file'] = $reflection->getFileName();
            $trace['line'] = $reflection->getStartLine();
        }

        return $trace;
    }

    /**
     * Find the template name from the hash.
     *
     * @param  string $hash
     * @return null|array
     */
    protected function findViewFromHash($hash)
    {
        $finder = app('view')->getFinder();

        if (isset($this->reflection['viewfinderViews'])) {
            $property = $this->reflection['viewfinderViews'];
        } else {
            $reflection = new \ReflectionClass($finder);
            $property = $reflection->getProperty('views');
            $property->setAccessible(true);
            $this->reflection['viewfinderViews'] = $property;
        }

        $xxh128Exists = in_array('xxh128', hash_algos());

        foreach ($property->getValue($finder) as $name => $path) {
            if (($xxh128Exists && hash('xxh128', 'v2' . $path) == $hash) || sha1('v2' . $path) == $hash) {
                return $path;
            }
        }
    }
}
