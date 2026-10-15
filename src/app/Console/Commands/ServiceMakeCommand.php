<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ServiceMakeCommand extends GeneratorCommand
{
    // 実行するコマンド名
    protected $name = 'make:service';

    // コマンドの説明
    protected $description = 'Create a new service class';

    // 生成するクラスのタイプ
    protected $type = 'Service';

    // スタブファイルの場所を指定
    protected function getStub()
    {
        return base_path('stubs/service.stub');
    }

    // デフォルトの保存先（app/Services）
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services';
    }
}
