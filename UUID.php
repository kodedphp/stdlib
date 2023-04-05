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

use AssertionError;
use InvalidArgumentException;
use function base64_decode;
use function base64_encode;
use function chr;
use function ctype_digit;
use function ctype_xdigit;
use function dechex;
use function explode;
use function gethostbyname;
use function gettimeofday;
use function hex2bin;
use function hexdec;
use function in_array;
use function md5;
use function mt_rand;
use function preg_match;
use function random_bytes;
use function sha1;
use function sprintf;
use function str_replace;
use function strlen;
use function strtolower;
use function substr;
use function trim;
use function unpack;
use function vsprintf;

/**
 * Class UUID generates Universally Unique Identifiers following the RFC 4122.
 *
 * The 5 fields of the UUID v1
 *  - 32 bit, *time_low*
 *  - 16 bit, *time_mid*
 *  - 16 bit, *time_high_and_version*
 *  - 16 bit, (8 bits for *clock_seq_and_reserved* + 8 bits for *clock_seq_low*)
 *  - 48 bit, *node*
 *
 * @link    http://tools.ietf.org/html/rfc4122
 * @link    https://docs.python.org/2/library/uuid.html
 * @link    https://en.wikipedia.org/wiki/Universally_unique_identifier
 */
final class UUID
{
    /* @link http://tools.ietf.org/html/rfc4122#appendix-C */

    /**
     * When this namespace is specified, the name string
     * is a fully-qualified domain name.
     */
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is a URL.
     */
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an ISO OID.
     */
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is
     * an X.500 DN in DER or a text output format.
     */
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Regex pattern for UUIDs
     */
    public const PATTERN = '[a-f0-9]{8}\-[a-f0-9]{4}\-[1345][a-f0-9]{3}\-[a-f0-9]{4}\-[a-f0-9]{12}';

    /**
     * Generates a UUID based on the MD5 hash of a namespace
     * identifier (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace UUID namespace identifier
     * @param string $name      A name
     *
     * @return string UUID v3
     */
    public static function v3(string $namespace, string $name): string
    {
        return UUID::fromName($namespace, $name, 3);
    }

    /**
     * Version 4, pseudo-random UUID
     * xxxxxxxx-xxxx-4xxx-[8|9|a|b]xxx-xxxxxxxxxxxx
     *
     * @return string 128bit of pseudo-random UUID
     * @throws \Exception
     *@see http://en.wikipedia.org/wiki/UUID#Version_4_.28random.29
     */
    public static function v4(): string
    {
        $bytes = unpack('n*', random_bytes(16));
        $bytes[4] = $bytes[4] & 0x0fff | 0x4000;
        $bytes[5] = $bytes[5] & 0x3fff | 0x8000;
        return vsprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', $bytes);
    }

    /**
     * Generates a UUID based on the SHA-1 hash of a namespace
     * identifier (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace UUID namespace identifier
     * @param string $name      A name
     *
     * @return string UUID v5
     */
    public static function v5(string $namespace, string $name): string
    {
        return UUID::fromName($namespace, $name, 5);
    }

    /**
     * Checks if a given UUID has valid format.
     *
     * @param string $uuid
     *
     * @return bool
     */
    public static function valid(string $uuid): bool
    {
        if (false === (bool)preg_match('/^' . UUID::PATTERN . '$/i', $uuid)) {
            return false;
        }
        if ('4' === $uuid[14]) {
            return in_array($uuid[19], ['8', '9', 'a', 'b']);
        }
        return true;
    }

    /**
     * Checks if a given UUID has valid format and matches against the version.
     *
     * @param string $uuid
     * @param int    $version Check against the version 1, 3, 4 or 5
     *
     * @return bool
     */
    public static function matches(string $uuid, int $version = 4): bool
    {
        assert(in_array($version, [1, 3, 4, 5]), new AssertionError("Expected UUID version 1, 3, 4 or 5 (got $version)"));
        return UUID::valid($uuid);
    }

