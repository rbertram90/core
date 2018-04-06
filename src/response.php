<?php
namespace rbwebdesigns\core;

use rbwebdesigns\core\Session;

/**
 * core/response.php
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 * 
 * Documentation:
 * https://github.com/rbertram90/core/wiki/Response
 */
class Response
{
    protected $scripts = [];
    protected $stylesheets = [];
    protected $variables = [];
    protected $body = "";

    /**
     * Set a variable to use in template
     */
    public function setVar($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Output page template
     */
    public function writeTemplate($templatePath)
    {
        global $session;

        $scripts = $this->prepareScripts();
        $stylesheets = $this->prepareStylesheets();
        $output = $this->body;
        $currentUser = $session->currentUser;
        $messages = $session->getAllMessages();

        foreach($this->variables as $name => $value) {
            $$name = $value;
        }

        if(!file_exists($templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>'); // todo - create a proper debug class
        }
        else {
            require $templatePath;
        }
    }

    /**
     * Output content template
     */
    public function write($templatePath)
    {
        global $session;

        $currentUser = $session->currentUser;
        
        foreach($this->variables as $name => $value) {
            $$name = $value;
        }

        if(!file_exists($templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>'); // todo - create a proper debug class
        }
        else {
            require $templatePath;
        }
    }

    /**
     * Set the meta title for the page
     */
    public function setTitle($title)
    {
        $this->setVar('page_title', $title);
    }

    /**
     * Set the meta description for the page
     */
    public function setDescription($description)
    {
        $this->setVar('page_description', $description);
    }

    /**
     * Set the main body content
     */
    public function setBody($output)
    {
        $this->body = $output;
    }

    /**
     * Quick get out of jail free card where page template is not used
     */
    public function writeBody()
    {
        print $this->body;
    }

    /**
     * Add a file to be included as a javascript file import
     * @param string $link
     */
    public function addScript($link)
    {
        $this->scripts[] = $link;
    }

    /**
     * Convert array of file paths to script tags for HTML head
     */
    protected function prepareScripts()
    {
        $scriptMarkup = '';

        foreach($this->scripts as $script) {
            $scriptMarkup .= '<script src="' . $script .'" type="text/javascript"></script>' . PHP_EOL;
        }

        return $scriptMarkup;
    }

    /**
     * Add a file to be included as a css file import
     * @param string $link
     */
    public function addStylesheet($link)
    {
        $this->stylesheets[] = $link;
    }

    /**
     * Convert array of file paths to link tags for HTML head
     */
    protected function prepareStylesheets()
    {
        $styleMarkup = '';

        foreach($this->stylesheets as $stylesheet) {
            $styleMarkup .= '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '">' . PHP_EOL;
        }

        return $styleMarkup;
    }

    /**
     * Redirect elsewhere with optional message
     * @param string $location
     * @param string $message
     * @param string $messageType
     */
    public function redirect($location, $message = '', $messageType = 'info')
    {
        if(strlen($message) > 0) {
            Session::addMessage($message, $messageType);
        }
        session_write_close();
        header('Location: ' . $location);
        exit;
    }

    /**
     * Get or set the response code
     */
    public function code($code = -1)
    {
        if (is_numeric($code) && $code > -1) http_response_code($code);

        return http_response_code();
    }

    /**
     * Set a response header
     */
    public function addHeader($name, $value)
    {
        header("{$name}: {$value}");
    }
}