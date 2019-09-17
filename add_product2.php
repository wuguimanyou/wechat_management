<?php
//require('../logs.php');   
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
require('../../../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../proxy_info.php');
mysql_query("SET NAMES UTF8");
require('../../../common/utility_4m.php');
require('../../../common/tupian/CreateExpQR.php');
require('../../../auth_user.php');
$u4m = new Utiliy_4m();
$rearr = $u4m->is_4M($customer_id);

//是4m分销
$is_shopgeneral = $rearr[0]  ;
//厂家编号
$adminuser_id = $rearr[1] ;
//是否是厂家总店
$is_samelevel = $rearr[2] ;
//总店模板编号
$general_template_id = $rearr[3] ;
//总店商家编号
$general_customer_id = $rearr[4] ;

//是否本身就是厂家总店
//1：厂家总店； 2：代理商总店
$owner_general = $rearr[5] ;

$orgin_adminuser_id = $rearr[6] ;


/* echo $_SESSION['is_auth_user'].'==='.$_SESSION['user_id']; */
$stock_pidarr="";
if(!empty($_GET["stock_pidarr"])){		
	$stock_pidarr =$configutil->splash_new($_GET["stock_pidarr"]);
}
if(!empty($_GET["adminuser_id"])){	
	$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
}
if(!empty($_GET["orgin_adminuser_id"])){
	$orgin_adminuser_id = $configutil->splash_new($_GET["orgin_adminuser_id"]);
}
if(!empty($_GET["owner_general"])){
	$owner_general = $configutil->splash_new($_GET["owner_general"]);
}

$description="";
$product_id=-1;
$product_name="";
$product_orgin_price=0;
$product_now_price=0;
$product_type_id=-1;
$product_introduce="";
$product_description="";
$product_unit="";
$product_asort=-1;
$product_isout=0;
$product_isnew=0;
$product_ishot=0;
$product_tradeprices="";
$product_propertyids = "";
$product_storenum=1;
$product_need_score=0;

$product_cost_price=0;
$product_foreign_mark="";
$imgids="";

$pro_discount=0;
$pro_reward=0;
$issell=0;
$type_ids="";
$default_imgurl="";
$class_imgurl="";
$product_asort_value=0;
$sell_count=0;
$show_sell_count=999;
$define_share_image_flag=0;
$supply_id=-1;
$product_id=-1;
$detail_template_type=1;
$define_share_image="";
$install_price = 0;
$weight = 0;
$product_weight = 0;
$nowprice_title="";
$pro_area = -1;

$agent_discount      = 0;//代理商折扣
$pro_card_level_id   = -1;//购买产品需要的会员卡等级ID
$isOpenAgent         = 0;//是否开启代理商
$isOpenInstall       = 0;//安装平台
$isOpenPublicWelfare = 0;//公益基金
$cashback            = 0;//返现金额
$is_identity         = 1;//产品是否需要身份证购买开关
$shop_is_identity    = 1;//身份证购买开关

$query ="select isOpenPublicWelfare,nowprice_title,isOpenInstall,isOpenAgent,pro_card_level,shop_card_id,is_identity from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;	//公益基金
	   $isOpenInstall = $row->isOpenInstall;	//安装平台
	   $isOpenAgent = $row->isOpenAgent;	//是否开启代理商
	   $base_nowprice_title = $row->nowprice_title;
	   $pro_card_level = $row->pro_card_level;//购买产品需要会员卡级别开关
	   $shop_card_id = $row->shop_card_id;//会员卡ID
	   $shop_is_identity = $row->is_identity;
	}
$is_QR = 0;
$countdown_time = "";
$product_type_parent_id = -1;
if(!empty($_GET["product_id"])){
   $product_id = $configutil->splash_new($_GET["product_id"]);
   $query="select customer_id,name,orgin_price,asort_value,unit,default_imgurl,class_imgurl,cost_price,foreign_mark,pro_discount,pro_reward,now_price,need_score,type_id,introduce,description,asort,isout,isnew,ishot,storenum,tradeprices,propertyids,type_ids,sell_count,show_sell_count,define_share_image,is_supply_id,install_price,weight,agent_discount,nowprice_title,is_QR,pro_card_level_id,cashback,pro_area,countdown_time,is_identity from weixin_commonshop_products where isvalid=true and id=".$product_id;
  // file_put_contents('hello.txt',$query);
   $result = mysql_query($query) or die('Query failed1: ' . mysql_error());
   while ($row = mysql_fetch_object($result)) {
      $product_name = $row->name;
	  $product_orgin_price = $row->orgin_price;
	  $product_now_price = $row->now_price;
	  $product_unit = $row->unit;
	  $product_type_id = $row->type_id;
	  $product_introduce = $row->introduce;
	  $product_description = $row->description;
	  $product_asort = $row->asort;
	  $product_isout = $row->isout;
	  $is_QR = $row->is_QR;
	  $product_isnew = $row->isnew;
	  $product_ishot= $row->ishot;
	  $product_tradeprices = $row->tradeprices;
	  $product_propertyids= $row->propertyids;
	  $product_storenum = $row->storenum;
	  $pro_area = $row->pro_area;
	  $product_need_score = $row->need_score;
	  $pro_discount = $row->pro_discount;
	  $pro_reward = $row->pro_reward;
	  $customer_id = $row->customer_id;
	  $type_ids = $row->type_ids;
	  $default_imgurl = $row->default_imgurl;
	  $class_imgurl = $row->class_imgurl;
	  $countdown_time= $row->countdown_time;//商品抢购时间
	  
	  $product_cost_price = $row->cost_price;
	  $product_foreign_mark = $row->foreign_mark;
	  $sell_count=$row->sell_count;
	  $show_sell_count=$row->show_sell_count;
	  $product_asort_value = $row->asort_value;
	  $define_share_image=$row->define_share_image;
	  $supply_id=$row->is_supply_id;//供应商ID
	  $define_share_image_flag=$define_share_image?1:0;
	  $install_price = $row->install_price;
	  $product_weight = $row->weight;//产品重量
	  $agent_discount = $row->agent_discount;//代理商折扣
	  $nowprice_title = $row->nowprice_title;//"现价"自定义名称
	  $pro_card_level_id = $row->pro_card_level_id;//购买产品需要的会员卡等级ID
	  $cashback = $row->cashback;
	  $is_identity = $row->is_identity;
	  break;
  }
  
  $query_type = "select parent_id from weixin_commonshop_types where isvalid = true and id = ".$product_type_id;
   $result_type = mysql_query($query_type) or die('L163 : Query failed1: ' . mysql_error());
   if($row_type = mysql_fetch_object($result_type)){
	   $product_type_parent_id = $row_type->parent_id;
   }
   
  $query="select id from weixin_commonshop_product_imgs where isvalid=true and product_id=".$product_id;
  $result = mysql_query($query) or die('Query failed4: ' . mysql_error());
  while ($row = mysql_fetch_object($result)) {
     $imgid = $row->id;
	 $imgids = $imgids.$imgid."_";
  }
}

