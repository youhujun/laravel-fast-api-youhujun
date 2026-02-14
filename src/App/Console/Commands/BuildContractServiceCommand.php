<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-19 11:48:08
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-12 21:51:21
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\App\Console\Commands\BuildContractServiceCommand.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-02-09 13:42:27
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-01 23:00:40
 */

namespace YouHuJun\LaravelFastApi\App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand as Command;

//use Illuminate\Console\Command;

class BuildContractServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'contract:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class implent interface';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ContractService';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /* public function __construct()
    {
        parent::__construct();
    } */

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = null;

        $stub = '/stubs/contract-service.stub';

        return __DIR__.$stub;
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name."Service");

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.'Services'.'\\'.'Contract'.'\\'.$name
        );
    }
}
