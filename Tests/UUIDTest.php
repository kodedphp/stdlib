<?php

namespace Tests\Koded\Stdlib;

use AssertionError;
use InvalidArgumentException;
use Koded\Stdlib\UUID;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    const NS3_1 = '4d436f52-5707-3cc3-b69d-ec060ccdbcba';
    const NS3_2 = 'b7890c8d-f62d-3048-ab4e-9cff3ab590d2';

    const NS5_1 = '832102e1-a4d7-5cf5-a554-5a8201259f49';
    const NS5_2 = 'fc03d336-5199-5422-bd34-678dd0867129';

    /**
     * @test
     */
    public function validates_the_uuid_format()
    {
        $this->assertTrue(UUID::valid(UUID::v4()));
    }

    /**
     * @test
     */
    public function v3_uuids_from_same_namespace_and_same_name_are_equal()
    {
        $uuid1 = UUID::v3(self::NS3_1, 'foo/bar');
        $uuid2 = UUID::v3(self::NS3_1, 'foo/bar');
        $this->assertSame($uuid1, $uuid2);
        $this->assertSame('3', $uuid1[14]);
    }

    /**
     * @test
     */
    public function v3_uuids_from_same_namespace_and_different_names_are_different()
    {
        $uuid1 = UUID::v3(self::NS3_1, '123');
        $uuid2 = UUID::v3(self::NS3_1, '456');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('3', $uuid1[14]);
        $this->assertSame('3', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v3_uuids_from_different_namespace_and_same_name_are_different()
    {
        $uuid1 = UUID::v3(self::NS3_1, 'foo');
        $uuid2 = UUID::v3(self::NS3_2, 'foo');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('3', $uuid1[14]);
        $this->assertSame('3', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v3_throws_exception_on_invalid_namespace()
    {
        $this->expectException(InvalidArgumentException::class);
        UUID::v3('foo', 'foo');
    }

    /**
     * @test
     */
    public function check_the_v4_format()
    {
        $v4 = UUID::v4();
        $this->assertTrue(UUID::valid($v4));

        // check v4 spec
        $this->assertEquals('4', $v4[14]);
        $this->assertTrue(in_array($v4[19], ['8', '9', 'a', 'b']));
    }

    /**
     * @test
     */
    public function v5_uuids_from_same_namespace_and_same_name_are_equal()
    {
        $uuid1 = UUID::v5(self::NS5_1, 'foo/bar/baz');
        $uuid2 = UUID::v5(self::NS5_1, 'foo/bar/baz');
        $this->assertSame($uuid1, $uuid2);
        $this->assertSame('5', $uuid1[14]);
    }

    /**
     * @test
     */
    public function v5_uuids_from_same_namespace_and_different_names_are_different()
    {
        $uuid1 = UUID::v5(self::NS5_1, '123');
        $uuid2 = UUID::v5(self::NS5_1, '456');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('5', $uuid1[14]);
        $this->assertSame('5', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v5_uuids_from_different_namespace_and_same_name_are_different()
    {
        $uuid1 = UUID::v5(self::NS5_1, 'foo');
        $uuid2 = UUID::v5(self::NS5_2, 'foo');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('5', $uuid1[14]);
        $this->assertSame('5', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v5_throws_exception_on_invalid_namespace()
    {
        $this->expectException(InvalidArgumentException::class);
        UUID::v5('foo', 'foo');
    }

    /**
     * @test
     */
    public function method_matches_fails_on_unsupported_uuid_version()
    {
        $this->markTestSkipped();

        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('Expected UUID version 1, 3, 4 or 5 (got 0)');
        UUID::matches(UUID::NAMESPACE_OID, 0);
    }

    /**
     * @test
     */
    public function verify_uuid_regex()
    {
        $this->assertFalse(UUID::valid('00000000-0000-2000-0000-000000000000'));
    }

    /**
     * @test
     */
    public function verify_uuid4()
    {
        $this->assertFalse(UUID::valid('00000000-0000-4000-0000-000000000000'));
        $this->assertTrue(UUID::valid('00000000-0000-4000-a000-000000000000'));
    }

    /**
     * @test
     */
    public function issue7()
    {
        $this->assertSame('1cb8bac3-bb8e-3973-93dc-5119246f0585', UUID::v3(UUID::NAMESPACE_URL, 'fubar'));
    }
}
