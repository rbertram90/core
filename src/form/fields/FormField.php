<?php

namespace rbwebdesigns\core\form\fields;

use rbwebdesigns\core\traits\HTMLElement;

abstract class FormField implements FormFieldInterface
{
    use HTMLElement;

    protected array $conditions = [];

    protected array $errors = [];

    public function __construct(
        public string $name,
        public array $options = [],
        public mixed $value = null
    )
    {
        $this->setAttributes($this->options['attributes'] ?? []);
    }

    /**
     * Generates HTML for a label
     * 
     * @param string $name
     * @param array  $options
     */
    protected function createLabel($label = null, $key = null)
    {
        if ($labelText = $label ?: $this->options['label'] ?? false) {
            $for = $key ?: $this->options['id'] ?? $this->name;

            return sprintf("<label for='%s'>%s</label>", $for, $labelText);
        }
    }

    /**
     * Create the field container
     * 
     * @param array $options
     */
    protected function createFieldWrapper(&$output): void
    {
        $before = $this->options['before'] ?? '<div class="field">';

        // @todo is this the best place for this?
        if ($this->options['conditions'] ?? []) {
            // @todo pass through an operator (> < !=)
            foreach ($this->options['conditions'] as $key => $value) {
                $this->addCondition($key, $value);
            }
        }

        foreach ($this->errors as $error) {
            $output .= "<p class='error message'>{$error}</p>";
        }

        $after = $this->options['after'] ?? '</div>';

        $output = $before . $output . $after;
    }

    /**
     * Does the field have any conditions on it's visibility.
     */
    public function hasConditions(): bool
    {
        return count($this->conditions) > 0;
    }

    /**
     * Get all visibility conditions added to the field.
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Add a script to conditionally hide or show a field based on another field.
     * 
     * @todo this can only be used as a protected function at the moment as field
     * objects are not created until render... think about offering both ways
     * to build a form.
     */
    public function addCondition(string $field, string $value, string $comparison = '='): static
    {
        $this->conditions[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $comparison,
        ];

        return $this;
    }

    public function hasErrors() {
        return count($this->errors) > 0;
    }

    public function setErrors(array $errors) {
        $this->errors = $errors;

        return $this;
    }
}
