<?php declare(strict_types=1);

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 */

namespace Koded\Stdlib;

use ArgumentCountError;
use Countable;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;
use function array_key_first;
use function count;
use function current;
use function dechex;
use function intval;
use function microtime;
use function mt_rand;
use function preg_match;
use function sprintf;
use function str_contains;
use function str_pad;
use function strlen;
use function strpos;
use function substr;

/**
 * Class ULID generates Universally Unique Lexicographically Sortable Identifiers
 * that are sortable, has monotonic sort order (correctly detects and handles the
 * same millisecond), uses Crockford's base32 for better readability
 * and will work until 10.889AD among other things.
 *
 *  ULID:
 *
 *  01GXDATSFG  43B0Y7R64G172FVH
 *  |--------|  |--------------|
 *  Timestamp      Randomness
 *   48bits          80bits
 *
 *  ULID as UUID:
 *
 *  01875aad-65f0-a911-bc1d-77989d5c99bb
 *  |-----------| |--------------------|
 *    Timestamp         Randomness
 *
 */
class ULID implements Countable
{
    public const REGEX = '[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}';
    private const ENCODING = '0123456789ABCDEFGHJKMNPQRSTVWXYZ'; // Crockford's base32

    protected array $timestamps = [];
    private array $randomness = [];

    private function __construct(int $timestamps, int $count)
    {
        if ($count < 1) {
            throw new ArgumentCountError('count must be greater then 0', 400);
        }
        $this->randomize(false);
        for ($i = 0; $i < $count; ++$i) {
            $this->timestamps[$i] = $timestamps;
        }
    }

    /**
     * Creates an instance of ULID with number
     * of timestamps defined by the count argument.
     * @param int $count
     * @return static
     */
    public static function generate(int $count = 1): self
    {
        return new static((int)(microtime(true) * 1000), $count);
    }

    /**
     * Decode the ULID string into an instance of ULID.
     * @param string $ulid
     * @return static
     */
    public static function fromULID(string $ulid): self
    {
        if (26 !== strlen($ulid)) {
            throw new InvalidArgumentException('Invalid ULID, wrong length', 400);
        }
        if (!preg_match('/^[' . static::ENCODING . ']{26}$/', $ulid)) {
            throw new InvalidArgumentException('Invalid ULID, non supported characters', 400);
        }
        $timestamp = 0;
        $chars = substr($ulid, 0, 10);
        for ($i = 0; $i < 10; ++$i) {
            $timestamp = $timestamp * 32 + strpos(static::ENCODING, $chars[$i]);
        }
        return new static ($timestamp, 1);
    }

    /**
     * Decode the ULID string in UUID format into an instance of ULID.
     * @param string $ulid UUID representation of ULID value
     * @return static
     */
    public static function fromUUID(string $ulid): self
    {
        if (false === static::valid($ulid)) {
            throw new InvalidArgumentException('Invalid ULID', 400);
        }
        $timestamp = hexdec(substr($ulid, 0, 8) . substr($ulid, 9, 4));
        return new static($timestamp, 1);
    }

    /**
     * Decode the date time string into an instance of ULID.
     * @param float $timestamp UNIX timestamp with or without the milliseconds part
     * @return static
     */
    public static function fromTimestamp(float $timestamp): self
    {
        if ($timestamp <= 0 || $timestamp >= PHP_INT_MAX) {
            throw new InvalidArgumentException("Invalid timestamp ($timestamp)", 400);
        }
        $timestamp = (string)$timestamp;
        if (str_contains($timestamp, '.')) {
            $timestamp = (string)($timestamp * 1000);
        }
        if (strlen($timestamp) >= 13) {
            $timestamp = substr($timestamp, 0, 13);
        }
        return new static(intval($timestamp), 1);
    }

