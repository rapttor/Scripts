<?php
/**
 * Created by JetBrains PhpStorm.
 * User: emir
 * Date: 10/20/12
 * Time: 9:28 AM
 * To change this template use File | Settings | File Templates.
 */
$coiFieldNum = 0;
$_LANGUAGE = array();
$coiStartTime = microtime(true);
$coiLastTime = $coiStartTime;
$paramters=array();

coiDefineEOL();
/* FACEBOOK table=(id(ID/U),data(LONGTEXT))*/

$fbDataTypes=explode(',','me,home,feed,likes,friends,groups,movies,books,notes,permissions,photos,videos,checkins,locations');
$fbDataBasic=explode
(',','me');

function fbDataTypes() { global $fbDataTypes; return $fbDataTypes; }
function fbDataBasic() { global $fbDataBasic; return $fbDataBasic; }

function fbFriendIDs($facebook) {
	$list=fbUserFriends($facebook);
	foreach($facebook as $k=>$i) {}
}
/* fql */

function fbUserPicture($user) {
	return 'https://graph.facebook.com/' . $user . '/picture';
}

function fbFriends($facebook, $user, $fields='uid,name', $options='') {
	$params = array(
	'method' => 'fql.query',
	'query' => "SELECT $fields FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = ".$user.") ".$options,
	);
	$result = $facebook->api($params);
	return $result;
}

/* standard */
function fbUserToken($facebook) {
	return $facebook->getAccessToken();
}

function fbUserData($facebook, $type, $token=null) {
	if (is_null($token)) $token=fbUserToken($facebook);
	$data=array();
	if (is_array($type)) {
		foreach($type as $t)
		$data[$t]=fbUserData($facebook, $t, $token);
	} else {
		if ($type=='me') $type='';else $type='/'.$type;
		try {
			$data=$facebook->api( '/me'.$type, 'GET', array( 'access_token=' => $token ) );
		}
    	catch(FacebookApiException $e) 
    	{
    		echo "<br/>$e<br/>";
    		$data=null;
    	}
    }
    return $data;
}

function fbUserGroups($facebook) {
	return fbUserData($facebook, 'groups');
}

function fbUserFeed($facebook) {
	return fbUserData($facebook, 'feed');
}

function fbUserHome($facebook) {
	return fbUserData($facebook, 'home');
}


function fbUserLikes($facebook) {
	return fbUserData($facebook, 'likes');
}

function fbUserMovies($facebook) {
	return fbUserData($facebook, 'movies');
}

function fbUserBooks($facebook) {
	return fbUserData($facebook, 'books');
}

function fbUserNotes($facebook) {
	return fbUserData($facebook, 'notes');
}

function fbUserPermissions($facebook) {
	return fbUserData($facebook, 'permissions');
}

function fbUserPhotos($facebook) {
	return fbUserData($facebook, 'photos');
}

function fbUserAlbums($facebook) {
	return fbUserData($facebook, 'albums');
}

function fbUserVideos($facebook) {
	return fbUserData($facebook, 'videos');
}

function fbUserUploaded($facebook) {
	return fbUserData($facebook, 'videos/uploaded');
}

function fbUserEvents($facebook) {
	return fbUserData($facebook, 'events');
}

function fbUserCheckins($facebook) {
	return fbUserData($facebook, 'checkins');
}

function fbUserLocations($facebook) {
	return fbUserData($facebook, 'locations');
}

function fbUserFriends($facebook) {
	return $facebook->api('/me/friends');
}

function fbUser($facebook) {
	return $facebook->api('/me');
}

function fbUserMe($facebook) {
	return fbUser($facebook);
}

function fbGetAll($facebook, $types=null) {
	if (is_null($types)) $types=fbDataTypes();
	
	$token=fbUserToken($facebook);
	$data=array();
	$data["token"]=$token;
	
	foreach($types as $i) {
		$func='fbUserData';
		$k=$i;
		if ($i=='me') $i=''; 
		$data[$k]=$func($facebook,$i,$token);	
	}
	return $data;
}

