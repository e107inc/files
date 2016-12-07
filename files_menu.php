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

if(empty($parm['category']))
{
	if(ADMIN)
	{
		e107::getRender()->tablerender("Files", "<div class='alert alert-warning'>No category set in Media-Manager configuration.</div>");
	}
	return;
}

require_once(e_PLUGIN.'files/files.class.php');
$filesFront = new pluginFiles;
$filesFront->setMode('menu');
$filesFront->setCategory($parm['category']);

if(isset($parm['limit']))
{
	$filesFront->setLimit($parm['limit']);
}


$ret = $filesFront->render();


if(!empty($parm['caption'][e_LANGUAGE]))
{
	$ret['caption'] = $parm['caption'][e_LANGUAGE];
}

e107::getRender()->tablerender($ret['caption'], $ret['text']);