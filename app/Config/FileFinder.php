<?php

namespace App\Config;

use Illuminate\Support\Arr;
use RuntimeException;
use Symfony\Component\Finder\Finder;

class FileFinder
{
    /**
     * The directories to look in.
     *
     * @var array
     */
    protected $directories = [];

    /**
     * The symfony file finder instance.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * Create a new config file finder instance.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     * @return  void
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        $this->finder
             ->ignoreDotFiles(false)
             ->depth('== 0')
             ->ignoreVCS(false)
             ->files();
    }

    /**
     * Add a directory to source for files.
     *
     * @param  string  $directory
     * @return $this
     */
    public function addDirectory($directory)
    {
        $this->directories[] = $directory;

        return $this;
    }

    /**
     * Find the files.
     *
     * @param  string  $filename
     * @return array
     *
     * @throws \RuntimeException
     */
    public function find(string $filename)
    {
        $files = $this->finder
                      ->in($this->directories)
                      ->name($filename);

        return Arr::first($files);
    }
}
