<?php

class Redirect
{
    public static function to($url)
    {
        header("Location: $url");
        exit();
    }

    public static function toIndex()
    {
        self::to('/1C-2026/tpfinal_mvc/vikingo/ver');
    }
}
