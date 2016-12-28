<?php


	/**
	 * e107 website system
	 *
	 * Copyright (C) 2008-2016 e107 Inc (e107.org)
	 * Released under the terms and conditions of the
	 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
	 *
	 */
	class pluginFiles
	{

		private $categories = array();
		private $currentCategory = null;
		private $mode= 'page';
		private $pageTitle = "Files";
		private $listLimit = 20;
		private $torrentMode = false;
		private $torrentTrackers = array();
		private $filesPref = array();

		function __construct()
		{

			$filesPref = e107::pref('files');

			if(!empty($filesPref['page_title'][e_LANGUAGE]))
			{
				$this->pageTitle = $filesPref['page_title'][e_LANGUAGE];
			}

			$this->categories = e107::getMedia()->getCategories('files');

			$this->filePref = $filesPref;
			$this->setTrackers();

			if(!empty($this->filePref['torrentMode']))
			{
				$this->torrentMode = true;
			}


		}

		private function setTrackers()
		{
			if(empty($this->filesPref['torrentTrackers']))
			{
				return false;
			}

			$tmp = explode($this->filesPref['torrentTrackers'],"\n");

			foreach($tmp as $val)
			{
				if(!empty($val))
				{
					$this->torrentTrackers[] = trim($val);

				}
			}

		}

		/**
		 * Detect $_GET and set
		 */
		public function init()
		{
			if(!empty($_GET['get']))
			{
				$this->sendFile($_GET['get']);
				exit;
			}



			if(!empty($_GET['catsef']))
			{
				$this->currentCategory = $this->getCategoryFromSef($_GET['catsef']);
			}


		}


		private function sendFile($id)
		{
			$id = intval($id);

			$sql = e107::getDb();
			if ($sql->select('core_media', 'media_id,media_name,media_caption,media_url', "media_id= ".$id." AND media_userclass IN (".USERCLASS_LIST.") LIMIT 1 "))
			{
				$row = $sql->fetch();
				// $file = $tp->replaceConstants($row['media_url'],'rel');

				if($this->torrentMode === true)
				{

					$this->sendTorrent($row);
					exit;
				}
				else
				{
					e107::getFile()->send($row['media_url']);
					exit;
				}


			}

			return false; 

		}


		private function sendTorrent($row)
		{

			require_once("Torrent.php");
			$oFile = basename($row['media_url']);
			$nFile = $oFile.".torrent";

			$path = e107::getFile()->getUserDir(false, true);

			if(file_exists($path.$nFile))
			{
				e107::getFile()->send($path.$nFile);
				return true;

			}


			$oFilePath = e107::getParser()->replaceConstants($row['media_url']);

			$name = vartrue($row['media_caption'],$row['media_name']);
			$parm = array('id'=>$row['media_id'], 'name'=> eHelper::title2sef($name,'dashl'));
			$url = e107::url('files', 'get', $parm);

			$torrent = new Torrent($oFilePath);
			$torrent->announce($this->torrentTrackers); // set tiered trackers
		//	$torrent->comment('hello world');
			$torrent->name($name);
			$torrent->is_private(false);
		//	$torrent->httpseeds('http://file-hosting.domain/path/'); // Bittornado implementation
		//	$torrent->url_list(array('http://file-hosting.domain/path/','http://another-file-hosting.domain/path/')); // GetRight implementation
			$torrent->httpseeds($url);
			$torrent->url_list($url);

			// print errors
			if( $errors = $torrent->errors())
			{
				e107::getDebug()->log($errors);
				return false;
			}

			$torrent->save($path.$nFile);
			$torrent->send();


			return true;

		}



		public function setMode($mode)
		{
			$this->mode = $mode;
		}


		public function setCategory($cat)
		{
			$this->currentCategory = $cat;
		}

		public function setLimit($int)
		{
			$this->listLimit = intval($int);
		}

		private function getCategoryFromSef($sef)
		{
			foreach($this->categories as $key=> $val)
			{
				if($sef == $val['media_cat_sef'])
				{
					return $key;
				}


			}

			return false;
		}


		/**
		 * Render the File Categories
		 * @todo add Template
		 * @return array
		 */
		public function categories()
		{

			$tp = e107::getParser();
			$filesPref = e107::pref('files',null,false);

			$text = $this->breadcrumb();

			if(!empty($filesPref['page_header'][e_LANGUAGE]) && $this->mode === 'page')
			{
				$text .= "<div class='files-page-header'>".$tp->toHtml($filesPref['page_header'][e_LANGUAGE],true)."</div>";
			}

			$text .= ($this->mode === 'page') ? "<div class='files-page'>" : "<div class='files-menu'>";
			$text .= "<ul class='categories'>";

			foreach($this->categories as $key=>$row)
			{
				$url = e107::url('files', 'category', $row);
				$text .= "<li><a href='".$url."'>".$row['media_cat_title']."</a></li>";
			}

			$text .= "</ul></div>";

			return array('caption'=>$this->pageTitle, 'text'=>$text);
		}


		private function breadcrumb()
		{
			if($this->mode != 'page')
			{
				return null;
			}


			$array = array(

				0 => array('text'=>"Files", 'url'=>e107::url('files', 'index'))
			);

			if(!empty($this->currentCategory))
			{
				$row = $this->categories[$this->currentCategory];
				$name = $row['media_cat_title'];
				$array[1]  = array('text'=>$name, 'url'=> e107::url('files', 'category', $row));
			}

			return e107::getForm()->breadcrumb($array);


		}


		private function getCategoryDescription()
		{

			if(!empty($this->categories[$this->currentCategory]['media_cat_diz']))
			{
				$text = $this->categories[$this->currentCategory]['media_cat_diz'];
				return e107::getParser()->toHtml($text);
			}

			return false;


		}


		/**
		 * @todo add Template
		 * @return array
		 */
		public function render()
		{



			if(empty($this->currentCategory))
			{
				return $this->categories();
			}

			$sql = e107::getDB(); 					// mysql class object
			$tp = e107::getParser(); 				// parser for converting to HTML and parsing templates etc.
			$frm = e107::getForm(); 				// Form element class.
			$ns = e107::getRender();				// render in theme box.
			$fl = e107::getFile();

			$filesPref = e107::pref('files');

			$text = $this->breadcrumb();

			if($categoryDiz = $this->getCategoryDescription() && $this->mode != 'menu')
			{
				$text .= $categoryDiz;
			}


			$orderby = vartrue($filesPref['file_order'],false);

			$data = e107::getMedia()->getImages($this->currentCategory,0,$this->listLimit,null,$orderby);

			$text .= "<div class='files-page'>";

			$text .= "<ul class='files'>";

			foreach($data as $id=>$row)
			{
				$name = vartrue($row['media_caption'],$row['media_name']);
				$parm = array('id'=>$id, 'name'=> eHelper::title2sef($name,'dashl'));
				$url = e107::url('files', 'get', $parm);
				$bbcode = "<a href='".$url."'>".$name."</a>";

				$text .= "<li>".$bbcode." <small class='text-muted'>(".$fl->file_size_encode($row['media_size'], false, 0).")</small>
							<div class='files-description'><small >".$tp->toHtml($row['media_description'],true)."</small></li>";

			}

			$text .= "</ul></div>";


			return array('caption'=>$this->pageTitle, 'text'=>$text);


		}




	}