<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * e107 Blank Plugin
 *
*/
if (!defined('e107_INIT'))
{
	require_once("../../class2.php");
}


require_once(e_PLUGIN.'files/files.class.php');

e107::css('files', 'files.css');

$filesFront = new pluginFiles;
$filesFront->init();
require_once(HEADERF); 					// render the header (everything before the main content area)
$ret = $filesFront->render();

$ns->tablerender($ret['caption'], $ret['text']);
require_once(FOOTERF);					// render the footer (everything after the main content area)
exit; 

// For a more elaborate plugin - please see e107_plugins/gallery

?>