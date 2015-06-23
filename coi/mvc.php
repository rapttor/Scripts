<?php
/**
 * Created by JetBrains PhpStorm.
 * User: emir
 * Date: 10/20/12
 * Time: 9:26 AM
 * To change this template use File | Settings | File Templates.
 */
require_once "helpers.php";

$coiRoute = coiIf($_GET, "COI", null);
$_DATA = array();
$params = array();

if (!is_null($coiRoute)) {
    $params = explode('/', $coiRoute);
    foreach ($params as $k => $p)
        if (strpos($p, '=') > 0) {
            $t = explode('=', $p);
            $params[$t[0]] = $t[1];
        }
}

$controller = coiIf($params, 0, coiParam("defaultController", "home", false));
$action = coiIf($params, 1, coiParam("defaultAction", "index", false));
$parameters = array_merge($_REQUEST, $params);
$_REQUEST=$parameters;

function coiRun($c = null, $a = null, $parameters = array())
{
	global $controller, $action,$parameters;
	if (is_null($c)) $c=$controller;
	if (is_null($a)) $a=$action;
    $cFile = ROOT . 'controllers/' . $c . ".php";

    if (is_file($cFile)) {
        include_once $cFile;
        $class = ucfirst($controller);
        $run = new $class();
        //$iscall = array(null, $class . '::' . $action);
        $run->$a($parameters);
//        $iscall = array($class, $action);
//        if (method_exists($class, $action) && is_callable($iscall, true, $callable))
//            call_user_func($iscall, $parameters);
    } else echo "Controller not found.";
}

function coiRender($view, $data = null, $directory = 'views', $common = true)
{
    global $_DATA;
    
	$_DATA = $data;
    $partial = substr($view, 0, 1) == '_';
    $directory = ROOT . $directory;

    $file = $directory . '/' . $view . '.php';

    if (!is_null($data) && is_array($data)) extract($data);

    //if ($common && !$partial && is_file($temp = $directory . "/common/header.php")) require $temp;
    if ($return = is_file($file)) require $file;
    //if ($common && !$partial && is_file($temp = $directory . "/common/footer.php")) require $temp;
    return null;
}

function dbSetup($a)
{
    //$a[ tablename=[ $field_name=[type,size,default,valid(function), ] .. ] ... ]
}

function dbSql($a)
{
    // $a[operation=>table structure=[field=[type,size,default,valid(f)]] values=[key=>item] where=[field=value,...] order [field=>option] ]

}