if($pro_discount==0 and $pro_reward==0){
	$query="select issell,sell_discount,init_reward,detail_template_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed2: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$issell = $row->issell;
		$pro_discount = $row->sell_discount;
		$pro_reward = $row->init_reward;
		$detail_template_type = $row->detail_template_type;
		break;
	}
}else{
	$query="select issell,detail_template_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed3: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$issell = $row->issell;
		$detail_template_type = $row->detail_template_type;
		break;
	}
}

if($imgids!=""){
   $imgids = rtrim($imgids,"_");
}
$propertylst= new ArrayList();
if($product_propertyids!=""){
   $propertyarr = explode("_",$product_propertyids);
   $len = count($propertyarr);
   for($i=0;$i<$len;$i++){
       $propertyid= $propertyarr[$i];
	   $propertylst->add($propertyid);
   }
}

$pidpnames="";

$pagenum = 1;
if(!empty($_GET["pagenum"])){
    $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$typeLst =  new ArrayList();
$typeLst->Add($product_type_id);
$typeid_arr = explode(",",$type_ids);
$tlen = count($typeid_arr);
if($tlen>0){
	for($i=0;$i<$tlen; $i++){
		$stid = $typeid_arr[$i];
		$typeLst->Add($stid);
	}
}


//代理模式,分销商城的功能项是 266
$is_distribution=0;//渠道取消代理商功能
$is_disrcount=0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城代理模式' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W_is_disrcount Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
   $is_disrcount = $row->is_disrcount;
   break;
}
if($is_disrcount>0){
   $is_distribution=1;
}

//供应商模式,渠道开通与不开通
$is_supplierstr=0;//渠道取消供应商功能
$sp_count=0;//渠道取消供应商功能
$sp_query="select count(1) as sp_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城供应商模式' and c.id=cf.column_id";
$sp_result = mysql_query($sp_query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($sp_result)) {
   $sp_count = $row->sp_count;
   break;
}
if($sp_count>0){
   $is_supplierstr=1;
}

//扫码模式,渠道开通与不开通
$is_scancode=0;//渠道取消供应商功能
$sc_count=0;//渠道取消供应商功能
$sc_query="select count(1) as sc_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城扫码模式' and c.id=cf.column_id";
$sc_result = mysql_query($sc_query) or die('W_is_scancode Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($sc_result)) {
   $sc_count = $row->sc_count;
   break;
}
if($sc_count>0){
   $is_scancode=1;
}

$query_cashback="select count(1) as count_cashback from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='消费返现' and c.id=cf.column_id";
$is_opencashback=0; //是否开通了消费返现 0不开通 1开通
$count_cashback=0;
$result_cashback = mysql_query($query_cashback) or die('W_count_cashback Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result_cashback)) {
   $count_cashback = $row->count_cashback;
   break;
}
if($count_cashback>0){
   $is_opencashback=1;
}

$query="select product_num from customers where isvalid=true and id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  	
while ($row = mysql_fetch_object($result)) {
   $product_num = $row->product_num;//最多上架商品数量
   break;
}
$query="select isout,count(1) as num from weixin_commonshop_products where isout=0 and isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  	
while ($row = mysql_fetch_object($result)) {
   $num = $row->num;//已经上架商品数量
   break;
}

$auth_user_id = -1;
if($_SESSION['is_auth_user']=='yes' && $_SESSION['user_id']){
	$auth_user_id = $_SESSION['user_id'];
}


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>添加产品</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script><!--添加时间插件-->

<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="/weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/handlers.js"></script>

