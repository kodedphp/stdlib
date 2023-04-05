<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\ULID;
use PHPUnit\Framework\TestCase;

class ULIDTest extends TestCase
{
    public function test_generate_one_uuid()
    {
        $ulid = ULID::generate();

        $this->assertSame(1, $ulid->count(),
            'generate() creates one value');

        $this->assertTrue(ULID::valid($ulid->toUUID()));
    }

    public function test_generates_multiple_uuids()
    {
        $ulid = ULID::generate(12);
        $list = $ulid->toUUID();

        $this->assertIsArray($list);
        $this->assertCount(12, $list);

        $uuids = array_keys($list);
        for ($i = 0; $i < 12; $i += 2) {
            $this->assertSame(
                substr($uuids[$i], 0, 31),
                substr($uuids[$i + 1], 0, 31),
                'characters are same for the first 4 sequences'
            );

            $this->assertNotSame(
                substr($uuids[$i], -12, 12),
                substr($uuids[$i + 1], -12, 12),
                'the last sequence is autoincremental'
            );

            $this->assertTrue(ULID::valid($uuids[$i]));
            $this->assertTrue(ULID::valid($uuids[$i + 1]));
        }
    }

    public function test_generate_one_ulid()
    {
        $ulid = ULID::generate();
        $this->assertSame(26, strlen($ulid->toULID()));
    }

    public function test_generates_multiple_ulids()
    {
        $ulid = ULID::generate(10);
        $list = $ulid->toULID();

        $this->assertIsArray($list);
        $this->assertCount(10, $list);

        $ulids = array_keys($list);

        for ($i = 0; $i < 10; $i += 2) {
            $this->assertSame(
                substr($ulids[$i], 0, 23),
                substr($ulids[$i + 1], 0, 23),
                'most characters except the last, are the same'
            );

            $this->assertNotSame(
                substr($ulids[$i], -1),
                substr($ulids[$i + 1], -1),
                'last characters are not the same'
            );
        }
    }

    public function test_invalid_generate_count()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessage('count must be greater then 0');
        $this->expectExceptionCode(400);

        ULID::generate(-1);
    }

    public function test_toULID_from_ulid_string()
    {
        $ulid = ULID::fromULID('01GX5BDH020BV0XHGGJ0RE0H2D');

        $this->assertStringContainsString(
            '2023-04-04 05:21:44.450',
            $ulid->toDateTime()->format('Y-m-d H:i:s.u')
        );
    }

    public function test_toUlid_with_invalid_length()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ULID, wrong length');
        $this->expectExceptionCode(400);

        ULID::fromULID('abc');
    }

    public function test_toUlid_with_invalid_characters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ULID, non supported characters');
        $this->expectExceptionCode(400);

        // contains "U"
        ULID::fromULID('01GX5C8QQFMPKF33UBGJM2GVW9');
    }

    public function test_should_fail_from_invalid_uuid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ULID');
        $this->expectExceptionCode(400);

        ULID::fromUUID('1234567890');
    }

    public function test_toUUID_from_ulid_string()
    {
        $ulid = ULID::fromUUID('01874ad1-12b6-328a-8cf8-97a3d48d8471');

        $this->assertSame(
            '2023-04-04 05:50:28.534',
            $ulid->toDateTime()->format('Y-m-d H:i:s.v')
        );
    }

    public function test_toUUID_from_garbage_ulid_string()
    {
        $ulid = ULID::fromUUID('5f347d67-17a9-4c71-8a54-db22e0235bd6');

        $this->assertInstanceOf(ULID::class, $ulid,
            'any UUID is converted, regardless of the encoded value');
    }

    public function test_from_timestamp()
    {
        $ulid = ULID::fromTimestamp(1680627803.321);
        $this->assertSame(
            '2023-04-04 17:03:23.321',
            $ulid->toDateTime()->format('Y-m-d H:i:s.v')
        );

        $ulid = ULID::fromTimestamp(1680627803);
        $this->assertSame(
            '2023-04-04 17:03:23.000',
            $ulid->toDateTime()->format('Y-m-d H:i:s.v')
        );
    }

    public function test_from_invalid_timestamp()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp (-123)');
        $this->expectExceptionCode(400);

        ULID::fromTimestamp(-123);
    }


    public function test_from_invalid_datetime()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid datetime (-123)');
        $this->expectExceptionCode(400);

        ULID::fromDateTime(-123);
    }

    public function test_should_transform_datetime()
    {
        $ulid = ULID::fromDateTime('2023-04-07 07:45:26');

        $this->assertSame(
            '2023-04-07 07:45:26.000',
            $ulid->toDateTime()->format('Y-m-d H:i:s.v'),
            'Date time string without milliseconds'
        );

        $ulid = ULID::fromDateTime('2023-04-07 07:45:26.443');

        $this->assertSame(
            '2023-04-07 07:45:26.443',
            $ulid->toDateTime()->format('Y-m-d H:i:s.v'),
            'Date time string with milliseconds'
        );
    }

    public function test_transformations_should_return_same_datetime()
    {
        $actual = '2023-04-07 07:45:26';
        $instance = ULID::fromDateTime($actual);

        $uuid = $instance->toUUID();
        $ulid = $instance->toULID();
        $dt = $instance->toDateTime();

        // from UUID
        $this->assertSame(
            $ts1 = $dt->format('Y-m-d H:i:s.v'),
            $ts2 = ULID::fromUUID($uuid)->toDateTime()->format('Y-m-d H:i:s.v')
        );
        $this->assertSame("$actual.000", $ts1);
        $this->assertSame("$actual.000", $ts2);

        // from ULID
        $this->assertSame(
            $ts3 = $dt->format('Y-m-d H:i:s.v'),
            $ts4 = ULID::fromULID($ulid)->toDateTime()->format('Y-m-d H:i:s.v')
        );
        $this->assertSame("$actual.000", $ts3);
        $this->assertSame("$actual.000", $ts4);

    }
}
