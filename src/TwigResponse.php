<?php
namespace rbwebdesigns\core;

use rbwebdesigns\core\Response;
use Twig\TemplateWrapper;

class TwigResponse extends Response
{
    protected $twig;

    public function __construct($templateDirectory, $cacheDirectory)
    {
        $loader = new \Twig\Loader\FilesystemLoader($templateDirectory);
        $this->twig = new \Twig\Environment($loader, [
            'cache' => $cacheDirectory,
        ]);
    }

    /**
     * Get the processed twig content without printing.
     */
    public function render(string|TemplateWrapper $templatePath): string
    {
        return $this->twig->render($templatePath, $this->variables);
    }

    /**
     * Provide a render function that uses Twig template
     */
    public function write($templatePath)
    {
        print $this->twig->render($templatePath, $this->variables);
    }

    /**
     * Output the template Twig style
     */
    public function writeTemplate($templatePath)
    {
        // $currentUser = $session->currentUser;
        // $messages = $session->getAllMessages();

        print $this->twig->render($templatePath, [
            ...$this->variables,
            'scripts' => $this->prepareScripts(),
            'stylesheets' => $this->prepareStylesheets(),
            'content' => $this->body
        ]);
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
