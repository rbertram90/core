<?php

namespace rbwebdesigns\core\form\fields;

class CheckboxField extends FormField
{
    public function render(): string
    {
        $field = $this->createLabel();

        $attributes = "";

        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        if (isset($this->options['checked']) && $this->options['checked']) {
            $attributes.= ' checked';
        }

        $attributes.= $this->outputAttributes();

        $field.= "<input type='checkbox' name='{$this->name}'{$attributes}>";

        $this->createFieldWrapper($field);

        return $field;
    }
}
