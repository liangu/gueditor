<?php
/**
 * 获取已上传的文件列表
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
//include "Uploader.class.php";

/* 判断类型 */
switch ($_GET['action']) {
    /* 列出文件 */
    case 'list-file':
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        break;
    /* 列出图片 */
    case 'list-image':
    default:
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

/* 获取参数 */
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
$prefix = isset($_GET['prefix']) ? htmlspecialchars($_GET['prefix']) : "/$newdir";
$end = $start + $size;

/* 获取文件列表 */
$path = $cfg_basedir .(substr($usr_basedir, 0, 1) == "/" ? "":"/").$usr_basedir."/".$prefix;
$files = getfiles($path, $allowFiles);
$list=array();
if ($start==0) $list[] = array('flag'=>'path','url'=>"$prefix/..",'mtime'=>'');

/* 获取指定范围的列表 */
$len = count($files);
for ($i = min($end, $len) - 1; $i < $len && $i >= 0 && $i >= $start; $i--){
    $list[] = $files[$i];
}

if (!count($list)) {
    return json_encode(array(
        "state" => "no match file",
        "list" => array(),
        "start" => $start,
        "total" => count($files)
    ));
}
//倒序
//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

/* 返回数据 */
$result = json_encode(array(
    "state" => "SUCCESS",
    "list" => $list,
    "start" => $start,
    "total" => count($files)
));

return $result;


/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
		global $cfg_basedir,$usr_basedir;
    if (!is_dir($path)) return null;
    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $path2 = $path . $file;
            if (is_dir($path2)) {
            	  $files[] = array(
                    		'flag'=> 'path',
                        'url'=> substr($path2, strlen($cfg_basedir)+strlen($usr_basedir)+1),
                        'mtime'=> filemtime($path2)
                );
                //getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                    $files[] = array(
                    		'flag'=> 'file',
                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime'=> filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}