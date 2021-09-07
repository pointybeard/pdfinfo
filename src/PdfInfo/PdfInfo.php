<?php

declare(strict_types=1);

/*
 * This file is part of the "PHP Wrapper for pdfinfo" repository.
 *
 * Copyright 2021 Alannah Kearney <hi@alannahkearney.com>
 *
 * For the full copyright and license information, please view the LICENCE
 * file that was distributed with this source code.
 */

namespace pointybeard\PdfInfo;

use Exception;
use pointybeard\Helpers\Functions\Cli;
use pointybeard\PdfInfo\Exceptions\PdfInfoAssertionFailedException;
use pointybeard\PdfInfo\Exceptions\PdfInfoExecutionFailedException;

class PdfInfo
{
    // This is the name of the pdfinfo executable
    public const PDFINFO = 'pdfinfo';

    private $file = null;

    private static $paths = [];

    private $properties = [];

    private $rawInfo = null;

    public const POINTS_TO_MM = 0.3528;

    public function __construct(string $file)
    {
        $this->file = $file;

        self::assertFileExists($this->file);
        self::assertPdfinfoInstalled();

        $args = $this->file.' -isodates';

        try {
            Cli\run_command(sprintf('%s %s', Cli\which(self::PDFINFO), $args), $this->rawInfo, $stderr, $exitCode);
        } catch (Exception $ex) {
            throw new PdfInfoExecutionFailedException($args, $stderr, $exitCode, 0, $ex);
        }

        // (guard) unable to get anthing back
        if (null == $this->rawInfo) {
            throw new PdfInfoExecutionFailedException($args, 'Error getting information about input file. Raw output was empty.', $exitCode);
        }

        $this->parseRawInfo();
    }

    private function parseRawInfo()
    {
        $bits = preg_split("@[\r\n]@", $this->rawInfo);

        foreach ($bits as $b) {
            [$name, $value] = explode(':', $b, 2);
            $value = trim($value);

            $this->properties[$name] = $value;
        }
    }

    public function dimensions(): ?array
    {
        // (guard) Cannot find "Page size"
        if (null == $this->{'Page size'}) {
            return null;
        }

        preg_match_all("@([-+]?[0-9]*\.?[0-9]+)@", $this->{'Page size'}, $matches);

        [$width, $height] = $matches[0];

        return [self::pointsToMm((float) $width), self::pointsToMm((float) $height)];
    }

    public function width(): float
    {
        [$width,] = $this->dimensions();

        return $width;
    }

    public function height(): float
    {
        [,$height] = $this->dimensions();

        return $height;
    }

    private static function pointsToMm(float $value): float
    {
        return $value * self::POINTS_TO_MM;
    }

    public function __get($name)
    {
        return $this->properties[$name] ?? null;
    }

    private static function assertPdfinfoInstalled(): void
    {
        if (null == Cli\which(self::PDFINFO)) {
            throw new PdfInfoAssertionFailedException(self::PDFINFO.' executable cannot be located.');
        }
    }

    private static function assertOptionExists($option): void
    {
        if (false == in_array($option, self::$options)) {
            throw new PdfInfoAssertionFailedException("Invalid option '{$option}' specified.");
        }
    }

    private static function assertFileExists($file): void
    {
        if (false == is_readable($file) || false == file_exists($file)) {
            throw new PdfInfoAssertionFailedException("File '{$file}' does not exist or is not readable.");
        }
    }

    public function toArray(): array
    {
        return $this->properties;
    }

    public function toJson(): string
    {
        return json_encode($this->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
