<?php
namespace qd\dbrowse;

class plugin_pdf  extends plugin{
	function getThumb($file){
		if(strtolower(substr($file,-4))!=='.pdf'){
			return '';
		}
		$hash = $this->dbrowseInst->cachePath.'/'.md5($file);
		$tfile = $hash.'-000.jpg';
		if(!file_exists($tfile)){
			$cmd = sprintf('/usr/bin/pdfimages -f 1 -l 1 -j "%s" "%s"',$file,$hash);
			exec($cmd);
			$cmd = sprintf('/usr/bin/convert "%s" -resize 100x160 "%s"',$tfile,$tfile);
			exec($cmd);
		}
		return $tfile;
	}
}