/* OTHERS */
function coiDefineEOL()
{
if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) 
{
    define("EOL", "\r\n");
}
elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) 
{
    define("EOL", "\r");
}
else
{
    define("EOL", "\n");
}
}

function __dbConnect($coiConfig) 
{
	if (!isset($coiConfig["db_host"]) && is_array($coiConfig)) {
		$coiConfig=array(
			"db_host"=>$coiConfig[0],
			"db_username"=>$coiConfig[1],
			"db_password"=>$coiConfig[2],
			"db_database"=>$coiConfig[3]
		);
	}
    if (isset($coiConfig["db_host"]) && isset($coiConfig["db_username"]) && isset($coiConfig["db_password"]) && isset($coiConfig["db_database"])) 
    {
        $db = mysql_connect($coiConfig["db_host"], $coiConfig["db_username"], $coiConfig["db_password"]) or die(mysql_error());
        mysql_select_db($coiConfig["db_database"], $db);
    }
}

function coiMail($from, $to, $subject, $message) 
{
    $headers = 'MIME-Version: 1.0' . EOL;
    $headers.= 'Content-type: text/html; charset=UTF-8' . EOL;
    
    if (is_array($to)) 
    {
        $headers.= 'To: ';
        
        foreach ($to as $k => $i) 
        {
            
            if (is_numeric($k)) $k = $i;
            $headers.= " $k <$i>,";
        }
        $headers = substr($headers, 0, strlen($headers) - 1);
        $headers.= EOL;
        $to = $from;
    }
    else $headers.= 'To: ' . $to . ' <' . $to . '>' . EOL;
    $headers.= 'From: ' . $from . ' <' . $from . '>' . EOL;
    $headers.= "X-Priority: 1 (Higuest)" . EOL;
    $headers.= "X-MSMail-Priority: High" . EOL;
    $headers.= "Importance: High" . EOL;
    
    return mail($to, $subject, $message, $headers);
}

function coiRunning4($title = null, $full = true) 
{
    global $coiStartTime, $coiLastTime;
    $r = microtime(true);
    
    if (!is_null($title)) 
    if ($full) 
    {
        echo " " . $title . " <b>" . number_format($r - $coiStartTime, 2) . "</b> D: <b>" . number_format($r - $coiLastTime, 2) . "</b> <br/>";
    }
    else
    {
        echo $title . number_format($r - $coiStartTime, 2);
    }
    $coiLastTime = $r;
    
    return $r - $coiStartTime;
}

function coiIf($a, $key, $def = null) 
{
    
    if (is_array($a) && isset($a[$key])) $def = $a[$key];
    
    return $def;
}




function htmlExtractLinks($string) 
{
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/g";
    
    if (preg_match_all($reg_exUrl, $string, $url)) 
    {
        
        return $url;
    }
    else 
    return false;
}

function htmlIsValidEmail($email) 
{
    return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
}

function htmlIsPrivateIpf($ip) 
{
    
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
}

function htmlExtractEmail($string, $type = 0) 
{
    $emails = null;
    
    switch ($type) 
    {
    case 1:
        
        foreach (preg_split('/ /', $string) as $token) 
        {
            $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL) , FILTER_VALIDATE_EMAIL);
            
            if ($email !== false) 
            {
                $emails[] = $email;
            }
        }
    break;
    default:
        $regEx = "/([\s]*)[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i";

        /*
        #(?<=/)[^\s@]+@[^\s@](?=\s$)#
        #(?<=/)[^\s@]+@[^\s@](?=_)#
        
        */
        
        if (preg_match_all($regEx, $string, $emails)) 
        {
            $emails = $emails[0];
            $mails = array();
            
            foreach ($emails as $k => $i) 
            if (htmlIsValidEmail(trim($i))) $mails[] = trim($i);
            $emails = array_unique($mails);
        }
    }
    
    return $emails;
}

