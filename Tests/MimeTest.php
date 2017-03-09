<?php

namespace Koded\Stdlib;

use PHPUnit\Framework\TestCase;

class MimeTest extends TestCase
{

    public function testWhenMimeIsNotFoundItShouldReturnDefaultValue()
    {
        $this->assertSame('text/html', Mime::type('fubar-type'));
    }

    public function testReturnFirstKnownMimeType()
    {
        $this->assertSame('application/x-httpd-php', Mime::type('php'));
    }

    public function testReturnKnownMimeTypeByIndex()
    {
        $this->assertSame('text/csv', Mime::type('csv', 3));
    }
}
