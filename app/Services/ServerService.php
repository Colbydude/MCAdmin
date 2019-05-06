<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Exceptions\ServerException;

class ServerService
{
    const CMD_SERVER_START = 'tmux new -d -s %s java -Xms%s -Xmx%s -jar %s nogui';
    const CMD_SERVER_EXEC = 'tmux send-keys -t %s "%s" C-m';
    const CMD_SERVER_KILL = 'tmux kill-session -t %s';
    const CMD_SERVER_KILLALL = 'tmux kill-server';

    /**
     * The Minecraft server instance.
     *
     * @var
     */
    private $instance;

    /**
     * Start the Minecraft server.
     *
     * @return void
     */
    public static function start()
    {
        // Make sure the server isn't already running.
        if (self::isRunning()) {
            throw new ServerException('Server already running.');
        }

        // Make sure the server jar exists.
        if (!file_exists(config('minecraft.directory') . '/' . config('minecraft.jar'))) {
            throw new ServerException('Could not find server jar file at '. config('minecraft.directory') . '/' . config('minecraft.jar') . '.');
        }

        // Launch server process in a detached GNU Screen.
        shell_exec(
            'cd ' . escapeshellarg(config('minecraft.directory')) . '; ' .   // Change to server directory.
            sprintf(
                self::CMD_SERVER_START,
                escapeshellarg(config('minecraft.process_name')),
                config('minecraft.ram.startup'),
                config('minecraft.ram.max'),
                escapeshellarg(config('minecraft.jar'))
            )
        );
    }

    /**
     * Run a command on the server.
     *
     * @param  string  $cmd
     * @return void
     */
    public static function cmd($cmd) {
        shell_exec(
            sprintf(
                self::CMD_SERVER_EXEC,
                escapeshellarg(config('minecraft.process_name')),
                str_replace(['\\', '"'], ['\\\\', '\\"'], (get_magic_quotes_gpc() ? stripslashes($cmd) : $cmd))
            )
        );
    }

    /**
     * Safely shut down the server.
     *
     * @return void
     */
    public static function stop()
    {
        shell_exec(
            // Run "stop" on the server.
            sprintf(
                self::CMD_SERVER_EXEC,
                escapeshellarg(config('minecraft.process_name')),
                'stop'
            ) . ';' .

            // Wait 5 seconds to ensure server has saved.
            'sleep 5;' .

            // Kill the process.
            sprintf(
                self::CMD_SERVER_KILL,
                escapeshellarg(config('minecraft.process_name'))
            )
        );
    }

    /**
     * Immediately/force shut down the server.
     *
     * @return void
     */
    public static function kill()
    {
        shell_exec(
            sprintf(
                self::CMD_SERVER_KILL,
                escapeshellarg(config('minecraft.process_name'))
            )
        );
    }

    /**
     * EMERGENCY: Kill all running GNU Screens.
     *
     * @return void
     */
    public static function killAll()
    {
        shell_exec(self::CMD_SERVER_KILLALL);
    }

    /**
     * Check if the server is running.
     *
     * @return bool
     */
    public static function isRunning()
    {
        return Str::contains(shell_exec('tmux ls'), config('minecraft.process_name'));
    }
}
