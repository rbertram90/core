<?php
namespace rbwebdesigns\core;

use rbwebdesigns\core\Response;

class TwigResponse extends Response
{
    protected $twig;
    protected $templateVarables;

    public function __construct($templateDirectory, $cacheDirectory)
    {
        $loader = new \Twig\Loader\FilesystemLoader($templateDirectory);
        $this->twig = new \Twig\Environment($loader, [
            'cache' => $cacheDirectory,
        ]);
        $this->templateVarables = [];
    }

    /**
     * Provide a render function that uses smarty template
     */
    public function write($templatePath)
    {
        print $this->twig->render($templatePath, $this->templateVarables);
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
        $this->templateVarables[$name] = $value;
    }

}