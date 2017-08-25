<?php
    header('Content-type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past   

    include("conf.inc.php"); // Include the db and form info.
    session_start();

    $returnUrl = $_SESSION["returnUrl"];

    $errors = "";
    $idsToReorder = $_SESSION['idsToReorder']; // Array of ids
    $chronosToReorder = $_SESSION['chronosToReorder']; // Array of chronos
    //unset($_SESSION['photosToReorder']);

    if (isset($_GET['locater']))
        $newPos = $_GET['locater']; // Chrono number
    else
        $errors = $errors . "Please select the image that you wish to place the photos before/after...\n";

    $insertAfter = $_GET['insertAfter'];
    if ($insertAfter == "0")
        $newPos = $newPos - 1;

    // Collect order of positions relative to the locater so that newPos can be adjusted
    

    if (count($chronosToReorder) == 0)
    {
        $errors = $errors . "Please select one or more pictures to reorder...\n";
    }
?>

<!DOCTYPE html>
<html>
    <body>
<?php
    $photosBeforeNewPos = 0;
    if (!empty($errors))
    {
        echo "<script>alert(\"$errors\");</script>";
        echo "<script>history.back();</script>";
    }
    else
    {
        // First for loop iterates through photos and updates the chrono numbers of the photos that are not being moved
        for ($i = 0; $i < count($idsToReorder); $i++)
        {
            // Updates chrono number of photos before the new position
            if ($chronosToReorder[$i] < $newPos)
            {
                if (isset($chronosToReorder[$i + 1]))
                    $stop = min($chronosToReorder[$i + 1], $newPos); // Prevents going past new position
                else
                    $stop = $newPos;

                mysqli_query($db_connect, "UPDATE `examplephotos` SET chrono=chrono - ($i + 1) WHERE chrono > $chronosToReorder[$i] AND chrono <= $stop") or die(mysqli_error());
                $photosBeforeNewPos++; // Keeps track of photos that come before the new position for use below
            }

            // Updates chrono number of photos after the new position
            if ($chronosToReorder[$i] > $newPos)
            {
                $start = $newPos; // Always start at the new position for each iteration.
                mysqli_query($db_connect, "UPDATE `examplephotos` SET chrono=chrono + (1) WHERE chrono > $start AND chrono <= $chronosToReorder[$i]") or die(mysqli_error());
            }
        }

        // Updates the chrono numbers of the photos being moved
        // Accounts for shift due to photos before the new position
        for ($i = 0; $i < count($idsToReorder); $i++)
            mysqli_query($db_connect, "UPDATE `examplephotos` SET chrono = $newPos + $i - $photosBeforeNewPos + 1 WHERE id = $idsToReorder[$i]") or die(mysqli_error());
        
        // Success message and redirect
        echo "<script>alert(\"Success!\");</script>";
        echo "<script>window.location = \"$returnUrl\"</script>";
    }
?>

        If you are not redirected, please click <a href="<?php echo $returnUrl?>">here</a>.
    </body>
</html>

<?php 
    mysqli_close($db_connect); // Closes the connection.
?>