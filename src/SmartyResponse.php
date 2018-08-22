<?php
namespace rbwebdesigns\core;

use rbwebdesigns\core\Response;

class SmartyResponse extends Response
{
    protected $smarty;

    public function __construct($templateDirectory)
    {
        $this->smarty = new \Smarty;
        $this->smarty->setTemplateDir($templateDirectory);
    }

    /**
     * Provide a render function that uses smarty template
     */
    public function write($templatePath)
    {
        if(!file_exists($this->smarty->getTemplateDir(0) . $templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>');
        }
        else {
            $this->smarty->display($templatePath);
        }
    }

    /**
     * Output the template SMARTY style
     */
    public function writeTemplate($templatePath)
    {
        $this->write($templatePath);
    }

    /**
     * Overwrites default to use smarty templates
     */
    public function setVar($name, $value)
    {
        $this->smarty->assign($name, $value);
    }

}