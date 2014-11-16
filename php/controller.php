<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
//date_default_timezone_set("Asia/chongqing");
//error_reporting(E_ERROR);
//header("Content-Type: text/html; charset=utf-8");
require_once(dirname(__FILE__)."/../../common.inc.php");
require_once(dirname(__FILE__)."/../../userlogin.class.php");
require_once(dirname(__FILE__)."/../../memberlogin.class.php");
$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];
list($act,$filepath)=explode("-",$action);
$cuserLogin = new userLogin();
$usr_basedir="";
if($cuserLogin->getUserID() <=0 )//非管理员
{
		$cfg_ml = new MemberLogin(-1);
		$mid=$cfg_ml->M_ID;
		if ($mid>0){
		    $activepath = $cfg_user_dir."/".$mid;
		    $usr_basedir= $activepath;
		}else{
				die("请登录后使用!");
		};
}else{
		$activepath=$cfg_medias_dir."/".$filepath;
		$usr_basedir=$activepath;
	  $nowtme = time();
	  $newdir = MyDate($cfg_addon_savetype, $nowtme);
    $activepath = $activepath.'/'.$newdir;
}
if(!is_dir($cfg_basedir.$activepath))
{
    MkdirAll($cfg_basedir.$activepath,$cfg_dir_purview);
}
switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'upload-image':
    /* 上传涂鸦 */
    case 'upload-scrawl':
    /* 上传视频 */
    case 'upload-video':
    /* 上传文件 */
    case 'upload-file':
        $result = include("dede_upload.php");
        break;

    /* 列出图片 */
    case 'list-image':
        $result = include("dede_list.php");
        break;
    /* 列出文件 */
    case 'list-file':
        $result = include("dede_list.php");
        break;

    /* 抓取远程文件 */
    case 'catch-image':
        $result = include("dede_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}