<style type="text/css">
.show_price{
	color:red;
}
.del{
	color:blue;
}
.del:hover{
	cursor:pointer;
}
</style>
<!--编辑器多图片上传引入结束--->
<script type="text/javascript">
Array.prototype.contains = function(item){
   return RegExp("\\b"+item+"\\b").test(this);
};
ppriceHash = new Hashtable();
var oldProArray = new Array();
var i = 0;
var supply_id = "<?php echo $supply_id; ?>";//供应商ID
<?php 
$query="select proids,orgin_price,now_price,storenum,need_score,cost_price,foreign_mark,unit,weight from weixin_commonshop_product_prices where product_id=".$product_id;
$result = mysql_query($query) or die('Query failed7: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {

   $proids = $row->proids;
   $orgin_price = $row->orgin_price;
   $now_price = $row->now_price;
   $storenum = $row->storenum;
   $need_score = $row->need_score;
   $cost_price = $row->cost_price;
   $foreign_mark = $row->foreign_mark;
   $unit = $row->unit;
   $weight = $row->weight;
   
?>
  
  var proids = "<?php echo $proids; ?>";
  var orgin_price = <?php echo $orgin_price; ?>;
  var now_price = <?php echo $now_price; ?>;
  var storenum = <?php echo $storenum; ?>;
  var need_score = <?php echo $need_score; ?>;
  var cost_price = <?php echo $cost_price; ?>;
  var foreign_mark = "<?php echo $foreign_mark; ?>";
  var unit = "<?php echo $unit; ?>";
  var weight = "<?php echo $weight; ?>";
  ppriceHash.add(proids,orgin_price+"_"+now_price+"_"+storenum+"_"+need_score+"_"+cost_price+"_"+foreign_mark+"_"+unit+"_"+weight);
  if(proids!=""){
	  var proArr = proids.split("_");
	  for(var j = 0 ; j < proArr.length ; j++){
		  if(i == 0){
			  oldProArray[j] = new Array();
		  }
		  if(oldProArray[j].contains(proArr[j]) == false){
			 var length = oldProArray[j].length;
			 oldProArray[j][length] = proArr[j];
		  }
	  }
  }
  i++;
<?php }
?>
</script>
<script type="text/javascript">
	
	
			
	
	
</script>
<style type="text/css">
	.t_div{
		line-height:25px;
	}
	.t_div input{
		display: inline!important;
		float: none!important;
		vertical-align: middle;
	}
	.t_parent{
		font-weight:bold;
		margin-top: 10px;
		min-width:300px;
		padding:3px;
		float: left;
		margin-left: 20px;
		border: solid 1px rgb(237, 225, 225);
	}
	.t_child{
		margin-left: 30px;
		padding: 5px;
		display: inline;
		font-weight: normal;
		background-color: rgb(226, 231, 236);
	}
</style>
</head>

