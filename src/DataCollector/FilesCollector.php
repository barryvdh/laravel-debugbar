<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Foundation\Application;

class FilesCollector extends DataCollector  implements Renderable
{

    /** @var \Illuminate\Foundation\Application  */
    protected $app;
    protected $basePath;
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app = null)
    {
        $this->app = $app;
        $this->basePath = $app['path.base'];
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $files = $this->getIncludedFiles();
        $compiled = $this->getCompiledFiles();

        $included = array();
        $alreadyCompiled = array();
        foreach($files as $file){
            // Skip the files from Debugbar, they are only loaded for Debugging and confuse the output.
            // Of course some files are stil always loaded (ServiceProvider, Facade etc)
            if(strpos($file, 'vendor/maximebf/debugbar/src') !== false  || strpos($file, 'vendor/barryvdh/laravel-debugbar/src') !== false){
                continue;
            }elseif(!in_array($file,$compiled)){
                $included[] = array(
                    'message' => "'".$this->stripBasePath($file)."',",      // Use PHP syntax so we can copy-paste to compile config file.
                    'is_string' => true,
                );
            }else{
                $alreadyCompiled[] = array(
                    'message' => "* '".$this->stripBasePath($file)."',",    // Mark with *, so know they are compiled anyways.
                    'is_string' => true,
                );
            }
        }

        // First the included files, then those that are going to be compiled.
        $messages = array_merge($included, $alreadyCompiled);

        return array(
                 'messages' => $messages,
                 'count'=> count($included),
             );
    }

    /**
     * Get the files that are going to be compiled, so they aren't as important.
     *
     * @return array
     */
    protected function getCompiledFiles(){
        if($this->app && class_exists('Illuminate\Foundation\Console\OptimizeCommand')){
            $reflector = new \ReflectionClass('Illuminate\Foundation\Console\OptimizeCommand');
            $path = dirname($reflector->getFileName()) . '/Optimize/config.php';

            if(file_exists($path)){
                $app = $this->app;
                $core = require $path;
                return array_merge($core, $app['config']['compile']);
            }
        }
        return array();
    }

    /**
     * Get the files included on load.
     *
     * @return array
     */
    protected function getIncludedFiles(){
        return get_included_files();
    }

    /**
     * Remove the basePath from the paths, so they are relative to the base
     *
     * @param $path
     * @return string
     */
    protected function stripBasePath($path){
        return ltrim(str_replace($this->basePath, '', $path), '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'files';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $name=$this->getName();
        return array(
            "$name" => array(
                "icon" => "files-o",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.messages",
                "default" => "{}"
            ),
            "$name:badge" => array(
                "map" => "$name.count",
                "default" => "null"
            )
        );
    }
}
