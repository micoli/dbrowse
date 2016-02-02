<?php
namespace qd\dbrowse;

class plugin_dir extends plugin{
	function thumb($file){
		$hash = $this->dbrowseInst->cachePath.'/'.md5($file).'.jpg';
		if(!file_exists($hash)){
			$cmd = sprintf('/usr/bin/convert "%s" -resize 100x160 "%s"',$file,$hash);
			exec($cmd);
		}
		return $hash;
	}

	function getThumb($file){
		if (file_exists($file.'/poster.jpg')){
			return $this->thumb($file.'/poster.jpg');
		}else if (file_exists($file.'/folder.jpg')){
			return $this->thumb($file.'/folder.jpg');
		}else if (file_exists($file.'/cover.jpg')){
			return $this->thumb($file.'/cover.jpg');
		}else{
			return '';
		}
	}
}