<body>
	<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<?php require('public/head.php');?>

  <!--关注用户开始-->
  <form id="frm_product" class="r_con_form" method="post" action="save_product.php?customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum; ?>&adminuser_id=<?php echo $adminuser_id; ?>&owner_general=<?php echo $owner_general; ?>&orgin_adminuser_id=<?php echo $orgin_adminuser_id; ?>" enctype="multipart/form-data">
	<div class="WSY_data">
		
		 <dl class="WSY_bulkbox w90px">
        	<dt>产品名称：</dt>
            <dd><input type="text" name="name" id="name" value="<?php echo $product_name;?>"></dd>
        </dl>
        <dl class="WSY_bulkdl w90px">
        	<dt style="margin-top:5px;">隶属分类：</dt>
            <dd style="min-width:500px">
			<?php
				$query_type_parent = "select id,name from weixin_commonshop_types where isvalid = true and customer_id = ".$customer_id." and parent_id = -1";
				$result_type_parent = mysql_query($query_type_parent) or die("L161 query error : ".mysql_error());
				while($row_type_parent = mysql_fetch_object($result_type_parent)){ 
					$ptid = $row_type_parent->id;
					$ptname = $row_type_parent->name;
				?>
				<?php
					$query_type_child = "select id,name from weixin_commonshop_types where isvalid = true and customer_id = ".$customer_id." and parent_id = ".$ptid;
					$result_type_child = mysql_query($query_type_child) or die("L169 query error : ".mysql_error());
					$childs = mysql_num_rows($result_type_child);
				?>
				<div class="t_div t_parent">
					<?php if($childs <= 0){ ?>
						<input type="checkbox" name="types" value="<?php echo $ptid;?>" id="ck_p<?php echo $ptid;?>"/>
					<?php }?><label for="ck_p<?php echo $ptid;?>"><?php echo $ptname;?></label>
					
					<?php if($childs > 0){?>
					<div class="t_div t_child">
					<?php
						while($row_type_child = mysql_fetch_object($result_type_child)){ 
							$pcid = $row_type_child->id;
							$pcname = $row_type_child->name;
					?>
						<input type="checkbox" name="types" id="ck_p<?php echo $ptid;?>_c<?php echo $pcid;?>" />
						<label for="ck_p<?php echo $ptid;?>_c<?php echo $pcid;?>"><?php echo $pcname;?></label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php }?>
					</div>
					<?php }?>
				</div>
				
				<?php
				}
			?>
            </dd>
        </dl>
        
        <div class="WSY_bulkbox02">
			<div class="WSY_bulkbox_top">
				<div class="WSY_bulkboximgbox">
					<div class="WSY_bulkboximg" id="WSY_bulkboximg">
						<p>封面图片</p>
						<iframe src="iframe_images_defaultproduct.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>&default_imgurl=<?php echo $default_imgurl; ?>" height=200 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
					</div>
					<input type=hidden name="default_imgurl" id="default_imgurl" value="<?php echo $default_imgurl ; ?>" />
				</div>
		   
				<div class="WSY_bulkboximgbox">
					<div class="WSY_bulkboximg" id="WSY_bulkboximg">
						<p>分类图片</p>
						<iframe src="iframe_class_images_defaultproduct.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>&default_imgurl=<?php echo $default_imgurl; ?>" height=200 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
					</div>
					<input type=hidden name="class_imgurl" id="class_imgurl" value="<?php echo $class_imgurl ; ?>" />
				</div>
			</div>
        </div>
		<div class="WSY_bulkbox02">
            <div class="WSY_bulkboximgbox">
                <div class="WSY_bulkboximg" id="WSY_bulkboximg" style="height:260px">
                    <p>产品图片</p>
                    <dl style="margin:30px 0px 0px 0px">
                       <iframe id="frmProImgs" src="iframe_images.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>&detail_template_type=<?php echo $detail_template_type; ?>" height="400" width="1024" FRAMEBORDER=0 SCROLLING=no></iframe>
                    </dl>
                </div>
				<input type=hidden name="imgids" id="imgids" value="<?php echo $imgids ; ?>" />
				<div class="WSY_bulkboximg">
                    <p>简短介绍</p>
                    <dl class="WSY_bulkdl">
                    	<dd><textarea name="introduce" class="briefdesc"><?php echo $product_introduce; ?></textarea></dd>
                    </dl>
                </div>
            </div>
		</div>
          
		  <dl class="WSY_bulkdl w90px">
                <dt style="margin-top:20px;">产品属性：</dt>
                <ul class="WSY_bulkul wdw">
				<?php
					 if($supply_id<0){
						  $query="select id,name from weixin_commonshop_pros where isvalid=true and parent_id=-1 and customer_id=".$customer_id." and supply_id<0";
					 }else{
						  $query="select id,name from weixin_commonshop_pros where isvalid=true and parent_id=-1 and customer_id=".$customer_id." and supply_id=".$supply_id;
					 }
					 $result = mysql_query($query) or die('Query failed11: ' . mysql_error());
				     while ($row = mysql_fetch_object($result)) {
					    $parent_id = $row->id;
					    $parent_name = $row->name;
				   ?>
                    <dd>
						<div class="WSY_cloropbox">
							<span><?php echo $parent_name; ?></span><input type="hidden" name="hidden_parent" value="<?php echo $parent_id;?>"/>
							<div class="WSY_clorop">
							<?php 
							   $query2="select id,name from weixin_commonshop_pros where isvalid=true and parent_id=".$parent_id;
							   $result2 = mysql_query($query2) or die('Query failed12: ' . mysql_error());
							   while ($row2 = mysql_fetch_object($result2)) {
								   $p_id = $row2->id;
								   $p_name = $row2->name;
								   
							?>
								<p><input type="checkbox" data_name="prop_<?php echo $parent_id; ?>" data_pid="<?php echo $p_id; ?>" data_text="<?php echo $p_name; ?>" data_parent="<?php echo $parent_id; ?>" value="<?php echo $p_id; ?>" <?php if($propertylst->Contains($p_id)){ $pidpnames = $pidpnames.$p_id.",".$parent_name."(".$p_name."),".$parent_id."_"; ?>checked<?php } ?> name="ptids" onclick="chkPro();">
									<?php echo $p_name; ?>
									<input type="hidden" id="<?php echo $p_id; ?>" value="<?php echo $p_name; ?>"/></p>
							<?php } ?>
							</div>
						</div>
                    </dd>
					 <?php }
                     $pidpnames = rtrim($pidpnames,"_");
					?>	
					<input type=hidden name="pidpnames" id="pidpnames" value="<?php echo $pidpnames; ?>" />
					<input type=hidden name="propertyids" id="propertyids" value="<?php echo $product_propertyids; ?>"/>		
                </ul>
            </dl>
		  
           
			<dl class="WSY_bulkdl WSY_bulkdldt w90px">
        	<dt style="width:90px">"现价"自定义:</dt>
				<dd style="line-height:24px"><input type="text" name="define_price_tag" value="<?php echo $nowprice_title; ?>" class="form_input num_check" size="5" maxlength="10">（默认 "现价"）</dd>
			</dl>
            <dl class="WSY_bulkdl w90px">
                <dt>产品价格：</dt>
                <div class="WSY_bulkul01box" id="div_proprices">
                    <div class="WSY_bulkul01">
                        <span class="WSY_red">现价和成本一致,则不返佣</span><br>
                        <li>原价￥：<input type="text" name="orgin_price" value="<?php echo $product_orgin_price; ?>" class="form_input num_check" size="5" maxlength="10"></li>
                        <li><?php if($nowprice_title){echo $nowprice_title;}else if($base_nowprice_title){echo $base_nowprice_title;}else{echo "现价";}?>:￥<input type="text" name="now_price" value="<?php echo $product_now_price; ?>" class="form_input num_check" size="5" maxlength="10"></li>
                       
                        <li>单位：<input type="text" name="unit" value="<?php echo $product_unit; ?>" class="form_input" size="5" maxlength="10"></li>
                        <li>重量：<input type="text" name="weight" value="<?php echo $product_weight; ?>" class="form_input" size="5" maxlength="10">KG</li>
                        <li>库存：<input type="text" name="storenum" value="<?php echo $product_storenum; ?>" class="form_input num_check" size="5" maxlength="10"></li>
						
                    </div>
                     <div class="WSY_bulkul02box">
                     	<div class="WSY_bulkul02">
                        	<span class="WSY_red">属性价格</span>
                            <ul class="WSY_bulkul01">
                                <li class="WSY_bulkuli_red">颜色(红色)</li>
                                <li>原价￥：<input type="text" value="150"></li>
                                <li>现价￥：<input type="text" value="100"></li>
                                <li>成本：<input type="text" value="0"></li>
                                <li>单位：<input type="text" value=""></li>
                                <li>所需积分：<input type="text" value="0"></li>
                                <li>库存：<input type="text" value="10000"></li>
                                <li>外部标识：<input type="text" value=""></li>
                            </ul>
							 <ul class="WSY_bulkul01">
                                <li class="WSY_bulkuli_red">颜色(红色)</li>
                                <li>原价￥：<input type="text" value="150"></li>
                                <li>现价￥：<input type="text" value="100"></li>
                                <li>成本：<input type="text" value="0"></li>
                                <li>单位：<input type="text" value=""></li>
                                <li>所需积分：<input type="text" value="0"></li>
                                <li>库存：<input type="text" value="10000"></li>
                                <li>外部标识：<input type="text" value=""></li>
                            </ul>
                        </div>
                    </div>
                </div>
				<input type=hidden id="hide_proprice" name="hide_proprice" value="" />
				<input type=hidden id="hide_selpros" name="hide_selpros" value="" />
            </dl>
            <dl class="WSY_bulkdl WSY_bulkdldt w90px">
                <dt>真实销售量：</dt>
                <dd><span>数量：</span><?php echo $sell_count; ?></dd>
            </dl>
            <dl class="WSY_bulkdl WSY_bulkdldt w90px">
                <dt>虚拟销售量：</dt>
                <dd><span>数量：</span><input type="text" name="show_sell_count" value="<?php echo $show_sell_count; ?>"><i class="WSY_red">显示销量=虚拟销售量+真实销售量</i></dd>
            </dl>
            <dl class="WSY_bulkdl WSY_bulkdl03 w90px">
                <dt>产品分享图片：</dt>
                <dd class="dd_margin"><input type="radio" id="define_share_image_flag_0" name="define_share_image_flag" value="0" <?php if($define_share_image_flag==0){ ?>checked<?php } ?> >
					<label for="define_share_image_flag_0">默认</label></dd>
                <dd class="dd_margin"><input type="radio" id="define_share_image_flag_1" name="define_share_image_flag" value="1" <?php if($define_share_image_flag==1){ ?>checked<?php } ?>>
					<label for="define_share_image_flag_1">自定义</label></dd>
                <!--上传文件代码开始-->
                <div class="uploader white"  id="define_share_image_div" <?php if(!$define_share_image_flag) echo "style='display:none'"; ?>>
                    <input type="text" class="filename" readonly />
                    <input type="button" name="file" class="button" value="上传..."/>
                    <input type="file" size="30" name='new_define_share_image' id='new_define_share_image'/>
					<input type='hidden' name='now_define_share_image' id='now_define_share_image' value='<?php echo $define_share_image;?>'>
                </div>
                <!--上传文件代码结束-->
            </dl>
			
			<dl class="WSY_bulkdl WSY_bulkdl03 w90px" <?php if(false == $is_scancode){ echo 'style="display:none"';  $is_QR=0;}  ?> >
				<dt>二维码核销：</dt>
				<dd class="dd_margin"><input type="radio" value="0" <?php if($is_QR==0){ ?>checked<?php } ?> name="is_QR" id="is_QR_0" ><label for="is_QR_0">关</label></dd>
				<dd class="dd_margin"><input type="radio" value="1" <?php if($is_QR==1){ ?>checked<?php } ?> name="is_QR" id="is_QR_1"><label for="is_QR_1">开</label></dd>
			</dl>
			<?php 
			if( $shop_is_identity ){
			?>
			<dl class="WSY_bulkdl WSY_bulkdl03 w90px">
				<dt>身份证购买：</dt>
				<dd class="dd_margin"><input type="radio" value="0" <?php if( 0 == $is_identity ){ ?> checked <?php } ?> name="is_identity" id="is_identity_0"><label for="is_QR_0">关</label></dd>
				<dd class="dd_margin"><input type="radio" value="1" <?php if( 1 == $is_identity ){ ?> checked <?php } ?> name="is_identity" id="is_identity_1"><label for="is_QR_1">开</label></dd>
			</dl>
			<?php } ?>	
			<dl class="WSY_bulkdl WSY_bulkdl03 w90px">
                <dt>产品所属城市：</dt>
                <dd class="dd_margin">
					<select name="city_id">
						<option value="-1" >无</option>
						<?php 
					
					$query_area='select id,province,city,area from weixin_commonshop_product_area where isvalid=true and customer_id='.$customer_id;
					$result_area = mysql_query($query_area) or die('Query_area failed: ' . mysql_error());  
					while ($row_area = mysql_fetch_object($result_area)) {				
						$province=  $row_area->province;
						$city=   $row_area->city;
						$city_id=   $row_area->id;
					?>	
						<option value="<?php echo $city_id;?>" <?php if($city_id == $pro_area){ echo 'selected="selected"'; } ?> ><?php echo $province.$city;?></option>
					<?php 	
					}								
					?>
					</select>
				</dd>
            </dl>
			  
            <dl class="WSY_bulkdl w90px">
                <dt>其他属性：</dt>
                <dd class="WSY_bulkdldd dd_margin"><input type="checkbox" <?php if($product_isout){?>checked<?php } ?> id="chk_isout" onclick="changeOut(this);"><label for="chk_isout">下架</label></dd>
                <dd class="WSY_bulkdldd dd_margin"><input type="checkbox" <?php if($product_isnew){?>checked<?php } ?> id="chk_isnew" onclick="changeNew(this);"><label for="chk_isnew">新品</label></dd>
                <dd class="WSY_bulkdldd dd_margin"><input type="checkbox" <?php if($product_ishot){?>checked<?php } ?> id="chk_ishot" onclick="changeHot(this);"><label for="chk_ishot">热卖</label></dd>
                <dd class="WSY_bulkdldd dd_margin">
					排序位置：<input class="weiz_input" type="text" name="asort_value" value="<?php echo $product_asort_value; ?>" />(按降序排序)
					<?php
						if($product_num !=-1 and $num>=$product_num ){ 
					?>
					(<span style="color:red">你上架的商品已经超过<?php echo $product_num;?>件了！</span>)
					<?php 
						}
					?>
				</dd>
            </dl>
			<?php if($issell){ ?>
			 <div class="w_leftkjbox">
            	<div class="w_leftkj">
                    <dl class="WSY_bulkdl WSY_bulkdldt w90px">
                        <dt>分销折扣率：</dt>
                        <dd><input type="text" name="pro_discount" id="pro_discount" style="width:50px;" value="<?php echo $pro_discount; ?>"><i style="color:#646464">% (0:表示无折扣)</i></dd>
                    </dl>
                    <dl class="WSY_bulkdl WSY_bulkdldt w90px">
                        <dt>总佣金比例：</dt>
                        <dd><input type="text" name="pro_reward" id="pro_reward" style="width:50px;" value="<?php echo $pro_reward; ?>"><i style="color:#646464">（0～1）</i></dd>
                    </dl>
					<?php if($isOpenInstall){?>
					 <dl class="WSY_bulkdl WSY_bulkdldt w90px">
                        <dt>产品安装费</dt>
                        <dd><input type="text" name="install_price" id="install_price" class="form_input" style="width:50px;" value="<?php echo $install_price; ?>" />元</dd>
                    </dl>
					<?php }?>
                </div>
                <div class="w_leftkj">
					<?php if($is_distribution){?>
						<dl class="WSY_bulkdl WSY_bulkdldt w90px">
							<dt>代理商折扣率：</dt>
							<dd><input type="text" name="agent_discount" id="agent_discount" style="width:50px;" value="<?php echo $agent_discount; ?>" >%</dd>
						</dl>
					<?php }?>
					<?php if($is_opencashback){?>
                    <dl class="WSY_bulkdl WSY_bulkdldt w90px">
                        <dt>返现金额：</dt>
                        <dd><input type="text" name="cashback" id="cashback" style="width:50px;" value="<?php echo $cashback; ?>" ><i style="color:#646464">元</i></dd>
                    </dl>
					<?php }?>
                </div>
            </div>
			<?php }?>
			<dl class="WSY_bulkdl WSY_bulkdldt w90px">
                <dt style="width:200px">商品截止抢购时间(48号模板专用)</dt>
                <dd><input type="text" style="width:200px" id="countdown_time" name="countdown_time" value="<?php echo $countdown_time; ?>"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'2013-10-25 10:00',maxDate:'2018-10-25 21:30'});" ></dd>
            </dl>
			<?php if($pro_card_level){?>
			<dl class="WSY_bulkdl WSY_bulkdl03 w90px">
                <dt>需要会员级别：</dt>
                <dd>
					<select name="pro_card_level_id" id="pro_card_level_id">
						<option value="-1"  <?php if($pro_card_level_id==-1){ ?>selected<?php } ?>>不限制</option>
						<?php 
					   $query="select id,title from weixin_card_levels where isvalid=true and card_id=".$shop_card_id;
					   $result = mysql_query($query) or die('Query failed: ' . mysql_error());
					   while ($row = mysql_fetch_object($result)) {
						   $cid = $row->id;
						   $cname = $row->title;
					?>   
						<option value="<?php echo $cid;?>" <?php if($pro_card_level_id==$cid){ echo 'selected="selected"'; } ?> ><?php echo $cname;?></option>
					<?php 	
					}								
					?>
					</select>
				</dd>
            </dl>
			<?php } ?>
		
		
            <dl class="WSY_bulkdl w90px">
                <dt>详细介绍：</dt>
                <div class="text_box input">
                	<textarea id="editor1"   name="description"><?php echo $product_description; ?></textarea>
                </div> 
            </dl>
            <div class="WSY_text_input01">
                <div class="WSY_text_input"><button class="WSY_button" id="btnSave" type="button" onclick="saveProduct()">提交保存</button></div>
                <div class="WSY_text_input"><button class="WSY_button" type="button">返回</button></div>
            </div>
		</div>
	</div>
	<input type=hidden name="stock_pidarr" id="stock_pidarr" value="<?php echo $stock_pidarr; ?>" />
	<input type=hidden name="keyid" id="keyid" value="<?php echo $product_id; ?>" />
	<input type=hidden name="isout" id="isout" value=<?php echo $product_isout; ?> />
	<input type=hidden name="isnew" id="isnew" value=<?php echo $product_isnew; ?> />
	<input type=hidden name="ishot" id="ishot" value=<?php echo $product_ishot; ?> />
	<input type=hidden name="pro_price_detail" id="pro_price_detail" />
	<input type=hidden name="tradeprices" id="tradeprices" value="<?php echo $product_tradeprices; ?>" />
	<input type=hidden name="type_ids" id="type_ids" value="<?php echo $type_ids; ?>" />
	<input type=hidden name="auth_user_id" id="auth_user_id" value=<?php echo $auth_user_id; ?> />
