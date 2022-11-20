<?php

namespace rbwebdesigns\core\form\fields;

class ColourField extends FormField
{
    public function render(): string
    {
        $attributes = $this->outputAttributes();

        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        $field.= "<input type='color' value='{$this->value}' name='{$this->name}'{$attributes}>" . PHP_EOL;

        $this->createFieldWrapper($field);

        return $field;
    }
}
