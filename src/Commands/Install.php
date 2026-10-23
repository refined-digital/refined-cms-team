<?php

namespace RefinedDigital\Team\Commands;

use Illuminate\Console\Command;
use Validator;
use Artisan;
use DB;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refinedCMS:install-team';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the team files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->migrate();
        $this->seed();
        $this->publish();
        $this->copyDefaults();
        $this->installContentBlock();
        $this->info('Team has been successfully installed');
    }


    protected function migrate()
    {
        $this->output->writeln('<info>Migrating the database</info>');
        Artisan::call('migrate', [
            '--path' => 'vendor/refineddigital/cms-team/src/Database/Migrations',
            '--force' => 1,
        ]);
    }

    protected function seed()
    {
        $this->output->writeln('<info>Seeding the database</info>');
        Artisan::call('db:seed', [
            '--class' => '\\RefinedDigital\\Team\\Database\\Seeds\\TeamDatabaseSeeder',
            '--force' => 1
        ]);
    }

    protected function publish()
    {
        Artisan::call('vendor:publish', [
            '--tag' => 'team',
        ]);

        // grab the team details template id
        $template = \DB::table('templates')
                        ->whereName('Team Details')
                        ->first();

        if (isset($template->id)) {
            // override the template id of the team details
            $configFile = config_path('team.php');
            $file = file_get_contents($configFile);
            $search = [ "'__ID__'" ];
            $replace = [ $template->id ];
            file_put_contents($configFile, str_replace($search, $replace, $file));
        }
    }

    private function copyDefaults()
    {
        $this->output->writeln('<info>Copying Templates</info>');
        $this->copy('views/templates');
        $this->copy('css/components');
    }

    private function copy($assetDir)
    {
        $dir = __DIR__.'/defaults/'.$assetDir.'/';

        if (!is_dir(resource_path($assetDir))) {
            mkdir(resource_path($assetDir), 0755, true);
        }

        foreach (array_diff(scandir($dir), ['.', '..']) as $template) {
            if (!is_file($dir.$template)) {
                continue;
            }

            // never overwrite a file the site has already customised
            if (is_file(resource_path($assetDir.'/'.$template))) {
                $this->output->writeln(' <comment>Skipping '.$template.', it already exists</comment>');
                continue;
            }

            $this->output->writeln(' <info>Copying '.$template.'</info>');
            copy($dir.$template, resource_path($assetDir.'/'.$template));
        }
    }

    private function installContentBlock()
    {
        $this->output->writeln('<info>Installing the Team Listing content block</info>');

        $to = app_path('RefinedCMS/Content/Blocks/TeamListing');
        if (!is_dir($to)) {
            \File::copyDirectory(__DIR__.'/defaults/app/RefinedCMS/Blocks/TeamListing', $to);
        }

        $appFile = app_path('RefinedCMS/Content/Providers/ContentServiceProvider.php');

        if (!is_file($appFile)) {
            $this->output->writeln('<error>Could not find '.$appFile.' - register TeamListing::class manually</error>');
            return;
        }

        $appData = file_get_contents($appFile);

        if (str_contains($appData, 'TeamListing::class')) {
            return;
        }

        $appData = str_replace(
            [
                '// register the content fields',
                'use Illuminate\Support\ServiceProvider;',
            ],
            [
                '// register the content fields'."\n\t\t".'$agg->register(TeamListing::class);',
                'use App\\RefinedCMS\\Content\\Blocks\\TeamListing\\TeamListing;'."\nuse Illuminate\\Support\\ServiceProvider;",
            ],
            $appData
        );

        file_put_contents($appFile, $appData);
    }

}
