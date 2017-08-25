<?php
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past 

    include("conf.inc.php"); // Includes the db and form info.

    $id = $_GET['id'];
    $name = $_GET['name'];

    if (!empty($_GET['newTitle'])) 
    {
        $title = "'" . addslashes($_GET['newTitle']) . "'"; // Added single quotes.
        mysqli_query($db_connect, "UPDATE `examplephotos` SET title = $title WHERE id = $id") or die (mysqli_error());
    }

    if (!empty($_GET['newCaption'])) 
    {
        $caption = "'" . addslashes($_GET['newCaption']) . "'"; // Added single quotes.
        mysqli_query($db_connect, "UPDATE `examplephotos` SET caption = $caption WHERE id = $id") or die (mysqli_error());
    }

    mysqli_close($db_connect); // Closes the connection.

    header("Location: imageview.php?img=$name"); // Return to image page.
?>

<!DOCTYPE html>
<html>
    <body>
        If you are not redirected, click <a href="imageview.php?img=<?php echo $name?>">here</a>.
    </body>
</html>