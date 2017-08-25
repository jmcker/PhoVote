<?php
header('Content-type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
header('Pragma: no-cache'); // HTTP 1.0. 
header('Expires: 0'); // Proxies.
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past 

session_start();

if (in_array($_GET['sortselect'], array("chrono asc", "votes desc", "votes asc")))
    $_SESSION["sortAssign"] = $_GET['sortselect']; // Gets value of sort select box in header of page

$returnUrl = $_GET['returnUrl'];

header("Location: $returnUrl"); // Return to page user came from
?>

<!doctype html>
<html>
<body>
If you are not redirected, click <a href="<?php echo $returnUrl?>">here</a>.
</body>
</html>