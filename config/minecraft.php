<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Minecraft Server Settings
    |--------------------------------------------------------------------------
    */

    'directory' => env('MINECRAFT_SERVER_DIRECTORY', '/minecraft/server'),

    'jar' => env('MINECRAFT_SERVER_JAR', 'minecraft_server.jar'),

    'process_name' => env('MINECRAFT_PROCESS_NAME', 'minecraft_server'),

    'ram' => [

        'startup' => env('MINECRAFT_SERVER_RAM_STARTUP', '512M'),
        'max' => env('MINECRAFT_SERVER_RAM_MAX', '1G'),

    ],

];
