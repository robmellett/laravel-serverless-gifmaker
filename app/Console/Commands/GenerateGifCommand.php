<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateGifCommand extends Command
{
    protected $signature = 'generate:gif {payload}';

    protected $description = 'Generate a gif from a video uploaded to AWS S3';

    public function handle(): void
    {
        $file = data_get($this->argument('payload'), 'Records.0.s3.object.key');

        if (! $this->ensureVideoIsProcessable($file)) {
            $this->warn("Cannot not process the following file [$file}].");
            return;
        }

        $this->downloadVideoToTemporaryStorage($file);

        $converted = $this->convertVideo($file);

        $this->uploadConvertedVideoToS3($converted);
    }

    private function ensureVideoIsProcessable(string $file): bool
    {
        return str($file)->endsWith('.mp4');
    }

    private function downloadVideoToTemporaryStorage(string $file)
    {
        $contents = Storage::disk('s3')->get("{$file}");

        Storage::disk('lambda-tmp')->put("{$file}", $contents);
    }

    private function convertVideo(string $file)
    {
        $commandOutput = null;
        $commandResult = null;

        $fileName = str($file)->remove('.mp4', $file);

        $input = Storage::disk('lambda-tmp')->path("{$fileName}.mp4");
        $output = Storage::disk('lambda-tmp')->path("{$fileName}.gif");

        $command = sprintf(
            "/opt/ffmpeg/ffmpeg -i %s -f gif %s",
            $input,
            $output
        );

        exec($command, $commandOutput, $commandResult);

        return "{$fileName}.gif";
    }

    private function uploadConvertedVideoToS3(string $file)
    {
        $contents = Storage::disk('lambda-tmp')->get("$file");

        Storage::disk('s3')->put("$file", $contents);
    }
}
