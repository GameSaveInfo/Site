<?php
    include_once 'libs/wideimage/WideImage.php';

    if(isset($_GET['name'])) {
        $name = $_GET['name'];
        
        $original_name = 'images/'.$name;
        
        if(isset($_GET['width'])) {
            $image_width = $_GET['width'];            
            $info = pathinfo($name);
            $adjusted_name = 'thumbs/'.$info['dirname'].'/'.$info['filename'].'_'.$image_width.'.'.$info['extension'];
            if(!file_exists($adjusted_name)||
                filemtime($original_name)>filemtime($adjusted_name)) {
                    
                    if(!is_dir('thumbs/'.$info['dirname']))
                        mkdir('thumbs/'.$info['dirname']);
                $image = WideImage::load($original_name);
                $image = $image->resize($image_width,null);
                $image->saveToFile($adjusted_name);
            }
        } else {
            $adjusted_name = $original_name;
        }
        
        
        $image = WideImage::load($adjusted_name);
        $image->output('jpg', 90);
    }
    
    
?>