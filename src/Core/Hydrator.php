<?php

namespace App\Core;

trait Hydrator
{
    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function hydrate(array $data): self
    {
        $reflection = new \ReflectionClass(self::class);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            if ($reflection->hasProperty($key)) {
                $property = $reflection->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($instance, $value);
            }
        }

        return $instance;
    }
}
