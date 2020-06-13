<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

final class GithubReport implements Report
{
    private array $failures = [];

    public function success(): void
    {
        // noop
    }

    public function failure(string $filename, int $startline, string $message): void
    {
        $this->failures[] = (object)['filename' => $filename, 'startline' => $startline, 'message' => $message];
    }

    public function print(): void
    {
        foreach ($this->failures as $failure) {
            echo sprintf(
                    '::%s file=%s,line=%s,col=%s::%s',
                    'error',
                    $failure->filename,
                    $failure->startline,
                    0,
                    $failure->message
                ) . "\n";
        }
    }
}