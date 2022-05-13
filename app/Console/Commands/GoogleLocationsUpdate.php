<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Imports\LocationGoogleImport;

class GoogleLocationsUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update locations in DB for Google';

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
        $file = 'google.csv';

        if(!Storage::disk('location')->exists($file))
            $this->output->title('Файл: ' . $file . ' не найден. Скачайте файл по ссылке: https://xmlstock.com/geotargets-google.csv И загрузите в папку для импорта: ' . storage_path('location'));
        else{
            $this->output->title('Starting import');
            (new LocationGoogleImport)->withOutput($this->output)->import($file, 'location');
            $this->output->success('Import successful');
        }
    }
}