function coiParam($key, $default = null, $save = true, $force=false) 
{
    global $coiConfig, $parameters;
    
    if (!$force) {
    if (isset($_REQUEST[$key])) $default = $_REQUEST[$key];
    else
    if (isset($parameters[$key])) $default = $parameters[$key];
    else 
    if (isset($_SESSION["COI"][$key])) $default = $_SESSION["COI"][$key];
    else 
    if (is_array($coiConfig) && isset($coiConfig[$key])) $default = $coiConfig[$key];
    }
    
    if ($save || $force) $_SESSION["COI"][$key] = $default;
    
    return $default;
}

function coiSql($sql) 
{
    $a = null;
    $command = strtoupper(substr($sql, 0, strpos($sql, ' ')));
    $r = mysql_query($sql);
    
    if ($r) 
    switch ($command) 
    {
    case "INSERT":
        $a = mysql_insert_id();
    break;
    case "UPDATE":
    break;
    case "DELETE":
        $a = mysql_affected_rows();
    break;
    case "CREATE":
        $a = $r;
    break;
    case "SELECT":
        $a = array();
        
        if (is_resource($r)) 
        while ($row = mysql_fetch_assoc($r)) 
        {
            
            if (isset($row["id"])) $a[$row["id"]] = $row;
            else $a[] = $row;
        }
        $limit = strtolower(trim(substr($sql, strlen($sql) - 7, 7)));
        
        if ($limit == 'limit 1') 
        {
            $temp = $a;
            
            foreach ($temp as $i) $a = $i;
        }
        
        break;
    }
    
    return $a;
}

//insget

function coiInsertGet($table, $values, $get = "id") 
{
    $where = '';
    
    foreach ($values as $k => $i) $where.= "`$k`='$i' and";
    $where = substr($where, 0, strlen($where) - 4);
    $sql = 'select `' . $get . '` from ' . $table . ' where ' . $where . ' limit 1';
    $r = coiSql($sql);
    
    if (isset($r) && isset($r[$get])) 
    {
        
        return $r[$get];
    }
    else
    {
        $sql = 'insert into `' . $table . '` (`' . implode('`,`', array_keys($values)) . '`) values ("' . implode('","', $values) . '")';
        
        return coiSql($sql);
    }
}

function coiGo($url) 
{
?>
<script language="javascript">self.location = "<?=$url?>";</script>
<?php
}

function coiJS($lib, $print = true) 
{
    $JSLibs = array(
        'angular' => 'ajax.googleapis.com/ajax/libs/angularjs/1.0.2/angular.min.js',
        'chromeframe' => 'ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js',
        'dojo' => 'ajax.googleapis.com/ajax/libs/dojo/1.8.0/dojo/dojo.js',
        'ext' => 'ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core.js',
        'jquery' => 'ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js',
        'jqueryui' => 'ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js',
        'jqtools' => 'cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js',
        'jqmobile' => 'jquerymobile.com/demos/1.1.1/js/jquery.mobile-1.1.1.js',
        'moo' => 'ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js',
        'prototype' => 'ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js',
        'scriptaculous' => 'ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js',
        'swfobject' => 'ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js',
        'webfonts' => 'ajax.googleapis.com/ajax/libs/webfont/1.0.31/webfont.js',
        'googlemaps' => 'maps.google.com/maps/api/js?sensor=true',
        'zepto' => 'zeptojs.com/zepto.min.js',
    );
    
    if (isset($JSLibs[$lib])) 
    {
        $return = '<script src="http://' . $JSLibs[$lib] . '"></script>' . "\n";
        
        if ($print) echo $return;
    }
    else $return = array_values(array_flip($JSLibs));
    
    return $return;
}

function jpegFileIsComplete($path) 
{
    
    if (!is_resource($file = fopen($path, 'rb'))) 
    {
        
        return FALSE;
    }

    // check for the existence of the EOI segment header at the end of the file
    
    if (0 !== fseek($file, -2, SEEK_END) || "\xFF\xD9" !== fread($file, 2)) 
    {
        fclose($file);
        
        return FALSE;
    }
    fclose($file);
    
    return TRUE;
}

