<?php

namespace rbwebdesigns\core\form\fields;

class UploadField extends FormField
{
    /**
     * @todo can we populate value?
     */
    public function render(): string
    {
        $attributes = $this->outputAttributes();

        if (isset($this->options['required']) && $this->options['required']) {
            $attributes.= ' required';
        }

        $field = $this->createLabel();

        $field.= "<input type='file' name='{$this->name}'{$attributes}>" . PHP_EOL;

        $this->createFieldWrapper($field);

        $this->encodingType = 'multipart/form-data';

        return $field;
    }
}
