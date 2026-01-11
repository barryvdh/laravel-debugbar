<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Storage;

use DebugBar\Storage\AbstractStorage;
use DebugBar\Storage\StorageInterface;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Stores collected data into files
 */
class FilesystemStorage extends AbstractStorage implements StorageInterface
{
    protected string $dirname;
    protected Filesystem $files;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files   The filesystem
     * @param string                            $dirname Directories where to store files
     */
    public function __construct(Filesystem $files, string $dirname)
    {
        $this->files = $files;
        $this->dirname = rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $id, array $data): void
    {
        if (!$this->files->isDirectory($this->dirname)) {
            if ($this->files->makeDirectory($this->dirname, 0o777, true)) {
                $this->files->put($this->dirname . '.gitignore', "*\n!.gitignore\n");
            } else {
                throw new \Exception("Cannot create directory '$this->dirname'..");
            }
        }

        try {
            $this->files->put($this->makeFilename($id), json_encode($data));
        } catch (\Exception $e) {
            //TODO; error handling
        }

        $this->autoPrune();
    }

    /**
     * Create the filename for the data, based on the id.
     *
     */
    public function makeFilename(string $id): string
    {
        return $this->dirname . basename($id) . ".json";
    }

    /**
     * Delete files older than a certain age
     */
    public function prune(int $hours = 24): void
    {
        foreach (
            Finder::create()->files()->name('*.json')->date('< ' . $hours . ' hour ago')->in(
                $this->dirname,
            ) as $file
        ) {
            $this->files->delete($file->getRealPath());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id): array
    {
        $fileName = $this->makeFilename($id);
        if (!$this->files->exists($fileName)) {
            return [];
        }

        return json_decode($this->files->get($fileName), true);
    }

    /**
     * {@inheritDoc}
     */
    public function find(array $filters = [], int $max = 20, int $offset = 0): array
    {
        // Sort by modified time, newest first
        $sort = function (\SplFileInfo $a, \SplFileInfo $b) {
            return $b->getMTime() <=> $a->getMTime();
        };

        // Loop through .json files, filter the metadata and stop when max is found.
        $i = 0;
        $results = [];
        foreach (Finder::create()->files()->name('*.json')->in($this->dirname)->sort($sort) as $file) {
            if ($i++ < $offset && empty($filters)) {
                $results[] = null;
                continue;
            }
            $data = json_decode($file->getContents(), true);
            $meta = $data['__meta'];
            unset($data);
            if ($this->filter($meta, $filters)) {
                $results[] = $meta;
            }
            if (count($results) >= ($max + $offset)) {
                break;
            }
        }
        return array_slice($results, $offset, $max);
    }

    /**
     * Filter the metadata for matches.
     *
     */
    protected function filter(array $meta, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if (!isset($meta[$key]) || fnmatch($value, $meta[$key]) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        foreach (Finder::create()->files()->name('*.json')->in($this->dirname) as $file) {
            $this->files->delete($file->getRealPath());
        }
    }
}
