<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Minecraft Server Settings
    |--------------------------------------------------------------------------
    */

    'edition' => env('MINECRAFT_EDITION', 'java'),

    'directory' => env('MINECRAFT_SERVER_DIRECTORY', '/minecraft/server'),

    'exec' => env('MINECRAFT_SERVER_EXEC', 'minecraft_server.jar'),

    'process_name' => env('MINECRAFT_PROCESS_NAME', 'minecraft_server'),

    'ram' => [

        'startup' => env('MINECRAFT_SERVER_RAM_STARTUP', '512M'),
        'max' => env('MINECRAFT_SERVER_RAM_MAX', '1G'),

    ],

];
