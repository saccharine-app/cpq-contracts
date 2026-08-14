<?php

namespace Saccharine\CPQ\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ScaffoldCpqUiCommand extends Command
{
    protected $signature = 'cpq:scaffold {--force : Overwrite existing files}';
    protected $description = 'Scaffold the CPQ Filament Page and Vue mounting scripts into the host application';

    public function handle(Filesystem $filesystem)
    {
        $this->info('Scaffolding CPQ UI components...');

        // Define paths
        $stubsPath = __DIR__ . '/../../../stubs';
        $hostAppPath = base_path();

        // Scaffold the Filament Page (PHP Class)
        $filamentPageDir = $hostAppPath . '/app/Filament/Pages';
        $filesystem->ensureDirectoryExists($filamentPageDir);
        $this->copyStub(
            $filesystem, 
            $stubsPath . '/selector-page.stub', 
            $filamentPageDir . '/QuoteBuilder.php'
        );

        // Scaffold the Filament View (Blade)
        $filamentViewDir = $hostAppPath . '/resources/views/filament/pages';
        $filesystem->ensureDirectoryExists($filamentViewDir);
        $this->copyStub(
            $filesystem, 
            $stubsPath . '/selector-template.stub', 
            $filamentViewDir . '/quote-builder.blade.php'
        );

        // Scaffold the Vue Mount Script
        $jsDir = $hostAppPath . '/resources/js/CPQ';
        $filesystem->ensureDirectoryExists($jsDir);
        $this->copyStub(
            $filesystem, 
            $stubsPath . '/selector-mount.stub', 
            $jsDir . '/mount.js'
        );

        $this->newLine();
        $this->info('Scaffolding complete!');
        
        // Output next steps for the developer
        $this->warn('Next Steps:');
        $this->line('1. Publish the Vue components: <comment>php artisan vendor:publish --tag=cpq-views</comment>');
        $this->line('2. Open <comment>resources/js/app.js</comment> and import the mount script: <comment>import "./CPQ/mount";</comment>');
        $this->line('3. Run <comment>npm run dev</comment> to compile your assets.');
    }

    protected function copyStub(Filesystem $filesystem, string $stubPath, string $targetPath): void
    {
        if (! $filesystem->exists($stubPath)) {
            $this->error("Stub not found: {$stubPath}");
            return;
        }

        if ($filesystem->exists($targetPath) && ! $this->option('force')) {
            $this->warn("File already exists (use --force to overwrite): {$targetPath}");
            return;
        }

        // We can add str_replace logic here later if we need to swap out {{ namespace }} placeholders
        $filesystem->copy($stubPath, $targetPath);
        $this->line("Copied: " . str_replace(base_path() . '/', '', $targetPath));
    }
}