<?php


namespace App\Foundation;


class App
{
    public const VERSION = '1.0';

    private static $instance;

    public static function getInstance(): App
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}