    /**
     * UUID v1 is generated from host (hardware) address, clock sequence and
     * current time. This is very slow method.
     *
     * @param string|int|null $address [optional] 48 bit number for the hardware address.
     *                                 It can be an integer or hexadecimal string
     *
     * @return string UUID v1
     */
    public static function v1(string|int $address = null): string
    {
        static $node, $clockSeq, $lastTimestamp;

        /**
         * If $node is not initialized, it will try to
         * get the network address of the machine,
         * or fallback to random generated hex string.
         * @return string
         */
        $fetchAddress = static function() use (&$node): string {
            if ($node) {
                return $node;
            }
            if ($node = `hostname -i 2> /dev/null`) {
                return $node = vsprintf('%02x%02x%02x%02x', explode('.', $node));
            }
            if ($node = `hostname 2> /dev/null`) {
                $node = gethostbyname(trim($node));
                return $node = vsprintf('%02x%02x%02x%02x', explode('.', $node));
            }
            // Cannot identify IP or host, fallback as described in
            // http://tools.ietf.org/html/rfc4122#section-4.5
            // https://en.wikipedia.org/wiki/MAC_address#Unicast_vs._multicast_(I/G_bit)
            // @codeCoverageIgnoreStart
            return $node = dechex(mt_rand(0, 1 << 48) | (1 << 40));
            // @codeCoverageIgnoreEnd
        };

        /**
         * Transform the address into hexadecimal string
         * as spatially unique node identifier.
         * @param string|int|null $address [optional]
         * @return string
         */
        $nodeIdentifier = static function(string|int $address = null) use ($fetchAddress): string {
            $address = null !== $address
                ? str_replace([':', '-', '.'], '', (string)$address)
                : $fetchAddress();

            if (ctype_digit($address)) {
                return sprintf('%012x', $address);
            }
            if (ctype_xdigit($address) && strlen($address) <= 12) {
                return strtolower($address);
            }
            throw new InvalidArgumentException('UUID invalid node value');
        };

        /**
         * Convert UNIX epoch in nanoseconds to Gregorian epoch
         * (15/10/1582 00:00:00 - 01/01/1970 00:00:00)
         * @return int[]
         */
        $fromUnixNano = static function() use (&$lastTimestamp) {
            $ts = gettimeofday();
            $ts = ($ts['sec'] * 10000000) + ($ts['usec'] * 10) + 0x01b21dd213814000;
            if ($lastTimestamp && $ts <= $lastTimestamp) {
                $ts = $lastTimestamp + 1;
            }
            $lastTimestamp = $ts;
            return [
                // timestamp low field
                $ts & 0xffffffff,
                // timestamp middle field
                ($ts >> 32) & 0xffff,
                // timestamp high field with version number
                (($ts >> 48) & 0x0fff) | (1 << 12)
            ];
        };

        if (!$clockSeq) {
            // Random 14-bit sequence number
            // http://tools.ietf.org/html/rfc4122#section-4.2.1.1
            $clockSeq = mt_rand(0, 1 << 14);
        }
        return vsprintf('%08x-%04x-%04x-%02x%02x-%012s', [
            ...$fromUnixNano(),
            $clockSeq & 0xff,
            ($clockSeq >> 8) & 0x3f,
            $nodeIdentifier($address)
        ]);
    }

    /**
     * Creates a base64 string out of the UUID.
     *
     * @param string $uuid UUID string
     *
     * @return string base64 encoded string
     */
    public static function toBase64(string $uuid): string
    {
        if (false === UUID::valid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID ' . $uuid);
        }
        return str_replace(['/', '+', '='], ['-', '_', ''],
            base64_encode(hex2bin(str_replace('-', '', $uuid)))
        );
    }

    /**
     * Converts a base64 string to UUID.
     *
     * @param string $base64
     *
     * @return string UUID string
     */
    public static function fromBase64(string $base64): string
    {
        $uuid = base64_decode(str_replace(
            ['-', '_', '='], ['/', '+', ''], $base64) . '=='
        );
        if (!preg_match('//u', $uuid)) {
            $uuid = vsprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', unpack('n*', $uuid));
        }
        if (UUID::valid($uuid)) {
            return $uuid;
        }
        throw new InvalidArgumentException(
            'Failed to convert base 64 string to UUID');
    }

    /**
     * Creates a v3 or v5 UUID.
     *
     * @param string $namespace UUID namespace identifier (see UUID constants)
     * @param string $name      A name
     * @param int    $version   3 or 5
     *
     * @throws InvalidArgumentException
     * @return string UUID 3 or 5
     */
    private static function fromName(string $namespace, string $name, int $version): string
    {
        if (false === UUID::matches($namespace, $version)) {
            throw new InvalidArgumentException('Invalid UUID namespace ' . $namespace);
        }
        $hex = str_replace('-', '', $namespace);
        $bits = '';
        for ($i = 0, $len = strlen($hex); $i < $len; $i += 2) {
            $bits .= chr((int)hexdec($hex[$i] . $hex[$i + 1]));
        }
        $hash = $bits . $name;
        $hash = (3 === $version) ? md5($hash) : sha1($hash);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | (3 === $version ? 0x3000 : 0x5000),
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }
}
