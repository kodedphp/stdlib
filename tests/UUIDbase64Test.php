<?php declare(strict_types=1);

namespace Tests\Koded\Stdlib;

use InvalidArgumentException;
use Koded\Stdlib\UUID;
use PHPUnit\Framework\TestCase;

class UUIDbase64Test extends TestCase
{
    /**
     * @dataProvider base64Binary
     */
    public function test_base64_binary_encoded($uuid, $base64)
    {
        $encoded = UUID::toBase64($uuid);
        $decoded = UUID::fromBase64($encoded);

        $this->assertSame($base64, $encoded);
        $this->assertSame($uuid, $decoded);;
    }

    /**
     * @dataProvider base64Plain
     */
    public function test_base64_plain_encoded($uuid, $base64)
    {
        $this->assertSame($uuid, UUID::fromBase64($base64));;
    }

    public function test_to_base64_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID 0');
        UUID::toBase64('0');
    }

    public function test_from_base64_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to convert base 64 string to UUID');
        UUID::fromBase64('0');
    }

    public function base64Binary()
    {
        return [
            // with hex2bin
            ['e6421c02-1aab-499b-9123-ef02d69f49ba', '5kIcAhqrSZuRI_8C1p9Jug'],
            ['524d188a-756d-453b-8af5-674a7291197d', 'Uk0YinVtRTuK9WdKcpEZfQ'],
            ['ed67a863-ff19-475a-a67d-9f9eb63e4dce', '7WeoY-8ZR1qmfZ_etj5Nzg'],
        ];
    }

    public function base64Plain()
    {
        return [
            // only base64 encoded
            ['e6421c02-1aab-499b-9123-ef02d69f49ba', 'ZTY0MjFjMDItMWFhYi00OTliLTkxMjMtZWYwMmQ2OWY0OWJh'],
            ['524d188a-756d-453b-8af5-674a7291197d', 'NTI0ZDE4OGEtNzU2ZC00NTNiLThhZjUtNjc0YTcyOTExOTdk'],
            ['ed67a863-ff19-475a-a67d-9f9eb63e4dce', 'ZWQ2N2E4NjMtZmYxOS00NzVhLWE2N2QtOWY5ZWI2M2U0ZGNl'],
        ];
    }
}
