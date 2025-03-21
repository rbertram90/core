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
    protected $meta = [];
    protected $variables = [];
    protected $body = "";

    /**
     * Set a variable to use in template
     */
    public function setVar($name, $value): self
    {
        $this->variables[$name] = $value;

        return $this;
    }

    /**
     * Set many variables in one call.
     * @param array $values Associative array of variable key, value
     */
    public function setVars(array $values): self
    {
        $this->variables = [
            ...$this->variables,
            ...$values,
        ];

        return $this;
    }

    public function getVar($name)
    {
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }
        return false;
    }

    /**
     * Output page template
     */
    public function writeTemplate($templatePath)
    {
        global $session;

        $scripts = $this->prepareScripts();
        $stylesheets = $this->prepareStylesheets();
        $meta = $this->prepareMeta();
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
    public function setTitle($title): self
    {
        $this->setVar('page_title', $title);

        return $this;
    }

    /**
     * Set the meta description for the page
     */
    public function setDescription($description): self
    {
        $this->setVar('page_description', $description);

        return $this;
    }

    /**
     * Add a meta tag.
     */
    public function addMeta($name, $content)
    {
        $this->meta[$name] = $content;
    }

    protected function prepareMeta()
    {
        $markup = '';

        foreach($this->meta as $name => $content) {
            $markup .= "<meta name=\"$name\" content=\"$content\">" . PHP_EOL;
        }

        return $markup;
    }

    /**
     * Set the main body content
     */
    public function setBody($output): self
    {
        $this->body = $output;

        return $this;
    }

    /**
     * Get the main body content
     */
    public function getBody()
    {
        return $this->body;
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
    public function addScript($link): self
    {
        $this->scripts[] = $link;

        return $this;
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
    public function addStylesheet($link): self
    {
        $this->stylesheets[] = $link;

        return $this;
    }

    /**
     * Convert array of file paths to link tags for HTML head
     */
    protected function prepareStylesheets()
    {
        $styleMarkup = '';

        foreach ($this->stylesheets as $stylesheet) {
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
        if (strlen($message) > 0) {
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
    public function addHeader($name, $value): self
    {
        header("{$name}: {$value}");

        return $this;
    }

}
