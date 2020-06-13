<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

final class ConsoleReport implements Report
{
    private array $failures = [];

    public function success(): void
    {
        echo ".";
    }

    public function failure(string $filename, int $startline, string $message): void
    {
        echo "F";
        $this->failures[] = (object) ['filename' => $filename, 'startline' => $startline, 'message' => $message];
    }

    public function print(): void
    {
        echo "\n";
        if(0 == count($this->failures)) {
            echo "OK\n";
        }
        foreach ($this->failures as $failure) {
            echo "\nThe code block in {$failure->filename}:{$failure->startline} failed.\n";
            echo $failure->message;
        }
    }
}