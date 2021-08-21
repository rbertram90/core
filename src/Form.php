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
    public $method = 'POST';
    /**
     * @var string  Encoding type attribute of the form
     */
    public $encodingType = 'application/x-www-form-urlencoded';
    /**
     * @var string  Message from failed validation
     */
    public $error;
    /**
     * @var bool  Result of running the validation method
     */
    public $isValid;
    /**
     * @var bool  Flag to show the error message if there is an error
     */
    public $showErrors = true;
    /**
     * @var array  Other attributes to add to the form tag
     */
    protected $attributes;
    /**
     * @var array  Form field definition
     */
    protected $fields;
    /**
     * @var array  Form submit / reset / cancel buttons
     */
    protected $actions;

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
    abstract public function validate();

    /**
     * We cannot provide implementation of the submit
     * method here as we don't know how the data is
     * to be saved.
     */
    abstract public function submit();

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
     * @return \rbwebdesigns\core\Form  this
     */
    public function addFields($fields)
    {
        foreach ($fields as $fieldName => $fieldOptions) {
            $this->addField($fieldName, $fieldOptions);
        }
        return $this;
    }

    /**
     * Copy the field values from the request to the fields array
     * 
     * @param \rbwebdesigns\core\Request  $request
     * 
     * @return \rbwebdesigns\core\Form  this
     */
    public function setFieldValues($values)
    {
        if (gettype($values) == 'array') {
            foreach ($this->fields as $key => $definition) {
                if ($values[$key]) {
                    $this->fields[$key]['value'] = $values[$key];
                }
            }
        }
        elseif (is_object($values) && get_class($values) == 'rbwebdesigns\core\Request') {
            foreach ($this->fields as $key => $definition) {
                if ($values->get($key, false)) {
                    $this->fields[$key]['value'] = $values->get($key, false);
                }
            }
        }
        
        return $this;
    }

    /**
     * Output or return the form contents as HTML
     * 
     * @param bool $print True if echoing the form HTML to the response
     * @return null|string
     */
    public function output($print = false)
    {
        $fieldOutput = [];

        foreach ($this->fields as $name => $field) {
            $fieldHTML = "";
            switch ($field['type']) {
                case 'upload':
                    $fieldHTML = $this->createFileUploadField($name, $field);
                    break;
                case 'date':
                    break;
                case 'markup':
                    $fieldHTML = $this->createMarkup($name, $field);
                    break;
                case 'checkbox':
                    $fieldHTML = $this->createCheckbox($name, $field);
                    break;
                case 'checkboxes':
                    break;
                case 'radiobuttons':
                    $fieldHTML = $this->createRadios($name, $field);
                    break;
                case 'year':
                    $start = isset($field['start']) ? $field['start'] : 1901;
                    $end = isset($field['end']) ? $field['end'] : date('Y');
                    $field['options'] = range($start, $end);
                case 'dropdown':
                    $fieldHTML = $this->createSelectField($name, $field);
                    break;
                case 'memo':
                case 'longtext':
                    $fieldHTML = $this->createTextarea($name, $field);
                    break;
                case 'text':
                default:
                    $fieldHTML = $this->createTextField($name, $field);
                    break;
            }
            if (isset($field['conditions'])) {
                $this->addConditionalVisibility($fieldHTML, $field);
            }
            $fieldOutput[] = $fieldHTML;
        }

        $attributes = "";
        if (strlen($this->action)) $attributes.= " action='{$this->action}'";
        if (strlen($this->method)) $attributes.= " method='{$this->method}'";
        if (strlen($this->encodingType)) $attributes.= " enctype='{$this->encodingType}'";

        foreach ($this->attributes as $key => $value) {
            $attributes.= sprintf(" %s='%s'", $key, $value);
        }

        $output = "<form{$attributes}>" . PHP_EOL;

        if ($this->showErrors && $this->error) {
            $output.= '<p class="message error">'. $this->error .'</p>';
        }

        $output.= implode(PHP_EOL, $fieldOutput);

        foreach ($this->actions as $action) {
            $attributes = $this->createAttributes($action);
            if (array_key_exists('type', $action)) $attributes .= " type='{$action['type']}'";
            $output.= "<button{$attributes}>{$action['label']}</button>";
        }

        $output.= "</form>";

        if ($print) echo $output;
        else return $output;
    }

    /**
     * Add a button to actions group
     * 
     * [
     *   'label' => 'Submit form'
     *   'type' => 'submit',
     *   'attributes' => [
     *      'data-foo' => 'bar',
     *      'class' => ''
     *   ]
     * ]
     * 
     * @param array $action
     */
    public function addAction($action)
    {
        $this->actions[] = $action;
    }

    /**
     * @param string $name
     * @param array  $options
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
        $attributes = $this->createAttributes($options);
        if (isset($options['placeholder'])) $attributes.= " placeholder='{$options['placeholder']}'";
        if (isset($options['required']) && $options['required']) $attributes.= ' required';

        $field.= $this->createLabel($name, $options);

        $value = isset($options['value']) ? $options['value'] : '';
        $validTypes = ['password', 'text', 'number', 'email', 'tel'];
        $type = in_array($options['type'], $validTypes) ? $options['type'] : 'text';
        $field.= "<input type='{$type}' value='{$value}' name='{$name}'{$attributes}>" . PHP_EOL;

        $this->createFieldWrapper($options, $field);
        return $field;
    }

    /**
     * @param string $name
     * @param array  $options
     * 
     * @example [
     *  'placeholder' => 'First name',
     *  'before' => '',
     *  'after' => '',
     *  'label' => 'First name',
     *  'type' => 'text',
     * ]
     */
    protected function createTextarea($name, $options)
    {
        $field = "";
        $value = isset($options['value']) ? $options['value'] : '';
        $attributes = $this->createAttributes($options);
        if (isset($options['placeholder'])) $attributes.= " placeholder='{$options['placeholder']}'";
        if (isset($options['required']) && $options['required']) $attributes.= ' required';

        $field.= $this->createLabel($name, $options);
        $field.= "<textarea name='{$name}'{$attributes}>{$value}</textarea>" . PHP_EOL;
        $this->createFieldWrapper($options, $field);

        return $field;
    }

    /**
     * @param string $name
     * @param array  $options
     * 
     * @example [
     *  'markup' => '<p>Hello!</p>',
     *  'type' => 'markup',
     * ]
     */
    protected function createMarkup($name, $options)
    {
        return $options['markup'];
    }

    /**
     * @param string $name
     * @param array  $options
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
        $attributes = $this->createAttributes($options);
        if (isset($options['placeholder'])) $attributes.= " placeholder='{$options['placeholder']}'";
        if (isset($options['before'])) $field.= $options['before'];
        if (isset($options['required']) && $options['required']) $attributes.= ' required';

        $field.= $this->createLabel($name, $options);

        $field.= "<select name='{$name}'{$attributes}>";
        foreach ($options['options'] as $value => $text) {
            $field.= "<option value='{$value}'>{$text}</option>" . PHP_EOL;
        }
        $field.= "</select>";

        $this->createFieldWrapper($options, $field);
        return $field;
    }

    /**
     * Generate HTML for a group of radio buttons
     * 
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
        $attributes = $this->createAttributes($options);

        foreach ($options['options'] as $option) {
            $field.= '<input type="radio">';
            $field.= $this->createLabel($name, [
                'label' => $option
            ]);
        }
        $this->createFieldWrapper($options, $field);

        return $field;
    }

    /**
     * Generates HTML for a single checkbox
     * 
     * @param string $name
     * @param array  $options
     */
    protected function createCheckbox($name, $options)
    {
        $field = "";
        $field.= $this->createLabel($name, $options);
        $attributes = $this->createAttributes($options);
        $field.= "<input type='checkbox' {$attributes}>";
        $this->createFieldWrapper($options, $field);
        return $field;
    }

    /**
     * Generates HTML for a file upload field
     * Valid option keys: before, after, placeholder, id, label
     * 
     * @param string $name
     * @param array  $options
     */
    protected function createFileUploadField($name, $options)
    {
        $field = "";
        $attributes = $this->createAttributes($options);
        if (isset($options['placeholder'])) $attributes.= " placeholder='{$options['placeholder']}'";
        if (isset($options['id'])) $attributes.= " id='{$options['id']}'";
        if (isset($options['required']) && $options['required']) $attributes.= ' required';

        $field.= $this->createLabel($name, $options);
        $field.= '<input type="file" name="' . $name . '"' . $attributes . '>';

        $this->createFieldWrapper($options, $field);

        $this->encodingType = 'multipart/form-data';
        return $field;
    }

    /**
     * Generates HTML for a label
     * 
     * @param string $name
     * @param array  $options
     */
    protected function createLabel($name, $options)
    {
        if ($options['label']) {
            $for = isset($options['id']) ? $options['id'] : $name;
            return sprintf("<label for='%s'>%s</label>", $for, $options['label']);
        }
    }

    /**
     * Create an attribute string from array
     * 
     * Turns:
     * ['attributes' => ['class' => 'something', 'id' => 'something_else']]
     * Into:
     *  class="something" id="something_else"
     * 
     * @param array $field
     * @return string
     */
    protected function createAttributes($field)
    {
        $attributes = "";
        if (array_key_exists('attributes', $field)) {
            foreach ($field['attributes'] as $key => $value) {
                $attributes.= sprintf(" %s='%s'", $key, $value);
            }
        }
        return $attributes;
    }

    /**
     * Create the field container
     * 
     * @param array $options
     */
    protected function createFieldWrapper($options, &$output)
    {
        $before = isset($options['before']) ? $options['before'] : '<div class="field">';
        $after = isset($options['after']) ? $options['after'] : '</div>';
        $output = $before . $output . $after;
    }

    /**
     * Create a field wrapper with JS logic to hide and show the field dynamically
     */
    protected function addConditionalVisibility(&$fieldHTML, $field) {
        $script = '';
        $uniqueFnId = uniqid();

        if (isset($field['conditions']['show'])) {
            $conditions = [];
            
            foreach ($field['conditions']['show'] as $fieldId => $targetValue) {
                $i = 0;
                $conditions[] = "document.getElementById('{$fieldId}').value == '{$targetValue}'";
                $listeners[] = "function showField{$uniqueFnId}_{$i}() {
                    document.getElementById('cond{$uniqueFnId}').style.display = showField{$uniqueFnId}() ? 'block' : 'none';
                }
                document.getElementById('{$fieldId}').addEventListener('change', showField{$uniqueFnId}_{$i});
                showField{$uniqueFnId}_{$i}();";
                $i++;
            }
            $script .= "function showField{$uniqueFnId}() { return " . implode(' && ', $conditions) . '; }';
            $script .= implode(PHP_EOL, $listeners);
        }

        $script = '<script>' . $script . '</script>';
        $fieldHTML = "<div id='cond{$uniqueFnId}'>{$fieldHTML}</div>{$script}";
    }

}
