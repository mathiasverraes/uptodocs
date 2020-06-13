<?php declare(strict_types=1);

namespace Verraes\UpToDocs;

interface Report
{
    public function success(): void;

    public function failure(string $filename, int $startline, string $message): void;

    public function print(): void;
}