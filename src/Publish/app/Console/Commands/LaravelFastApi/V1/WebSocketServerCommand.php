<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 13:32:27
 * @FilePath: \app\Console\Commands\LaravelFastApi\V1\WebSocketServerCommand.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Console\Commands\LaravelFastApi\V1;

use Illuminate\Console\Command;

use App\Facade\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacade;

class WebSocketServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the WebSocket server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         /* $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            8080
        );

        $server->run(); */

        PhoneSocketFacade::init();
    }
}
