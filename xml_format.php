<html>
<head>
<?php
    require_once 'headers.php';
    
    function formatXml($code, $lines = null) {
        echo '<div class="code">';
        $geshi = new GeSHi($code,"xml");
        if(!is_null($lines)) {
            $geshi->highlight_lines_extra($lines);
        }
        echo $geshi->parse_code();
        echo '</div>';
    }
    function formatReg($code) {
        echo '<div class="code">';
        $geshi = new GeSHi($code,"reg");
        echo $geshi->parse_code();
        echo '</div>';
    }
    
    function printNames($table) {
        global $db;
        $items = $db->Select($table,null,null,"name");
        foreach($items as $item) {
            echo $item->name."<br/>";
        }
    }
    function printTable($table, $header=null) {
        global $db;
        $items = $db->Select($table,null,null,"name");
        echo "<table>";
        if(!is_null($header)) {
            echo "<tr><th colspan='2'>".$header."</th></tr>";   
        }
        foreach($items as $item) {
            echo "<tr><td>".$item->name."</td><td>";
            if($table=="game_environment_variables") {
                include_once 'gamedata/Location.php';
                include_once 'gamedata/GameVersion.php';
                echo Location::getEvDescription($item->name,$db);
            } else {
                echo $item->description;
            }
            echo "</td></tr>";
        }
        echo "</table>";
    }
    
?>
<title>GameSave.Info - XML Format</title>
<link media="Screen" href="css/ogsip.css" type="text/css" rel="stylesheet" />
<link media="Screen" href="libs/jquery/css/redmond/jquery-ui-1.8.21.custom.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="libs/jquery/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="libs/jquery/jquery-ui-1.8.21.custom.min"></script>
<script type="text/javascript" src="libs/yoxview/yoxview-init.js"></script>
<script type="text/javascript">
function createLink(current, i) {
    current.attr("id", "subtitle" + i);
    $("#toc").append("<a id='link" + i + "' href='#subtitle" +
        i + "' title='" + current.attr("tagName") + "'>" + 
        current.html() + "</a>");
    
}

$(document).ready(function() {
    $("#toc").append("<h1>Table Of Contents</h1><ol>");
    $("h2").each(function(i) {
        createLink($(this),i);
        $(this).find('h3').each(function(i) {
            createLink($(this),i);            
        });
    });
    $("#toc").append("</ol>");
});

</script>
<style>
body {
    font-size:12pt;
}
div.code, td {
}
table, div.code {
    border:dashed 3px black;
    margin-left:20px;
    margin-right:20px;

}
table {
    background:gray;
}
th {
padding:5px;
    color:#eeeeec;
    white-space:nowrap;
}
td {
padding:5px;
    background:rgba(256,256,256,0.9);
}
div.code {
    background:rgba(0,0,0,0.1);
    padding-left:15px;
    padding-right:15px;
}
</style>
</head>
<body>

<h1><a href="http://gamesave.info">Back To GameSave.Info</a></h1>

<p>
Now hold up! You don't have to follow the directions on this page to get games added to GameSave.Info!
Just use the Analyzer program included with <a href="http://masgau.org/">MASGAU</a> to send me a report on a game (it even has a button to automatically e-mail it for you!), and I'll add it for you! 
But, if you really want to understand how GameSave.Info and MASGAU sources their game data, feel free to read on!</p>

<div id="toc"></div>

<ol>
<h2><li>Introduction</li></h2>
<p>GameSave.Info uses an <a href="https://github.com/GameSaveInfo/Data/blob/master/games.xml">XML file checked into Github</a> to describe where each game keeps its settings and saves. 
As you may or may not know, an XML file is little more than a specially typed text file, and can be created in programs as simple as notepad. All you do is fire up your favorite text editor and type the correct lines. Here's an excerpt from the games.xml file:</p>

