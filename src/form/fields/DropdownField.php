<?php

namespace rbwebdesigns\core\form\fields;

use rbwebdesigns\core\form\InvalidFieldDefinitionException;

class DropDownField extends FormField
{
    public function render(): string
    {
        if (!isset($this->options['options'])) {
            throw new InvalidFieldDefinitionException("Field is missing required option 'options'.");
        }
        
        $attributes = $this->outputAttributes();

        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        $field.= "<select name='{$this->name}'{$attributes}>";

        foreach ($this->options['options'] ?? [] as $value => $text) {
            $selected = '';

            if (isset($this->value) && $this->value === $value) {
                $selected = ' selected';
            }

            $field.= "<option value='{$value}'{$selected}>{$text}</option>" . PHP_EOL;
        }

        $field.= "</select>";

        $this->createFieldWrapper($field);

        return $field;
    }
}
