<?php

namespace rbwebdesigns\core\form\fields;

class LongTextField extends FormField
{
    public function render(): string
    {
        $attributes = $this->outputAttributes();
        
        if (isset($options['required']) && $options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        $field.= "<textarea name='{$this->name}'{$attributes}>{$this->value}</textarea>" . PHP_EOL;

        $this->createFieldWrapper($field);

        return $field;
    }
}
