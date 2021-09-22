<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImportHelper;
use \Zipper;

class OnofficeSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openimmo:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Integrate onoffice integration';

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
        $this->info('**** starting sync of openimmo ****');
        $files = Storage::disk('onoffice')->files();
        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            if ($fileInfo['extension'] == 'zip') {
                $folderName = $fileInfo['filename'];
                $zipPath = public_path('onoffice/' . $file);
                $unzipPath = public_path('onoffice/' . $folderName);
                Zipper::make($zipPath)->extractTo($unzipPath);
                ImportHelper::parseXML($folderName);
                unlink($zipPath);
                ImportHelper::delete_files($unzipPath);
            }
        }
        $this->info('**** finalizing sync of openimmo ****');
    }
}