function imageCreateFromAny($filepath, $type = null) 
{
    
    if (is_null($type)) $type = getImageSize($filepath); // [] if you don't have exif you could use getImageSize()

    
    if (isset($type[2])) 
    switch ($type[2]) 
    {
    case 2:
        $im = @imagecreatefromjpeg($filepath); //jpeg file

        
        
    break;
    case 1:
        $im = @imagecreatefromgif($filepath); //gif file

        
        
    break;
    case 3:
        $im = @imagecreatefrompng($filepath); //png file

        
        
    break;
    case 4:
        $im = @imagecreatefrombmp($filepath); //bmp file

        
        
    break;
    default:
        $im = false;
    }
    
    return $im;
}

function coiGetImage($url, $local, $minWH = 30) 
{
    $ret = false;
    $img_size = getimagesize($url);
    
    if (!isset($img_size["mime"]) || $img_size["0"] < $minWH || $img_size["1"] < $minWH) 
    return false;
    $save_img = imageCreateFromAny($url, $img_size);
    
    if (!($save_img == false)) $ret = @imagejpeg($save_img, $local);
    
    return $ret;
}

function coiGetSiteThumbnail($url, $local, $size = "l", $minWH = 100) 
{
    $img_url = 'http://immediatenet.com/t/' . $size . '?Size=1024x768&URL=' . $url;
    $ret = false;
    $ret = coiGetImage($img_url, $local, $minWH);
    
    if (!$ret) 
    {
        $img_url = 'http://open.thumbshots.org/image.aspx?url=' . $url;
        $ret = coiGetImage($img_url, $local);
    }
    
    return $ret;
}

function coiConvert($data = null, $format = "formats", $level = 0) // level is eternal


{
    $level++;
    
    if (is_null($data) && $format = "formats") 
    return explode(',', 'obj,xml,json,array,text,csv,ulli,table,html');
    $return = '';
    
    if (is_object($data)) $data = (array)$data;
    
    if (is_array($data)) 
    switch (strtolower($format)) 
    {
    case "obj":
        $return = unserialize(serialize($data));
    break;
    case "xml":
        
        foreach ($data as $k => $v) $return.= (is_string($v) || is_numeric($v)) ? "<$k>$v</$k>" : "<$k>" . coiConvert($v, $format) . "</$k>";
        
        break;
    case "json":
        
        return json_encode($data);
        
        break;
    case "array":
        
        foreach ($data as $k => $v) 
        if (is_object($v)) $data[$k] = coiConvert($v, $format);
        $return = $data;
        
        break;
    case "text":
        
        foreach ($data as $k => $v) $return.= implode("\t", (array)$v) . "\n";
        
        break;
    case "csv":
        
        foreach ($data as $k => $v) $return.= '"' . implode('","', (array)$v) . '"' . "\n";
        
        break;
    case "ulli":
        
        foreach ($data as $k => $v) 
        if (is_string($v) || is_numeric($v)) $return.= '<li>' . $v . '</li>';
        else $return.= "<ul>" . (!is_numeric($k) ? "<span class='title'>$k</span>" : "") . coiConvert($v, $format) . "</ul>";
        
        break;
    case "table":
        $first = true;
        
        foreach ($data as $k => $v) 
        {
            
            if ($first) 
            {
                $return.= "<table>";
                $temp = array_flip((array)$v);
                $return.= '<tr><th>' . implode('</th><th>', $temp) . '</th></tr>';
                $first = false;
            }
            $return.= '<tr><td>' . implode('</td><td>', (array)$v) . '</td></tr>';
        }
        
        if (!$first) $return.= "</table>";
        
        break;
    case "html":
        
        foreach ($data as $k => $v) 
        if (is_string($v) || is_numeric($v)) $return.= '<p>' . $v . '</p>';
        else $return.= "<ul>" . (!is_numeric($k) ? "<h$level>$k</h$level>" : "") . coiConvert($v, $format, $level) . "</ul>";
        
        break;
    }
    $level--;
    
    return $return;
}

function language($lngfile) 
{
    global $_LANGUAGE;
    
    if (is_file($lngfile)) $_LANGUAGE = parse_ini_file($lngfile);
}

function T($term) 
{
    global $_LANGUAGE;
    
    return (is_array($_LANGUAGE) && isset($_LANGUAGE[$term])) ? $_LANGUAGE[$term] : $term;
}

