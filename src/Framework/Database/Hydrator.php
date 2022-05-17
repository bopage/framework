<?php
namespace Framework\Database;

class Hydrator
{
    public static function hydrate(array $array, string $object)
    {
        $instance = new $object;
        foreach ($array as $key => $value) {
            $method = self::getSetter($key);
            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } else {
                $property = lcfirst(self::getProperty($key));
                $instance->$property = $value;
            }
        }
        return $instance;
    }

    private static function getSetter(string $fileName): string
    {
        return 'set' . self::getProperty($fileName);
    }

    private static function getProperty(string $fileName): string
    {
        return join('', array_map('ucfirst', explode('_', $fileName)));
    }
}
