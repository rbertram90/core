<?php

namespace rbwebdesigns\core\traits;

trait HTMLElement
{
    protected array $attributes = [];

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function setAttribute(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get attributes as a formatted string.
     * 
     * Turns attributes:
     * ['class' => 'something', 'id' => 'something_else']
     * 
     * Into:
     *  class="something" id="something_else"
     */
    public function outputAttributes()
    {
        $result = "";

        array_walk($this->attributes, function($value, $key) use (&$result) {
            $result .= sprintf(" %s='%s'", $key, $value);
        });

        return $result;
    }
}