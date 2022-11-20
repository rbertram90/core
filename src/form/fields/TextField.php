<?php

namespace rbwebdesigns\core\form\fields;

class TextField extends FormField
{
    public function render(): string
    {
        $attributes = $this->outputAttributes();
        
        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        $validTypes = ['password', 'text', 'number', 'email', 'tel'];
        $type = in_array($this->options['type'] ?? null, $validTypes) ? $this->options['type'] : 'text';

        $field.= "<input type='{$type}' value='{$this->value}' name='{$this->name}'{$attributes}>" . PHP_EOL;

        $this->createFieldWrapper($field);

        return $field;
    }
}
