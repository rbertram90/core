<?php

namespace rbwebdesigns\core\form\fields;

class HiddenField extends FormField
{
    public function render(): string
    {
        return "<input type='hidden' value='{$this->value}' name='{$this->name}'{$this->outputAttributes()}>" . PHP_EOL;
    }
}
