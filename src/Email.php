<?php

namespace rbwebdesigns\core;

/**
 * Simple wrapper class around php mail() function.
 */
class Email {

	/** @var bool Does the email contain HTML formatting? */
	public $html = true;

	/** @var string Recipient email address */
	public $recipient;

	/** @var string 'From' email address */
	public $sender;

	/** @var string Email subject line */
	public $subject;

	/** @var string Email body */
	public $message;
	
	/**
	 * Send the email.
	 * 
	 * @return bool Was the email sent correctly?
	 */
	public function send() {
		if (!$this->validate()) {
			// @todo some sort of logging
			return false;
		}

		$headers = 'From: ' . $this->sender . "\r\n";
		$headers .= "Reply-To: ". $this->sender . "\r\n";

		if ($this->html) {
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		}
		else {
			$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
		}

		// Send the email
		return mail($this->recipient, 'Subject: ' . $this->subject, $this->message, $headers);
	}

	protected function validate() {
		if (!filter_var($this->sender, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		if (!filter_var($this->recipient, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		return true;
	}

}
