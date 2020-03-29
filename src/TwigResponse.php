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
     * Provide a render function that uses Twig template
     */
    public function write($templatePath)
    {
        print $this->twig->render($templatePath, $this->templateVarables);
    }

    /**
     * Output the template Twig style
     */
    public function writeTemplate($templatePath)
    {
        // $currentUser = $session->currentUser;
        // $messages = $session->getAllMessages();

        print $this->twig->render($templatePath, array_merge($this->templateVarables, [
            'scripts' => $this->prepareScripts(),
            'stylesheets' => $this->prepareStylesheets(),
            'content' => $this->body
        ]));
    }

    /**
     * Overwrites default to use Twig templates
     */
    public function setVar($name, $value)
    {
        $this->templateVarables[$name] = $value;
    }

    /**
     * Enable twig debug mode
     */
    public function enableDebug() {
        $this->twig->enableDebug();
        $this->enableAutoReload();
    }

    /**
     * Enables the auto_reload option.
     */
    public function enableAutoReload() {
        $this->twig->enableAutoReload();
    }


}