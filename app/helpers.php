<?php

use XdgBaseDir\Xdg;

if (!function_exists('home_data_path')) {
    /**
     * Get the user's home data path.
     *
     * @param  string|null  $path
     * @return string
     */
    function home_data_path($path = null)
    {
        $homePath = (new Xdg())->getHomeDir();

        if (!$path) {
            return $homePath;
        }

        return $homePath.DIRECTORY_SEPARATOR.$path;
    }
}
