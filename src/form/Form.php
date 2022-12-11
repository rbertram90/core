<?php

namespace rbwebdesigns\core\form;

use rbwebdesigns\core\form\fields\FormField;
use rbwebdesigns\core\form\fields\TextField;
use rbwebdesigns\core\traits\HTMLElement;

abstract class Form
{
    use HTMLElement;

    /** Form identifier - generated on construct so that values can be persisted */
    protected $id;

    /** Location of the form handler */
    public string $action = '';

    /** Request method attribute of the form */
    public string $method = 'POST';

    /** Encoding type attribute of the form */
    public string $encodingType = 'application/x-www-form-urlencoded';

    /** Message from a successful submission */
    public string $successMessage = '';

    public bool $showSuccessMessage = false;

    /** Result of running the validation method */
    public bool $isValid = true;

    /** Errors found during data validation. */
    protected array $validationErrors = [];

    /** Form field definition */
    protected array $fields = [];

    /** Form submit / reset / cancel buttons */
    protected array $actions = [];

    /**
     * Form constructor
     * 
     * @param string $key
     *   A string to represent the instance of the form on a page if there are
     *   multiple instances, this will be used to generate the form ID and
     *   identify the form instance when submitting data.
     */
    public function __construct($key = '')
    {
        $requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

        $id = filter_input($requestMethod === 'POST' ? INPUT_POST : INPUT_GET, 'form_id');

        // Currently ID does not include the URL path, could have data come
        // through from the same form on another page, as long as the form_id
        // is passed in the query string.
        $formId = __CLASS__;
        if ($key) {
            $formId .= '.' . $key;
        }

        // Identify if it's this form that is responsible for the POST.
        $active = false;

        if (is_string($id) && strlen($id) === 32) {
            if (md5($formId) === $id) {
                $active = true;
            }
        }

        $this->id = md5($formId);

        if ($requestMethod === 'POST' && $active) {
            $this->id = filter_input(INPUT_POST, 'form_id');

            if (method_exists(get_called_class(), 'preSubmit')) {
                static::{'preSubmit'}();
            }

            $this->populateValuesFromPost();

            $this->submit();
        }
        elseif ($active) {
            $this->populateValuesFromSession();

            if (filter_input(INPUT_GET, 'form_success')) {
                $this->showSuccessMessage = true;
            }
        }

        $this->setAttribute('id', 'form_' . $this->id);
        $this->setAttribute('name', 'form_' . $this->id);
    }

    /**
     * Get the form ID.
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Run validation based on defined rules.
     */
    public function validate()
    {
        foreach ($this->fields as $key => $options) {
            if (($options['required'] ?? false) && !$options['value']) {
                $this->validationErrors[$key] = 'Required field';
            }
        }

        return count($this->validationErrors) === 0;
    }

    public function submit()
    {
        if (!$this->validate()) {
            $this->saveToSession();
            $this->setRedirectBackToForm();
            exit;
        }

        $this->saveToSession();

        static::saveData();

        $this->removeFromSession();
    }

    public function successRedirect()
    {
        $queryString = $this->getQueryStringAsArray();

        header('Location: ' . $_SERVER['PHP_SELF'] . '?' . ($queryString ? $this->implodeQueryStringArray($queryString) . '&' : '') . 'form_id=' . $this->id . '&form_success=1');
    }

    /**
     * Get $_SERVER['QUERY_STRING'] as an array, without form related variables.
     */
    protected function getQueryStringAsArray($queryString = null)
    {
        parse_str($queryString ?: $_SERVER['QUERY_STRING'], $queryStringArray);

        unset($queryStringArray['form_id']);
        unset($queryStringArray['form_success']);
        unset($queryStringArray['form_error']);

        return $queryStringArray;
    }

    /**
     * Takes a key value pair array and turn it into "&" separated
     * string of key=value pairs
     */
    protected function implodeQueryStringArray($array)
    {
        $queryString = [];

        foreach ($array as $key => $value) {
            $queryString[] = "$key=$value";
        }

        return implode('&', $queryString);
    }

