<?php
    header('Content-type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past 

    include("conf.inc.php"); // Includes the db and form info.
    session_start();

    $img = "'" . $_GET['img'] . "'"; // Form result from index or search-results


    // Query for current photo's info
    $result = mysqli_fetch_array(mysqli_query($db_connect, "SELECT * FROM `examplephotos` WHERE name = $img")) or die (mysqli_error());

    $id = $result['id'];
    $name = $result['name'];
    $title = $result['title'];
    $votes = $result['votes'];
    $caption = $result['caption'];



    $sort = $_SESSION['sortAssign'];
    $searchSQL = $_SESSION['searchSQL'];
    $returnUrl = $_SESSION['returnUrl'];
    if (strpos($returnUrl, "index.php") == 0 and strpos($returnUrl, "#") === FALSE)
    {
        $returnUrl = $returnUrl . "#" . $name;
        $_SESSION['returnUrl'] = $returnUrl;
    }
    else
    {
        $returnUrl = substr($returnUrl, 0, strpos($returnUrl, "#") + 1) . $name; // Strip last #photoname and replace with new.
        $_SESSION['returnUrl'] = $returnUrl;
    }




    $imagesArray = array();
    $imagesArray[0] = 0; // Create array and waste position 0.
    $result = mysqli_query($db_connect, "SELECT id FROM `examplephotos`$searchSQL ORDER BY $sort") or die (mysqli_error());
    while ($row = mysqli_fetch_array($result))
    {
        $imagesArray[] = $row['id']; // Creates array of all images from SQL query.
    }

    $pos = array_search($id, $imagesArray); // Find the position of the current photo in the array of limited results.
    $prevpos = $pos - 1;
    $nextpos = $pos + 1;
    $firstpos = 1;
    $lastpos = count($imagesArray) - 1;
    
    // Assignments for header links to first, previous, next, and last photos.
    $first = mysqli_fetch_array(mysqli_query($db_connect, "SELECT name FROM `examplephotos` WHERE id = $imagesArray[$firstpos]")) or die (mysqli_error()); // Never changes
    $last = mysqli_fetch_array(mysqli_query($db_connect, "SELECT name FROM `examplephotos` WHERE id = $imagesArray[$lastpos]")) or die (mysqli_error()); // Never changes
    if ($pos != $firstpos) {
        $previous = mysqli_fetch_array(mysqli_query($db_connect, "SELECT name FROM `examplephotos` WHERE id = $imagesArray[$prevpos]")) or die (mysqli_error());
    } else {    
        $previous = array($last[0]); // Link in menu uses $previous[0] because SQL result from above is usually an array; force array to mimic.
    }
    if ($pos != $lastpos) {
        $next = mysqli_fetch_array(mysqli_query($db_connect, "SELECT name FROM `examplephotos` WHERE id = $imagesArray[$nextpos]")) or die (mysqli_error());
    } else {
        $next = array($first[0]);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <script type="text/javascript">
            function editTitle() {
                var text = document.getElementById('titleText');
                text.style.display = "inline"; // Show title input.
                document.getElementById('titleSubmit').style.display = "inline"; // Show submit button.
                document.getElementById('titleDiv').style.display = "none"; // Hide display div.
        
                closeCaption();
                focusCursor(text);
                keyListeners(true, false) // (title, caption)
            };
            function editCaption() {
                var text = document.getElementById('captionText');
                text.style.display = "inline"; // Show caption input.
                document.getElementById('captionSubmit').style.display = "inline"; // Show submit button.
                document.getElementById('captionDiv').style.display = "none"; // Hide display div.
        
                closeTitle();
                focusCursor(text);
                keyListeners(false, true); // Starts listening for control enter combo.
            };
            function closeTitle() {
                // Hides title box when caption is clicked.
                document.getElementById('titleText').style.display = "none";
                document.getElementById('titleSubmit').style.display = "none";
                document.getElementById('titleDiv').style.display = "";
                keyListeners(false, false); // Close both.
            };
            function closeCaption() {
                // Hides caption box when title is clicked.
                document.getElementById('captionText').style.display = "none";
                document.getElementById('captionSubmit').style.display = "none";
                document.getElementById('captionDiv').style.display = "";
                keyListeners(false, false); // Close both.
            };
            function keyListeners(titleOpen, captionOpen) {
                document.onkeydown = function (event) {
                    event = event || window.event;
                    // Listen for t to open edit title.
                    if (!titleOpen && !captionOpen && event.keyCode == 84) {
                        editTitle();
                        event.preventDefault();
                    }
                        
                    // Listen for c to open edit caption.
                    if (!titleOpen && !captionOpen && event.keyCode == 67) {
                        editCaption();
                        event.preventDefault();
                    }
                    // Listen for escape.
                    if (event.keyCode == 27) {
                        closeTitle();
                        closeCaption();
                    }
                    // Listen for control enter only when caption text area is focused.
                    if (captionOpen) {
                        if ((event.keyCode == 10 || event.keyCode == 13) && event.ctrlKey) {
                            document.getElementById('captionForm').submit();
                        }
                    }
                };
            };
            function focusCursor(text) {
                // Workaround to focus and put cursor at end of input
                text.focus();
                var tmp = text.value; // Stores value already in input
                text.value = ""; // Clears input
                text.value = tmp; // Resets input to previous value, moving cursor to end
            };
        
            keyListeners(false, false); // Start listener for escape key; neither box is open
        
        </script>
    </head>
    <body>

        <!--Header-->
            <div style="max-width: 450px; padding-bottom: 10px;">

                <!--Hidden Title Form-->            
                    <form id="titleForm" action="update-info.php" method="get">
                        <div id="titleDiv" class="title textbg" onclick="editTitle()"><?php echo $title?></div>
                        <input type="text" name="newTitle" value="<?php echo $title?>" id="titleText" class="hidden"/>
                        <input type="text" name="id" value="<?php echo $id?>" class="hidden"/>
                        <input type="text" name="name" value="<?php echo $name?>" class="hidden"/>
                        <input type="submit" value="Save" id="titleSubmit" class="hidden"/>
                    </form><br>
    
                <!--Menu Bar-->
                    <span class="textreg">
                        <a href="imageview.php?img=<?php echo $first[0]?>">First</a> |
                        <a href="imageview.php?img=<?php echo $previous[0]?>">Previous Picture</a> |
                        <a href="imageview.php?img=<?php echo $next[0]?>">Next Picture</a> |
                        <a href="imageview.php?img=<?php echo $last[0]?>">Last</a> | <a href="<?php echo $returnUrl?>">Back</a><br>
                    </span><br>
            
                <span class="textreg">Click to edit the caption or title (shortcuts c or t).</span>

                <!--Vote Submit Form-->
                    <div style="display: inline-block; float: right;">
                        <form method="get" action="vote-submit.php">
                            <input type="hidden" name="id" value="<?php echo $id?>"/>
                            <input type="hidden" name="name" value="<?php echo $name?>" />
                            <button type="submit" value="Submit" id="voteButton">Vote [<?php echo $votes?>]</button>
                        </form>
                    </div>
            
            </div>
        
            <hr size="1"><br>
            
            <!--Main Display-->
                <table>
                    <tr>
                        <td>
                            <!--Hidden Caption Form-->
                                <form id="captionForm" name="captionForm" action="update-info.php" method="get">
                                    <div id="captionDiv" class="caption" onclick="editCaption()"><?php echo $caption?></div>
                                    <textarea form="captionForm" style="width: 500px; height: auto;" name="newCaption" id="captionText" class="hidden"><?php if ($caption != 'Click to add caption...') {echo $caption;}?></textarea>
                                    <input type="text" name="id" value="<?php echo $id?>" class="hidden"/>
                                    <input type="text" name="name" value="<?php echo $name?>" class="hidden"/>
                                    <input type="submit" value="Save" id="captionSubmit" class="hidden"/>
                                </form>
                            
                            <!--Image-->
                                <a href="<?php echo $returnUrl?>"><img src="images/<?php echo $name?>" title="<?php echo $title?>" alt="<?php echo $title?>" class="fullimage"></a>

                        </td>
                    </tr>
                </table>

    </body>
</html>

<?php 
    mysqli_close($db_connect); // Close the connection.
?>