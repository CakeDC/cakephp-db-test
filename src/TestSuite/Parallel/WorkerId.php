<?php
declare(strict_types=1);

/**
 * Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\TestSuite\Parallel;

/**
 * Resolves the parallel test worker identity.
 *
 * ParaTest exposes a per-worker `TEST_TOKEN` environment variable. When it is
 * absent or empty (plain `phpunit`), the helpers return neutral values so the
 * single-process behavior is unchanged.
 */
final class WorkerId
{
    /**
     * Per-worker token assigned by the parallel runner, if any.
     *
     * @return string|null
     */
    public static function token(): ?string
    {
        $token = getenv('TEST_TOKEN');
        if ($token === false || $token === '') {
            return null;
        }

        return $token;
    }

    /**
     * Filename-safe suffix for per-worker files (`''` or `'_<token>'`).
     *
     * @return string
     */
    public static function suffix(): string
    {
        $token = static::token();
        if ($token === null) {
            return '';
        }

        return '_' . $token;
    }

    /**
     * Base name with the worker suffix appended when running in parallel.
     *
     * @param string $base Base name used in single-process runs.
     * @return string
     */
    public static function suffixed(string $base): string
    {
        $token = static::token();
        if ($token === null) {
            return $base;
        }

        return $base . '_' . $token;
    }
}
