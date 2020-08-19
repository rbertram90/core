<?php

namespace rbwebdesigns\core\querybuilder;

class QueryCondition {

    public const EQUALS_OPERATOR = '=';
    public const LESS_THAN_OPERATOR = '<';
    public const GREATER_THAN_OPERATOR = '>';

    public $name;
    public $value;
    public $operator = self::EQUALS_OPERATOR;

    public function __construct() {

    }

    public function __toString() {
        if (is_null($this->value)) {
            return $this->name . ' IS NULL';
        }
        elseif ($this->value == 'CURRENT_TIMESTAMP') {
            return $this->name . ' ' . $this->operator . ' ' . $this->value;
        }
        else {
            return $this->name . ' ' . $this->operator . '"' . $this->value . '"';
        }
    }

}
