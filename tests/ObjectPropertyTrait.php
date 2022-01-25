<?php

namespace Tests\Koded\Stdlib;

trait ObjectPropertyTrait
{
    private function property(object $object, string $property)
    {
        $prop = new \ReflectionProperty($object, $property);
        $prop->setAccessible(true);
        return $prop->getValue($object);
    }
}
