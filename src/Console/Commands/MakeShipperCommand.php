<?php

namespace Obelaw\Shipping\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeShipperCommand extends GeneratorCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'shipping:make:shipper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new shipper class in the Shipping folder.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Shipper Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/../../../stubs/shipper.php.stub';

        return __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Shipping';
    }
}
