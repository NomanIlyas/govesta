<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File as SFile;
use Illuminate\Support\Facades\Storage;
use App\Model\General\File;
use Illuminate\Http\File as HttpFile;

class GenerateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Images are generating';

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
    /* public function handle()
    {
        $this->info('**** starting generating images ****');
        $files = ImageFile::all();
        ini_set('memory_limit', '256M');
        foreach ($files as $file) {
            $oldFile = public_path('uploads/' . $file->name.'.'.$file->extension);
            $newFolder = public_path('_uploads/' . $file->name);
            $original = $newFolder.'/original.'.$file->extension;
            if (isset($file->name) && isset($file->extension) && File::exists($oldFile)) {
                File::makeDirectory($newFolder, 0777, true, true);
                File::copy($oldFile, $original);
                FileHelper::generateSizes($file->name, $file->extension, $file->type, '_uploads');
            }
        }
        $this->info('**** finalizing generating images ****');
    } */

    /* public function handle()
    {
        $this->info('**** starting generating images ****');
        $files = ImageFile::all();
        ini_set('memory_limit', '256M');
        foreach ($files as $file) {
            $fileFolder = public_path('uploads/' . $file->name);
            $filePath = $fileFolder . '/original.' . $file->extension;
            if (isset($file->name) && isset($file->extension) && File::exists($filePath)) {
                File::delete($fileFolder . '/x.' . $file->extension);
                File::delete($fileFolder . '/2x.' . $file->extension);
                FileHelper::generateSizes($file->name, $file->extension, $file->type);
            }
        }
        $this->info('**** finalizing generating images ****');
    } */

    public function handle()
    {
        $this->info('**** starting generating images ****');
        $files = File::all();
        foreach ($files as $file) {
            if (isset($file->name) && isset($file->extension)) {
                $folder = 'uploads/' . $file->name;
                $original = 'original.' . $file->extension;
                $originalFile = public_path($folder . '/' . $original);
                $x = 'x.jpg';
                $xFile = public_path($folder . '/' . $x);
                $x2 = '2x.jpg';
                $x2File = public_path($folder . '/' . $x2);
                if (SFile::exists($originalFile)) {
                    Storage::disk('s3')->putFileAs('/' . $folder, new HttpFile($originalFile), $original, 'public');
                }
                if (SFile::exists($xFile)) {
                    Storage::disk('s3')->putFileAs('/' . $folder, new HttpFile($xFile), $x, 'public');
                }
                if (SFile::exists($x2File)) {
                    Storage::disk('s3')->putFileAs('/' . $folder, new HttpFile($x2File), $x2, 'public');
                }
            }
        }

        $this->info('**** finalizing generating images ****');
    }
}
