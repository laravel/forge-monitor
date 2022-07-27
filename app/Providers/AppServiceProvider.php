<?php

namespace App\Providers;

use App\Config\FileFinder;
use App\Monitors\MonitorConfig;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;
use XdgBaseDir\Xdg;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FileFinder::class, function ($app) {
            $finder = new Finder();

            return (new FileFinder($finder))
                ->addDirectory($this->getHomePath());
        });

        $this->app->singleton(MonitorConfig::class, function ($app) {
            return new MonitorConfig(app(FileFinder::class));
        });
    }

    /**
     * Get the homepath.
     *
     * @param  string|null  $path
     * @return string
     */
    protected function getHomePath($path = null)
    {
        $homePath = (new Xdg())->getHomeDir();

        if (! $path) {
            return $homePath;
        }

        return $homePath.DIRECTORY_SEPARATOR.$path;
    }
}
