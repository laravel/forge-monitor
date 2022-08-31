<?php

namespace App\Monitors;

use App\Config\FileFinder;
use Illuminate\Support\Arr;
use Yosymfony\Toml\Toml;

class MonitorConfig
{
    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    protected $configFile;

    /**
     * The base config collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * Create a new monitor config instance.
     *
     * @param  \App\Config\FileFinder  $configFileFinder
     * @return void
     */
    public function __construct(FileFinder $configFileFinder)
    {
        $this->configFile = $configFileFinder->find('/\.monitor/');
        $this->config = $this->parseConfig();
    }

    /**
     * Return the configurations for a given stat type.
     *
     * @param  array|string  $types
     * @return mixed
     */
    public function forType($types)
    {
        $types = Arr::wrap($types);

        return $this->config->whereIn('type', $types);
    }

    /**
     * Parse the config file into a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function parseConfig()
    {
        if (! $this->configFile) {
            return collect();
        }

        return collect(Toml::ParseFile($this->configFile->getPathname()))->transform(function ($monitor, $key) {
            return new Monitor(
                $key,
                Arr::get($monitor, 'type'),
                Arr::get($monitor, 'operator'),
                Arr::get($monitor, 'threshold'),
                Arr::get($monitor, 'minutes', 0), // Disk requires no minutes.
                Arr::get($monitor, 'token')
            );
        });
    }

    /**
     * Get the config path.
     *
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    public function getConfigPath()
    {
        return $this->configFile;
    }
}
