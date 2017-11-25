<?php
namespace rbwebdesigns\core;

/**************************************************************************
	 sanitize.php
     @description static functions to sanitize input for database storage
     @author R.Bertram
     @date 2017
***************************************************************************/

class Sanitize
{
	// Sanitize a string
	public static function string($str)
	{
		return filter_var($str, FILTER_SANITIZE_STRING);
	}

	// Check if a variable is a boolean - be harsh and raise an error if it isn't!
	public static function boolean($bool)
	{
		// note - this is more validation than sanitization!
		if($bool !== true && $bool !== false && $bool !== 0 && $bool !== 1 && $bool !== "1" && $bool !== "0") {
			die("Input Type Error - Expected Boolean Found ".$bool." (".getType($bool).")");
		} else {
			return $bool;
		}
	}
	
	// Sanitize a blob upload
	public static function blob($str)
	{
		// Filter will not work here!
		return addslashes($str);
	}

	public static function timestamp($ts)
	{
		// note - this is more validation than sanitization!
		if(!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $ts)) {
			die("Unable to match timestamp - expected 'YYYY-MM-DD HH:mm:SS' found ".$ts);
		} else {
			return $ts;
		}
	}

	// Sanitize an integer
	public static function int($num)
	{
		return filter_var($num, FILTER_SANITIZE_NUMBER_INT);
	}

	// Sanitize a floating point number
	public static function float($float)
	{
		return filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}

	// Sanitize an Email address
	public static function email($email)
	{
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}
}
