<?php

namespace YouHuJun\LaravelFastApi\App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand as Command;

class BuildContractCommand extends Command
{
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new contarct interface';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Contract';

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

        $stub = '/stubs/contract.stub';

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

        $name = str_replace('/', '\\', $name);
       
        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.'Contracts'.'\\'.$name
        );
        
    }
}
