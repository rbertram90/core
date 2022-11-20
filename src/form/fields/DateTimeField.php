<?php

namespace rbwebdesigns\core\form\fields;

class DateTimeField extends FormField
{
    public function render(): string
    {
        $attributes = $this->outputAttributes();

        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        $field.= "<input type='datetime-local' value='{$this->value}' name='{$this->name}'{$attributes}>" . PHP_EOL;

        $this->createFieldWrapper($field);

        return $field;
    }
}