</form>
</div>

<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script> 
<!--编辑器多图片上传引入结束--->
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
$(function(){
	var num=<?php echo $num;?>;//已经上架的数量
	var product_num=<?php echo $product_num;?>;//限制上架数量
	var product_id=<?php echo $product_id;?>;
	var is_auth_user = '<?php echo $_SESSION['is_auth_user'];?>';  //是否是授权用户, yes 是, no 不是
	
	if(product_num<=num && product_num != -1){
		document.getElementById("isout").value=1;
		document.getElementById("chk_isout").checked="checked";
		document.getElementById("chk_isout").disabled="disabled"; 
	}
	
	if(is_auth_user=="yes"){	//授权用户 上传产品只能是下架状态.
		document.getElementById("isout").value=1;
		document.getElementById("chk_isout").checked="checked";
		document.getElementById("chk_isout").disabled="disabled"; 
	}
});
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});


var pro_reward = '<?php echo $pro_reward;?>';

function setParentImgIds(ids){
   $("#imgids").attr("value",ids);
}
function chkPro(){ // this, pid , parent_name , parent_id
	 var allpids = $("input[name='hidden_parent']");
	 var str = "";
	 var parentArray = new Array();
	 for(var i = 0 , index = 0; i < allpids.length ; i++ ){ //循环所有属性类型
		 var parent_id = allpids[i].value;
		 var allprops = $("input[data_name='prop_"+parent_id+"']:checked");  //获取属性类型中选中的属性值
		if(allprops.length > 0){
			var childArray = new Array();
			for(var j = 0 ; j < allprops.length; j++ ){ //将每个选中的属性值信息拼接添加到数组
				var ckprop = allprops[j];
				//console.log("ckprop : "+ckprop);
				var pid = $(ckprop).attr("data_pid");
				var text = $(ckprop).attr("data_text");
				var parent = $(ckprop).attr("data_parent");
				childArray[j] = pid+","+text+","+parent;
			}
			parentArray[index] = childArray;
			index++;
		 }
	 }
	 setPropPrice(parentArray);
}