function coiRegister($email = null, $password = null, $table = 'users') 
{
    $user = coiLogin($email, $password, $table);
    
    if (is_null($user)) 
    {
        $sql = new MYSQL();
    }
}

function coiLogin($email = null, $password = null, $table = 'users') 
{
    
    if (!is_null($email) && !is_null($password) && strlen($email) > 1 && strlen($password) > 1) 
    {
        $user = coiSql('select * from `users` where `email`="' . $email . '" and `password`="' . sha1($password) . '" limit 1');
        
        if (!is_null($user) && isset($user["email"]) && $user["email"] == $email) 
        {
            
            if (isset($_POST['rememberme'])) 
            {

                /* Set cookie to last 1 year */
                setcookie('username', $email, time() + 60 * 60 * 24 * 365, '/account', '.' . $_SERVER["SERVER_NAME"]);
                setcookie('password', sha1($password) , time() + 60 * 60 * 24 * 365, '/account', '.' . $_SERVER["SERVER_NAME"]);
            }
            else
            {

                /* Cookie expires when browser closes */
                setcookie('username', $email, false, '/account', '.' . $_SERVER["SERVER_NAME"]);
                setcookie('password', sha1($password) , false, '/account', '.' . $_SERVER["SERVER_NAME"]);
            }
            define("USER", serialize($user));

            //header('Location: index.php');
            
        }
        else
        {
            echo T('Username/Password combination Invalid');
        }
    }
    else
    {
        
        if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) 
        {
            $user = sql('select * from users where `email`="' . $_COOKIE["username"] . '" and `password`="' . $_COOKIE["password"] . '" limit 1');
            
            if (!is_null($user) && $user["email"] == $email) define("USER", serialize($user));
        }
    }
}

function coiLogout() 
{
    setcookie("username", "", time() - 60000, '/account', '.' . $_SERVER["SERVER_NAME"]);
    setcookie("password", "", time() - 60000, '/account', '.' . $_SERVER["SERVER_NAME"]);
}

function coiLogged() 
{
    
    return (defined("USER")) ? (array)unserialize(USER) : array(
        "id" => 1,
        "role" => 100,
        "email" => "Emir"
    );
}

function A($link, $title = null) 
{
    
    if (is_null($title)) $title = ucfirst($link);
    
    return '<a href="' . $link . '">' . T($title) . '</a>';
}

function coiForm($fields) 
{
    $return = '';
    
    foreach ($fields as $k => $i) $return.= F($i);
    
    return $return;
}

function F($a) 
{
    global $coiFieldNum;
    $name = coiIf($a, "name", 'field' . ($coiFieldNum++));
    $title = coiIf($a, "title", ucfirst($name));
    $value = coiIf($a, "value", coiIf($_POST, $name, ''));
    $hint = coiIf($a, "hint", '');
    $wrap = coiIf($a, "wrap", true);
    $prefix = coiIf($a, "prefix", '');
    $suffix = coiIf($a, "suffix", '');
    $description = coiIf($a, "description", null);
    $type = coiIf($a, "type", "text");
    $return = '';
    if ($wrap) $return.='<div class="field">';
    switch (strtoupper($type)) 
    {
    case "TEXTAREA":
        $return.= '<label for="' . $name . '" >' . T($title) . '</label>
                ' . $prefix . '<textarea class="'.$type.'" id="' . $name . '" type="' . $type . '" name="' . $name . '" placeholder="' . T($hint) . '">' . $value . '</textarea>' . $suffix;
    break;
    case "SELECT":
        $return.= '<label for="' . $name . '" >' . T($title) . '</label>
            ' . $prefix . '<select class="'.$type.'" name="' . $name . '">';
        
        if (!is_array($value)) $value = explode(',', $value);
        
        foreach ($value as $k => $i) $return.= '<option value="' . $k . '">' . T($i) . '</option>';
        $return.= '</select>' . $suffix;
        
        break;
    default:
        
        if (isset($type) && in_array($type, array(
            "checkbox",
            "radiobox"
        ))) 
        {
        $return .= $prefix.
        '<input class="'.$type.'" id="' . $name . '" type="' . $type . '" name="' . $name . '" value="' . $value . '" placeholder="' . T($hint) . '"/>'.
        '<label class="'.$type.'" for="' . $name . '" >' . T($title) . '</label>' . $suffix;
        }
        else
        {
            $return .= (strtolower($type) == "submit" ? '' : '<label class="'.$type.'" for="' . $name . '" >' . T($title) . '</label>') . '
        ' . $prefix . '<input class="'.$type.'" id="' . $name . '" type="' . $type . '" name="' . $name . '" value="' . $value . '" placeholder="' . T($hint) . '"/>' . $suffix;
        }
    }
    
    if (!is_null($description)) $return.= '<span class="description">' . $description . '</span>';
    
    if ($wrap) $return.='</div>';
    
    return $return;
}

