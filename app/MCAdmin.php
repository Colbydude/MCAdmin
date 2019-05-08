<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class MCAdmin
{
    /**
     * Passes down any variables from the backend to use for our frontend.
     *
     * @return string
     */
    public static function jsonVariables()
    {
        return [
            'serverConfig' => [
                'directory' => config('minecraft.directory'),
                'jar' => config('minecraft.exec'),
                'startupRam' => config('minecraft.ram.startup'),
                'maxRam' => config('minecraft.ram.max')
            ],
            'userId' => Auth::id() ?? null,
        ];
    }

    /**
     * Dynamically proxy static method calls.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     */
    public static function __callStatic($method, $parameters)
    {
        if (! property_exists(get_called_class(), $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return static::${$method};
    }
}
