<?php
    require_once "GSISiteMap.php";
    require_once 'headers.php';

    function outputStats($sql, $title, $div) {
        global $db;
        $rows = $db->RunStatement($sql);
        
        
    echo "var data = new google.visualization.DataTable();";
        echo "data.addColumn('string', 'DISPLAY');";
        echo "data.addColumn('number', 'VALUE');";

        echo '        data.addRows([';

        foreach($rows as $row) {
            echo "['".htmlspecialchars($row->DISPLAY, ENT_QUOTES )."', ".$row->VALUE."],\n";
        }
        echo ']);';
        echo "var options = {'title':'".$title."',";
                       echo "'width':500,";
                       echo "'height':300};\n";
                // Instantiate and draw our chart, passing in some options.
        echo "var chart = new google.visualization.PieChart(document.getElementById('".$div."'));";
        echo "chart.draw(data, options);\n\n";

    }
?>
<html>
<head> <!--Load the AJAX API-->
<style>
div.footer {
font-height:10px;
}
</style>
<link media="Screen" href="/css/gsi.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawCharts);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawCharts() {

            <?php
                outputStats("select  IFNULL(os.Description,'Unspecified') AS DISPLAY, count(*) AS VALUE from  game_versions game 
                LEFT JOIN version_operating_systems os ON os.name = game.os
                group by game.os order by count(*) desc","Games by Operating System","games_by_os");
                
                outputStats("select type AS DISPLAY, count(*) AS VALUE from games where type != 'system' group by type order by count(*) desc",
                "Games By Type","games_by_type");
                
                outputStats("select  IFNULL(platform.Description,'Unspecified') AS DISPLAY, count(*) AS VALUE from  game_versions game 
                LEFT JOIN version_platforms platform ON platform.name = game.platform
                where game.platform is not null
                group by game.platform order by count(*) desc","Games by Technology Platform*","games_by_platform");
                
                outputStats("select  IFNULL(region.Description,'Unspecified') AS DISPLAY, count(*) AS VALUE from  game_versions game 
                LEFT JOIN version_regions region ON region.name = game.region
                where game.region is not null
                group by game.region order by count(*) desc","Games by Region*†","games_by_region");
                
                outputStats("select  IFNULL(game.Media,'Unspecified') AS DISPLAY, count(*) AS VALUE from  game_versions game 
                where game.media is not null
                group by game.media order by count(*) desc","Games by Media*†","games_by_media");
            
                outputStats("select  IFNULL(game.release,'Unspecified') AS DISPLAY, count(*) AS VALUE from  game_versions game 
                where game.release is not null
                group by game.release order by count(*) desc","Games by Release Type*†","games_by_release");
                
                outputStats("select path.ev AS DISPLAY, count(*) AS VALUE from game_location_paths path group by path.ev order by count(*) desc",
                "Save Locations By Environment Variable","paths_by_ev");
                
                outputStats("select path.root AS DISPLAY, count(*) AS VALUE from game_location_registry_keys path group by path.root order by count(*) desc",
                "Save Locations By Registry Root","registry_by_root");
                
                outputStats("select path.prefix AS DISPLAY, count(*) AS VALUE from game_playstation_codes path group by path.prefix order by count(*) desc",
                "PlayStation Codes by Prefix","playstation_by_prefix");
                
                outputStats("select path.prefix AS DISPLAY, count(*) AS VALUE from game_playstation_codes path group by path.prefix order by count(*) desc",
                "PlayStation Codes by Prefix","playstation_by_prefix");
                
                outputStats("select path.filename AS DISPLAY, count(*) AS VALUE from game_files path 
                left join game_file_types type ON type.id = path.type
                where filename IS NOT NULL AND type.name IS NULL group by path.filename having count(*) > 2 order by count(*) desc",
                "Save Filenames*","save_filenames");
                
                outputStats("select IFNULL(path.path,'ROOT FOLDER') AS DISPLAY, count(*) AS VALUE from game_files path 
                left join game_file_types type ON type.id = path.type
                where path is not null AND type.name IS NULL group by path.path having count(*) > 4 order by count(*) desc ",
                "Save Paths*","save_paths");

                
                outputStats("select contributor AS DISPLAY, count(*) AS VALUE from game_contributions group by contributor order by count(*) desc",
                "Contributions By Contributor","contributors");
            ?>
      }
    </script>
    <title>GameSave.Info - Statistics</title>
</head>
 <body>
 <form action="statistics.php" method="get">
 <h1>GameSave.Info Statistics!</h1>

 <table>
 
<tr>
    <td><div class="chart" id="games_by_os"></div></td>
    <td>Most games are for Windows? <br/> <img src="images/no-wai.jpg" height="200" /></td>
</tr>
<tr>
    <td><div class="chart" id="games_by_type"></div></td>
    <td>This doesn't reflect many DLC.</td>
</tr>
<tr>
    <td><div class="chart" id="games_by_platform"></div></td>
    <td>This chart shows games that utilize a particular technology. This category is almost a catch-all</td>
</tr>
<tr>
    <td><div class="chart" id="games_by_region"></div></td>
    <td>The database only records the region for a game if the save for it is specific to that region. This hasn't come up much, but unsurprisingly most of the entries are for the USA.</td>
</tr>
<tr>
    <td><div class="chart" id="games_by_media"></div></td>
    <td>Not many games have saves that are specific to what kind of media they came on. This is mostly older games.</td>
</tr>
<tr>
    <td><div class="chart" id="games_by_release"></div></td>
    <td>This is a fun look at all the different "double dipping" titles that get tagged on to successive versions of a game.</td>
</tr>

<tr>
    <td><div class="chart" id="paths_by_ev"></div></td>
    <td>This is an interesting one. This shows where games tend to keep their saves. Fortunately, My Documents has taken over as the king of save locations. Just a few short years ago games were mostly still keeping their saves in the install folder. Windows Vista and later have a "Saved Games" folder, but as you can see here it's only used in about 2% of games.</td>
</tr>

<tr>
    <td><div class="chart" id="registry_by_root"></div></td>
    <td>Put your keys in local_machine, people.</td>
</tr>

<tr>
    <td><div class="chart" id="playstation_by_prefix"></div></td>
    <td>This doesn't really demonstrate anything, but that just makes it more interesting.</td>
</tr>

<tr>
    <td><div class="chart" id="save_filenames"></div></td>
    <td>This shows the names that saves tend to have. Only names with more than two occurances are shown.</td>
</tr>
<tr>
    <td><div class="chart" id="save_paths"></div></td>
    <td>This shows the paths (relative to the root of the install folder or whatever) that are typically used for saves. Only paths with more than four occurances are shown.</td>
</tr>


<tr>
    <td><div class="chart" id="contributors"></div></td>
    <td>This site wouldn't be anything without the contributions from these people, so I guess they deserve a chart.</td>
</tr>
</table>

<div class="footers">
* - This chart is omitting entries that have no value, so it should not be seen as a representative of real-world statistics.<br/>
† - Game versions in this database are only broken down by save compatability. If a game gets re-released as special edition, an HD re-release, a CD and a floppy edition, in Spain and in Mexico, as long as the saves are all the same, they only get one entry. This means that this particular chart is not a good representation of real-world statistics.<br/>
‡, §, ‖, ¶.[1
</div>
</form>
</body>
  
</html>