setPropPrice(oldProArray);


function setPropPrice(propArrays){
	var cIndex = propArrays.length - 1;
	var counter = new Array();
	var str = getAppendText("" , "" , "");
    if(propArrays.length > 0){
		var total = 1;
		for(var i = 0 ; i<propArrays.length ; i++){
			counter[i] = 0;
			total = total * propArrays[i].length;
		}
		for(var j = 0;j < total ; j ++){ //笛卡尔乘积
			var ids = "";
			var text = "";
			for(var index = 0 ; index < propArrays.length ; index++){
				var props = propArrays[index][counter[index]].split(",");
				if(props.length == 1){
					props[1] = $("#"+props[0]).val();
				}
				if(index > 0){
					ids += "_";
					text += " - ";
				}
				ids += props[0];
				text += props[1];
			}
			str = getAppendText(str , ids , text);
			console.log(" ids : "+ ids +"  text : "+text);
			calcIndex();
		}
	}
	var divp = document.getElementById("div_proprices");
	str = str + "</div></div>";
	divp.innerHTML = str;
	function calcIndex(){
		counter[cIndex]++; 
		if(counter[cIndex] >= propArrays[cIndex].length){
			counter[cIndex] = 0 ; 
			cIndex --;  
			if(cIndex >= 0){
				calcIndex();
			} 
			cIndex = propArrays.length -1;
		}
	}
}