    /**
     * Default implementation of a redirect back to the form page,
     * called when server-side validation fails.
     */
    public function setRedirectBackToForm()
    {
        $queryString = $this->getQueryStringAsArray();

        if ($queryString) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $this->implodeQueryStringArray($queryString) . '&form_id=' . $this->id);
        }
        else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?form_id=' . $this->id);
        }
    }

    /**
     * This function is to be implemented on a form-by-form basis, this
     * is where the business logic for the submission of the form should
     * occur.
     */
    abstract protected function saveData();

    /**
     * Add a field to the form
     * 
     * @param string $name     Name of the form field
     * @param array  $options  Definition of the field
     * 
     * @return \rbwebdesigns\core\Form $this
     */
    public function addField($name, $options = [])
    {
        if (isset($this->fields[$name])) {
            trigger_error('Overwritten form field ' . $name, E_USER_NOTICE);
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
                if (isset($values[$key])) {
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
     * Helper function so that labels don't have to be added in fields definition
     * if they follow a common format.
     */
    public function guessLabels()
    {
        foreach ($this->fields as $key => $options) {
            if (!isset($options['label'])) {
                $this->fields[$key]['label'] = ucfirst(str_replace('_', ' ', $key));
            }
        }
    }

    public function populateValues($data)
    {
        foreach ($this->fields as $key => $options) {
            $type = $options['type'] ?? null;
            if (isset($data[$key]) && ($value = $data[$key])) {
                $this->fields[$key]['value'] = $value;
            }
        }
    }

    /**
     * Grab the data from $_POST and add to values array.
     */
    protected function populateValuesFromPost()
    {
        $data = $_POST;

        if (isset($_FILES)) {
            $data = [...$_POST, ...$_FILES];
        }

        return $this->populateValues($data);
    }

    /**
     * Grab the data from $_SESSION
     */
    protected function populateValuesFromSession()
    {
        if (!isset($_SESSION[$this->id])) {
            return;
        }

        foreach ($this->fields as $key => $options) {
            if (isset($_SESSION[$this->id]['values'][$key])) {
                $this->fields[$key]['value'] = $_SESSION[$this->id]['values'][$key];
            }
        }

        $this->validationErrors = $_SESSION[$this->id]['errors'];
    }

    /**
     * Returns all values for fields.
     */
    public function getValues()
    {
        $data = [];

        foreach ($this->fields as $key => $options) {
            if (!isset($options['value'])) {
                continue;
            }
            $data[$key] = $options['value'];
        }

        return $data;
    }

    /**
     * Save form values to the session incase validation fails.
     */
    protected function saveToSession()
    {
        $_SESSION[$this->id] = [
            'values' => $this->getValues(),
            'errors' => $this->validationErrors,
        ];
    }

    /**
     * Remove form values from the session.
     */
    protected function removeFromSession()
    {
        unset($_SESSION[$this->id]);
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

            $fieldClass = __NAMESPACE__ . '\\fields\\' . ($field['type'] ?? null) . 'Field';

            if (!class_exists($fieldClass)) {
                $fieldClass = TextField::class;
            }

            $value = $field['value'] ?? null;

            $attributes = $field['attributes'] ?? [];

            unset($field['value'], $field['attributes']);

            /** @var \rbwebdesigns\core\form\fields\FormField */
            $fieldObject = new $fieldClass($name, $field, $value);

            $fieldObject->setAttributes($attributes);

            if (isset($this->validationErrors[$name])) {
                $fieldObject->setErrors($this->validationErrors[$name]);
            }

            $fieldHTML = $fieldObject->render();
            
            if ($fieldObject->hasConditions()) {
                $this->outputConditions($fieldObject, $fieldHTML);
            }
            
            $fieldOutput[] = $fieldHTML;
        }

        $attributes = "";
        if (strlen($this->action)) $attributes.= " action='{$this->action}'";
        if (strlen($this->method)) $attributes.= " method='{$this->method}'";
        if (strlen($this->encodingType)) $attributes.= " enctype='{$this->encodingType}'";

        foreach ($this->attributes ?? [] as $key => $value) {
            $attributes.= sprintf(" %s='%s'", $key, $value);
        }

        $output = "<form{$attributes}>" . PHP_EOL;

        if ($this->showSuccessMessage && $this->successMessage) {
            $output.= $this->formatSuccessMessage();
        }

        $output.= implode(PHP_EOL, $fieldOutput);

        foreach ($this->actions ?? [] as $action) {
            $attributes = $this->outputAttributes($action);
            if (array_key_exists('type', $action)) $attributes .= " type='{$action['type']}'";
            $output.= "<button{$attributes}>{$action['label']}</button>";
        }

        $output .= "<input type='hidden' name='form_id' value='{$this->id}'>";

        $output.= "</form>";

        if ($print) echo $output;
        else return $output;
    }

    /**
     * Format the HTML that will be shown for the success message.
     */
    public function formatSuccessMessage()
    {
        return '<div class="message success">'. $this->successMessage .'</div>';
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

    public function outputConditions(FormField $field, &$output)
    {
        $checks = [];
        
        foreach ($field->getConditions() as $condition) {
            if ($condition['operator'] === '=') {
                $condition['operator'] = '=='; // treat everything as string?
            }

            $checks[] = "if (document.forms.form_{$this->id}.{$condition['field']}.value {$condition['operator']} \"{$condition['value']}\") show = false;";

            $listeners[] = "document.forms.form_{$this->id}.{$condition['field']}.addEventListener('change', update{$field->name}Visibility);";
        }

        // Create one function per field which checks all conditions.
        $script = "function update{$field->name}Visibility() {
            let show = true;

            ".implode(PHP_EOL, $checks)."

            if (show) {
                document.forms.form_{$this->id}.{$field->name}.parentElement.classList.add('hidden');
            }
            else {
                document.forms.form_{$this->id}.{$field->name}.parentElement.classList.remove('hidden');
            }
        }";

        // Add the on change listeners.
        $script .= implode(PHP_EOL, $listeners);

        // Run check on load.
        $script.= "update{$field->name}Visibility();";

        $output .= "<script>{$script}</script>";
    }
}