function C($key, $default = null) 
{
    global $coiConfig;
    $return = $default;
    
    if (!is_null($key) && isset($coiConfig[$key])) $return = $coiConfig[$key];
    
    return $return;
}

function M($items) 
{
    $return = '';
    
    foreach ($items as $k => $i) 
    if (is_array($i)) 
    {
        $return.= '<ul>' . M($i) . '</ul>';
    }
    else
    {
        $return.= '<li><a href="' . $k . '">' . T($i) . '</a></li>';
    }
    
    return $return;
}

function coiDebug($print = true) 
{
    global $_DATA, $controller, $action;
    $result = " <br/>CONTROLLER/ACTION: <b>$controller/$action</b><hr/>";
    $result.= '<h3>REQUEST</h3><hr>' . var_export($_REQUEST, true);
    $result.= '<h3>POST</h3><hr>' . var_export($_POST, true);
    $result.= '<h3>GET</h3><hr>' . var_export($_GET, true);
    $result.= '<h3>DATA</h3><hr>' . var_export($_DATA, true);
    $result.= '<h3>COOKIE</h3><hr>' . var_export($_COOKIE, true);
    $result.= '<h3>SESSION</h3><hr>' . var_export($_SESSION, true);
    $result.= '<h3>SERVER</h3><hr>' . var_export($_SERVER, true);
    $result.= '<h3>DEBUGBACKTACE</h3><hr>' . var_export(debug_backtrace() , true);
    
    if ($print) echo $result;
    
    return $result;
}

function gallery($imgdir = 'images/') 
{
    $a_img = array();
    $allowed_types = array(
        'png',
        'jpg',
        'jpeg',
        'gif'
    ); //Allowed types of files

    $dimg = @opendir('.' . $imgdir); //Open directory

    
    if ($dimg) 
    while ($imgfile = readdir($dimg)) 
    {
        
        if (in_array(strtolower(substr($imgfile, -3)) , $allowed_types) OR in_array(strtolower(substr($imgfile, -4)) , $allowed_types)) /*If the file is an image add it to the array*/

        
        {
            $a_img[] = $imgfile;
        }
    }
    echo "<ul class='gallery'>";
    $totimg = count($a_img); //The total count of all the images

    //Echo out the images and their paths incased in an li.

    
    for ($x = 0;$x < $totimg;$x++) 
    {
        echo "<li>
                <a target=_blank rel='lightbox'  href='" . $imgdir . $a_img[$x] . "'>
                <img src='" . $imgdir . $a_img[$x] . "' />
            </a></li>";
    }
    echo "</ul>";
}

function htmlDivContent($html, $id) 
{
    $pattern = "/<([\w]+)([^>]?) (([\s]/>)| (>((([^<]?|<!--.?-->)| (?R)))</\1[\s]>))/xsm";
    
    if (preg_match_all($pattern, $html, $matches)) 
    {
        print_r($matches);
    }
    else echo "$html";

    //    preg_match("/<div .* id=\"{$id}\" .*>(.*)<\\/div>/i", $html, $matches);
    //    var_dump($matches);

    //    return isset($matches[1])?$matches[1]:null;

    
}

