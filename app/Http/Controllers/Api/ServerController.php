<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ServerService as Server;

class ServerController extends Controller
{
    /**
     * Handle server control actions.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        switch ($request->input('action')) {
            case 'start':
                return $this->start();
            break;
            case 'stop':
                return $this->stop();
            break;
            default:
                return response()->json([
                    'message' => 'OK'
                ], 200);
            break;
        }
    }

    /**
     * Send a command to the server.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function command(Request $request)
    {
        return response()->json(Server::cmd($request->input('cmd')), 200);
    }

    /**
     * Fetch the log file.
     *
     * @return Illuminate\Http\Response
     */
    public function log()
    {
        return response()->json(mclogparse2(file_backread(config('minecraft.directory') . '/logs/latest.log', 64)), 200);
    }

    /**
     * Determine if the server is running.
     *
     * @return Illuminate\Http\Response
     */
    public function running()
    {
        return response()->json(Server::isRunning(), 200);
    }

    /**
     * Start the Minecraft server.
     *
     * @return Illuminate\Http\Response
     */
    private function start()
    {
        Server::start();

        return response()->json([
            'message' => 'OK'
        ], 200);
    }

    /**
     * Stop the Minecraft server.
     *
     * @return Illuminate\Http\Response
     */
    private function stop()
    {
        Server::stop();

        return response()->json([
            'message' => 'OK'
        ], 200);
    }
}
