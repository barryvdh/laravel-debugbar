<?php

namespace Barryvdh\Debugbar\Storage;

use DebugBar\Storage\StorageInterface;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Stores collected data into files
 */
class FilesystemStorage implements StorageInterface
{
    protected $dirname;
    protected $files;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files The filesystem
     * @param string $dirname Directories where to store files
     */
    public function __construct($files, $dirname)
    {
        $this->files = $files;
        $this->dirname = rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritDoc}
     */
    public function save($id, $data)
    {
        if (!$this->files->isDirectory($this->dirname)) {
            if($this->files->makeDirectory($this->dirname, 0777, true)){
                $this->files->put($this->dirname.'.gitignore', "*\n!.gitignore");
            }else{
                throw new \Exception("Cannot create directory '$this->dirname'..");
            }
        }
        $this->files->put($this->makeFilename($id), json_encode($data));
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        return json_decode($this->files->get($this->makeFilename($id)), true);
    }

    /**
     * {@inheritDoc}
     */
    public function find(array $filters = array(), $max = 20, $offset = 0)
    {
        // Sort by modified time, newest first
        $sort = function (\SplFileInfo $a, \SplFileInfo $b)
        {
            return strcmp($b->getMTime(), $a->getMTime());
        };

        // Loop through .json files, filter the metadata and stop when max is found.
        $i = 0;
        $results = array();
        foreach(Finder::create()->files()->name('*.json')->in($this->dirname)->sort($sort) as $file){
            if($i++ < $offset && empty($filters)){
                $results[] = null;
                continue;
            }
            $data = json_decode($file->getContents(), true);
            $meta = $data['__meta'];
            unset($data);
            if (array_keys(array_intersect($meta, $filters)) == array_keys($filters)) {
                $results[] = $meta;
            }
            if(count($results) >= ($max + $offset)){
                break;
            }
        }
        return array_slice($results, $offset, $max);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        foreach(Finder::create()->files()->name('*.json')->in($this->dirname) as $file){
            $this->files->delete($file->getRealPath());
        }
    }

    public function makeFilename($id)
    {
        return $this->dirname . basename($id). ".json";
    }
}