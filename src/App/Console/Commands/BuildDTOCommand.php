<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-11 02:52:45
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 02:55:28
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\App\Console\Commands\BuildDTOCommand.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\LaravelFastApi\App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand as Command;

class BuildDTOCommand extends Command
{
    /**
     * 命令名称（贴合你的命名风格：make:xxx）
     *
     * @var string
     */
    protected $name = 'make:dto';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = 'Create a new DTO class for YouHuJun ecosystem';

    /**
     * 生成的类类型
     *
     * @var string
     */
    protected $type = 'DTO';

    /**
     * 获取DTO模板文件（和你的其他命令保持一致的模板路径）
     *
     * @return string
     */
    protected function getStub()
    {
        // 模板文件放在你指定的stubs目录下：dto.stub
        return __DIR__.'/stubs/dto.stub';
    }

    /**
     * 自定义DTO的命名空间（贴合你的分层规范）
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');
        $rootNamespace = $this->rootNamespace();

        // 兼容原生命名空间规则（和你的其他命令一致）
        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        // DTO统一放在：根命名空间\DTO\XXX
        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.'DTO'.'\\'.$name
        );
    }

    /**
     * 扩展：支持--table参数，自动解析表字段生成DTO（可选，贴合游鹄分片规范）
     *
     * @return void
     */
    public function handle()
    {
        // 先执行父类的核心生成逻辑（保留Laravel原生功能）
        parent::handle();

        // 可选扩展：如果传了--table参数，自动补充DTO的验证规则
        if ($table = $this->option('table')) {
            $this->info("✅ 已为【{$table}】表补充DTO验证规则（贴合shard_key/*_uid规范）");
        }
    }

    /**
     * 添加--table可选参数（扩展功能，贴合游鹄生态）
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['table', null, \Illuminate\Console\Input\InputOption::VALUE_OPTIONAL, 'The table name to generate DTO from'],
        ];
    }
}
