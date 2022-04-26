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
    protected $directory;
    protected $controller;
    protected $model;
    protected $migration;
    protected $view;
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
        $this->controller = ucwords($this->ask('What is your controller name?e.g ProductController'));
        $this->model = ucwords($this->ask('What is your Model name?e.g Product,User,Category'));
//        $this->migration = ucwords($this->ask('What is your Migration name?e.g create_product_table'));
        $this->fire();
    }

    public function fire()
    {
        $this->makeController();
        $this->makeModel();
    }

    private function makeController()
    {

        $path =$this->getPath();
        $this->makeDirectory($path);

        $this->files->put($path, $this->compileControllerStub());

        $filename = pathinfo($path, PATHINFO_FILENAME);

        $this->line("<info>Created Controller:</info> {$filename}");

        $this->composer->dumpAutoloads();
    }

    private function makeModel()
    {
        $model = $this->model . 'Model';
        $path =base_path() . '/app/Models'.'/'. $model.'.php';

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileModelStub());

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $this->line("<info>Created Model:</info> {$filename}");

        $this->composer->dumpAutoloads();
    }

    private function makeView()
    {
        $view = $this->model . 'View';
        $path =base_path() . '/resources/views/' . $view;

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $this->line("<info>Created Migration:</info> {$filename}");

        $this->composer->dumpAutoloads();
    }

    private function makeMigration()
    {
        $migration = $this->model . 'Migration';
        $path =base_path() . '/database/migrations'.'/'. $migration.'.php';

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $this->line("<info>Created Migration:</info> {$filename}");

        $this->composer->dumpAutoloads();
    }

    protected function compileControllerStub()
    {
        $stub = $this->files->get(__DIR__ . '/stubs/Controller.stub');

        $this->replaceClassName($stub)
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
        $stub = $this->files->get(__DIR__ . '/stubs/Controller.stub');

        $this->replaceClassName($stub)
             ->replaceNameSpace($stub);

        return $stub;
    }

    protected function getPath()
    {
        return base_path() . '/app/Http/Controllers/'.$this->directory.'/' . $this->controller . 'Controller.php';
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function replaceClassName(&$stub)
    {
        $className = ucwords($this->controller);

        $stub = str_replace('{{class}}', $className, $stub);

        return $this;
    }

    protected function replaceNameSpace(&$stub)
    {
        $nameSpace = ucwords($this->directory);

        $stub = str_replace('{{namespace}}', $nameSpace, $stub);

        return $this;
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_OPTIONAL, 'name =', null],
            ['controller', null, InputOption::VALUE_OPTIONAL, 'controller =', null],
            ['model', null, InputOption::VALUE_OPTIONAL, 'model =', null],
            ['view', null, InputOption::VALUE_OPTIONAL, 'view =', null],
            ['migration', null, InputOption::VALUE_OPTIONAL, ' migration = ', null],
        ];
    }

}
