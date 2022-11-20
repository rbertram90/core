<?php

namespace rbwebdesigns\core\form\fields;

class RadiosField extends FormField
{
    public function render(): string
    {
        $attributes = $this->outputAttributes();

        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        foreach ($this->options['options'] as $key => $label) {
            $field.= "<input type='radio' name='{$this->name}' id='$key'>";
            $field.= $this->createLabel($label, $key);
        }

        $this->createFieldWrapper($field);

        return $field;
    }
}