<?php
$code = '<game name="DeusEx">
    <title>Deus Ex</title>
    <version os="PS2" region="USA">
      <title>Deus Ex: The Conspiracy</title>
      <ps_code prefix="SLUS" suffix="20111"/>
      <contributor>GameSave.Info</contributor>
    </version>
    <version os="Windows">
      <locations>
        <path ev="installlocation" path="DeusEx"/>
        <path ev="installlocation" path="GOG.com\Deus Ex"/>
        <path ev="steamcommon" path="deus ex"/>
        <registry root="local_machine" key="SOFTWARE\GOG.com\GOGDEUSX" value="PATH"/>
        <registry root="local_machine" key="SOFTWARE\Unreal Technology\Installed Apps\Deus Ex" value="Folder"/>
        <shortcut ev="startmenu" path="Programs\Deus Ex\Play Deus Ex.lnk" detract="System"/>
        <shortcut ev="startmenu" path="Programs\GOG.com\Deus Ex GOTY\Deus Ex GOTY.lnk" detract="System"/>
      </locations>
      <files>
        <include path="Save"/>
      </files>
      <files type="Settings">
        <include path="System" filename="*.ini"/>
      </files>
      <linkable path="Save"/>
      <identifier path="Save"/>
      <contributor>GameSave.Info</contributor>
    </version>
    <comment>The best game EVER!</comment>
</game>';
formatXml($code);

?>

<p>This looks more intimidating than it is. Let's go into it line-by-line, but first some terms:
<dl>
<?php formatXml('<game name="DeusEx">'); ?>
  <dt>Element or Tag</dt>
    <dd>That little bit of code is an element. Elements are surrounded by < and >.</dd>
  <dt>Attribute</dt>
    <dd>The word "name" in the above is an attribute. It's like a property of an element.</dd>
</dl> 

<p><b>NOTE: IN PATHS AND FILENAMES LEAVE OFF ALL LEADING AND TRAILING SLASHES ( \ AND / )</b></p>

<p><b>ANOTHER NOTE: There is <a href="https://github.com/GameSaveInfo/Data/blob/master/games.xsd">a schema file on GitHub</a>. If you know what that means, use it.</b></p>

<h2><li>Game Tag</li></h2>

<?php formatXml('<game name="DeusEx">'); ?>

<p>The main purpose of this tag is to provide a unique internal name for the game.
No spaces, and no symbols.
Use <a href="http://en.wikipedia.org/wiki/CamelCase">CamelCase</a> for legibility. 
Always use numbers instead of roman numerals, for sorting purposes.
All versions of a game go under the same game tag. In this case, there is a version for Window and a version for PS2, but we'll talk about that more later.</p>

<p>There are actually several variations on this tag, and you should try to use the one appropriate for your entry:</p>

<?php formatXml('<expansion name="MechWarrior4BlackKnight" for="MechWarrior4Vengeance">'); ?>

<p>Use this if the entry is for an expansion pack, add-on or DLC for another game.
In this example, Mechwarrior 4: Black Knight is an expansion for MechWarrior 4: Vengeance.
The "for" attribute is required for an expansion, and MUST reference another game in the XML file.
"Stand-alone expansions" do NOT get to be marked as an expansion. The term is an oxymoron, and makes no sense.</p>

<?php formatXml('<mod name="NamelessMod" for="DeusEx">'); ?>

<p>Use this if the entry is for an MOD for another game.
In this example, The Nameless Mod is a MOD for Deus Ex.
The "for" attribute is required for a MOD, and MUST reference another game in the XML file.</p>

<?php formatXml('<system name="GamesForWindows">'); ?>
<p>Use this when describing system data.</p>

<p>There is a completely optional "follows" attribute that can be added to any of these variations:</p>

<?php formatXml('<game name="DeusExInvisibleWar" follows="DeusEx">'); ?>

<p>It basically just indicates that the entry is somehow a follow-up (or sequel) to the indicated other entry. 
It's not parsed or used anywhere yet, but one day maybe.</p>

<?php formatXml('<game name="DeprecatedGame" deprecated="true">'); ?>

