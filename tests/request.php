<?php
namespace rbwebdesigns\core;
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>request - rbwebdesigns core tests</title>
</head>
<body>
<a href="index.php">Menu</a>
<?php
    $root = __DIR__ . "/../src/";

    include_once $root . 'request.php';
    
    echo "Creating request... ";

    $request = new Request();

    echo "Request Created<br>";

    echo "Request Method = " . $request->method() ."<br>";

    echo "Request isAjax? = ";
    var_dump($request->isAjax);
?>
</body>
</html>