<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laracasts\Generators\Migrations\NameParser;
use Laracasts\Generators\Migrations\SchemaParser;
use Laracasts\Generators\Migrations\SyntaxBuilder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrationCommand extends Command
{
    protected $name = 'generate:crud';
    protected $description = 'Create a new controller,model & migration at the same time';
    protected $files;
    private $composer;


    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = app()['composer'];
    }

    public function handle()
    {
        $this->directory = ucwords($this->ask('Which directory ?e.g Product,User,Category'));
        $this->fire();
    }

    public function fire()
    {
        $this->makeController();
        $this->makeModel();
        $this->makeMigration();
        $this->makeView();
    }

    private function makeController()
    {
        $name = 'controller';
        $path = $this->getPath($name);

        $this->checkExists($path, $name);
        $this->makeDirectory($path);

        $this->files->put($path, $this->compileControllerStub());
        $filename = pathinfo($path, PATHINFO_FILENAME);

        $this->line("<info>Created Controller:</info> {$filename}");
    }

    private function makeModel()
    {
        $name = 'model';
        $path = $this->getPath($name);

        $this->checkExists($path, $name);
        $this->makeDirectory($path);

        $this->files->put($path, $this->compileModelStub());
        $filename = pathinfo($path, PATHINFO_FILENAME);

        $this->line("<info>Created Model:</info> {$filename}");

    }

    private function makeView()
    {
        $name = 'view';
        $paths = $this->getPath($name);

        foreach ($paths as $path) {
            $this->checkExists($path, $name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->compileViewStub());
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $this->line("<info>Created View:</info> {$filename}");
        }

//        $this->line("<info>Created View:</info> {$filename}");

    }

    private function makeMigration()
    {
        $name = 'migration';
        $path = $this->getPath($name);

        $this->checkExists($path, $name);
        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $this->line("<info>Created Migration:</info> {$filename}");
    }

    protected function compileControllerStub()
    {
        $stub = $this->files->get(__DIR__ . '/stubs/Controller.stub');
        $name = 'controller';

        $this->replaceClassName($stub, $name)
            ->replaceNameSpace($stub);

        return $stub;
    }

    protected function compileModelStub()
    {
        $stub = $this->files->get(__DIR__ . '/stubs/Model.stub');

        $this->replaceClassName($stub)
            ->replaceNameSpace($stub);

        return $stub;
    }

    protected function compileMigrationStub()
    {
        $stub = $this->files->get(__DIR__ . '/stubs/migration.stub');

        $this->replaceClassName($stub)
            ->replaceNameSpace($stub);

        return $stub;
    }

    protected function compileViewStub()
    {
        $stub = $this->files->get(__DIR__ . '/stubs/view/create.blade.stub');
        $stub = $this->files->get(__DIR__ . '/stubs/view/edit.blade.stub');
        $stub = $this->files->get(__DIR__ . '/stubs/view/list.blade.stub');

        return $stub;

    }

    protected function replaceClassName(&$stub, $name = null)
    {

        $className = ucwords($this->directory);
        if ($name === 'controller') {
            $className = $className . 'Controller';
        }

        $stub = str_replace('{{class}}', $className, $stub);

        return $this;
    }

    protected function replaceNameSpace(&$stub)
    {
        $nameSpace = ucwords($this->directory);

        $stub = str_replace('{{namespace}}', $nameSpace, $stub);

        return $this;
    }

    protected function getPath($name)
    {
        if ($name === 'controller') {
            return base_path() . '/app/Http/Controllers/' . $this->directory . '/' . $this->directory . 'Controller.php';
        }
        if ($name === 'model') {
            return base_path() . '/app/Models/' . $this->directory . '/' . $this->directory . '.php';
        }
        if ($name === 'view') {
            $basePath = base_path() . '/resources/views/';
            $path['create'] = $basePath . $this->directory . '/create.blade.stub';
            $path['list'] = $basePath . $this->directory . '/list.blade.stub';
            $path['edit'] = $basePath . $this->directory . '/edit.blade.stub';
            return $path;
        }
        if ($name === 'migration') {
            return base_path() . '/database/migrations/' . date('Y_m_d_His') . '_' . 'create_' . $this->directory . '_table.php';
        }

    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function checkExists($path, $name)
    {
        if ($name === 'controller' && $this->files->exists($path)) {
            return $this->error('Controller already exists!');
        }
        if ($name === 'model' && $this->files->exists($path)) {
            return $this->error('Model already exists!');
        }
        if ($name === 'view' && $this->files->exists($path)) {
            return $this->error('View already exists!');
        }
        if ($name === 'migration' && $this->files->exists($path)) {
            return $this->error('Migration already exists!');
        }

    }


    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_OPTIONAL, 'name =', null]
        ];
    }

}