<p>If a game is marked as deprecated, it means that the information provided is no longer considered correct.
It's kept only for posterity and backwards-compatability.</p>

<p>Obviously your closing tag should match your opening tag. Other than this, the contained tags are all the same.</p>

<h2><li>Game Title</li></h2>
<?php formatXml('<title>Deus Ex</title>'); ?>
<p>Between the two title tags you just type up the name of the game.
This should be the name that was first attached to a game when it was release, other names would be delegated to version titles, which we will talk about later.
Try to include the entire name, no reason to skimp on length. 
It might be tempting to shorten <i>Penny Arcade Adventures: On The Rain Slick Precipice Of Darkness Chapter One</i>
to <i>Penny Arcade Adventures 1</i>, but resist it.</p>

<h2><li>Game Version</li></h2>
<?php formatXml('<version os="Windows">'); ?>

<p>The version tag is used to specify the versions of the game the contained saves are compatible with.
In this case, it's compatible with Windows. 
This does NOT mean this save will only work on Windows, 
only that it's only for the Windows version of the game. 
This save would also be compatible with Linux if you have Deus Ex installed under WINE.
The reason it is organized like this is because GameSave.Info also doubles as the data source for the game save backup program MASGAU.
</p>
<p>There are 5 attributes that allow us to describe a unique game save version:</p>
<table>
<tr>
<th>Attribute -></th>
<th>os</th>
<th>platform</th>
<th>region</th>
<th>media</th>
<th>release</th>
</tr>
<tr>
<th>Description -></th>
<td>The operating system the save is compatible with</td>
<td>The technology platform the save is compatible with</td>
<td>The region of the world the save is for</td>
<td>The delivery medium of the game version that the save is compatible with</td>
<td>The release of the game the save is compatible with</td>
</tr>
<tr>
<th>Possible Values -></th>
<td><?php printNames("version_operating_systems") ?></td>
<td><?php printNames("version_platforms") ?></td>
<td>Any <a href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-3#Officially_assigned_code_elements">3-letter ISO 3166-1 country code</a>,
or any of these two-letter continent codes:<br /><br />
AF - Africa<br/>
AS - Asia<br/>
EU - Europe<br/>
NA - North America<br/>
SA - South America<br/>
OC - Oceania<br/>
(Antarctica has its own country code, so I won't support its continent code)
<td><?php printNames("version_medias") ?></td>
<td>This is freeform, can be anything at all. Some examples:<br/><br/>
CollectorsEdition<br/>
TitaniumEdition<br/>
Gold<br/>
HD<br/>
Remastered<br />
GOTY
</td>
</tr>
</table>

<p>If a save is compatible with more than one thing in any of these categories, just don't specify the attribute. Try to keep the version specification as general as possible, while simultaneously making sure that a save would not accidentally get labelled as belonging to the wrong version of the game.</p>

<?php formatXml('<version os="Windows" media="CD" release="TitaniumEdition" region="USA">'); ?>

<p>This example states that the contained saves are only compatible with the Windows version of the Titanium Edition of the game that was released on CD in the USA. This example is fake, I have yet to encounter saves that had such specific requirements.</p>

<?php formatXml('<version media="Steam">'); ?>

<p>An important distinction should be made between a version for Steam and a version specifically for Steam Cloud data. The above is for the former, and the below for the latter.</p>

<?php formatXml('<version platform="SteamCloud">'); ?>

<p>You could also specify media="Steam" on this, and it would be accurate but since SteamCloud automatically imples Steam, it's not necessary.</p>

<p>My policy right now on DOS games is to label it as DOS if the save produced is only compatible with the DOS version of the game. If there exists a Windows version of a DOS game, and the saves are compatible with both, then both of the games' information would be combined into one Windows profile, such as with Master Of Orion 2 or Descent II.</p>


<p>
If you omit these attributes, then it is saying that the saves described are compatible with all versions of the game. This is pretty rare, but these games do exist. One example is fs2_open:
<?php formatXml('  <game name="fs2_open">
    <title>fs2_open</title>
    <version>
      <locations>
        <path ev="installlocation" path="fs2_open"/>
        <parent name="FreeSpace2" os="Windows"/>
      </locations>
      <files>
        <include path="data\players"/>
      </files>
      <identifier filename="fs2_open*"/>
      <comment>Doesn\'t have a default install folder, so might require an Alt. Install Path.</comment>
      <contributor>GameSave.Info</contributor>
    </version>
  </game>',array(3)); ?>
  
</p>
<p>If a game's saves were to work across just Linux and Windows, I would also not add a platform attribute, even if there was a Mac version with incompatible saves. 
By adding an additional Mac-specific version we would be declaring such an incompatibility.</p>

<p>You can specify more than one version of a game within the same game tag:</p>
<?php formatXml(' <game name="MechWarrior2">
    <title>MechWarrior 2: 31st Century Combat</title>
    <version os="Windows">
      <locations>
        <path ev="installlocation" path="Activision\BattlePack\MW2"/>
        <shortcut ev="startmenu" path="Programs\BattlePack\MechWarrior 2\MechWarrior 2 Uninstall.lnk"/>
      </locations>
      <files type="Mechs">
        <include path="mek"/>
      </files>
      <files>
        <include filename="userstar.bwd"/>
      </files>
      <files type="Settings">
        <include filename="MW2PRM.CFG"/>
        <include filename="MW2REG.CFG"/>
      </files>
      <contributor>GameSave.Info</contributor>
    </version>
    <version os="Windows" release="TitaniumEdition">
      <title>MechWarrior 2: 31st Century Combat: Titanium Edition</title>
      <locations>
        <path ev="installlocation" path="Activision\Titanium\Mechwarrior2"/>
        <path ev="altsavepaths" path="MechVM\games\mw2-31stcc-tt"/>
        <registry root="local_machine" key="SOFTWARE\Activision\Activenet\Applications\1020.2.1" value="Cwd"/>
        <shortcut ev="startmenu" path="Programs\Titanium\Mechwarrior2\Play MechWarrior2.lnk" detract="splash"/>
      </locations>
      <files type="Mechs">
        <include path="mek"/>
      </files>
      <files>
        <include filename="userstar.bwd"/>
      </files>
      <files type="Settings">
        <include filename="MW2PRM.CFG"/>
        <include filename="MW2REG.CFG"/>
      </files>
      <contributor>GameSave.Info</contributor>
    </version>
  </game>',array(3,20)); ?>
  <p>As you can see we only specify a version title when that version has a title different than the main one specified under the game tag.</p>


<?php formatXml('<version os="Windows" virtualstore="ignore" detect="required">'); ?>

<p>There are two additional attributes demonstrated here:

<ul>
<li>virtualstore - Specified if the game ignores VirtualStore in Windows Vista and later.
Can be set to "ignore" or "use". Default is use.</li>
<li>detect - Specifies wether the game's save location cannot be predicted without an existing save location. 
Can be either "required" or "optional". Default is "optional".</li>
</ul>
</p>

<?php formatXml('<version deprecated="true">'); ?>

<p>If a version is marked as deprecated, it means that the information provided is no longer considered correct.
It's kept only for posterity and backwards-compatability.</p>



<h2><li>Locations</li></h2>
<p>Games can keep their saves anywhere, so here we try to provide as many ways as possible of finding them.
These locations are not the exact locations of the saves, but are instead roots used as the first step to find the saves.
Why do we do it like this? Here's why:</p>
<table><tr>
<th>These are some possible save locations for Deus Ex:</th>
<th>See how they're all different, but all end with the same Save folder? <br/>
We can be certain that the Save folder is always used, no matter the location,<br/>
and just specify the part of the path that we <u>can't</u> predict:</th>
</tr>
<tr>
<td>C:\DeusEx\Save\</td>
<td>C:\DeusEx\</td>
</tr>
<tr>
<td>C:\Program Files\GOG.com\Deus Ex\Save\</td>
<td>C:\Program Files\GOG.com\Deus Ex\</td>
</tr>
<tr>
<td>C:\Program Files\Steam\steamapps\common\deus ex\Save\</td>
<td>C:\Program Files\Steam\steamapps\common\deus ex\</td>
</tr>
</table>
<p>The main point here is to not store the same information (the Save folder) more than once.
This has space saving advantages, btu it also allows us to re-use the same location to specify Settings, Replays, Screenshots, or anything else that might happen to be there.
We must be mindful to include enough of the root path that we can't mistake one game's paths for another.
We have a few ways of finding these locations:
</p>
<ol>
<h3><li>Location Using A Path</li></h3>
<?php formatXml('<path ev="installlocation" path="DeusEx"/>'); ?>
<p>The path tag lets us specify an actual folder name, but sitll allows us to do so in a format that can adjust automatically for any system.
This is attained via environment variables. 
The environment variable will be replaced with whatever the appropriate path from the system is, and prepended onto the provided path.</p>
<td><?php printTable("game_environment_variables","The environment variables available for use:") ?></td>

<p>Different versions of a game can install to all kinds of locations, so if a game keeps its saves in the install folder we have to specify as many different install paths as we can discover. 
For Deus Ex, the CD, Steam and GoG.com versions all install to different locations, so we add a path element for each one:</p>
<?php formatXml('<path ev="installlocation" path="DeusEx"/>
<path ev="installlocation" path="GOG.com\Deus Ex"/>
<path ev="steamcommon" path="deus ex"/>'); ?>


<p>If we're lucky, the game keeps its saves somewhere other than the install folder, which usually means that all versions of the gam use the exact same path.
A good example, and one that most games follow these days, is using the "My Documents" folder.
Deus Ex's sequel, Invisible War, was wise enough to do this:</p>

<?php formatXml('<path ev="userdocuments" path="Deus Ex - Invisible War"/>'); ?>

<p>This will check each user's My Documents folder for a path called "Deus Ex - Invisible War", and should work for the disc, Steam, Impulse, Gog.com or any other versions of the game. 
This is not universal unfortuantely, as some games (like Alan Wake) use different folder names for different versions, despite all using folders like My Documents.</p>

<h3><li>Location Using A Registry Key</li></h3>
<?php formatXml('<registry root="local_machine" key="SOFTWARE\Unreal Technology\Installed Apps\Deus Ex" value="Folder"/>'); ?>
<p>The registry element lets you specify a registry key that contains the path to a game's saves.
For thsoe not in the know, Windows keeps what is called a registry, and it's basically a fancy collection of names and value. 
If you click Start, Run then type in regedit, you'll be able to browse it.
Windows keeps all of its settings, and the install locations of a lot of programs here, which means we can take advantage of it to find a game, but it's pretty much only useful for games that keep their saves in the isntall folder.
The root of a key indicates which registry root will be used. Windows has several, here are what's available:
</p>

<ul>
<li>classes_root - I don't know
<li>current_user - The registry for the currently logged in user
<li>current_config - The registry for Windows' settings
<li>dyn_data - I don't know
<li>local_machine - The registry for the computer as a whole
<li>performance_data - I don't know
<li>users - The registry for all the users
</ul>

<p>The key is like a folder path, pointing to the location of the key in the registry root (browse around regedit, it'll make sense).
A key can have several values, one default and zero or more named values.
If you ommit the value attribute, the default one will be used, otherwise only a value matching the name you provide will be used.
</p>

<p>There is one caveat on 64-bit systems. On these systems, Windows places the registry keys for 32-bit programs (which most games are) inside of a special folder, seperate from the 64-bit programs.
For instance Deus Ex's registry entry on a 32-bit system would be:</p>

<?php formatReg('SOFTWARE\Unreal Technology\Installed Apps\Deus Ex'); ?>

<p>But on a 64-bit system it would be placed in:</p>

<?php formatReg('SOFTWARE\Wow6432Node\Unreal Technology\Installed Apps\Deus Ex'); ?>

<p>The policy right now is to write entries WITHOUT the Wow6432Node. Adding the node is trivial, so this way is more compatible.</p>

<p>Registry keys frequently can make use of the append and detract attributes available to all location elements. See the section below for more details.</p>

<h3><li>Location Using A Shortcut</li></h3>

<?php formatXml('<shortcut ev="startmenu" path="Programs\Deus Ex\Play Deus Ex.lnk" detract="System"/>'); ?>

<p>Quite frequently there is a shortcut in the Start menu, or on the desktop, or somewhere else, that points to the install folder of a game.
If the game keeps its saves in the install folder, then this is yet another way we can use to find them!
The ev attribute here supports the same values as the path element described above, but you'll pretty much always be using startmenu.
From there you provide the path to the shortcut, easy peasy!</p>

<p>As shown in the example, this tag can also take advantage of the append and detract attributes, which are explained in a later section.</p>

<h3><li>Location Using Another Game</li></h3>
<?php formatXml('<expansion name="MechWarrior4BlackKnight" for="MechWarrior4Vengeance">
    <title>MechWarrior 4: Black Knight</title>
    <version os="Windows">
      <locations>
        <registry root="local_machine" key="SOFTWARE\Microsoft\Microsoft Games\MechWarrior Black Knight" value="EXE Path"/>
        <parent name="MechWarrior4Vengeance" os="Windows"/>
      </locations>
      <files type="Mechs">
        <include path="resource\Variantsx"/>
      </files>
      <files>
        <include path="resource\Pilotsx"/>
      </files>
      <files type="Settings">
        <include filename="optionsx.ini"/>
      </files>
      <identifier filename="optionsx.ini"/>
      <contributor>GameSave.Info</contributor>
    </version>
</expansion>',array(6)); ?>
<p>If the game shares its save location in any way with another game, we can specify that game as a location source. 
More specifically, we specify a specific version of the game to take locations from.
We specify the name of the game, along with all the version attributes of the version we want.
These version elements MUST match a game version that is in the XML file.
</p>

<p>This also allows you to create entries whose detection is dependent on the detection of another game.</p>

<p>Quite often this will need to make use of the append and detract attributes, as well as the identifier element, all of which are explained later.</p>

<h3><li>Append And Detract Attributes</li></h3>
<?php formatXml('<game name="AloneInTheDark">
    <title>Alone in the Dark</title>
    <version os="DOS">
      <locations>
        <path ev="installlocation" path="GOG.com\Alone in the Dark\INDARK"/>
        <registry root="local_machine" key="SOFTWARE\GOG.com\GOGALONE1" value="PATH" append="INDARK"/>
        <shortcut ev="startmenu" path="Programs\GOG.com\Alone in the Dark\Alone in the Dark.lnk" detract="DOSBOX" append="INDARK" />
      </locations>
      <files>
        <include filename="SAVE?.ITD"/>
      </files>
      <contributor>GameSave.Info</contributor>
    </version>
</game>',array(7)); ?>
<p>All of the location elements can use the append and detract attributes. In the above example, the shortcut provided actually points to:</p>

<?php formatReg('C:\Program Files\GOG.com\Alone in the Dark\DOSBOX'); ?>

<p>But the saves are actually in:</p>

<?php formatReg('C:\Program Files\GOG.com\Alone in the Dark\INDARK'); ?>

<p>These attributes tell us to detract (or take away) DOSBOX, then append (or add to the end) INDARK from the location the shortcut points to.
This is frequently needed for expansions, registry keys and shortcuts, which will usually point close to the desired location, but not to exactly the right spot.
</p>

<p>You can use both of the attributes, only one or the other, or none at all, there are no requirements format-wise.</p>

<h3><li>only_for Attribute</li></h3>

<?php formatXml('<path ev="allusersprofile" path="Documents\Monolith Productions\Condemned" only_for="WindowsXP"/>
<path ev="public" path="Documents\Monolith Productions\Condemned" only_for="WindowsVista"/>'); ?>

<p>Some locations are only applicable to certain operating systems.
For these you can use the only_for attribute to specify an OS that the path is for.
All the OSs that can be used for the os attribute for the version element can be used here.
See the above version element section for a list of them.
</p>

<h3><li>Deprecated Locations</li></h3>

<?php formatXml('<game name="OddworldStrangersWrath" follows="OddworldMunchsOddysee">
    <title>Oddworld: Stranger\'s Wrath</title>
    <version os="Windows">
      <locations>
        <path ev="steamcommon" path="stranger\'s wrath" deprecated="true"/>
        <path ev="userdocuments" path="Oddworld\Stranger\'s Wrath"/>
      </locations>
      <files>
        <include path="Save"/>
      </files>
      <files type="Settings">
        <include filename="config.txt"/>
      </files>
      <contributor>Arc Angel</contributor>
      <contributor>slake_jones</contributor>
    </version>
</game>',array(5)); ?>

<p>Sometimes a game changes where it keeps its saves. In this example, Oddworld: Stranger's Wrath USED TO keep its saves in its install folder.
A patch changed this.
By adding the deprecated attribute, we're saying that this WAS a save location, but it isn't used anymore.
We keep these locations because there may still be saves there, and mark it as deprecated so we know we should never place saves there.
</p>

</ol>
<h2><li>Files</li></h2>

<p>Now we get to the nitty-gritty of specifying which files are saves, settings, etc. 
After the locations element's closing tag, we can specify one or more "files" elements, specifying and sorting these files by type.
<ol>
<h3><li>Types</li></h3>

<?php formatXml('<files>
<files type="Settings">'); ?>

<p>Each "files" element has an optional "type" attribute.
This tells us what type of files are going to be specified within the files tag.
It could also be "Settings", "Profiles", or anything else.
The attribute isn't constrained, so you can be as accurate as necessary.
When possible, try to conform to existing type names, so there can be some semblence of consistency, but if it absolutely needs to be a new type name, go for it.</p>


<h3><li>Files To Save</li></h3>

<?php formatXml('<files>
    <include path="Save"/>
</files>',array(2)); ?>

<p>Within each files element, we specify one or more "save" elements that describe the files.
There are three attributes used to specify files:
</p>

<ul>
<li>path - This specifies the folder path (starting at the end of the locations found from the above sections' specifications). This can use wildcards (like SAVE*)</li>
<li>filename - This specifies the name of the files. This can also use wildcards, like *.sav</li>
<li>modified_after - This specifies a time and date that the file must be modified after in order to qualify</li>
</ul>

<p>Different combinations of path and filename have different meanings:</p>
<ul>
<li>If no path or filename are specified, then that means ALL the files in ALL the folders in the location.</li>
<?php formatXml('<include />'); ?>
<li>If only a path is specified, then that means all the files in that folder, but NOT the subfolders.</li>
<?php formatXml('<include path="Save" />'); ?>
<li>If only a filename is specified, then that means all the files matching that name in the location, but NOT the subfolders.</li>
<?php formatXml('<include filename="*.sav" />'); ?>
<li>If a path and a filename are specified, then that means all the files matching the name in that specific folder, but NOT the subfolders.</li>
<?php formatXml('<include path="System" filename="*.ini"/>'); ?>
</ul>

<p>The modified_after date is formatted as follows:</p>
<?php formatXml('<include path="Data\Campaigns" modified_after="2001-10-09T00:00:00"/>'); ?>


<h3><li>Except For...</li></h3>
<?php formatXml('<include path="userdata">
  <exclude path="userdata\mp3"/>
</include>',array(2)); ?>

<p>To make things easier, you can specify a very broad save definition, and refine it using one or more "except" elements under that save element.
This element can use all the same tags as the "save" element, and they all work exactly the same, except the deselect files instead of selecting them.
</p>

</ol>

<h2><li>Identifier</li></h2>
<?php formatXml('<identifier path="Save" />'); ?>

<p>Some games aren't consistent about where their saves are. For example Peggle will store its setting in its install folder under XP, but under ProgramData under Vista and 7. 
This means that both must be specified in the XML file, leading to confusion if both are detected. 
The identifier tag is a way of telling for sure that we've got the right location. 
It can use the exact same attributes as a "save" element (described above).</p>
</p>

<p>In Peggle's case we'd specify
<?php formatXml('<identifier path="userdata" />'); ?>
<p>because only the right location will have a userdata folder in it. It would probably be best for every game to have one of these, but the only ones that absolutely need it are ones that can have multiple locations, or if you need to distinguish between two versions of a game (such as with The Longest Journey's 2-disc and 4-disc variants).
</p>

<h2><li>Linkable</li></h2>
<?php formatXml('<linkable path="Save"/>'); ?>

<p>Some games can handle having their save folders symlinked to another folder, like inside of a Dropbox or Google Drive folder.
Do this on two computers, and you've got their saves automatically synced!
The "linkable" element allows you to explain how to link a particular game.
This process only works reliably with folders, so that's the only thing you can specify.
If you specify a path, it will append that path to any detected locations.
If no path, then the detected location itself will be used.
</p>

<h2><li>PlayStation Games</li></h2>
<?php formatXml('<game name="BrutalLegend">
    <title>Brütal Legend</title>
    <version os="PS3" region="USA">
        <ps_code prefix="BLUS" suffix="30330"/>
        <contributor>GameSave.Info</contributor>
    </version>
</game>',array(3,4)); ?>

<p>PlayStation Games are considered just another version of the base game, and are marked with an OS matching the PlayStation platform (PS1, PS2, PS3, PSP).
Instead of "locations", "files" (and the optional "identifier"), you only specify the game's PlayStation code, which can usually be found on the game disc and case.
Each disc (even within the same game) usually has a unique code,  4 letters then 5 numbers. 
This code is used in the name of a game's saves.
Like file tags, you can sepcify a type on a PlayStation Code</p>

<p>For console games you should include the region code, as almost all console saves are guaranteed to be incompatible with those from other countries.</p>

<p>Some more modern PS3 games keep multiple save files for different types of data, sometimes seperating out Profile or setting data.
Usually these saves wil have extra letters appended to the name. You can specify these with the append tag, as shown in this example from Tomb Raider: Anniversary: </p>

<?php formatXml('<ps_code prefix="BLUS" suffix="30718" append="-TALIST"/>
<ps_code prefix="BLUS" suffix="30718" append="-TAPROFILE" type="Profile"/>'); ?>

<p>Usually you would want to accompany an append with an appropriate type.
Having a code with an append will NOT indicate that it should be excluded from other types, so make sure each code entry will indicate a unique entry.</p>

<h2><li>Contributor</li></h2>
<?php formatXml('<contributor>GameSave.Info</contributor>'); ?>

<p>This is used by the site to credit contributors.</p>

<h2><li>Comment</li></h2>
<?php formatXml('<comment>The best game EVER!</comment>'); ?>

<p>Used to specify a comment that will be visible on the game info page. 
You can have a comment element instide the version and/or inside the game.</p>

<h2><li>Restore Comment</li></h2>
<?php formatXml('<restore_comment>Restoring saves for this game also requires restoring Game for Windows Account Data, which MASGAU automatically backs up in G4WAccountData.</restore_comment>'); ?>

<p>Used to specify a comment pertaining to restoring a game's saves.
Can only be inside of a version element.</p>

<h2><li>Close Tags</li></h2>
<?php formatXml('</version>
</game>'); ?>

<p>Ending tag of course. If you opened with game, you need to end with game, if you started with expansion, you need to end with expansion, etc.</p>


</body>
</html>