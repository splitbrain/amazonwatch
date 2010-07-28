<?php
error_reporting(E_ALL ^ E_NOTICE);

define('BASE',dirname(__FILE__).'/../');

require_once(BASE.'config.php');
$PDO = new PDO('sqlite:'.BASE.'db/database.sqlite');

