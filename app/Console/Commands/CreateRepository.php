<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class with interface';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Add 'Repository' suffix if not present
        if (!str_ends_with($name, 'Repository')) {
            $name = $name . 'Repository';
        }

        // Remove 'Repository' suffix for interface name
        //$interfaceName = str_replace('Repository', '', $name);

        // Create directories if they don't exist
        $repositoryDirectory = app_path('Repositories/Eloquents');
        $interfaceDirectory = app_path('Repositories/Interfaces');

        if (!File::isDirectory($repositoryDirectory)) {
            File::makeDirectory($repositoryDirectory, 0755, true);
        }

        if (!File::isDirectory($interfaceDirectory)) {
            File::makeDirectory($interfaceDirectory, 0755, true);
        }

        // Create Interface
        $interfaceStub = File::get(base_path('stubs/interface.stub'));
        $interfaceStub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ['App\\Repositories', $name],
            $interfaceStub
        );

        $interfacePath = $interfaceDirectory . '/' . $name . 'Interface.php';

        if (File::exists($interfacePath)) {
            $this->error('Interface already exists!');
            return;
        }

        File::put($interfacePath, $interfaceStub);

        // Create Repository
        $repositoryStub = File::get(base_path('stubs/repository.stub'));
        $repositoryStub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ['App\\Repositories\\Eloquents', $name],
            $repositoryStub
        );

        $repositoryPath = $repositoryDirectory . '/' . $name . '.php';

        if (File::exists($repositoryPath)) {
            $this->error('Repository already exists!');
            return;
        }

        File::put($repositoryPath, $repositoryStub);

        $this->info('Repository and Interface created successfully!');
    }
}
