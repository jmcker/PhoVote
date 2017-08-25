<!DOCTYPE html>
<html>
    <head>

<?php
    header('Content-type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past    

    include("conf.inc.php"); // Includes the db and form info.
    session_start();

    $id = $_GET['id'];
    $name = $_GET['name'];

    $returnUrl = $_SESSION['returnUrl'];
    $returnUrl = $_SESSION['returnUrl'];
    if (strpos($returnUrl, "#") === FALSE)
    {
        $returnUrl = $returnUrl . "#" . $name;
        $_SESSION['returnUrl'] = $returnUrl;
    }
    else
    {
        $returnUrl = substr($returnUrl, 0, count($returnUrl) - (count($returnUrl) - strpos($returnUrl, "#")) + 1) . $name; // Strip last #photoname and replace with new.
        $_SESSION['returnUrl'] = $returnUrl;
    }

    mysqli_query($db_connect, "UPDATE `examplephotos` SET votes = votes + 1 WHERE id = $id") or die (mysqli_error());

    echo "<script type=\"text/javascript\">window.location = \"$returnUrl\"</script>"; // Changing php header location moved too quickly. Updated vote total didn't show.
?>
    </head>

    <body>
        If you are not redirected, click <a href="index.php#<?php echo $name?>">here</a>.
    </body>
</html>

<?php
    mysqli_close($db_connect); // Closes the connection.
?>