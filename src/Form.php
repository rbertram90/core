<?php

namespace rbwebdesigns\core;

/**
 * This is a simple HTML form helper.
 * 
 * @todo Would really like to persist data on form
 * submission error
 * 
 * @package Core
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
abstract class Form
{

    /**
     * @var string  Location of the form handler
     */
    public $action;
    /**
     * @var string  Method attribute of the form (GET, POST)
     */
    public $method;
    /**
     * @var string  Message from failed validation
     */
    public $error;
    /**
     * @var string  Result of running the validation method
     */
    public $isValid;
    /**
     * @var string  Text to show on the form submit button
     */
    public $submitLabel = 'Submit';
    /**
     * @var array  Other attributes to add to the form tag
     */
    protected $attributes;
    /**
     * @var array  Form field definition
     */
    protected $fields;

    /**
     * Constructor, currently does nothing!
     */
    public function __construct()
    {

    }

    /**
     * We cannot provide implementation of the validate
     * method here as we don't know enough information
     * about the data.
     */
    abstract public function validate($request);

    /**
     * We cannot provide implementation of the submit
     * method here as we don't know how the data is
     * to be saved.
     */
    abstract public function submit($request);

    /**
     * Get an attribute from the form tag.
     * 
     * @param  string  Attribute key
     * @return string  Attribute value
     */
    public function getAttribute($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return false;
    }

    /**
     * Set an attribute to be added to the form tag.
     * 
     * @param string $name   Attribute key
     * @param string $value  Attribute value
     * 
     * @return \rbwebdesigns\core\Form $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Add a field to the form
     * 
     * @param string $name     Name of the form field
     * @param array  $options  Definition of the field
     * 
     * @return \rbwebdesigns\core\Form $this
     */
    public function addField($name, $options)
    {
        if (isset($this->fields[$name])) {
            trigger_error('Overwritten form field ' . $name, E_USER_NOTICE);
        }
        if (!isset($options['type'])) {
            trigger_error('Unable to add field ' . $name . ' - required \'type\' field missing', E_USER_ERROR);
        }

        $this->fields[$name] = $options;
        return $this;
    }

    /**
     * Add multiple fields at once.
     * 
     * @param array $fields
     * 
     * @return \rbwebdesigns\core\Form $this
     */
    public function addFields($fields) {
        foreach ($fields as $fieldName => $fieldOptions) {
            $this->addField($fieldName, $fieldOptions);
        }
        return $this;
    }

    /**
     * Get an array of submitted values
     * 
     * @param \rbwebdesigns\core\Request $request
     * 
     * @return array
     */
    public function getFieldData($request)
    {
        $return = [];
        foreach ($this->fields as $key => $definition) {
            if ($request->get($key, false)) {
                $return[$key] = $request->get($key);
            }
        }
        return $return;
    }

    /**
     * Output or return the form contents as HTML
     * 
     * @param bool $print True if echoing the form HTML to the response
     * @return null|string
     */
    public function output($print = false)
    {
        $attributes = "";
        if (strlen($this->action)) $attributes.= " action='{$this->action}'";
        if (strlen($this->method)) $attributes.= " method='{$this->method}'";

        foreach ($this->attributes as $key => $value) {
            $attributes.= sprintf(" %s='%s'", $key, $value);
        }

        $output = "<form{$attributes}>" . PHP_EOL;

        foreach ($this->fields as $name => $field) {
            switch ($field['type']) {
                case 'upload':
                    break;
                case 'date':
                    break;
                case 'checkboxes':
                    break;
                case 'radiobuttons':
                    $output.= $this->createRadios($name, $field);
                    break;
                case 'year':
                    $start = isset($field['start']) ? $field['start'] : 1901;
                    $end = isset($field['end']) ? $field['end'] : date('Y');
                    $field['options'] = range($start, $end);
                case 'dropdown':
                    $output.= $this->createSelectField($name, $field);
                    break;
                case 'text':
                default:
                    $output.= $this->createTextField($name, $field);
                    break;
            }
        }

        $output.= "<button>{$this->submitLabel}</button>";

        $output.= "</form>";

        if ($print) echo $output;
        else return $output;
    }

    /**
     * @param string $name
     * @param array  $options
     * type (required)
     * placeholder
     * before
     * after
     * 
     * @example [
     *  'placeholder' => 'First name',
     *  'before' => '',
     *  'after' => '',
     *  'label' => 'First name',
     *  'type' => 'text',
     * ]
     */
    protected function createTextField($name, $options)
    {
        $field = "";
        $attributes = "";
        if (isset($options['placeholder'])) $attributes.= " placeholder='{$options['placeholder']}'";
        if (isset($options['before'])) $field.= $options['before'];

        $field.= $this->createLabel($name, $options);

        $type = $options['type'] == 'password' ? 'password' : 'text';

        $field.= "<input type='{$type}' name='{$name}'{$attributes}>" . PHP_EOL;

        if (isset($options['after'])) $field.= $options['after'];

        return $field;
    }

    /**
     * @param string $name
     * @param array  $options
     * type (required)
     * placeholder
     * before
     * after
     * 
     * @example [
     *  'placeholder' => 'First name',
     *  'before' => '',
     *  'after' => '',
     *  'label' => 'First name',
     *  'options' => [
     *    'key' => 'value'
     *  ],
     *  'type' => 'text',
     * ]
     */
    protected function createSelectField($name, $options)
    {
        $field = "";
        $attributes = "";
        if (isset($options['placeholder'])) $attributes.= " placeholder='{$options['placeholder']}'";
        if (isset($options['before'])) $field.= $options['before'];

        $field.= $this->createLabel($name, $options);

        $field.= "<select name='{$name}'{$attributes}>";

        foreach ($options['options'] as $value => $text) {
            $field.= "<option value='{$value}'>{$text}</option>" . PHP_EOL;
        }

        $field.= "</select>";

        if (isset($options['after'])) $field.= $options['after'];

        return $field;
    }

    /**
     * @param string $name
     * @param array  $options
     * type (required)
     * before
     * after
     * options
     */
    protected function createRadios($name, $options)
    {
        $field = "";

        foreach ($options['options'] as $option) {
            $field.= '<input type="radio">';
            $field.= $this->createLabel($name, [
                'label' => $option
            ]);
        }

        return $field;
    }

    /**
     * 
     */
    protected function createCheckbox()
    {
        $field = "";
        $field.= $this->createLabel();
        $field.= '<input type="checkbox">';
        return $field;
    }

    protected function createLabel($name, $options)
    {
        if ($options['label']) {
            $for = isset($options['id']) ? $options['id'] : $name;
            return sprintf("<label for='%s'>%s</label>", $for, $options['label']);
        }
    }
}