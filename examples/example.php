<?
/*
 * Errur
 * Example to demonstrate how to use Errur
 */

// Require the lib
require ("/home/www-data/lib/errur/errur.php");

// Make a template
$template = '<h1>Error!</h1>
						<p>Oh snap, we encountered an error:</p>
						<p>[ERROR_MSG]</p>
						<p>The full details have been logged in Airbrake</p>';

// Init the class (add your api key, too)
Errur::init($template, '<MY_API_KEY_GOES_HERE>');

// Now cause an error
$errorVar = 1 / 0;
// Will cause a division by zero error
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