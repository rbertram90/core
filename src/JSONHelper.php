<?php
namespace rbwebdesigns\core;

/**
 * @method static array  jsonFileToArray  takes a json file and returns an array
 * @method static array  jsonToArray      takes a json string and returns an array
 * @method static string arrayToJSON      takes an array and converts it to a formatted JSON string
 */
class JSONhelper
{
    /**
     * @param string $filepath
     */
    public static function JSONFileToArray($filepath)
    {
        // Check path is valid
        if(!file_exists($filepath)) return false;
        
        // Read the JSON in from a file
        $json = file_get_contents($filepath);

        // Return as array
        return JSONhelper::JSONtoArray($json);
    }

    /**
     * @param string $json
     */
    public static function JSONtoArray($json)
    {
        return json_decode($json, true);
    }

    /**
     * @param array $array
     */
    public static function arrayToJSON($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT);
    }
}
