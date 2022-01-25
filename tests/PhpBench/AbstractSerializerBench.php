<?php

namespace Tests\Koded\Stdlib\PhpBench;

use Koded\Stdlib\Serializer;

/**
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 * @OutputTimeUnit("milliseconds")
 * @Groups("serializers")
 */
abstract class AbstractSerializerBench
{
    /** @var Serializer */
    protected $serializer;

    abstract public function setUp(): void;

    /**
     * @Revs(1000)
     * @Iterations(5)
     * @ParamProviders({"dataProvider"})
     */
    public function benchmark($data)
    {
        $result = $this->serializer->serialize($data);
        $this->serializer->unserialize($result);
    }

    public function tearDown(): void
    {
        $this->serializer = null;
    }

    public function dataProvider()
    {
        $message = require_once __DIR__ . '/../fixtures/error-message.php';
        unset($message['datetime'], $message['object']);

        yield 'message' => $message;
        yield 'nested' => require_once __DIR__ . '/../fixtures/nested-array.php';
        yield 'simple' => require_once __DIR__ . '/../fixtures/expected-env-data.php';
    }
}
