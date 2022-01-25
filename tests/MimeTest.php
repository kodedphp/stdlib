<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\Mime;
use PHPUnit\Framework\TestCase;

class MimeTest extends TestCase
{

    public function test_when_mime_is_not_found_it_should_return_empty_value()
    {
        $this->assertSame('', Mime::type('fubar-type'));
    }

    public function test_return_first_known_mime_type()
    {
        $this->assertSame('application/x-httpd-php', Mime::type('php'));
    }

    public function test_return_known_mime_type_by_index()
    {
        $this->assertSame('text/csv', Mime::type('csv', 3));
    }

    public function test_mime_list()
    {
        $this->assertSame(
            ['application/x-zip', 'application/zip', 'application/x-zip-compressed'],
            Mime::types('zip')
        );
    }

    public function test_unknown_mime_list()
    {
        $this->assertSame([], Mime::types('junk-extension-name'));
    }

    public function test_supports()
    {
        $this->assertTrue(Mime::supports('application/x-msg'));
        $this->assertFalse(Mime::supports('fubar'));
    }

    public function test_extensions()
    {
        $this->assertEquals(['json'], Mime::extensions('application/json'));
    }
}
