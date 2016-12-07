<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2016 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *
*/

if (!defined('e107_INIT')) { exit; }

//v2.x Standard for extending menu configuration within Menu Manager. (replacement for v1.x config.php)


class files_menu
{

	function __construct()
	{

	}

	/**
	 * Configuration Fields.
	 * @return array
	 */
	public function config($menu='')
	{

		$fields = array();



		switch($menu)
		{
			case "files":
				$fields['caption']      = array('title'=> LAN_CAPTION, 'type'=>'text', 'multilan'=>true, 'writeParms'=>array('size'=>'xxlarge'));
				$fields['limit']        = array('title'=> LAN_LIMIT, 'type'=>'text', 'writeParms'=>array('pattern'=>'[0-9]*', 'size'=>'mini'));
				$fields['category']      = array('title'=> LAN_CATEGORY, 'type'=>'dropdown',  'writeParms'=>array('optArray'=>$this->getCategories()));

			break;


			case "files_category":
					$fields['caption']      = array('title'=> LAN_CAPTION, 'type'=>'text', 'multilan'=>true, 'writeParms'=>array('size'=>'xxlarge'));
			//		$fields['limit']        = array('title'=> LAN_LIMIT, 'type'=>'text', 'writeParms'=>array('pattern'=>'[0-9]*', 'size'=>'mini'));
			break;
		}

		 return $fields;




	}

	private function getCategories()
	{
		$data = e107::getDb()->retrieve('core_media_cat','*',"media_cat_owner='files'",true);

		$cats = array();

		foreach($data as $row)
		{
			$id = $row['media_cat_category'];
			$cats[$id] = $row['media_cat_title'];
		}

		return $cats;

	}


}



/*
// optional
class files_form extends e_form
{

	public function xxx($curVal)
	{

	}

}*/


?>