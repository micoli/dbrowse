<?php
namespace qd\dbrowse;

include 'plugin.php';

class dbrowse {
	var $map		= array();
	var $serverRoot	= '/';
	var $cachePath	= '/var/tmp';
	var $tpl		= 'template.php';
	var $aPlugins	= array();

	public function __construct($sServerRoot,$sCachePath,$aMap,$sTpl){
		$this->serverRoot	= $sServerRoot;
		$this->map			= $aMap;
		$this->cachePath	= $sCachePath;
		$this->tpl			= $sTpl;
		foreach(glob(dirname(__FILE__).'/plugin_*') as $plugin){
			include $plugin;
			$class = 'qd\\dbrowse\\'.str_replace('.php','',basename($plugin));
			$this->aPlugin[]=new $class($this);
		}
	}

	public function run (){
		$aResult = $this->_run();
		if(!$aResult['success']){
			die($aResult['error']);
		}
		require dirname(__FILE__).'/'.$this->tpl;
	}

	public function getRessourceLink($file){
		print sprintf("%s?r=%s",$this->serverRoot,$file);
	}

	public function getEmbededRessourceLink($file){
		$t = '';
		foreach($this->aPlugin as $plugin){
			if($t = $plugin->getThumb($file)){
				break;
			}
		}
		return $t==''?'':sprintf("%s?c=%s",$this->serverRoot,base64_encode($t));
	}

	public function _run(){
		$fileUri = urldecode($_SERVER['REQUEST_URI']);

		if(array_key_exists('r',$_REQUEST)){
			$file=realpath('./static/'.preg_replace('!(\.\.)|\\|\/]!','',$_REQUEST['r']));
			header('Content-Type: ' . $this->mime_content_type(basename($file)));
			die(file_get_contents($file));
		}

		if(array_key_exists('c',$_REQUEST)){
			$file=realpath(base64_decode($_REQUEST['c']));
			header('Content-Type: ' . $this->mime_content_type(basename($file)));
			die(file_get_contents($file));
		}

		$subUri	= preg_replace('!^'.preg_quote($this->serverRoot,'!').'!','',$fileUri);
		if(!preg_match('!^(.*?)\/(.*)$!',$subUri,$match)){
			return array(
				'success'	=> false,
				'error'		=> 'no (path/file) structure'
			);
		}
		$rootMap=$match[1];
		if(!array_key_exists($rootMap,$this->map)){
			return array(
				'success'	=> false,
				'error'		=> 'Path not found in map structure'
			);
		}
		$fileUri  = $match[2];
		$realPath = $this->map[$rootMap].'/'.$fileUri;
		if(!file_exists($realPath)){
			return array(
				'success'	=> false,
				'error'		=> 'file does not exists'
			);
		}
		if(is_file($realPath)){
			header('Content-type: '.$this->mime_content_type($realPath));
			header('Content-Disposition: inline; filename="'.basename($realPath).'"');
			die(file_get_contents($realPath));
		}else{
			$uppath= array();
			$aList = array();
			if(is_dir($realPath)){
				$uppath=array(
					'name'	=> '..',
					'href'	=> '../',
					'mtime'	=> date('Y-m-d H:i:s',filemtime($realPath)),
				);
				$aPaths = glob($realPath.'/*');
				sort($aPaths);
				foreach($aPaths as $file){
					$file = realpath($file);
					$base = basename($file);
					$href = urlencode($base);
					$isFile=true;
					if(is_dir($file)){
						$href.='/';
						$isFile=false;
					}
					$aList[] = array(
						'type'	=> $isFile?'file':'path',
						'name'	=> $base,
						'href'	=> $href,
						'stype'	=> $isFile?$this->mime_content_type(basename($file)):'dir',
						'mtime'	=> date('Y-m-d H:i:s',filemtime($file)),
						'size'	=> filesize($file),
						'thumb' => $this->getEmbededRessourceLink($file)
					);
				}
				return array (
					'thumb'		=> $this->getEmbededRessourceLink($realPath),
					'success'	=> true,
					'path'		=> $realPath,
					'uppath'	=> $uppath,
					'list'		=> $aList
				);
			}
		}
	}

