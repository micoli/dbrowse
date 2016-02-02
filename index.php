<?php
include 'dbrowse.php';
use \qd\dbrowse\dbrowse;

$cb = new dbrowse('/browse/','/var/tmp',array(
	'media'=>'/mnt'
),'tpl.php');
$cb->run();