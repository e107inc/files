<?php

// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

// e107::lan('files',true);


class files_adminArea extends e_admin_dispatcher
{

	protected $modes = array(	
	
		'main'	=> array(
			'controller' 	=> 'files_ui',
			'path' 			=> null,
			'ui' 			=> 'files_form_ui',
			'uipath' 		=> null
		),
		

	);	
	
	
	protected $adminMenu = array(

		 'main/prefs'		=> array('caption'=>LAN_PREFS, 'perm' => 'P')
	);

	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'				
	);	
	
	protected $menuTitle = 'Files';
}




				
class files_ui extends e_admin_ui
{
			
		protected $pluginTitle		= 'Files';
		protected $pluginName		= 'files';
	//	protected $eventName		= 'files-'; // remove comment to enable event triggers in admin. 		
		protected $table			= '';
		protected $pid				= '';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
	//	protected $batchCopy		= true;		
	//	protected $sortField		= 'somefield_order';
	//	protected $orderStep		= 10;
	//	protected $tabs				= array('Tabl 1','Tab 2'); // Use 'tab'=>0  OR 'tab'=>1 in the $fields below to enable. 
		
	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.
	
		protected $listOrder		= ' DESC';
	
		protected $fields 		= array();
		
		protected $fieldpref = array();
		

	//	protected $preftabs        = array('General', 'Other' );
		protected $prefs = array(
			'page_title'        => array('title'=>'Page Title', 'type'=>'text', 'data'=>'str', 'multilan'=>true, 'writeParms'=>array('size'=>'xxlarge')),
			'page_header'        => array('title'=>'Page Header', 'type'=>'bbarea', 'data'=>'str', 'multilan'=>true, 'writeParms'=>array('size'=>'xxlarge')),
			'file_order'        => array('title'=>'File Order', 'type'=>'dropdown', 'data'=>'str', 'writeParms'=>array()),

		);

	
		public function init()
		{
			$this->prefs['file_order']['writeParms']['optArray'] = array(
				'media_caption asc'=>'Media Caption ASC',
				'media_caption desc'=>'Media Caption DESC',
				'media_datestamp asc'=>'Media Datestamp ASC',
				'media_datestamp desc'=>'Media Datestamp DESC',
				'media_type asc'=>'Media Type ASC',
			);
		}

		
		// ------- Customize Create --------
		
		public function beforeCreate($new_data,$old_data)
		{
			return $new_data;
		}
	
		public function afterCreate($new_data, $old_data, $id)
		{
			// do something
		}

		public function onCreateError($new_data, $old_data)
		{
			// do something		
		}		
		
		
		// ------- Customize Update --------
		
		public function beforeUpdate($new_data, $old_data, $id)
		{
			return $new_data;
		}

		public function afterUpdate($new_data, $old_data, $id)
		{
			// do something	
		}
		
		public function onUpdateError($new_data, $old_data, $id)
		{
			// do something		
		}		
		
			
	/*	
		// optional - a custom page.  
		public function customPage()
		{
			$text = 'Hello World!';
			$otherField  = $this->getController()->getFieldVar('other_field_name');
			return $text;
			
		}
	*/
			
}
				


class files_form_ui extends e_admin_form_ui
{

}		
		
		
new files_adminArea();

require_once(e_ADMIN."auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN."footer.php");
exit;

?>