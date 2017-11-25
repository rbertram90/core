<?php

    /**
     * Check if a key in a nested array exists
     * @param $array array to search
     * @param $key path to item to check - seperated by .
     * @return true if element exists
     */

    function multiarray_key_exists($array, $key)
    {
        $targetKeys = explode('.', $key);
        $targetArray = $array;
        $keyIndex = 0;
        
        foreach($targetKeys as $targetkey)
        {
            if(array_key_exists($targetkey, $targetArray))
            {
                // Found key
                if($keyIndex < count($targetKeys) - 1)
                {
                    // This should be an array
                    if(gettype($targetArray[$targetkey] == 'array'))
                    {
                        $targetArray = $targetArray[$targetkey];
                    }
                    else return false;
                }
            }
            else return false; // key doesn't exist
            
            $keyIndex++;
        }
        
        return true;
    }
