<?php
    header('Content-type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past   

    include("conf.inc.php"); // Includes the db and form info.
    session_start();

    $returnUrl = $_SESSION["returnUrl"];

    $photosToRename = $_GET['photosToRename']; // Array of id's
    $title = "'" . addslashes($_GET['title']) . "'"; // add single quotes to user input

    foreach ($photosToRename as $photo) // add results from form to single string for SQL in() search
    {
        if (empty($inString))
            $inString = $photo;
        else
            $inString = $inString . ", " . $photo;
    }
?>

<!DOCTYPE html>
<html>
    <body>

<?php    
    if ($title == "''") // previously added single quotes around user entry; if title was left empty single qoutes will be the only thing stored
    {
        echo "<script type=\"text/javascript\">alert(\"Please enter a title...\");</script>";
        echo "<script type=\"text/javascript\">history.back();</script>";
    }
    else
    {
        mysqli_query($db_connect, "UPDATE `examplephotos` SET title = $title WHERE id in ($inString)") or die (mysqli_error());
        echo "<script type=\"text/javascript\">alert(\"Success!\");</script>";
        echo "<script type=\"text/javascript\">window.location = \"$returnUrl\"</script>";
    }
?>

        If you are not redirected, click <a href="<?php echo $returnUrl?>">here</a>.
    </body>
</html>

<?php 
    mysqli_close($db_connect); // Closes the connection.
?>