<?php

namespace rbwebdesigns\core\form;

use rbwebdesigns\core\traits\HTMLElement;

class Action
{
    use HTMLElement;

    public string $label;

    public function __construct($config)
    {
        $this->label = $config['label'];

        if (array_key_exists('attributes', $config)) {
            $this->setAttributes($config['attributes']);
        }

        if (array_key_exists('type', $config)) {
            $this->setAttribute('type', $config['type']);
        }
    }

    public function render()
    {
        return "<button{$this->outputAttributes()}>{$this->label}</button>";
    }
}
