<?php
/**
 * Created by JetBrains PhpStorm.
 * User: emir
 * Date: 10/20/12
 * Time: 9:28 AM
 * To change this template use File | Settings | File Templates.
 */
$coi_start = microtime(true);
error_reporting(E_ALL ^ E_NOTICE);
ini_set('error_reporting', E_ALL);

if (!isset($_SESSION)) session_start();

if (!isset($_SESSION["COI"])) $_SESSION["COI"] = array();

foreach ($_REQUEST as $k => $i) 
if (!is_numeric($i)) $_REQUEST[$k] = htmlspecialchars($i, ENT_QUOTES);

foreach ($_POST as $k => $i) 
if (!is_numeric($i)) $_POST[$k] = htmlspecialchars($i, ENT_QUOTES);

foreach ($_GET as $k => $i) 
if (!is_numeric($i)) $_GET[$k] = htmlspecialchars($i, ENT_QUOTES);
$coiConfig = array();

$serverIP=$_SERVER["SERVER_ADDR"];
$dbConfig=$coiConfig;
if(isset($coiConfig[$serverIP])) $dbConfig=$coiConfig[$serverIP];

if (defined("CONFIG") && is_file(CONFIG)) 
{
    $coiConfig = parse_ini_file(CONFIG, true);    
    foreach ($coiConfig as $k => $i) 
    if ($k == strtoupper($k) && (is_scalar($i))) define($k, $i);
    $_CONFIG = $coiConfig;
}

$data = array();

function __coiAutoload($class, $root = null) 
{
    
    if (is_null($root) && defined("COIROOT")) $root = COIROOT;
    
    if (file_exists($root . '/models/' . $class . '.php')) 
    {
        require_once ($root . '/models/' . $class . '.php');
        
        return true;
    }
    else 
    if (strpos($class, 'Controller') !== false) 
    {
        
        if (file_exists($root . '/controllers/' . $class . '.php')) 
        {
            require_once ($root . '/controllers/' . $class . '.php');
            
            return true;
        }
    }
    
    return false;
}
spl_autoload_register('__coiAutoload');
include_once "helpers.php";