    /**
     * Decode the date time string into an instance of ULID.
     * @param string $datetime in format: Y-m-d H:i:s with optional 'v' (milliseconds)
     * @return static
     */
    public static function fromDateTime(string $datetime): self
    {
        try {
            $dt = (str_contains($datetime, '.'))
                ? DateTime::createFromFormat('Y-m-d H:i:s.v', $datetime, new DateTimeZone('UTC'))
                : DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new DateTimeZone('UTC'));
            return new static(intval($dt->getTimestamp() . $dt->format('v')), 1);
        } catch (Throwable) {
            throw new InvalidArgumentException("Invalid datetime ($datetime)", 400);
        }
    }

    public static function valid(string $uuid): bool
    {
        return (bool)preg_match('/^' . static::REGEX . '$/i', $uuid);
    }

    /**
     * Creates a single, or a list, of UUID values.
     * @return array|string
     */
    public function toUUID(): array|string
    {
        $list = [];
        foreach ($this->timestamps as $ts) {
            $timestamp = $this->generateUuidParts($ts);
            $hex = substr(str_pad(dechex(intval($timestamp)), 12, '0', STR_PAD_LEFT), -12);
            $list[sprintf('%08s-%04s-%04x-%04x-%012x',
                substr($hex, 0, 8),
                substr($hex, 8, 4),
                ...$this->randomness
            )] = $ts;
        }
        return (1 === $this->count())
            ? array_key_first($list)
            : $list;
    }

    /**
     * Creates a single, or a list of ULID values.
     * @return array|string
     */
    public function toULID(): array|string
    {
        $list = [];
        foreach ($this->timestamps as $ts) {
            [$timestamp, $randomness] = $this->generateUlidParts(intval($ts));
            $list[$timestamp . $randomness] = $ts;
        }
        return (1 === $this->count())
            ? array_key_first($list)
            : $list;
    }

    /**
     * Returns a single, or a list, of DateTime instances.
     * @return array|DateTime
     */
    public function toDateTime(): array|DateTime
    {
        $list = [];
        foreach ($this->timestamps as $timestamp) {
            $timestamp = (string)$timestamp;
            $datetime = new DateTime('@' . substr($timestamp, 0, 10), new DateTimeZone('UTC'));
            if (strlen($timestamp) >= 13) {
                $ms = substr($timestamp, 10, 3);
                $datetime->modify("+{$ms} milliseconds");
            }
            $list[] = $datetime;
        }
        return (1 === $this->count())
            ? current($list)
            : $list;
    }

    /**
     * The number of generated timestamps in the ULID instance.
     * @return int
     */
    public function count(): int
    {
        return count($this->timestamps);
    }

    private function generateUuidParts(int $milliseconds): int
    {
        static $lastTime = 0;
        $sameTimestamp = $lastTime === $milliseconds;
        $lastTime = $milliseconds;
        if ($sameTimestamp) {
            ++$this->randomness[2];
        } else {
            $this->randomize(false);
        }
        return $lastTime;
    }

    private function generateUlidParts(int $milliseconds): array
    {
        static $lastTime = 0;
        $sameTimestamp = $lastTime === $milliseconds;
        $lastTime = $milliseconds;
        $timestamp = $randomness = '';
        // Timestamp
        for ($i = 10; $i > 0; $i--) {
            $mod = $milliseconds % 32;
            $timestamp = static::ENCODING[$mod] . $timestamp;
            $milliseconds = ($milliseconds - $mod) / 32;
        }
        // Randomness
        if (count($this->randomness) < 16) {
            $this->randomize(true);
        }
        if ($sameTimestamp) {
            for ($i = 15; $i >= 0 && (31 === $this->randomness[$i]); $i--) {
                $this->randomness[$i] = 0;
            }
            ++$this->randomness[$i];
        }
        for ($i = 0; $i < 16; ++$i) {
            $randomness .= static::ENCODING[$this->randomness[$i]];
        }
        return [$timestamp, $randomness];
    }

    private function randomize(bool $list): void
    {
        if ($list) {
            $this->randomness = [];
            for ($i = 0; $i < 16; ++$i) {
                $this->randomness[] = mt_rand(0, 31);
            }
        } else {
            $this->randomness = [
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 1 << 48)
            ];
        }
    }
}
