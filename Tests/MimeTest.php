<?php

namespace Koded\Stdlib;

use PHPUnit\Framework\TestCase;

class MimeTest extends TestCase
{

    public function test_when_mime_is_not_found_it_should_return_default_value()
    {
        $this->assertSame('text/html', Mime::type('fubar-type'));
    }

    public function test_return_first_known_mime_type()
    {
        $this->assertSame('application/x-httpd-php', Mime::type('php'));
    }

    public function test_return_known_mime_type_by_index()
    {
        $this->assertSame('text/csv', Mime::type('csv', 3));
    }
}
