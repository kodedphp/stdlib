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

use InvalidArgumentException;
use function foo\func;

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
    const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is a URL.
     */
    const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an ISO OID.
     */
    const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is
     * an X.500 DN in DER or a text output format.
     */
    const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Regex pattern for UUIDs
     */
    const PATTERN = '[a-f0-9]{8}\-[a-f0-9]{4}\-[1|3|4|5][a-f0-9]{3}\-[a-f0-9]{4}\-[a-f0-9]{12}';

    /**
     * UUID v1 is generated from host (hardware address), clock sequence and
     * current time. This is very slow method.
     *
     * @param int|string $address [optional] 48 bit number for the hardware address.
     *                            It can be an integer or hexadecimal string
     *
     * @return string UUID v1
     */
    public static function v1($address = null): string
    {
        static $ip;

        $addr = static function() use (&$ip): string {
            //return '207.38.93.78';
            //return '127.0.0.1';
            if ($ip) {
                //echo '(1)';
                return $ip;
            }
            if ($ip = `hostname 2> /dev/null`) {
                //echo '(2)';
                $ip = gethostbyname(trim($ip));
                $ip = sprintf('%02x%02x%02x%02x', ...explode('.', $ip));
                return $ip;
            }
            //echo '(3)';
            // Cannot identify host, fallback is described in
            // http://tools.ietf.org/html/rfc4122#section-4.5
            // @codeCoverageIgnoreStart
            return bin2hex(random_bytes(6));
            // @codeCoverageIgnoreEnd
        };

        //var_dump($addr());
        //var_dump($addr());
        //var_dump($addr()); exit;
        /**
         * Get the hardware address as a 48-bit positive integer.
         *
         * @param string|int $node [optional]
         *
         * @return string
         * @throws InvalidArgumentException
         */
        $node = function($node = null) use ($addr): string {
            if (null === $node) {
                $node = $addr();
                //$node = 0x7fffffff;
            } else {
                $node = str_replace([':', '-', '.'], '', (string)$node);
            }

            //var_dump('(1)', $node); exit;

            if (ctype_digit($node)) {
                //var_dump('(1)', $node);
                $node = sprintf('%012x', $node);
                //$node = dechex((int)$node);
                //var_dump('(2)', $node); exit;
            } else if (ctype_xdigit($node) && strlen($node) <= 12) {
                //$node = strtolower(sprintf('%012s', $node));
                $node = strtolower($node);
            } else {
                throw new InvalidArgumentException('UUID invalid node value');
            }
            return $node;
        }; // end $node

        static $seq;

        $clockSeq = function() use (&$seq) {
            // Random 14-bit sequence number
            // http://tools.ietf.org/html/rfc4122#section-4.2.1.1
            return mt_rand(0, 1 << 14);
        };

        static $last;

        $uuidTime = static function() use (&$last) {
            // 0x01b21dd213814000 is a 100 nanoseconds interval between UUID epoch
            // and UNIX epoch datetime (15/10/1582 00:00:00 - 01/01/1970 00:00:00)
            $time = gettimeofday();
            $time = ($time['sec'] * 10000000) + ($time['usec'] * 10) + 0x01b21dd213814000;
            //$time = ($time['sec'] * 10000000) + 0x01b21dd213814000;
            //$time = floor((int)hrtime(true) / 100 + 0x01b21dd213814000);

            if ($last && $time <= $last) {
                $time = $last + 1;
            }
            $last = $time;
            return [
                'low' => $time & 0xffffffff,
                'mid' => ($time >> 32) & 0xffff,
                'high' => ($time >> 48) & 0x0fff
            ];
        };

        $__uuidTime = function() use ($clockSeq){
            $timestamp = gettimeofday();
            $time = '';
            $time = ($timestamp['sec'] * 10000000) + ($timestamp['usec'] * 10) + 0x01b21dd213814000;
            $time2 = floor(hrtime(true) / 100 + 0x01b21dd213814000);

            ob_start();
            passthru('/usr/bin/python ' . getcwd() . '/_timens.py');
            //passthru(getcwd() . '/timens.py');
            $py = ob_get_clean();
            var_dump($py, $time, $time2, /*, $timestamp*/); //exit;
         };

//$__uuidTime();

        $uuidTime = $uuidTime();
        $clockSeq = $clockSeq();

        // Set to version 1
        $version = $uuidTime['high'] & 0x0fff;
        $version &= ~(0xf000);
        $version |= 1 << 12;

        $variant = ($clockSeq >> 8) & 0x3f;
        $variant &= ~(0xc0);
        $variant |= 0x80;

        return sprintf('%08x-%04x-%04x-%02x%02x-%012s',
            $uuidTime['low'],
            $uuidTime['mid'],
            // set to version 1
            $uuidTime['high'] |= 1 << 12,
        //$version,
            // variant
            //($clockSeq >> 8) & 0x3f,
            $variant,
            $clockSeq & 0xff,
            $node($address)
        );
    }

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
     * Version 4, pseudo-random (xxxxxxxx-xxxx-4xxx-[8|9|a|b]xxx-xxxxxxxxxxxx)
     *
     * @return string 128bit of pseudo-random UUID
     * @see http://en.wikipedia.org/wiki/UUID#Version_4_.28random.29
     * @throws \Exception
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
        assert(in_array($version, [1, 3, 4, 5]), 'Expected UUID version 1, 3, 4 or 5, got ' . $version);
        return UUID::valid($uuid);
    }

    /**
     * Creates the v3 and/or v5 UUID.
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