	function mime_content_type($filename) {
		$idx = explode( '.', $filename );
		$count_explode = count($idx);
		$idx = strtolower($idx[$count_explode-1]);

		$mimet = array(
			'ai'		=> 'application/postscript',
			'aif'		=> 'audio/x-aiff',
			'aifc'		=> 'audio/x-aiff',
			'aiff'		=> 'audio/x-aiff',
			'asc'		=> 'text/plain',
			'atom'		=> 'application/atom+xml',
			'avi'		=> 'video/x-msvideo',
			'bcpio'		=> 'application/x-bcpio',
			'bmp'		=> 'image/bmp',
			'cdf'		=> 'application/x-netcdf',
			'cgm'		=> 'image/cgm',
			'cpio'		=> 'application/x-cpio',
			'cpt'		=> 'application/mac-compactpro',
			'crl'		=> 'application/x-pkcs7-crl',
			'crt'		=> 'application/x-x509-ca-cert',
			'csh'		=> 'application/x-csh',
			'css'		=> 'text/css',
			'dcr'		=> 'application/x-director',
			'dir'		=> 'application/x-director',
			'djv'		=> 'image/vnd.djvu',
			'djvu'		=> 'image/vnd.djvu',
			'doc'		=> 'application/msword',
			'dtd'		=> 'application/xml-dtd',
			'dvi'		=> 'application/x-dvi',
			'dxr'		=> 'application/x-director',
			'eps'		=> 'application/postscript',
			'etx'		=> 'text/x-setext',
			'ez'		=> 'application/andrew-inset',
			'gif'		=> 'image/gif',
			'gram'		=> 'application/srgs',
			'grxml'		=> 'application/srgs+xml',
			'gtar'		=> 'application/x-gtar',
			'hdf'		=> 'application/x-hdf',
			'hqx'		=> 'application/mac-binhex40',
			'html'		=> 'text/html',
			'html'		=> 'text/html',
			'ice'		=> 'x-conference/x-cooltalk',
			'ico'		=> 'image/x-icon',
			'ics'		=> 'text/calendar',
			'ief'		=> 'image/ief',
			'ifb'		=> 'text/calendar',
			'iges'		=> 'model/iges',
			'igs'		=> 'model/iges',
			'jpe'		=> 'image/jpeg',
			'jpeg'		=> 'image/jpeg',
			'jpg'		=> 'image/jpeg',
			'js'		=> 'application/x-javascript',
			'kar'		=> 'audio/midi',
			'latex'		=> 'application/x-latex',
			'm3u'		=> 'audio/x-mpegurl',
			'man'		=> 'application/x-troff-man',
			'mathml'	=> 'application/mathml+xml',
			'me'		=> 'application/x-troff-me',
			'mesh'		=> 'model/mesh',
			'mid'		=> 'audio/midi',
			'midi'		=> 'audio/midi',
			'mif'		=> 'application/vnd.mif',
			'mov'		=> 'video/quicktime',
			'movie'		=> 'video/x-sgi-movie',
			'mp2'		=> 'audio/mpeg',
			'mp3'		=> 'audio/mpeg',
			'mpe'		=> 'video/mpeg',
			'mpeg'		=> 'video/mpeg',
			'mpg'		=> 'video/mpeg',
			'mpga'		=> 'audio/mpeg',
			'ms'		=> 'application/x-troff-ms',
			'msh'		=> 'model/mesh',
			'mxu m4u'	=> 'video/vnd.mpegurl',
			'nc'		=> 'application/x-netcdf',
			'oda'		=> 'application/oda',
			'ogg'		=> 'application/ogg',
			'pbm'		=> 'image/x-portable-bitmap',
			'pdb'		=> 'chemical/x-pdb',
			'pdf'		=> 'application/pdf',
			'pgm'		=> 'image/x-portable-graymap',
			'pgn'		=> 'application/x-chess-pgn',
			'php'		=> 'application/x-httpd-php',
			'php4'		=> 'application/x-httpd-php',
			'php3'		=> 'application/x-httpd-php',
			'phtml'		=> 'application/x-httpd-php',
			'phps'		=> 'application/x-httpd-php-source',
			'png'		=> 'image/png',
			'pnm'		=> 'image/x-portable-anymap',
			'ppm'		=> 'image/x-portable-pixmap',
			'ppt'		=> 'application/vnd.ms-powerpoint',
			'ps'		=> 'application/postscript',
			'qt'		=> 'video/quicktime',
			'ra'		=> 'audio/x-pn-realaudio',
			'ram'		=> 'audio/x-pn-realaudio',
			'ras'		=> 'image/x-cmu-raster',
			'rdf'		=> 'application/rdf+xml',
			'rgb'		=> 'image/x-rgb',
			'rm'		=> 'application/vnd.rn-realmedia',
			'roff'		=> 'application/x-troff',
			'rtf'		=> 'text/rtf',
			'rtx'		=> 'text/richtext',
			'sgm'		=> 'text/sgml',
			'sgml'		=> 'text/sgml',
			'sh'		=> 'application/x-sh',
			'shar'		=> 'application/x-shar',
			'shtml'		=> 'text/html',
			'silo'		=> 'model/mesh',
			'sit'		=> 'application/x-stuffit',
			'skd'		=> 'application/x-koan',
			'skm'		=> 'application/x-koan',
			'skp'		=> 'application/x-koan',
			'skt'		=> 'application/x-koan',
			'smi'		=> 'application/smil',
			'smil'		=> 'application/smil',
			'snd'		=> 'audio/basic',
			'spl'		=> 'application/x-futuresplash',
			'src'		=> 'application/x-wais-source',
			'sv4cpio'	=> 'application/x-sv4cpio',
			'sv4crc'	=> 'application/x-sv4crc',
			'svg'		=> 'image/svg+xml',
			'swf'		=> 'application/x-shockwave-flash',
			't'			=> 'application/x-troff',
			'tar'		=> 'application/x-tar',
			'tcl'		=> 'application/x-tcl',
			'tex'		=> 'application/x-tex',
			'texi'		=> 'application/x-texinfo',
			'texinfo'	=> 'application/x-texinfo',
			'tgz'		=> 'application/x-tar',
			'tif'		=> 'image/tiff',
			'tiff'		=> 'image/tiff',
			'tr'		=> 'application/x-troff',
			'tsv'		=> 'text/tab-separated-values',
			'txt'		=> 'text/plain',
			'ustar'		=> 'application/x-ustar',
			'vcd'		=> 'application/x-cdlink',
			'vrml'		=> 'model/vrml',
			'vxml'		=> 'application/voicexml+xml',
			'wav'		=> 'audio/x-wav',
			'wbmp'		=> 'image/vnd.wap.wbmp',
			'wbxml'		=> 'application/vnd.wap.wbxml',
			'wml'		=> 'text/vnd.wap.wml',
			'wmlc'		=> 'application/vnd.wap.wmlc',
			'wmlc'		=> 'application/vnd.wap.wmlc',
			'wmls'		=> 'text/vnd.wap.wmlscript',
			'wmlsc'		=> 'application/vnd.wap.wmlscriptc',
			'wmlsc'		=> 'application/vnd.wap.wmlscriptc',
			'wrl'		=> 'model/vrml',
			'xbm'		=> 'image/x-xbitmap',
			'xht'		=> 'application/xhtml+xml',
			'xhtml'		=> 'application/xhtml+xml',
			'xls'		=> 'application/vnd.ms-excel',
			'xml xls'	=> 'application/xml',
			'xpm'		=> 'image/x-xpixmap',
			'xslt'		=> 'application/xslt+xml',
			'xul'		=> 'application/vnd.mozilla.xul+xml',
			'xwd'		=> 'image/x-xwindowdump',
			'xyz'		=> 'chemical/x-xyz',
			'zip'		=> 'application/zip'
		);

		if (isset( $mimet[$idx] )) {
			return $mimet[$idx];
		} else {
			return 'application/octet-stream';
		}
	}
}