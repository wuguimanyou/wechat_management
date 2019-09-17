<?php
header("Content-type: text/html; charset=utf-8"); //test
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");
require('../auth_user.php');
$key_id=$configutil->splash_new($_GET["key_id"]);	
if($key_id){
$query="select * from weixin_commonshop_distributor_article where id=$key_id";
$re=mysql_query($query);
while($row=mysql_fetch_object($re)){
	$title=$row->title;
	$description=$row->description;
	$share_description=$row->share_description;
	$share_img=$row->share_img;
	$p_id=$row->p_id;
}	


}

$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}
$query = 'SELECT id,appid,appsecret,access_token FROM weixin_menus where isvalid=true and customer_id='.$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
$access_token="";
while ($row = mysql_fetch_object($result)) {
	$keyid =  $row->id ;
	$appid =  $row->appid ;
	$appsecret = $row->appsecret;
	$access_token = $row->access_token;
	break;
}
//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(paytime)=".$year." and month(paytime)=".$month." and day(paytime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

$query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}

$search_user_id=-1;
if(!empty($_GET["search_user_id"])){
   $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if(!empty($_POST["search_name"])){
    $search_name = $configutil->splash_new($_POST["search_name"]);
}
$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if(!empty($_POST["search_phone"])){
    $search_phone = $configutil->splash_new($_POST["search_phone"]) ;
}
$search_name_type=1;	//1为搜索微信名称 2为搜索收货名称
if(!empty($_GET["search_name_type"])){		
    $search_name_type = $configutil->splash_new($_GET["search_name_type"]);
}
if(!empty($_POST["search_name_type"])){
    $search_name_type = $configutil->splash_new($_POST["search_name_type"]);
}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}

?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content<?php echo $theme; ?>.css"> 
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/product.js"></script>
<script type="text/javascript" src="../common/utility.js"></script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="/weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束--->
<style type="text/css" media="screen">#PicUploadUploader {visibility:hidden}</style>
<body>

<style type="text/css">
body, html{background:url(images/main-bg.jpg) left top fixed no-repeat;}
#cke_1_contents{
	min-height: 500px!important;
}
.title_input{
	width: 280px;
    height: 28px;
    line-height: 28px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: #F5F5F5;
    text-indent: 3px;
    margin-top: 5px;
    font-size: 12px;
}
</style>
<div id="iframe_page">
	<div class="iframe_content">
	<link href="operamasks-ui.css" rel="stylesheet" type="text/css">
	<link href="css/shop.css" rel="stylesheet" type="text/css">

	<div class="r_nav">
		<ul>
			<li id="auth_page0" class=""><a href="base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a></li>
			<li id="auth_page1" class=""><a href="fengge.php?customer_id=<?php echo $customer_id_en; ?>">风格设置</a></li>
			<li id="auth_page2" class=""><a href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&default_set=1">首页设置</a></li>
			<li id="auth_page3" class=""><a href="product.php?customer_id=<?php echo $customer_id_en; ?>">产品管理</a></li>
			<li id="auth_page4" class=""><a href="order.php?customer_id=<?php echo $customer_id_en; ?>&status=-1">订单管理</a></li>
			<?php if($is_supplierstr){?><li id="auth_page5" class=""><a href="supply.php?customer_id=<?php echo $customer_id_en; ?>">供应商</a></li><?php }?>
			<?php if($is_distribution){?><li id="auth_page6" class=""><a href="agent.php?customer_id=<?php echo $customer_id_en; ?>">代理商</a></li><?php }?>
			<li id="auth_page7" class=""><a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>">推广员</a></li>
			<li id="auth_page8" class="cur"><a href="customers.php?customer_id=<?php echo $customer_id_en; ?>">顾客</a></li>
			<li id="auth_page9"><a href="shops.php?customer_id=<?php echo $customer_id_en; ?>">门店</a></li>
			<?php if($isOpenPublicWelfare){?><li id="auth_page10"><a href="publicwelfare.php?customer_id=<?php echo $customer_id_en; ?>">公益基金</a></li><?php }?>
			<li id="auth_page10" class="cur"><a href="distributor_article.php?customer_id=<?php echo $customer_id_en; ?>">单品推广文章列表</a></li>
		</ul>
	</div>
<div id="products" class="r_con_wrap">

<form id="frm_product" class="r_con_form" method="post" action="save_distritor_article.php" enctype="multipart/form-data" onsubmit="return check_submit()">
		<div class="rows">
			<label>文章标题</label>
			<span class="input">
			  <input id="title" class="title_input"  name="title" value="<?php echo $title; ?>">
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>分享描述</label>
			<span class="input">
			  <input id="share_description" class="title_input"   name="share_description" value="<?php echo $share_description; ?>">
			</span>
			<div class="clear"></div>
		</div>
		<div class="WSY_member rows">
                    <label style="margin-left:-20px;">分享图片</label>
                    <div class="WSY_memberimg input" style="width:300px">
						<?php if($share_img){ $share_img=substr($share_img,1); ?>
						<img src="<?php echo $share_img ?>" style="width:100px;height:100px;">	
						<?php }else{ ?>
								<img src="../common/images_V6.0/table_icon/photo.png" style="width:100px;height:100px;">
						<?php } ?>
						
                        <span>(建议尺寸：宽240px,图片大小不大于500k)</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly/>
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile" id="upfile" type=file value="<?php echo $imgurl ?>">

                        </div>
                        <!--上传文件代码结束-->
                    </div>
					<div class="clear"></div>
        </div>
		<div class="rows">
			<label>关联产品</label>
			<span class="input">
					  <?php 
						$query = "select id,name from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id;
						$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
					?>
					  <select name="p_id" id="p_id">
						<option value="" >--请选择一个单品--</option>
					<?php 
						while ($row = mysql_fetch_object($result)) {
							$r_p_id = $row->id;
							$r_name = $row->name;
							
					?>
						 <option value="<?php echo $r_p_id; ?>" <?php if($p_id == $r_p_id){ ?>selected<?php } ?>><?php echo $r_name; ?></option>
					<?php } ?>
					</select>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>编辑单品文章</label>
			<span class="input">
			  <textarea id="editor1"   name="description"><?php echo $description; ?></textarea>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
		<label></label>
		<input type="hidden" name="key_id" value="<?php echo $key_id ;?>" />
		<input type="hidden" name="customer_id" value="<?php echo $customer_id_en ;?>" />
		<span class="input"><input type="submit" class="btn_green"  name="submit_button" value="提交保存">
		<a  class="btn_gray" onclick="history.go(-1)">返回</a></span>
		<div class="clear"></div>
	</div>
</form></div>	</div>
<div>

<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/js/handlers.js"></script> 
<!--编辑器多图片上传引入结束--->
<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
function check_submit(){
	var title=$('#title').val();
	var p_id=$('#p_id').val();
	if(!title){
		alert("请输入标题");
		return false;	
	}else if(!p_id){
		alert("请选择一个关联产品");
		return false;
		
	}
}
</script>
<?php 

mysql_close($link);
?>
</div></div></body></html>