function getAppendText(str,pid,text){
		var orgin_price="";
		var now_price="";
		var storenum=1;
		var need_score="";
		var cost_price="";
		var foreign_mark="";
		var unit="";
		var weight=0;
		if(ppriceHash.contains(pid)){
			var onprice = ppriceHash.items(pid);
			onprices =  onprice.split("_");
			orgin_price = onprices[0];
			now_price = onprices[1];
			storenum = onprices[2];
			need_score = onprices[3];
			cost_price = onprices[4];
			foreign_mark = onprices[5];
			unit = onprices[6];
			weight = onprices[7];
		}
		if(pid!=""){
			str = str + '<ul class="WSY_bulkul01">';
			str = str + '<li class="WSY_bulkuli_red">'+text+'</li>';
			str = str +" <li>原价:￥<input type=\"text\" name=\"pro_orgin_price\" value=\""+orgin_price+"\" class=\"form_input num_check \" size=\"5\" maxlength=\"10\"></li>";
			str = str +" <li class='now_price'><?php if($nowprice_title){echo $nowprice_title;}else if($base_nowprice_title){echo $base_nowprice_title;}else{echo "现价";}?>:￥<input type=\"text\" name=\"pro_now_price\" value=\""+now_price+"\" class=\"form_input num_check calc_np\" size=\"5\" maxlength=\"10\"></li>";
			str = str +" <li class='base_price'>成本:￥<input type=\"text\" name=\"pro_cost_price\" value=\""+cost_price+"\" class=\"form_input num_check calc_bp\" size=\"5\" maxlength=\"10\"></li>";
			str = str +" <li>单位:<input type=\"text\" name=\"pro_unit\" value=\""+unit+"\" class=\"form_input\" size=\"5\" maxlength=\"10\"></li>";
			str = str +" <li>重量:<input type=\"text\" name=\"pro_weight\" value=\""+weight+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">KG</li>";
			str = str +" <li>所需积分: <input type=\"text\" name=\"pro_need_score\" value=\""+need_score+"\" class=\"form_input num_check\" size=\"5\" maxlength=\"10\"></li>";
			str = str +" <li>库存: <input type=\"text\" name=\"pro_storenum\" value=\""+storenum+"\" class=\"form_input num_check\" size=\"5\" maxlength=\"10\"></li>";
			str = str +" <li>外部标识: <input type=\"text\" name=\"pro_foreign_mark\" value=\""+foreign_mark+"\" class=\"form_input\" size=\"5\" maxlength=\"50\"></li>";
			var reward = 0;
			if(pro_reward !="" && (parseFloat(pro_reward) > 0 && parseFloat(pro_reward) <1 ) &&  //佣金比
				now_price != "" && parseFloat(now_price) > 0 && //现价
				cost_price != "" && parseFloat(cost_price) >= 0){ //成本
				reward = calcReward(now_price,cost_price,pro_reward);
				//console.log("now_price : "+now_price+" ; cost_price : "+cost_price+" ; profit : "+profit+" ; reward : "+reward);
			}
			str = str +" <li class='show_price'>"+(reward > 0 ? "（返佣金额：￥"+reward+"）" : "" )+"</li>";
			str = str +" <li class='del'>删除</li>";
			str = str +" <input type=hidden name=\"proids\" value=\""+pid+"\" />";
			str = str +"</ul>";
		}else{
			
			str = str + "<div class='WSY_bulkul01'>";
			str = str + '<span class="WSY_red">现价和成本一致,则不返佣</span><br>';
			str = str +"  <li>原价:￥<input type=\"text\" name=\"orgin_price\" value=\"<?php echo $product_orgin_price; ?>\" class=\"form_input num_check\" size=\"5\" maxlength=\"10\"> </li>";
			str = str +" <li class='now_price'> <?php if($nowprice_title){echo $nowprice_title;}else if($base_nowprice_title){echo $base_nowprice_title;}else{echo "现价";}?>:￥<input type=\"text\" name=\"now_price\" value=\"<?php echo $product_now_price; ?>\" class=\"form_input num_check calc_np\" size=\"5\" maxlength=\"10\"> </li>";
			str = str +" <li class='base_price'> 成本:￥<input type=\"text\" name=\"cost_price\" value=\"<?php echo $product_cost_price; ?>\" class=\"form_input num_check calc_bp\" size=\"5\" maxlength=\"10\"> </li>";
			str = str +" <li> 单位:<input type=\"text\" name=\"unit\" value=\"<?php echo $product_unit; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\"> </li>";
			str = str +" <li> 重量:<input type=\"text\" name=\"weight\" value=\"<?php echo $product_weight; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\">KG</li>";
			str = str +" <li> 所需积分: <input type=\"text\" name=\"need_score\" value=\"<?php echo $product_need_score; ?>\" class=\"form_input num_check\" size=\"5\" maxlength=\"10\"> </li>";
			str = str +" <li> 库存: <input type=\"text\" name=\"storenum\" value=\"<?php echo $product_storenum; ?>\" class=\"form_input num_check\" size=\"5\" maxlength=\"10\"> </li>";
			str = str +" <li> 外部标识: <input type=\"text\" name=\"foreign_mark\" value=\"<?php echo $product_foreign_mark; ?>\" class=\"form_input\" size=\"5\" maxlength=\"50\"></li>";
			
			var reward = 0;
			var now_price = '<?php echo $product_now_price; ?>';
			var cost_price = '<?php echo $product_cost_price; ?>';
			if(pro_reward !="" && (parseFloat(pro_reward) > 0 && parseFloat(pro_reward) < 1 ) &&  //佣金比
				now_price != "" && parseFloat(now_price) > 0 && //现价
				cost_price != "" && parseFloat(cost_price) >= 0 ){ //成本
				reward = calcReward(now_price,cost_price,pro_reward);
				//console.log("now_price : "+now_price+" ; cost_price : "+cost_price+" ; profit : "+profit+" ; reward : "+reward);
			}
			str = str +" <li class='show_price'>"+(reward > 0 ? "（返佣金额：￥"+reward+"）" : "" )+"</li>";
			str = str +"</div>";
			str = str + '<div class="WSY_bulkul02box" style="width:95%">';
            str = str + '<div class="WSY_bulkul02">';
            str = str + '<span class="WSY_red">属性价格</span>';
		}
		return str;
}

