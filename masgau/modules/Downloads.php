<?php
include_once 'AModule.php';

class Downloads extends AModule
{
    
    private $body;
    public function footer() {
    }

    public function draw() {
        
        echo 'MASGAU is technically still Beta, so please add all problems found to the <a href="https://github.com/MASGAU/.NET/issues/new">Issues List</a>';

        
        
        echo '<h2>Download Installer</h2>';
        echo '<p>You can download the latest version from the <a href="https://github.com/MASGAU/">GitHub page</a>, or from here:</p>';
        $data = $this->db->Select("program_versions",null,
                                    array("edition"=>'installable',"stable"=>1),
                                    array("major"=>"DESC","minor"=>"DESC","revision"=>"DESC"));
        foreach($data as $row) {
            echo '<h3><a href="'.$row->url.'">DOWNLOAD MASGAU V.'.$row->string.' INSTALLER FOR '.strtoupper($row->os).'</a></h3>';
        }
/*        
        
        
        <p>Release highlights:
        <ul>
        <li>Added client code for the new automatically generating update server</li>
        <li>Monitor being able to synchronize all your saves with a folder, such as with DropBox, allowing you to keep your saves the same on separate systems without having to manually restore save archives</li>
        <li>Sorting files into separate archives (Saves, Settings, etc.)</li>
        <li>New restore procedure</li>
        <li>New task program</li>
        <li>Better support for dealing with DropBox and other syncing programs</li>
        <li>New detection backend</li>
        <li>Better Windows 7 taskbar support (Progress Bars and Jump Lists)</li>
        <li>New WPF-based interface</li>
        <li>New server-client update system for more rapid game data deployments</li>
        <li>Another year's worth of little changes and fixes, see the Changelog for a complete list</li>
        </p>
        */
        
        
        echo '<p>0.8 and later use a different install system then the earlier versions, so if you installed ANY version prior to 0.8, it would be very wise to manually uninstall it before installing 0.8 or later. Later than 0.8 will no longer require uninstallation before upgrading. And I know I said this before, but I didn\'t expect to fall out of love with NSIS.</p>'
        
        .'<p>MASGAU REQUIRES <a href="http://www.microsoft.com/NET/">Microsoft\'s .NET framework</a> to be installed. Setup will  download and install it automatically if it isn\'t.</p>'
        
        .'<p>No guarantees that this won\'t destroy your computer and all that.</p>'
        
        .'<h2>Download Portable Version</h2>'
        
        .'<p>This version is just a zip file, extract it wherever, then go in and double-click MASGAU.exe or whatever you want to run. All config files are contained within the program\'s folder.</p>';
        
        $data = $this->db->Select("program_versions",null,
                                    array("edition"=>'portable',"stable"=>1),
                                    array("major"=>"DESC","minor"=>"DESC","revision"=>"DESC"));
        foreach($data as $row) {
            echo '<h3><a href="'.$row->url.'">DOWNLOAD MASGAU V.'.$row->string.' PORTABLE VERSION FOR '.strtoupper($row->os).'</a></h3>';
        }
        
        echo '<p>This version does not contain .NET 4.0, which must be installed on the computer in order for MASGAU to run.</p>'
        
        .'<h2>Download Older Versions</h2>'
        .'<p>All the previous releases of MASGAU are available here:</p>'
        
        .'<h3><a href="https://github.com/MASGAU/MASGAU/downloads">https://github.com/MASGAU/MASGAU/downloads</a></h3>'
        
        .'<h2>Download Test Build</h2>'
        
        .'<p>This link always points to the latest test build:</p>';
        
        $data = $this->db->Select("program_versions",null,
                                    array("edition"=>'installable',"stable"=>0),
                                    array("major"=>"DESC","minor"=>"DESC","revision"=>"DESC"));
                                    
                                    
        if(sizeof($data)>0) {
            foreach($data as $row) {
                echo '<h3><a href="'.$row->url.'">DOWNLOAD MASGAU V.'.$row->string.' <B>TEST</B> VERSION FOR '.strtoupper($row->os).'</a></h3>';
            }
}            else {
        echo '<h3>CURRENTLY DOWN, NO TEST BUILDS</h3>';
            }
        echo '<p>Features currently in testing:'
        .'<ul>'
        .'<li>Interface re-write</li>'
        .'<li>New XML data format for <a href="http://gamesave.info/">GameSave.Info</a> compatability</li>'
        .'</ul>'
        .'</p>'
        
        .'<h2>Download Source Code (GitHub)</h2>'
        .'<p>The source code for MASGAU is available through GitHub</p>'
        
        .'<h3><a href="https://github.com/MASGAU/">https://github.com/MASGAU/</a></h3>';
    }
        
    public function title() {
        return " - Downloads";
    }
    
    public function headers() {
        echo '<link media="Screen" href="css/pages.css" type="text/css" rel="stylesheet" />';
        echo '<script type="text/javascript" src="javascript/pages.js"></script>';
    }

    
    public function ajax() { 
        
    }
    
    public static function CreateLink() {
        return '<a href="?module=downloads">';
    }

}
?>
