<?php
namespace rbwebdesigns\core;

/**
 * src/validation.php
 * static functions to validate input
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Validate
{
	/**
	 * Determine if a string is a valid email address
	 * 
	 * @param string $email
	 * @return bool
	 */
	public static function email($email)
	{
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Determine if a string is a valid integer
	 * 
	 * @param mixed $int
	 * @return bool
	 */
	public static function int($int)
	{
		if(filter_var($int, FILTER_VALIDATE_INT)) {
			return true;
		}
		return false;
	}

	/**
	 * Determine if a string is a valid date & time (format = Y-m-d H:i:s)
	 * @todo doesn't actually check the date is a valid in the gregorian calendar
	 * 
	 * @param string $timestamp
	 * @return bool
	 */
	public static function dateTime($timestamp)
	{
		if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $timestamp)) {
			return true;
		}
		return false;
	}
	
	/**
	 * @todo Implement!
	 */
	public static function postcode($postcode) {
		
	}

	/**
	 * @todo Implement!
	 */
	public static function phoneNumber($phoneNumber) {
	
	}
}
