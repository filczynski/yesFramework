<?php
/*
Author: Marcin Romanowicz
URL: http://yesframework.com/
License: MIT
Version: 1.5.0
*/

//check PHP Version
if(phpversion()<'5.5.24' ){
    echo 'Your version of PHP is '.phpversion().'. Required min. 5.5.24';
    exit;
}


//start session
session_start();

//autloader
function ClassLoader($className)
{
    require('../'.$className.'.class.php');
    return true;
} 
spl_autoload_register('ClassLoader');

//load helpers
require_once('../Core/helper.php');

//register functions as base
use Core\Classess\Base;
use Core\Classess\Db;
$base = new Base();

//load config
require_once('../core/config.php');

//PDO connect
if(strlen(DBNAME)>0){
	if(PORT==''){$port_nr='';}else{$port_nr=';port='.PORT;}
	if(DBTYPE==0){$database_type='mysql:';}elseif(DBTYPE==1){$database_type='pgsql:';}else{echo 'error database type';exit;}

	try {
		  $PDO = new PDO($database_type.'host='.HOST.$port_nr.';dbname='.DBNAME, DBUSER, DBPASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                  if(strlen(PGSQLSCHEMA)>0){$PDO->exec('SET search_path TO '.PGSQLSCHEMA);}
	}

	catch(PDOException $e) {
		 echo 'Connection error: ' . $e->getMessage();
	}
}

//AntiCSRF
if(!isset($_SESSION['csrf'])){
	$csrf = hash('sha256',uniqid('yF', true).rand(1,1000));
	$_SESSION['csrf'] = $csrf;
}


//start
if(!isset($_GET['page'])){
        require_once('../Apps/Controllers/'.CONTROLLER);
}
else
{	
        $dir = scandir('../Apps/Controllers/');
        $dir_ok=0;
        foreach ($dir as $check_dir){
                $check_dir = str_replace('.php','',$check_dir);
                if($check_dir==$_GET['page']){$dir_ok=$check_dir;}
        }
        if($dir_ok!='0'){
                require_once('../Apps/Controllers/'.$dir_ok.'.php');
                }else
                {
                    redirect('ErrorPages/404.html');
                }

}

	



?>