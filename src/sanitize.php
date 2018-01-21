<?php
namespace rbwebdesigns\core;

/**
 * sanitize.php
 * static functions to sanitize input for database storage
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Sanitize
{
	/**
	 * Sanitize funny characters in a string
	 * @param string $string
	 * @return string
	 */
	public static function string($string)
	{
		return filter_var($string, FILTER_SANITIZE_STRING);
	}
	
	/**
	 * Sanitize a blob upload
	 */
	public static function blob($blob)
	{
		return addslashes($blob);
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
