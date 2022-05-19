<?php
namespace Framework\Database;

class Hydrator
{

    /**
     * hydrate
     *
     * @param  array $array
     * @param  string|object $object
     * @return mixed
     */
    public static function hydrate(array $array, $object)
    {
        if (is_string($object)) {
            $instance = new $object;
        } else {
            $instance = $object;
        }
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
