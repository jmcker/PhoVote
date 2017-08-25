<?php
    header('Content-type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past    

    include("conf.inc.php"); // Includes the db and form info.
    session_start();

    if (isset($_GET['query']))
        $query = $_GET['query'];
    else
        $query = "";

    
    $sort = $_SESSION['sortAssign'];
    $_SESSION['returnUrl'] = "search-results.php?query=$query";
    $_SESSION['searchSQL'] = " WHERE (title LIKE '%" . $query . "%' OR caption LIKE '%" . $query . "%')"; // Constraines results when entering rename-multiple-select.
    $searchSQL = $_SESSION['searchSQL'];

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Search Results - Example Vote Site</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="apple-touch-icon" sizes="57x57" href="/images/favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/images/favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/images/favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/images/favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/images/favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/images/favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/images/favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/images/favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/images/favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="images/favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="images/favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
    </head>
    <body>
    
        <!--Header-->
            <span class="textbg nohref">
                <a href="index.php"><u><b>Search Results</b></u></a><br><br>
            </span>

            <!--Menus-->
                <div style="display: inline-block; position: absolute; right: 40px;">
                   
                    <!--Search-->
                        <form method="get" name="search" action="search-results.php">
                            <input type="text" name="query" value="<?php echo $query; ?>"/>
                            <button type="submit" value="Search">Search</button>
                        </form><br>
                    
                    <!--Sort Order-->
                        <u>Sort Order</u><br><br>
                        <form method="get" name="sort" action="sort.php">
                            <select name="sortselect" id="sortselect">
                                <option value="chrono asc">Chronological</option>
                                <option value="votes desc">Votes (Highest to Lowest)</option>
                                <option value="votes asc">Votes (Lowest to Highest)</option>
                            </select><br>
                            <input type="text" name="returnUrl" value="<?php echo $_SESSION['returnUrl']; ?>" class="hidden"/>
                            <button type="submit" value="Submit">Sort</button>
                        </form><br>
    
                    <!--Rename Multiple Photos link-->
                    <a href="rename-multiple-select.php">Edit multiple titles</a>  -  

                    <!--Reorder Photos Link-->
                    <a href="reorder-select.php">Reorder photos</a><br>

                </div>
                <br><br><br><br><br><br><br><br><br><br>

                <span class="textsm"></span>
                <p class="desc"><span class="textreg">Click a picture to see a larger view.</span><br></p>
                <hr size="1">

<?php
    // JavaScript updates the select display in the menu
    // Default is chrononoligical order

    if ($_SESSION['sortAssign'] == "votes desc") {
        echo "<script>document.getElementById(\"sortselect\").value = \"votes desc\";</script>\n\n";
    }
    else if ($_SESSION['sortAssign'] == "votes asc") {
        echo "<script>document.getElementById(\"sortselect\").value = \"votes asc\";</script>\n\n";
    }
    else {
        $_SESSION['sortAssign'] = "chrono asc"; // Default
        echo "<script>document.getElementById(\"sortselect\").value = \"chrono asc\";</script>\n\n";
    }



    // SQL query for photos constrained by search
    $result = mysqli_query($db_connect, "SELECT * FROM `examplephotos`$searchSQL ORDER BY $sort") or die (mysqli_error()); // no space before $searchSQL to prevent double space if null
    $numOfRows = mysqli_num_rows($result);

    if ($numOfRows == 0)
    {   
        echo "<br>No results found.";
    }
    else
    {
        // Loop through all recieved SQL data and create the rest of the page
        for ($i = 0; $i < $numOfRows; $i++) 
        { 
            $row = mysqli_fetch_array($result);
            $name = $row['name'];
            $title = $row['title'];
            $id = $row['id'];
            $width = $row['width'];
            $height = $row['height'];
    
            echo "<div class=\"photo\">\n<a name=\"" . $name . "\">";
            echo "<a href=\"imageview.php?img=" . $name . "\"><img align=\"center\" width=\"" . $width . "\" height=\"" . $height . "\" src=\"images/thumbnails/" . $name . "\" title=\"" . $title . "\" border=\"0\"></a>";
            echo "\n<form method=\"get\" action=\"vote-submit.php\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">\n";
            echo "<input type=\"hidden\" name=\"name\" value=\"" . $name . "\"><br>\n";
            echo "<center><button type=\"submit\" value=\"Submit\">Vote [" . $row['votes'] . "]</button></center>\n";
            echo "</form><br>\n";
            echo "</div>\n\n";
        }
    }
?>


    </body>
</html>

<?php 
    mysqli_close($db_connect); // Closes the connection.
?>