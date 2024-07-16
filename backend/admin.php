<?php

class Admin
{
    public static $SESSION_NAME = 'admin';

    public static $NEWS_DIR;

    public static function init()
    {
        self::$NEWS_DIR = __DIR__ . '\resources\welcome\news\\';
    }
}