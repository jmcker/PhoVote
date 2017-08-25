<?php
    header('Content-type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1. 
    header('Pragma: no-cache'); // HTTP 1.0. 
    header('Expires: 0'); // Proxies.
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past    

    include("conf.inc.php"); // Includes the db and form info.
    session_start();

    $sort = $_SESSION["sortAssign"]; //So I don't have to use quotes in the SQL query :p
    $searchSQL = $_SESSION["searchSQL"]; // Constrains results if coming from search page

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Example Vote Site</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="apple-touch-icon" sizes="57x57" href="/images/faviconapple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/images/faviconapple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/images/faviconapple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/images/faviconapple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/images/faviconapple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/images/faviconapple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/images/faviconapple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/images/faviconapple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/faviconapple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/images/faviconandroid-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/faviconfavicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/images/faviconfavicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/faviconfavicon-16x16.png">
        <link rel="manifest" href="images/faviconmanifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="images/faviconms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <script type="text/javascript">
            function switchCheckbox(id) {
                var box = document.getElementById(id); // Get checkbox
                box.click();
            }; 
            function cancel() {
                window.location.href = "javascript:history.back()";
            };
            function clearSelection() {
                var inputs = document.getElementsByTagName("input");
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].type == 'checkbox')
                        inputs[i].checked = false;
                }
            };
            function keyListeners() {
                document.onkeydown = function (event) {
                    event = event || window.event;
                    // Listen for escape
                    if (event.keyCode == 27) {
                        cancel();
                    }
                };
            };
            keyListeners(); // Start listener for escape key
        </script>
    </head>
    <body>

        <!--Page Wide Form-->
            <form method="get" name="rename" action="rename-multiple-submit.php">
                
                <!--Anchored Top Menu Bar-->
                    <div class="anchoredbox">
                        <input type="text" name="title" value="" autofocus="true" placeholder="Enter a new title..." />
                        <button type="submit" value="submit">Save</button>
                        <input type="button" value="Cancel" onclick="cancel();"/>
                        <input type="button" value="Clear Selection" onclick="clearSelection();"/><br>

                        <span class="textsm"></span>
                        <p class="desc"><p><span class="textreg">Click a photo to check it.</span><br></p></p>
                        <hr size="1">

                    </div><br><br><br><br><br><br>

                <!--Header-->
                    <span class="textbg nohref">
                        <a href="index.php"><u><b>Example Vote Site</b></u></a><br>
                    </span>
                <hr size="1">


<?php
    // SQL query for all photos
    $result = mysqli_query($db_connect, "SELECT * FROM `examplephotos`$searchSQL ORDER BY $sort") or die (mysqli_error()); // SQL query with restrictions if from search results and sorting
    $numOfRows = mysqli_num_rows($result);

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
        echo "<input type=\"checkbox\" id=\"$id\" name=\"photosToRename[]\" value=\"$id\"><br>";
        echo "<a href=\"javascript:void(0)\" onclick=\"switchCheckbox($id);\"><img align=\"center\" width=\"" . $width . "\" height=\"" . $height . "\" src=\"images/thumbnails/" . $name . "\" title=\"" . $title . "\" border=\"0\"></a>";
        echo "\n";
        echo "<br>\n";
        echo "</div>\n\n";
    }
?>

            </form>

        <map name="Map">
            <area shape="rect" coords="95,1,129,44" href="frameset.htm" alt="map">
        </map>

    </body>
</html>

<?php mysqli_close($db_connect); // Closes the connection.?>