function calcReward(now_price,base_price,rate){
	var profit = parseFloat(now_price) - parseFloat(base_price);
	var reward = parseFloat(now_price) * parseFloat(rate);
	reward = reward > profit ? profit : reward;
	reward = reward.toFixed(2);
	return reward;
}

$(function(){
	$("#div_proprices").on("keyup",".num_check",function(){
		var val = $(this).val();
		if(isNaN(val) || val < 0){
			$(this).val(0);
		}
	});
});

$(document).ready(function() {
	$("input[name='define_share_image_flag']").click(
		function() {
		var $selectedvalue = this.value;
		if ($selectedvalue == 1) {
			$("#define_share_image_div").show();
		}
		else {
			$("#define_share_image_div").hide();
		}
	});
	$("#pro_reward").on("blur",function(){
		var rate = $(this).val();
		if(isNaN(rate) && (parseFloat(rate) <= 0 || parseFloat(rate) >= 1)){
			alert("请输入正确的佣金比！");
			return;
		}
		var all_show = $(".show_price");
		all_show.each(function(){
			var np = $(this).siblings(".now_price").find("input").val();
			var bp = $(this).siblings(".base_price").find("input").val();
			if(np != "" && bp != ""){
				np = parseFloat(np);
				bp = parseFloat(bp);
				reward = calcReward(np,bp,rate);
				$(this).text("（返佣金额：￥"+reward+"）");
			}
		});
	});
	$("#div_proprices").on("blur",".calc_bp",function(){
		//console.log("成本 - blur ");
		var rate = $("#pro_reward").val();
		if(!isNaN(rate) && parseFloat(rate) >= 0){
			var np = $(this).parent().siblings(".now_price").find("input").val();
			var bp = $(this).val();
			if(np != "" && bp != ""){
				np = parseFloat(np);
				bp = parseFloat(bp);
				reward = calcReward(np,bp,rate);
				$(this).parent().siblings(".show_price").text("（返佣金额：￥"+reward+"）");
			}
		}
	});
	$("#div_proprices").on("blur",".calc_np",function(){
		//console.log("现价 - blur ");
		var rate = $("#pro_reward").val();
		if(!isNaN(rate) && parseFloat(rate) >= 0){
			var np = $(this).val();
			var bp = $(this).siblings(".base_price").find("input").val();;
			if(np != "" && bp != ""){
				np = parseFloat(np);
				bp = parseFloat(bp);
				reward = calcReward(np,bp,rate);
				$(this).parent().siblings(".show_price").text("（返佣金额：￥"+reward+"）");
			}
		}
	});
	$("#div_proprices").on("click",".del",function(){
		$(this).parent().remove();
	});
});


function setParentDefaultimgurl(default_imgurl){

    document.getElementById("default_imgurl").value=default_imgurl;
}
function setParentClassDefaultimgurl(class_imgurl){

    document.getElementById("class_imgurl").value=class_imgurl;
}
customer_id_en = '<?php echo $customer_id_en;?>';
page_index = 4;
</script>
<script type="text/javascript" src="../../Common/js/Product/product_common.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product/product.js"></script>
</body>
</html>
