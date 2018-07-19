<?php

namespace ApiClient\Config;

class Config
{
    private const SettingsFilePath = 'settings.php';
    private static $_settings = [];

    private function __construct(){}
    private function __clone(){}

    public static function get(string $parameter)
    {
        if(empty(self::$_settings)){
            self::$_settings = include self::SettingsFilePath;
        }

        $path = explode('.', $parameter);
        $temp =& self::$_settings;

        foreach($path as $key) {
            $temp =& $temp[$key];
        }
        return $temp;
    }
}