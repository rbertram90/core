<?php
	namespace rbwebdesigns\core;
?>
<!DOCTYPE html>
<html>
<head>
	<title>RBwebdesigns core Form Tools Tester</title>
</head>
<body>
<a href="index.php">Menu</a>
<?php
	$root = __DIR__ . "/../src/";

	include_once $root . 'HTMLFormTools.php';
	include_once $root . 'appsecurity.php';	
	
	$sec = new AppSecurity();
	$html = new HTMLFormTools($sec);

	$data = array(
		'name' => 'Bill',
		'surname' => 'Gates',
		'username' => 'billgates01',
		'description' => 'Hi i\'m billy the programmer',
		'icon' => 'test01.png'
	);
	
	echo $html->generateFromJSON(file_get_contents("data/form_1.json"), $data);
	
	echo "<hr>";

	echo $html->createTextField("fld_test", "Question 1", "", array(
		'helptext' => 'Further " information',
		'placeholder'=> 'Hello World'
	));

	echo "<hr>";
	
	echo $html->createDropdown('fld_test', 'Question 2', array(
		'zero' => 'zero',
		'one' => 'one',
		'two' => 'two',
		'three' => 'three'
	), 'one');

	echo "<hr>";
	
	echo $html->createDropdownFromArray(array(
		'name' => 'fld_test',
		'caption' => 'Test',
		'values' => array(
			'0' => 'a',
			'1' => 'b',
			'2' => 'c',
			'3' => 'd',
			'4' => 'e'
		),
		'default' => 'None'
	));

	echo "<hr>";
	
	echo $html->createYearSelector();
	