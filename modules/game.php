
<?php
include_once '../headers.php';



function printFiles($files) {
    foreach ($files as $file) {
        echo '<tr>';
        if ($file->mode == 'IDENTIFIER') {
            echo '<td>' . $file->path . '</td>';
            echo '<td>' . $file->name . '</td>';
        } else {

            echo '<td>' . $file->path . '</td>';
            echo '<td>';
            if ($file->name == null)
                echo "* (Includes subfolders)";
            else
                echo $file->name;
            echo '</td>';
            echo '<td>' . $file->type . '</td>';
            echo '<td>' . $file->modified_after . '</td>';
        }
        echo '</tr>';
    }
}



function printFile($file) {
    
}










echo '<div class="game_version">';


if($printed) {
    $printed = false;
    echo '<hr />';
    
    


    echo '<hr />';
}


<script type="text/javascript">

document.title = site_title + " - <?php echo htmlspecialchars($game_data->title) ?>";

</script>

