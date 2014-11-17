<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";
/* 上传配置 */
$base64 = "upload";
$activepath.="/";
switch (htmlspecialchars($_GET['action'])) {
    case 'upload-image':
        $config = array(
            "pathFormat" => $activepath.$CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'upload-scrawl':
        $config = array(
            "pathFormat" => $activepath.$CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'upload-video':
        $config = array(
            "pathFormat" => $activepath.$CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'upload-file':
    default:
        $config = array(
            "pathFormat" => $activepath.$CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}

/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);
$res=$up->getFileInfo();
$imgfile_type=$res["type"];
$fullfilename=$cfg_basedir.$res["url"];
$filename=$res["title"];
$sizes[0] = 0; $sizes[1] = 0;
$mediatype=4;
if(strpos(".png.jpg.jpeg.gif.bmp",$imgfile_type)>=0){
	$mediatype=1;
	require_once(dirname(__FILE__)."/../../image.func.php");
	if($resize==1)
	{
		ImageResize($fullfilename, $iwidth, $iheight);
	}
	else
	{
		WaterImg($fullfilename, 'up');
	}
	$info = '';
	$sizes = getimagesize($fullfilename, $info);
}
$imgsize = $res["size"];
$imgwidthValue = $sizes[0];
$imgheightValue = $sizes[1];
$inquery = "INSERT INTO `#@__uploads`(arcid,title,url,mediatype,width,height,playtime,filesize,uptime,mid)
VALUES ('0','$filename','".$activepath."/".$filename."',".$mediatype.",'$imgwidthValue','$imgheightValue','0','{$imgsize}','{$nowtme}','".$cuserLogin->getUserID()."'); ";
$dsql->ExecuteNoneQuery($inquery);
$fid = $dsql->GetLastID();
AddMyAddon($fid, $activepath.'/'.$filename);
/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
return json_encode($res);
