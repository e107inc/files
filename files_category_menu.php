<?php
	/**
	 * e107 website system
	 *
	 * Copyright (C) 2008-2016 e107 Inc (e107.org)
	 * Released under the terms and conditions of the
	 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
	 *
	 */

if (!defined('e107_INIT')) { exit; }
require_once(e_PLUGIN.'files/files.class.php');
$filesF = new pluginFiles;
$filesF->setMode('menu');
$ret = $filesF->render();

if(!empty($parm['caption'][e_LANGUAGE]))
{
	$ret['caption'] = $parm['caption'][e_LANGUAGE];
}


e107::getRender()->tablerender($ret['caption'], $ret['text']);