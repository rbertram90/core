<?php
	use rbwebdesigns\core\Form\Form;

	$root = __DIR__ . "/../src";

	require_once $root . '/HTMLElementTrait.php';
	require_once $root . '/form/Form.php';
	require_once $root . '/form/InvalidFieldDefinitionException.php';
	require_once $root . '/form/fields/FormFieldInterface.php';
	require_once $root . '/form/fields/FormField.php';
	require_once $root . '/form/fields/TextField.php';
	require_once $root . '/form/fields/LongTextField.php';
	require_once $root . '/form/fields/DateField.php';
	require_once $root . '/form/fields/TimeField.php';
	require_once $root . '/form/fields/DateTimeField.php';
	require_once $root . '/form/fields/DropDownField.php';
	require_once $root . '/form/fields/CheckboxField.php';
	require_once $root . '/form/fields/HiddenField.php';
	require_once $root . '/form/fields/RadiosField.php';
	require_once $root . '/form/fields/UploadField.php';
	require_once $root . '/form/fields/RangeField.php';
	require_once $root . '/form/fields/ColourField.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>RBwebdesigns core Form Tester</title>
	<style>
		body { font-family: sans-serif; font-size: 14px; background-color:#aaa;}
		.field { padding: 10px; border: 1px solid #666; margin-bottom: 20px; display: flex; justify-content: space-between; background-color:#eee;}
		.hidden { display: none;}
	</style>
</head>
<body>
	<h1>Form Field examples</h1>
<?php
	class TestForm extends Form
	{
		public function saveData() {}
	}

	$form = new TestForm();
	
	$form->addField('name', [
		'label' => 'Text'
	]);

	$form->addField('age', [
		'type' => 'number',
		'label' => 'Number'
	]);

	$form->addField('email', [
		'type' => 'email',
		'label' => 'Email',
		'attributes' => [
			'placeholder' => 'someone@example.com'
		],
	]);

	$form->addField('phone', [
		'type' => 'tel',
		'label' => 'Telephone'
	]);

	$form->addField('password', [
		'type' => 'password',
		'label' => 'Password'
	]);

	$form->addField('memo', [
		'type' => 'longtext',
		'label' => 'Long text'
	]);

	$form->addField('date', [
		'type' => 'date',
		'label' => 'Date'
	]);

	$form->addField('time', [
		'type' => 'time',
		'label' => 'Time',
	]);

	$form->addField('datetime', [
		'type' => 'datetime',
		'label' => 'Date + time'
	]);

	$form->addField('options', [
		'type' => 'dropdown',
		'label' => 'Dropdown',
		'options' => [
			'one' => 'Option 1',
			'two' => 'Option 2',
			'three' => 'Option 3',
		],
	]);

	$form->addField('checkbox', [
		'type' => 'checkbox',
		'label' => 'Checkbox',
		'checked' => true,
	]);

	$form->addField('radios', [
		'type' => 'radios',
		'label' => 'Radios',
		'options' => [
			'first' => 'One',
			'second' => 'Two',
			'third' => 'Three',
		]
	]);

	$form->addField('upload', [
		'type' => 'upload',
		'label' => 'Upload',
	]);

	$form->addField('colour', [
		'type' => 'colour',
		'label' => 'Colour',
	]);

	$form->addField('range', [
		'type' => 'range',
		'label' => 'Range',
	]);

	$form->addField('system_id', [
		'type' => 'hidden',
	]);

	$form->addAction([
		'label' => 'Submit'
	]);

	$form->output(true);
?>
<?php

	$form = new TestForm();

	$form->addField('choices', [
		'label' => 'Show or hide the field below',
		'type' => 'dropdown',
		'options' => [
			'' => '',
			'show' => 'Show',
			'hide' => 'Hide',
		],
	]);

	$form->addField('conditional', [
		'label' => 'My conditionally visible field',
		'conditions' => [
			'choices' => 'show',
		]
	]);

	$form->output(true);
?>
</body>
</html>