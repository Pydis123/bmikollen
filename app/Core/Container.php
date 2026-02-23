<?php
namespace App\Core;

class Container {
    private static array $instances = [];

    public static function set(string $id, $instance): void {
        self::$instances[$id] = $instance;
    }

    public static function get(string $id) {
        if (!isset(self::$instances[$id])) {
            throw new \Exception("Service not found: {$id}");
        }
        return self::$instances[$id];
    }
}
