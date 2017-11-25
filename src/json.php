<?php
namespace rbwebdesigns\core;

class JSONhelper
{
    public static function jsonToArray($filepath)
    {
        // Check path is valid
        if(!file_exists($filepath)) return false;
        
        // Read the JSON in from a file
        $json = file_get_contents($filepath);

        // Return as array
        return json_decode($json, true);
    }

    public static function arrayToJSON($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT);
    }
}

?>