-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 11, 2012 at 03:30 PM
-- Server version: 5.5.24
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `masgau_site`
--

-- --------------------------------------------------------

--
-- Table structure for table `program_versions`
--

CREATE TABLE IF NOT EXISTS `program_versions` (
  `major` int(11) NOT NULL,
  `minor` int(11) NOT NULL,
  `revision` int(11) NOT NULL,
  `url` longtext NOT NULL,
  `release_date` datetime NOT NULL,
  `xml_version` int(11) NOT NULL,
  `string` varchar(10) NOT NULL,
  `edition` enum('portable','installable') NOT NULL DEFAULT 'installable',
  `os` enum('windows','linux','osx') NOT NULL DEFAULT 'windows',
  `stable` tinyint(1) NOT NULL,
  PRIMARY KEY (`major`,`minor`,`revision`,`edition`,`os`),
  KEY `xml_version` (`xml_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `program_versions`
--

INSERT INTO `program_versions` (`major`, `minor`, `revision`, `url`, `release_date`, `xml_version`, `string`, `edition`, `os`, `stable`) VALUES
(0, 9, 1, 'https://github.com/downloads/MASGAU/MASGAU/MASGAU-0.9.1-Portable.zip', '2012-01-07 00:00:00', 1, '0.9.1', 'portable', 'windows', 1),
(0, 9, 1, 'https://github.com/downloads/MASGAU/MASGAU/MASGAU-0.9.1-Setup.exe', '2012-01-07 00:00:00', 1, '0.9.1', 'installable', 'windows', 1),
(1, 0, 0, 'https://docs.google.com/open?id=0By2Mfv6zO9SkTkxwM0xfX1BYOTA', '2012-07-10 00:00:00', 2, '1.0 ALPHA', 'installable', 'windows', 0);

-- --------------------------------------------------------

--
-- Table structure for table `site_menus`
--

CREATE TABLE IF NOT EXISTS `site_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `site_menus`
--

INSERT INTO `site_menus` (`id`, `title`, `order`) VALUES
(1, 'For The Users', 0),
(2, 'For The Geeks', 1),
(3, 'For Some Help', 2),
(4, 'For Fun', 3);

-- --------------------------------------------------------

--
-- Table structure for table `site_menu_items`
--

CREATE TABLE IF NOT EXISTS `site_menu_items` (
  `menu` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `type` enum('page','support','game_data','contributors','hyperlink','downloads') NOT NULL,
  `option` longtext,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`menu`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_menu_items`
--

INSERT INTO `site_menu_items` (`menu`, `title`, `type`, `option`, `order`) VALUES
(1, 'Download MASGAU!', 'downloads', NULL, 0),
(1, 'Automatically Backup Your Saves On The Net', 'page', 'cloud', 4),
(1, 'Using MASGAU (Screenshots!)', 'page', 'using', 1),
(1, 'Current Game Support', 'support', NULL, 2),
(2, 'How To Contribute', 'page', 'contribute', 0),
(2, 'How MASGAU Works', 'page', 'how', 1),
(2, 'How To Add New Games', 'page', 'xml_spec', 2),
(2, 'How To Use The Analyzer', 'page', 'analyzer', 3),
(2, 'Game Database', 'hyperlink', 'http://gamesave.info/', 4),
(2, 'Changelog', 'hyperlink', 'https://github.com/MASGAU/MASGAU/blob/master/Docs/changelog.txt', 5),
(2, 'Data Changelog', 'hyperlink', 'https://github.com/GameSaveInfo/Data/blob/master/changelog.txt', 6),
(2, 'MASGAU on GitHub', 'hyperlink', 'https://github.com/MASGAU/', 7),
(4, 'MASGAU on alternativeTo', 'hyperlink', 'http://alternativeto.net/software/masgau/', 0),
(3, 'Forums', 'hyperlink', 'http://forums.masgau.org/', 0),
(3, 'Report A Problem!', 'hyperlink', 'https://github.com/MASGAU/MASGAU/issues/new', 1),
(3, 'MASGAU On Twitter', 'hyperlink', 'http://twitter.com/MASGAU/', 2),
(3, 'MASGAU On Facebook', 'hyperlink', 'http://www.facebook.com/masgau', 3),
(3, 'MASGAU On Google+', 'hyperlink', 'http://plus.google.com/115220090159606871198', 4),
(3, 'My Blog', 'hyperlink', 'http://sanmadjack.blogspot.com/', 5),
(4, 'Contributor Hall of Fame', 'contributors', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `site_pages`
--

CREATE TABLE IF NOT EXISTS `site_pages` (
  `name` varchar(50) NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `footer` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_pages`
--

INSERT INTO `site_pages` (`name`, `title`, `content`, `footer`) VALUES
('download', 'Downloads', 'MASGAU is technically still Beta, so please add all problems found to the <a href="https://github.com/MASGAU/MASGAU/issues/new">Issues List</a>\r\n\r\n<h2>Download Installer</h2>\r\n\r\n<p>You can download version 0.9.1 (released 2012-01-07) from the project page, or from here:</p>\r\n\r\n<h3><a href="https://github.com/downloads/MASGAU/MASGAU/MASGAU-0.9.1-Setup.exe">DOWNLOAD MASGAU INSTALLER</a></h3>\r\n\r\n<p>Release highlights:\r\n<ul>\r\n<li>Added client code for the new automatically generating update server</li>\r\n<li>Monitor being able to synchronize all your saves with a folder, such as with DropBox, allowing you to keep your saves the same on separate systems without having to manually restore save archives</li>\r\n<li>Sorting files into separate archives (Saves, Settings, etc.)</li>\r\n<li>New restore procedure</li>\r\n<li>New task program</li>\r\n<li>Better support for dealing with DropBox and other syncing programs</li>\r\n<li>New detection backend</li>\r\n<li>Better Windows 7 taskbar support (Progress Bars and Jump Lists)</li>\r\n<li>New WPF-based interface</li>\r\n<li>New server-client update system for more rapid game data deployments</li>\r\n<li>Another year''s worth of little changes and fixes, see the Changelog for a complete list</li>\r\n</p>\r\n<p>0.8 and later use a different install system then the earlier versions, so if you installed ANY version prior to 0.8, it would be very wise to manually uninstall it before installing 0.8 or later. Later than 0.8 will no longer require uninstallation before upgrading. And I know I said this before, but I didn''t expect to fall out of love with NSIS.</p>\r\n\r\n<p>MASGAU REQUIRES <a href="http://www.microsoft.com/NET/">Microsoft''s .NET framework</a> to be installed. Setup will  download and install it automatically if it isn''t.</p>\r\n\r\n<p>No guarantees that this won''t destroy your computer and all that.</p>\r\n\r\n<h2>Download Portable Version</h2>\r\n\r\n<p>This version is just a zip file, extract it wherever, then go in and double-click MASGAU.exe or whatever you want to run. All config files are contained within the program''s folder.</p>\r\n\r\n<h3><a href="https://github.com/downloads/MASGAU/MASGAU/MASGAU-0.9.1-Portable.zip">DOWNLOAD PORTABLE VERSION</a></h3>\r\n\r\n<p>This version does not contain .NET 4.0, which must be installed on the computer in order for MASGAU to run.</p>\r\n\r\n<h2>Download Older Versions</h2>\r\n<p>All the previous releases of MASGAU are available here:</p>\r\n\r\n<h3><a href="https://github.com/MASGAU/MASGAU/downloads">https://github.com/MASGAU/MASGAU/downloads</a></h3>\r\n\r\n<h2>Download Test Build</h2>\r\n\r\n<p>This link always points to the latest test build:</p>\r\n\r\n<h3>CURRENTLY DOWN, NO TEST BUILDS</h3>\r\n \r\n<p>Features currently in testing:\r\n<ul>\r\n<li>None!</li>\r\n</ul>\r\n</p>\r\n\r\n<h2>Download Source Code (GitHub)</h2>\r\n<p>The source code for MASGAU is available through GitHub</p>\r\n\r\n<h3><a href="https://github.com/MASGAU/MASGAU/">https://github.com/MASGAU/MASGAU/</a></h3>', ''),
('using', 'How To Use', '<p>I tried to make MASGAU as automated as possible, so with any luck this page won''t really be necessary.</p>\r\n\r\n<thumb src="using/startmenu.jpg" width="200" alt="MASGAU In My Start Menu" /><h2>Modes</h2>\r\n\r\n\r\n<p>So that you don''t have to be an admin to use it, MASGAU has 2 different modes it can run in: Single User Mode and All Users Mode. </p>\r\n\r\n<h3>Single User Mode</h3>\r\n\r\n<p>This mode can be used by any user. It only scans the current user''s folders and publicly readable system-wide folders. Depending on the specific setup, Single User Mode may or may not be able to restore all saves. If a permission problem occurs, MASGAU will prompt with a choice of elevating MASGAU with admin privileges to complete a restore. This mode is configured per-user.</p>\r\n\r\n<h3>All Users Mode</h3>\r\n\r\n<p>This mode can only be used by admins or a user with admin credentials. It scans all possible locations for every user on the system. Since it requires elevation, this mode can restore any save to any location. This mode also allows access to the Schedule tab, as even reading the scheduled task requires admin privileges. This mode is globally configured for all users. A scheduled task will use this mode''s config for backup path, steam override, and alternate install paths.</p>\r\n\r\n<thumb src="using/settings.jpg" width="200" alt="The Settings Tab"/>\r\n<h2>Settings</h2>\r\n<p>I''ve tried to keep the settings to a minimum, and only one of them is actually necessary.</p>\r\n\r\n<h3>Auto-check For Updates</h3>\r\n<p>New for 0.8! MASGAU can check for updated game information and program versions. Checking this box will make MASGAU automatically check for these updates when you start the program. This setting also applies to the Monitor.</p>\r\n\r\n<h3>Ignore Dates During Backup</h3>\r\n<p>By default MASGAU will only archive a file if it''s modification time is more recent then the last backup of the game. Use this checkbox to override this behavior.</p>\r\n\r\n<h3>Check For Updates</h3>\r\n<p>New for 0.8! This button will force MASGAU to immediately check for updates.</p>\r\n\r\n<h3>Backup Path</h3>\r\n<p>This is where to put the archives of the game saves. You should choose a folder on a non-system drive if possible, so that if your system drive dies you still have your saves.</p>\r\n\r\n<h3>Steam Path</h3>\r\n<p>MASGAU checks your computer''s registry to find where Steam is installed. If you installed Steam you should never have to touch this setting. If you''re like my good buddy Mikey, you might have Steam installed on an external drive, and it wasn''t necessarily on the system you''re currently on. You can point MASGAU to this location here, and it will remember it on that computer henceforth. The reset button will tell MASGAU to re-attempt detecting Steam on the system, and if not found MASGAU will default to Not Detected.</p>\r\n\r\n<h3>E-Mail Address</h3>\r\n<p>MASGAU can send in reports on errors and new games (at your request, of course), but sometimes I''ll need to contact you for more info to help fix the problem or add the game. You can set the e-mail address that I will see here.</p>\r\n\r\n<h3><a name="alt_install_paths">Alternate Install Paths</a></h3>\r\n<p>Some people don''t install their game where they ought. For games that store their saves and settings in the user''s folder this is not a problem, but for those that store their saves and settings in the install folder, this becomes an issue. To help with this, you can add additional paths that will be searched when a game config specifies %INSTALLLOCATION%.  For this to work you must install the games while only changing the Program Files folder to the new location. For example, American McGee''s Alice installs to:\r\n<code lang="text">\r\n   C:\\Program Files\\EA GAMES\\American McGee''s Alice\r\n</code></p>\r\n<p>Now let''s say you keep your games in F:\\Games. You would add F:\\Games to the Alternate Install Paths and make sure that Alice installs to:\r\n<code lang="text">\r\n   F:\\Games\\EA GAMES\\American McGee''s Alice\r\n</code></p>\r\n<p>MASGAU will automatically eliminate folders starting at the beginning of the root to check variations of the install location. For example, the above will also try:\r\n<code lang="text">\r\n   F:\\Games\\American McGee''s Alice\r\n</code></p>\r\n<p>Of course if there is a registry entry pointing to the game, this is a moot point.</p>\r\n\r\n<h3>Make Extra Backups</h3>\r\n<p>Click this button and every so often MASGAU will switch to a new archive when backing up, leaving an archive containing saves only up to that time.</p>\r\n\r\n<thumb src="using/backup.jpg" width="200" alt="The Backup Tab"/>\r\n<h2>Backing Up</h2>\r\n<p>To back up all the detected games, just click ''''Back Everything Up''''. To back up a single game, select it''s name from the list and click ''''Back This Up''''. To back up multiple selected games, just select the games you want then click ''''Back These Up''''. To exercise futility, click ''''Back Nothing up''''. ''''Redetect Games'''' uses my patent-pending "Really Obvious Naming™" technology. If you hover the mouse over a detected game, a ToolTip will display containing all the detected roots for the game, as well as any comments pertinent to the game.</p>\r\n\r\n<p>The checkbox in the backup column controls whether that game is backed up when clicking "Back Everything Up", as well as if the game is watched by the Monitor. When running in All Users mode, it also controls the game being backed up by the scheduled task backup.</p>\r\n\r\n<thumb src="using/backupcontextmenu.jpg" width="200" alt="Backup Tab''s Context Menu"/>\r\n<h3>Context Menu</h3>\r\n<p>New for 0.4! You can right-click in the detected list to reveal a context menu providing per-game functions and options.</p>\r\n\r\n<thumb src="using/manualbackup.jpg" width="200" alt="The Custom Backup Window"/>\r\n<h4>Create Custom Archive</h4>\r\n<p>MASGAU can create an archive using only the files you specify. Perfect for sending a save or two to a friend, or uploading onto a save sharing site!</p>\r\n<h4>Back This/These Up</h4>\r\n<p>A duplication of the button below the backup list.</p>\r\n<h4>Purge</h4>\r\n<p>Ever have a game leave saves behind after it''s uninstalled? Then purge is for you! This will try to wipe whatever was detected from your hard drive. If you don''t have the right permissions it''ll try anyway and let you know if it was successful or not. This option is obviously the most dangerous thing in MASGAU, so use it with caution. I accept no responsibility if you accidentally delete your 60-hour Mass Effect save, but you should feel bad anyway for playing it for so long.</p>\r\n<h4>Enable Backup</h4>\r\n<p>Provides a way to enable/disable multiple game backups. Or one at a time if you feel like wasting time.</p>\r\n\r\n<thumb src="using/backupprogress.jpg" width="200" alt="A Backup In Progress"/>\r\n<h3>Backup Progress</h3>\r\n<p>While backing up, a progress bar appears at the bottom along with a Stop button. The Stop button that will terminate the backup job after the current file has been archived. Please don''t pid-kill MASGAU while it''s backing up, it can leave a mess.</p>\r\n\r\n<thumb src="using/restore.jpg" width="200" alt="The Restore Tab"/>\r\n\r\n<h2>Restoring</h2>\r\n<p>MASGAU makes 2 kinds of archives: user and global. For user the name of the user is recorded in the archive''s file name, in the form of GameName«UserName.gb7. Global are only named after the game, as in GameName.gb7. All detected restores will appear in a list in the ''''Restore'''' tab. To restore an archive, find the game in the list. Then click the plus sign next to the game to show all the archives related to the game. There will never be more than one ''''Global'''' archive, but it can contain the saves from several users depending on how the game saves. Just double click a ''''Global'''' or one of the users and it will attempt to restore it. If you double-click a user, it will ask you what user you want to restore it to. Most restores require that the game is detected by the backup system, so that MASGAU can figure out where the saves need to go.</p>\r\n\r\n<p>If you have a .gb7 file, such as one a friend e-mailed to you, you should be able to just double-click it and it will be restored to your system.</p>\r\n\r\n<p>New for 0.8! The Restore tab now has a "Restore Other Save(s)" button, allowing you to browse to one or more saves and restore it/them.</p>\r\n\r\n<p>New for 0.9! Saves are also sorted out by platform and region, so that saves that are not compatible with another platform or region will not be restored to the wrong location. Saves are also sorted into types, so that Settings, Saves, and other various types of files will be separated into different archives.</p>\r\n\r\n<thumb src="using/schedule.jpg" width="200" alt="The Schedule Tab"/>\r\n<h2>Scheduling</h2>\r\n<p>This tab will only appear under All Users Mode. Here you can set a schedule to automatically run backups. In order to be able to see all the saves on the system, the task requires the username and password of an admin user. It will automatically enter the name of the admin that it''s running as, but you must provide a password to add or change the task. This sadly does not work on Windows XP Home.</p>\r\n\r\n<thumb src="using/about.jpg" width="200" alt="The About Tab"/>\r\n<h2>About</h2>\r\n<p>This tab shows the current version, and a complete list of all the contributors, along with a count and tooltip-list of all the games they''ve contributed information on. Also has a link to the MASGAU web page at the bottom.</p>\r\n\r\n<thumb src="using/masgaumonitor.jpg" width="200" alt="Desktop Running Monitor"/>\r\n<h2>MASGAU Monitor</h2>\r\n<p>New for 0.4! MASGAU Monitor is a tray application that watches detected save game roots for changes. When one is reported, the file is grabbed as soon as the game releases it and archived. Together with the "Extra Backups" option this can be used to create multiple backups of savegames in real-time. Handy if your save gets corrupted. <p>\r\n\r\n<thumb src="using/monitorcontext.jpg" width="200" alt="The Monitor''s Context Menu"/>\r\n<p>Right-clicking the Monitor icon opens a context menu providing several options. ''''Rescan Games'''' will make Monitor re-scan the system for games, as well as refresh any settings that may have been changed in MASGAU''s other modules. Clicking ''''Exit'''' will shut down the Monitor.</p>\r\n\r\n<thumb src="using/monitorgameslist.jpg" width="200" alt="The Monitor''s Game List"/>\r\n<p>Clicking ''''Settings...'''' in the context menu will open the Monitor-specific settings window. The first tab is a list of all the detected games, along with checkboxes to control monitor''s behavior. The Backup column controls whether Monitor will watch that game for saves to be archived (this setting is shared with the Task and Main module). The sync column controls MASGAU''s newest feature:</p>\r\n\r\n<h3>Synchronization</h3>\r\n<thumb src="using/monitorsettings.jpg" width="200" alt="The Monitor''s Settings Tab"/>\r\n<p>New for 0.9! MASGAU Monitor can also has a synchronization function that maintains an identical folder structure of all the sync-enabled game saves. Just enable the games you want to sync the saves for, pick the sync path, and MASGAU will copy all those games saves into that folder (sorted into subfolders by game of course). If anything changes in either the save folder OR the sync folder, MASGAU will make the same change happen to the other folder. With this, you can put your sync folder in something like a dropbox folder, then do the same on another computer, keeping your saves in sync between computers.</p>\r\n\r\n<thumb src="using/analyzer.jpg" width="200" alt="The Analyzer"/>\r\n<h2><a name="analyzer">Analyzer</a></h2>\r\n<p>Analyzer is a tool for gathering information about unsupported games so I can add them to MASGAU. Use is simple (sort of):\r\n<thumb src="using/analyzerscanning.jpg" width="200" alt="Analyzer Scanning A Game"/>\r\n<ol>\r\n<li>Type in the name of the game.\r\n<li>Use the buttons to specify where the game is installed.\r\n<li>Use the buttons to specify where the game keeps its saves.\r\n<li>Click the Scan button.\r\n<li>Wait for the scan to finish.\r\n<thumb src="using/analyzerreport.jpg" width="200" alt="An Analyzer Report"/>\r\n<li>(Optional) Edit the report\r\n<li>Click the E-Mail button to send me the report.\r\n<li>Do something else.\r\n</ul></p>\r\n<p>Note: Submitted reports are uploaded to the GitHub server, and can be browsed at <a href="https://github.com/MASGAU/MASGAU/tree/data/Reports">https://github.com/MASGAU/MASGAU/tree/data/Reports</a></p>', ''),
('cloud', 'Cloud Backups', 'Dropbox is a wonderful program that creates a special folder on your computer. Anything you put in that folder gets magically transferred onto the Internet. If you set MASGAU to put its backups in the Dropbox folder, then all your save archives will be immediately uploaded for safe storage. If you install Dropbox with the same account on a different computer, it will download everything that has been uploaded, effectively creating the exact same folder on the second computer. If you use Dropbox with MASGAU, this means that you can have all your saves automatically appear on all your computers!\r\n\r\n<h3><a href="https://www.getdropbox.com/referrals/NTE3NzU2NDk">Click here to get started</a></h3>', ''),
('contribute', 'How to Contribute', 'There are a few ways you can contribute, and here''s the order of priority as far as need goes:\r\n<ol>\r\n<li>Game Information!<br />\r\nMASGAU works in conjunction with <a href="http://gamesave.info">GameSave.Info</a> to provide a comprehensive save information database AND backup solution (fancy words!). If you contribute save information to GameSave.Info, it''ll automatically be in MASGAU, and vice-versa. There''s a couple ways you can do this:\r\n<ol>\r\n<li>Use MASGAU''s Analyzer component to submit game information. This is the easiest for you, as it only takes a couple clicks, then I do all the heavy lifting of adding it to the database</li>\r\n<li>E-mail me your custom XML. This requires you to write your own XML, the format of which is explained over at <a href="http://gamesave.info/xml_format.php">the GameSave.Info site</a>. MASGAU will read from game info from any XML file in the data folder, so you can add as many as you want! While testing, you should probably not add any entries to the built-in XML files, so you don''t risk breaking your MASGAU install.</li>\r\n<li>Commit your own XML into <a href="https://github.com/GameSaveInfo/Data">GameSave.Info''s GitHub Repository</a>. This still requires you to write your own XML, but it also takes advantage of GitHub''s excellent peer review process. Just go to the repository, fork it into your own account (register one if you don''t already have one, they''re free!) and then commit all you want! As long as it''s in your own fork, you run no risk of damaging GameSave.Info''s master data files, so you can experiment all you want! I watch people''s forks and offer tips and corrections as they work, so you don''t have to worry about making any mistakes! Once your new data is ready, issue a pull request back into GameSaveInfo/Data. Once I approve the pull, your data will be part of GameSave.Info!</li>\r\n</ol>\r\n</li>\r\n<li>Programming<br />\r\nMASGAU stil needs plenty of work! All you need to do is for it over in GitHub, then start hacking away!\r\n</li>\r\n</ol>', ''),
('analyzer', 'How To Use The Analyzer', 'TBW', ''),
('how', 'How It Works', 'Everything I had here doesn''t apply any more. I''ll re-write this when I have the time.', ''),
('xml_spec', 'How To Add New Games', '<p>MASGAU uses the <a href="http://gamesave.info/xml_format.php">GameSave.Info XML 2.0 format</a>. That link should provide all the info you need to add your own games. Unfortunately, it''s quite complicated! To counter this, MASGAU includes an analyzer component, which (with your help) automatically scans your computer for all the info needed to add a game to the GameSave.Info database (which also means adding it to MASGAU)! It''ll even e-mail it to me with the click of a button!', ''),
('home', 'Home', '<table class="home_page_feature">\r\n<tr>\r\n<td>\r\n<h3>What Is MASGAU?</h3>\r\nMASGAU is a program that can automatically backup AND restore your game saves! Imagine: never losing a save just because of something silly like a system crash* or the apocalypse*.\r\n</td>\r\n<td>\r\n<h3>How Do I Get It?</h3>\r\nClick the giant Download button up above! Or visit the  <module name="downloads">download page</module> to see all the available versions!\r\n</td>\r\n<td>\r\n<h3>What Games Are Supported?</h3>\r\nCheck out the <module name="support">Compatibility Table</module>! It lists all the games that MASGAU supports, and on which platforms!\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>\r\n<h3>Where Can I Go For Help?</h3>\r\n<ul>\r\n<li>The <a href="http://forums.masgau.org/">forums</a>!\r\n<li>Tweeting <a href="http://twitter.com/#!/masgau">@MASGAU</a>!\r\n<li>Posting on <a href="http://www.facebook.com/masgau">Facebook</a>!\r\n<li><a href="https://github.com/MASGAU/MASGAU/issues/new">Add an issue on GitHub</a>!\r\n<li><a href="mailto:masgau@masgau.org">E-Mail Me</a>!\r\n</ul>\r\n</td>\r\n<td>\r\n<h3>How Can I Help?</h3>\r\nBy submitting game information! MASGAU includes an <page name="using" target="analyzer">Analyzer</page> that makes sending me game info a snap! And a breeze! A crisp, refreshing breeze through your nethers.\r\n</td>\r\n<td>\r\n<h3>What Does It Look Like?</h3>\r\n<page name="using">\r\n<img src="images.php?name=using%2Fbackup.jpg&width=200" alt="Like This!" />\r\n<br />Like this! Click to see more pictures!\r\n</page>\r\n</td>\r\n</tr>\r\n</table>', '* MASGAU cannot magically keep your saves safe if something happens to the location you tell it to put your saves. If your external drive dies in a fire, obviously so will your saves. A horrible painful death that should make you feel ashamed.');

-- --------------------------------------------------------

--
-- Table structure for table `xml_versions`
--

CREATE TABLE IF NOT EXISTS `xml_versions` (
  `id` int(11) NOT NULL,
  `major` int(11) NOT NULL,
  `minor` int(11) NOT NULL,
  `string` varchar(10) NOT NULL,
  `exporter` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `major` (`major`,`minor`),
  UNIQUE KEY `string` (`string`),
  KEY `exporter` (`exporter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `xml_versions`
--

INSERT INTO `xml_versions` (`id`, `major`, `minor`, `string`, `exporter`) VALUES
(1, 1, 1, '1.1', 'MASGAU11'),
(2, 2, 0, '2.0', 'GameSaveInfo20');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `program_versions`
--
ALTER TABLE `program_versions`
  ADD CONSTRAINT `program_versions_ibfk_1` FOREIGN KEY (`xml_version`) REFERENCES `xml_versions` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
