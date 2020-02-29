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
     * Load a JSON file and put into an array
     * 
     * @param string $filepath
     * 
     * @return array
     */
    public static function JSONFileToArray($filepath)
    {
        // Check path is valid
        if (!file_exists($filepath)) return false;
        
        // Read the JSON in from a file
        $json = file_get_contents($filepath);

        // Return as array
        return JSONhelper::JSONtoArray($json);
    }

    /**
     * Convert a JSON string to an array
     * 
     * @param string $json
     * 
     * @return array
     */
    public static function JSONtoArray($json)
    {
        return json_decode($json, true);
    }

    /**
     * Convert an array into a JSON string
     * 
     * @param array $array
     * 
     * @return string
     */
    public static function arrayToJSON($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

}
