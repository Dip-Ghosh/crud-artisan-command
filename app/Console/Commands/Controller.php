<?php

namespace App\Console\Commands;

class Controller
{
    public function makeController()
    {
        $name = 'controller';
        $path = $this->getPath($name);
        $this->makeDirectory($path,);

        $this->files->put($path, $this->compileControllerStub());
        $filename = pathinfo($path, PATHINFO_FILENAME);

        $this->line("<info>Created Controller:</info> {$filename}");
    }

    public function compileControllerStub()
    {
        $stub = $this->files->get(__DIR__ . '/stubs/Controller.stub');
        $name = 'controller';

        $this->replaceClassName($stub,$name)
            ->replaceNameSpace($stub);

        return $stub;
    }

}
