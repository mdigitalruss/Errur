<?
/*
 * Errur
 * Example to demonstrate how to use Errur
 */

// Require the lib
require ("/home/www-data/lib/errur/errur.php");

// Make a template
$template = file_get_contents("example_error.html");

// Init the class (add your api key, too)
Errur::init($template, 'YOUR_API_KEY_GOES_HERE', false);

// Now cause an error
$errorVar = 1 / 0;
// That will cause a division by zero error
?>
<!doctype html>
<html>
	<head>
		<title>Super Example</title>
	</head>
	<body>
		<h1>Hello</h1>
		<p>
			This is my awesome website!
		</p>
	</body>
</html>