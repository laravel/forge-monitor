<?php

namespace App\Monitors;

use App\Config\FileFinder;
use Illuminate\Support\Arr;
use Yosymfony\Toml\Toml;

class MonitorConfig
{
    /**
     * The config file finder instance.
     *
     * @var \App\Config\FileFinder
     */
    protected $configFileFinder;

    /**
     * The base config collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * Create a new monitor config instance.
     *
     * @param  \App\Config\FileFinder $configFileFinder
     * @return void
     */
    public function __construct(FileFinder $configFileFinder)
    {
        $this->configFileFinder = $configFileFinder;

        $this->config = $this->parseConfig();
    }

    /**
     * Return the configurations for a given stat type.
     *
     * @param  array|string $types
     * @return mixed
     */
    public function forType($types)
    {
        $types = Arr::wrap($types);

        if ($this->config) {
            return $this->config->whereIn('type', $types);
        }
    }

    /**
     * Parse the config file into a collection.
     *
     * @return \Illuminate\Support\Collection|void
     */
    protected function parseConfig()
    {
        $configFile = $this->configFileFinder->find('/\.monitor$/');

        if (!$configFile) {
            return;
        }

        $validTypes = ['disk', 'cpu_load', 'free_memory', 'used_memory'];
        $validOperators = ['gte', 'lte'];

        return collect(Toml::ParseFile($configFile))
            ->filter(function ($monitor) use ($validTypes, $validOperators) {
                $type = Arr::get($monitor, 'type');
                $operator = Arr::get($monitor, 'operator');
                $threshold = Arr::get($monitor, 'threshold');
                $token = Arr::get($monitor, 'token');

                return $type && in_array($type, $validTypes)
                    && $operator && in_array($operator, $validOperators)
                    && $threshold !== null
                    && $token;
            })
            ->transform(function ($monitor, $key) {
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
}
