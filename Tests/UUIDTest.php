<?php

namespace Koded\Stdlib;

use InvalidArgumentException;

class UUIDTest extends \PHPUnit_Framework_TestCase {

    const NS1 = '4d436f52-5707-4cc3-b69d-ec060ccdbcba';
    const NS2 = 'b7890c8d-f62d-4048-ab4e-9cff3ab590d2';

    /**
     * @test
     */
    public function v1_with_node() {
        $uuid = UUID::v1('08:60:6e:11:c0:8e');
        $this->assertTrue(UUID::isValid($uuid));
        $this->assertSame('1', $uuid[14]);
    }

    /**
     * @test
     */
    public function v1_with_integer_node() {
        $uuid = UUID::v1(0x7fffffff);
        $this->assertTrue(UUID::isValid($uuid));
    }

    /**
     * @test
     */
    public function v1_with_invalid_node() {
        $this->expectException(InvalidArgumentException::class);
        UUID::v1('127.0.0.1');
    }

    /**
     * @test
     */
    public function v1_with_invalid_integer_node() {
        $this->expectException(InvalidArgumentException::class);
        UUID::v1(2987918954764484727721);
    }

    /**
     * @test
     */
    public function v1_with_invalid_hexadecimal_node() {
        $this->expectException(InvalidArgumentException::class);
        UUID::v1('z7ba3e221');
    }

    /**
     * @test
     */
    public function v1_created_without_node() {
        $uuid = UUID::v1();
        $this->assertTrue(UUID::isValid($uuid));
    }

    /**
     * @test
     */
    public function v3_uuids_from_same_namespace_and_same_name_are_equal() {
        $uuid1 = UUID::v3(self::NS1, 'foo/bar');
        $uuid2 = UUID::v3(self::NS1, 'foo/bar');
        $this->assertSame($uuid1, $uuid2);
        $this->assertSame('3', $uuid1[14]);
    }

    /**
     * @test
     */
    public function v3_uuids_from_same_namespace_and_different_names_are_different() {
        $uuid1 = UUID::v3(self::NS1, '123');
        $uuid2 = UUID::v3(self::NS1, '456');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('3', $uuid1[14]);
        $this->assertSame('3', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v3_uuids_from_different_namespace_and_same_name_are_different() {
        $uuid1 = UUID::v3(self::NS1, 'foo');
        $uuid2 = UUID::v3(self::NS2, 'foo');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('3', $uuid1[14]);
        $this->assertSame('3', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v3_throws_exception_on_invalid_namespace() {
        $this->expectException(InvalidArgumentException::class);
        UUID::v3('foo', 'foo');
    }

    /**
     * @test
     */
    public function check_the_v4_format() {
        $v4 = UUID::v4();
        $this->assertTrue(UUID::isValid($v4));

        // check v4 spec
        $this->assertEquals('4', $v4[14]);
        $this->assertTrue(in_array($v4[19], ['8', '9', 'a', 'b']));
    }

    /**
     * @test
     */
    public function v5_uuids_from_same_namespace_and_same_name_are_equal() {
        $uuid1 = UUID::v5(self::NS1, 'foo/bar/baz');
        $uuid2 = UUID::v5(self::NS1, 'foo/bar/baz');
        $this->assertSame($uuid1, $uuid2);
        $this->assertSame('5', $uuid1[14]);
    }

    /**
     * @test
     */
    public function v5_uuids_from_same_namespace_and_different_names_are_different() {
        $uuid1 = UUID::v5(self::NS1, '123');
        $uuid2 = UUID::v5(self::NS1, '456');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('5', $uuid1[14]);
        $this->assertSame('5', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v5_uuids_from_different_namespace_and_same_name_are_different() {
        $uuid1 = UUID::v5(self::NS1, 'foo');
        $uuid2 = UUID::v5(self::NS2, 'foo');
        $this->assertTrue($uuid1 !== $uuid2);
        $this->assertSame('5', $uuid1[14]);
        $this->assertSame('5', $uuid2[14]);
    }

    /**
     * @test
     */
    public function v5_throws_exception_on_invalid_namespace() {
        $this->expectException(InvalidArgumentException::class);
        UUID::v5('foo', 'foo');
    }
}
