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

		function __construct()
		{
			$this->categories = e107::getMedia()->getCategories('files');

			if(!empty($_GET['catsef']))
			{
				$this->currentCategory = $this->getCategoryFromSef($_GET['catsef']);
			}

			$filesPref = e107::pref('files');

			if(!empty($filesPref['page_title'][e_LANGUAGE]))
			{
				$this->pageTitle = $filesPref['page_title'][e_LANGUAGE];
			}

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

			$text .= "<div class='files-page'>";
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