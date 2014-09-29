<?php

// constants used: DEFAULTDIR CACHEDISABLED

class Cache extends Controller
{
    public static $DEFAULTDIR = null; // uses DEFAULTDIR if exists

    public static function dir($date = null)
    {
        if (is_null($date)) $date = date("dmY");
        $dir = (defined("DEFAULTDIR") ? DEFAULTDIR : self::$DEFAULTDIR);
        if (is_null($dir))
            $dir = DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $date;
        if (!is_dir($dir))
            @mkdir($dir, 0777, true);
        if (defined("NOISE")) echo "<log cache dir $dir ></log>";
        return (is_dir($dir) ? $dir : "/");
    }

    public static function filename($key)
    {
        $filename = self::dir() . DIRECTORY_SEPARATOR . urlencode(sha1($key)) . '.cache';
        if (defined("NOISE")) echo "<log cache filename: $filename ></log>";
        return $filename;
    }

    public static function get($key)
    {
        if (self::exists($key) && !defined("CACHEDISABLED")) {
            $data = file_get_contents(self::filename($key));
            if (defined("NOISE")) echo "<log cache get:$key datalen:" . strlen($data) . " ></log>";
            return $data;
        }
        return false;
    }

    public static function exists($key)
    {
        return (!defined("CACHEDISABLED") && file_exists(self::filename($key)));
    }

    public static function set($key, $data, $flags = 0755)
    {

        if (!defined("CACHEDISABLED")) {
            if (defined("NOISE")) echo "<log cache set:$key datalen:" . strlen($data) . " flags:$flags ></log>";
            return file_put_contents(self::filename($key), $data, $flags);
        }
        return false;
    }

    public static function clear($limit = 3000)
    {
        $date = date("dmY", time() - 60 * 60 * 24);
        $dir = self::dir($date);
        $i = 0;
        if (!is_null($dir) && $handle = opendir($dir)) {
            /* This is the correct way to loop over the directory. */
            while (($limit == -1 || $limit < $i) && false !== ($entry = readdir($handle))) {
                if (strpos($entry, ".cache"))
                    unlink($dir . DIRECTORY_SEPARATOR . $entry);
                $i++;
            }
            closedir($handle);
        }
    }
}
