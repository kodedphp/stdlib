<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class BugConfigFromIniStringTest extends TestCase
{
    public function test_parse_ini_string_expected_same_transformation_as_from_parse_ini_file()
    {
        $config = new Config;
        $config->fromEnvironment(['KEY_4'], '', false);

        $this->assertNotSame(true, $config->get('KEY_4'), 'Expects (bool)true instead (int)1');
        $this->assertSame(1, $config->get('KEY_4'), 'parse_ini_string(): booleans are returned as integers...');
    }
}
