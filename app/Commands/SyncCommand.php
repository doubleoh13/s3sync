<?php

namespace App\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncCommand extends Command
{
    protected $signature = 'sync';

    public function handle()
    {
        $starttime = now();

        Log::debug('Sync Started');
        $local = Storage::disk('backupFiles');
        $s3 = Storage::disk('s3');
        $s3Files = $s3->files('', true);

        Log::debug('S3 Files Loaded: ' . count($s3Files));

        $directories = collect($local->directories())
            ->filter(
                function ($directory) {
                    return !in_array($directory, explode(',', config('app.directories_to_ignore')));
                }
            );

        foreach($directories as $directory) {
            $filesToUpload = collect($local->files($directory, true))->diff($s3Files);

            foreach($filesToUpload as $file) {
                $fileAge = Carbon::createFromTimestamp($local->lastModified($file))->diffInMinutes();

                // Only upload files that have existed for 2 minutes to make sure that writing is done.
                if($fileAge > 2) {
                    try {
                        Log::info("Uploading File: '{$file}'");
                        $s3->put(
                            $file,
                            fopen($local->path($file), 'r'),
                            [
                                'StorageClass' => 'ONEZONE_IA',
                            ]
                        );

                        Log::info("Finished Uploading File: '{$file}");
                    }catch (\Exception $exception) {
                        Log::error("Failed to upload file: {$file}");
                    }
                }
            }
        }


        Log::info("Successfully uploaded files in " . now()->diffInMinutes($starttime));
    }
}
