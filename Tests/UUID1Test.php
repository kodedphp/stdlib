<?php

namespace Tests\Koded\Stdlib;

use InvalidArgumentException;
use Koded\Stdlib\UUID;
use PHPUnit\Framework\TestCase;

class UUID1Test extends TestCase
{
    /**
     * @test
     */
    public function v1_with_mac_address()
    {
        $uuid = UUID::v1('08:60:6e:11:c0:8e');
        $this->assertTrue(UUID::matches($uuid, 1));
        $this->assertSame('1', $uuid[14], $uuid);
    }

    /**
     * @test
     */
    public function v1_with_integer_node()
    {
        $uuid = UUID::v1(0x7fffffff);
        $this->assertTrue(UUID::matches($uuid, 1), $uuid);
    }

    /**
     * @test
     */
    public function v1_with_ipv6_node_is_not_supported()
    {
        $this->expectException(InvalidArgumentException::class);
        UUID::v1('2001:db8:85a3:8d3:1319:8a2e:370:7348');
    }

    /**
     * @test
     */
    public function v1_with_invalid_node()
    {
        //$this->expectException(InvalidArgumentException::class);
        $uuid = UUID::v1('192.168.100.97');
        $this->assertSame('1', $uuid[14], $uuid);
    }

    /**
     * @test
     */
    public function v1_with_invalid_integer_node()
    {
        $this->expectException(InvalidArgumentException::class);
        UUID::v1(2987918954764484727721);
    }

    /**
     * @test
     */
    public function v1_with_invalid_hexadecimal_node()
    {
        $this->expectException(InvalidArgumentException::class);
        UUID::v1('z7ba3e22');
    }

    /**
     * @test
     */
    public function v1_created_without_node()
    {
        $uuid = UUID::v1();
        $this->assertTrue(UUID::matches($uuid, 1));
    }
}
