<?php
/**
 * 获取已上传的文件列表
 * User: ssly
 * Date: 14-10-20
 * Time: 上午11:21
 */

include_once('oss_sdk.class.php');  //引入 OSS 类
/* 判断类型 */
switch ($_GET['action']) {
    /* 列出文件 */
    case 'listfile':
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];
        break;
    /* 列出图片 */
    case 'listimage':
    default:
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

/* 获取参数 */
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : "";
$prefix = isset($_GET['prefix']) ? htmlspecialchars($_GET['prefix']) : "";
$end = $start + $size;

/* 获取文件列表 */
$oss = new ALIOSS();;  //初始化oss
$options = array(
		'delimiter' => '/',
		'prefix' => $prefix,
		'max-keys' => $size,
		'marker' => $start,
	);
	
$bucket='0597house';
$response = $oss->list_object($bucket,$options);
$xml = new SimpleXMLElement($response->body);
//print_r($xml);die;
$list = array();

if ($prefix<>""){
	  $list[] = array('flag'=>'path','url'=>"../",'mtime'=>'');
}
foreach($xml->CommonPrefixes as $k=>$v){
   $list[] = array('flag'=>'path','url'=>"$v->Prefix",'mtime'=>'');
}
foreach($xml->Contents as $k=>$v){
	 $list[] = array('flag'=>'file','url'=>"$v->Key",'mtime'=>$v->LastModified);
}

$start=$xml->NextMarker;
if (!count($list)) {
    return json_encode(array(
        "state" => "no match file",
        "list" => array(),
        "start" => $start,
        "total" => count($list)
    ));
}


/* 返回数据 */
$result = json_encode(array(
    "state" => "SUCCESS",
    "list" => $list,
    "start" => "$start",
    "total" => count($list)
));
return $result;

