<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class CreateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class with repository and interface';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Remove 'Service' suffix if present (we'll add it back where needed)
        $baseName = str_replace('Service', '', $name);

        // Create Service
        $serviceName = $baseName . 'Service';
        $stub = File::get(base_path('stubs/service.stub'));

        // Replace the placeholders for service
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ['App\\Services', $baseName],
            $stub
        );

        // Create the services directory if it doesn't exist
        $directory = app_path('Services');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory);
        }

        // Create the service file
        $path = $directory . '/' . $serviceName . '.php';

        if (File::exists($path)) {
            $this->error('Service already exists!');
            return;
        }

        File::put($path, $stub);

        // Create Repository and Interface using existing command
        Artisan::call('make:repository', [
            'name' => $baseName
        ]);

        // Get the output from the repository creation
        $repoOutput = Artisan::output();

        $this->info('Service created successfully!');
        $this->info($repoOutput);
    }
}
