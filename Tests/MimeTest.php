<?php

namespace Koded\Stdlib;

class MimeTest extends \PHPUnit_Framework_TestCase
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
