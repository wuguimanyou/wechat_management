<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../back_init.php');
require('../common/utility.php');
$link = mysql_connect("localhost",DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");

$description="";
$product_id=-1;
$product_name="";
$product_orgin_price=0;
$product_now_price=0;
$product_type_id=-1;
$product_introduce="";
$product_description="";
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
$sell_count=999;
$pro_discount=0;
$pro_reward=0;
$issell=0;
$type_ids="";
$default_imgurl="";
$product_asort_value=0;

if(!empty($_GET["product_id"])){
   $product_id = $_GET["product_id"];
   $query="select customer_id,name,orgin_price,asort_value,default_imgurl,cost_price,foreign_mark,pro_discount,pro_reward,now_price,need_score,type_id,introduce,description,asort,isout,isnew,ishot,storenum,tradeprices,propertyids,type_ids from weixin_commonshop_products where isvalid=true and id=".$product_id;
   $result = mysql_query($query) or die('Query failed: ' . mysql_error());
   while ($row = mysql_fetch_object($result)) {
      $product_name = $row->name;
	  $product_orgin_price = $row->orgin_price;
	  $product_now_price = $row->now_price;
	  $product_type_id = $row->type_id;
	  $product_introduce = $row->introduce;
	  $product_description = $row->description;
	  $product_asort = $row->asort;
	  $product_isout = $row->isout;
	  $product_isnew = $row->isnew;
	  $product_ishot= $row->ishot;
	  $product_tradeprices = $row->tradeprices;
	  $product_propertyids= $row->propertyids;
	  $product_storenum = $row->storenum;
	  $product_need_score = $row->need_score;
	  $pro_discount = $row->pro_discount;
	  $pro_reward = $row->pro_reward;
	  $customer_id = $row->customer_id;
	  $type_ids = $row->type_ids;
	  $default_imgurl = $row->default_imgurl;
	  
	  $product_cost_price = $row->cost_price;
	  $product_foreign_mark = $row->foreign_mark;
	  $sell_count=$row->sell_count;
	  $product_asort_value = $row->asort_value;
	  break;
  }
  
  $detail_template_type=1;
  if($pro_discount==0 and $pro_reward==0){
     $query="select issell,sell_discount,init_reward,detail_template_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
     while ($row = mysql_fetch_object($result)) {
	     $issell = $row->issell;
		 $pro_discount = $row->sell_discount;
		 $pro_reward = $row->init_reward;
		 $detail_template_type = $row->detail_template_type;
		 break;
     }
  }else{
     $query="select issell,detail_template_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
     while ($row = mysql_fetch_object($result)) {
	     $issell = $row->issell;
		 $detail_template_type = $row->detail_template_type;
		 break;
     }
  }
  
  $query="select id from weixin_commonshop_product_imgs where isvalid=true and product_id=".$product_id;
  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
  while ($row = mysql_fetch_object($result)) {
     $imgid = $row->id;
	 $imgids = $imgids.$imgid."_";
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
    $pagenum = $_GET["pagenum"];
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



?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
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
<script>
ppriceHash = new Hashtable();

<?php 
$query="select proids,orgin_price,now_price,storenum,need_score,cost_price,foreign_mark from weixin_commonshop_product_prices where product_id=".$product_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {

   $proids = $row->proids;
   $orgin_price = $row->orgin_price;
   $now_price = $row->now_price;
   $storenum = $row->storenum;
   $need_score = $row->need_score;
   $cost_price = $row->cost_price;
   $foreign_mark = $row->foreign_mark;
   
?>
  var proids = "<?php echo $proids; ?>";
  var orgin_price = <?php echo $orgin_price; ?>;
  var now_price = <?php echo $now_price; ?>;
  var storenum = <?php echo $storenum; ?>;
  var need_score = <?php echo $need_score; ?>;
  var cost_price = <?php echo $cost_price; ?>;
  var foreign_mark = "<?php echo $foreign_mark; ?>";
  ppriceHash.add(proids,orgin_price+"_"+now_price+"_"+storenum+"_"+need_score+"_"+cost_price+"_"+foreign_mark);
<?php }
?>
</script>
<style type="text/css" media="screen">#PicUploadUploader {visibility:hidden}</style>
<body>

<style type="text/css">body, html{background:url(images/main-bg.jpg) left top fixed no-repeat;}</style>
<div id="iframe_page">
	<div class="iframe_content">
	<link href="operamasks-ui.css" rel="stylesheet" type="text/css">
	<link href="css/shop.css" rel="stylesheet" type="text/css">

	<div class="r_nav">
		<ul>
			<li class=""><a href="base.php?customer_id=<?php echo $customer_id; ?>">基本设置</a></li>
			<li class=""><a href="fengge.php?customer_id=<?php echo $customer_id; ?>">风格设置</a></li>
			<li class=""><a href="defaultset.php?customer_id=<?php echo $customer_id; ?>">首页设置</a></li>
			<li class="cur"><a href="product.php?customer_id=<?php echo $customer_id; ?>">产品管理</a></li>
			<li class=""><a href="order.php?customer_id=<?php echo $customer_id; ?>">订单管理</a></li>
			<li class=""><a href="qrsell.php?customer_id=<?php echo $customer_id; ?>">推广员</a></li>
			<li class=""><a href="customers.php?customer_id=<?php echo $customer_id; ?>">顾客</a></li>
		</ul>
	</div>
<div id="products" class="r_con_wrap">

<form id="frm_product" class="r_con_form" method="post" action="save_product.php?customer_id=<?php echo $customer_id; ?>&pagenum=<?php echo $pagenum; ?>">
	<div class="rows">
		<label>产品名称</label>
		<span class="input"><input type="text" name="name" id="name" value="<?php echo $product_name;?>" class="form_input" size="35" maxlength="100" notnull=""> <font class="fc_red">*</font></span>
		<div class="clear"></div>
	</div>
		<div class="rows">
			<label>隶属分类</label>
			<span class="input">
			<?php 
			  $query="select id,name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=-1";
			  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			  while ($row = mysql_fetch_object($result)) {
				   $pt_id = $row->id;
				   $pt_name = $row->name;
			?>
			  
			  <label><input type=checkbox name="type_id"  value="<?php echo $pt_id; ?>" <?php if($typeLst->Contains($pt_id)){?>checked <?php } ?> /><?php echo $pt_name; ?> &nbsp;&nbsp;</label>
			<?php 
			      $query2="select id,name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=".$pt_id;
				  $result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
				  while ($row2 = mysql_fetch_object($result2)) {
					   $sub_pt_id = $row2->id;
					   $sub_pt_name = $row2->name;
					   
					   
					  $query3="select id,name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=".$sub_pt_id;
					  $result3 = mysql_query($query3) or die('Query failed: ' . mysql_error());
					  while ($row3 = mysql_fetch_object($result3)) {
						   $sub_sub_pt_id = $row3->id;
						   $sub_sub_pt_name = $row3->name;
			 ?>
				 <label><input type=checkbox name="type_id"  value="<?php echo $sub_sub_pt_id; ?>" <?php if($typeLst->Contains($sub_sub_pt_id)){?>checked <?php } ?> /><?php echo $sub_sub_pt_name; ?> &nbsp;&nbsp;</label>
			 <?php } ?>
				 <label><input type=checkbox name="type_id"   value="<?php echo $sub_pt_id; ?>" <?php if($typeLst->Contains($sub_pt_id)){?>checked <?php } ?> /><?php echo $sub_pt_name; ?> &nbsp;&nbsp;</label>
			 <?php } ?>
			 
			 
			 <br/>
			 <?php  } ?>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>封面图片</label>
			<span class="input">
				<span class="upload_file">
					<div>
						<iframe src="iframe_images_defaultproduct.php?customer_id=<?php echo $customer_id; ?>&product_id=<?php echo $product_id; ?>&default_imgurl=<?php echo $default_imgurl; ?>" height=200 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
					</div>
				</span>
			</span>
			<div class="clear"></div>
			<input type=hidden name="default_imgurl" id="default_imgurl" value="<?php echo $default_imgurl ; ?>" />
		</div>
		<div class="rows">
			<label>产品图片</label>
			<span class="input">
				<span class="upload_file">
					<div>
						<iframe src="iframe_images.php?customer_id=<?php echo $customer_id; ?>&product_id=<?php echo $product_id; ?>&detail_template_type=<?php echo $detail_template_type; ?>" height=200 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
					</div>
				</span>
			</span>
			<div class="clear"></div>
			<input type=hidden name="imgids" id="imgids" value="<?php echo $imgids ; ?>" />
		</div>
		<div class="rows">
			<label>简短介绍</label>
			<span class="input"><textarea name="introduce" class="briefdesc"><?php echo $product_introduce; ?></textarea></span>
			<div class="clear"></div>
		</div>
		<div class="rows">
				<label>产品属性</label>
				<span class="input property">
				   <?php 
				     $query="select id,name from weixin_commonshop_pros where isvalid=true and parent_id=-1 and customer_id=".$customer_id;
					 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
				     while ($row = mysql_fetch_object($result)) {
					    $parent_id = $row->id;
					    $parent_name = $row->name;
				   ?>
					<h1><?php echo $parent_name; ?></h1>
						<ul>
						<?php 
						   $query2="select id,name from weixin_commonshop_pros where isvalid=true and parent_id=".$parent_id;
						   $result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
				           while ($row2 = mysql_fetch_object($result2)) {
						       $p_id = $row2->id;
							   $p_name = $row2->name;
							   
						?>
							 <li>
								<label><input type="checkbox" value="<?php echo $p_id; ?>" <?php if($propertylst->Contains($p_id)){ $pidpnames = $pidpnames.$p_id.",".$parent_name."(".$p_name."),".$parent_id."_"; ?>checked<?php } ?> name="ptids" onclick="chkPro(this,<?php echo $p_id; ?>,'<?php echo $parent_name."(".$p_name.")"; ?>',<?php echo $parent_id; ?>);">
								<?php echo $p_name; ?></label>
							</li>
						<?php } ?>
					</ul>
						<div class="clear"></div>
				    <?php }
                     $pidpnames = rtrim($pidpnames,"_");
					?>
						
											
					</span>
					
					<input type=hidden name="pidpnames" id="pidpnames" value="<?php echo $pidpnames; ?>" />
					
					<input type=hidden name="propertyids" id="propertyids" value="<?php echo $product_propertyids; ?>" />
				<div class="clear"></div>
		</div>
		<div class="rows">
			<label>产品价格</label>
			<span class="input price" id="div_proprices" style="height:auto;">
					原价:￥<input type="text" name="orgin_price" value="<?php echo $product_orgin_price; ?>" class="form_input" size="5" maxlength="10">
					现价:￥<input type="text" name="now_price" value="<?php echo $product_now_price; ?>" class="form_input" size="5" maxlength="10">
					库存: <input type="text" name="storenum" value="<?php echo $product_storenum; ?>" class="form_input" size="5" maxlength="10">
			</span>
			<input type=hidden id="hide_proprice" name="hide_proprice" value="" />
			<input type=hidden id="hide_selpros" name="hide_selpros" value="" />
			<div class="clear"></div>
		</div>
		<div class="rows">
			<?php 
			if($product_id>0){?>
			<label>销售量</label>
			<?php
			}else{
			?>
			<label>初始销售量</label>
			<?php
			}
			?>
			<span class="input sell_count" id="div_sell_count" style="height:auto;">
					数量：<input type="text" name="sell_count" value="<?php echo $sell_count; ?>" class="form_input" size="5" maxlength="10">
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
		<label>其他属性</label>
		<span class="input attr">
			<label><input type="checkbox" <?php if($product_isout){?>checked<?php } ?> id="chk_isout" onclick="changeOut(this);">下架&nbsp;</label>
			<label><input type="checkbox" <?php if($product_isnew){?>checked<?php } ?> id="chk_isnew" onclick="changeNew(this);">新品&nbsp;</label>
			<label><input type="checkbox"  <?php if($product_ishot){?>checked<?php } ?> id="chk_ishot" onclick="changeHot(this);">热卖&nbsp;</label>&nbsp;&nbsp;
			排序优先级: 
			<select name="asort">
				<option value="-1" selected>默认</option>
				<option value="5" <?php if($product_asort==5){?>selected<?php } ?>>一级优先</option>
				<option value="4" <?php if($product_asort==4){?>selected<?php } ?>>二级优先</option>
				<option value="3" <?php if($product_asort==3){?>selected<?php } ?>>三级优先</option>
				<option value="2" <?php if($product_asort==2){?>selected<?php } ?>>四级优先</option>
				<option value="1" <?php if($product_asort==1){?>selected<?php } ?>>五级优先</option>
			</select>
			&nbsp;&nbsp;
			排序位置：<input type="text" style="width:50px;" name="asort_value" value="<?php echo $product_asort_value; ?>" />(按降序排序)
			
			
		</span>
		<div class="clear"></div>
	  </div>
  	<?php if($issell){ ?>
	    <div class="rows">
		<label>分销折扣率</label>
		<span class="input attr">
		   <input type="text" name="pro_discount" id="pro_discount" style="width:50px;" value="<?php echo $pro_discount; ?>" />% (0:表示无折扣)
		</span>
		<div class="clear"></div>
		</div>
		
		<div class="rows">
		<label>总佣金比例</label>
		<span class="input attr">
		   <input type="text" name="pro_reward" id="pro_reward" style="width:50px;" value="<?php echo $pro_reward; ?>" />（0～1）
		</span>
		<div class="clear"></div>
		</div>
    <?php } ?>		
			<div class="rows">
			<label>详细介绍</label>
			<span class="input">
			  <textarea id="editor1"   name="description"><?php echo $product_description; ?></textarea>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
		<label></label>
		<span class="input"><input type="button" class="btn_green" onclick="saveProduct();" name="submit_button" value="提交保存">
		<a href="#" class="btn_gray">返回</a></span>
		<div class="clear"></div>
	</div>
	<input type=hidden name="keyid" id="keyid" value="<?php echo $product_id; ?>" />
	<input type=hidden name="isout" id="isout" value=<?php echo $product_isout; ?> />
	<input type=hidden name="isnew" id="isnew" value=<?php echo $product_isnew; ?> />
	<input type=hidden name="ishot" id="ishot" value=<?php echo $product_ishot; ?> />
	<input type=hidden name="pro_price_detail" id="pro_price_detail" />
	<input type=hidden name="tradeprices" id="tradeprices" value="<?php echo $product_tradeprices; ?>" />
	<input type=hidden name="type_ids" id="type_ids" value=<?php echo $type_ids; ?> />
	
</form></div>	</div>
<div>

	<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>

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

function setParentImgIds(ids){
   $("#imgids").attr("value",ids);
}

function chkPro(obj, proid,proname,proparent_id){
   var proidobj = document.getElementById("pidpnames");
   var propertyids = proidobj.value;
   var pidarr = propertyids.split("_");
   var len = pidarr.length;
   var isin = false;
   var oldnames="";
   if(len>0){
	  for(i=0;i<len;i++){
		 var pidpnames = pidarr[i];
		 var pidpnamearr = pidpnames.split(",");
		 var pid = pidpnamearr[0];
		 var pn = pidpnamearr[1];
		 if(pid==proid){
			isin =true;
			break;
		 }
	  }
   }
   if(obj.checked){
	   if(!isin){
	       var s = proid+","+proname+","+proparent_id;
		   
		   var str = "";
		   var isadd = false;
		   if(propertyids){
			   for(i=0;i<len;i++){
					var pidpnames = pidarr[i];
					var pidpnamearr = pidpnames.split(",");
					var pid = pidpnamearr[0];
					 
					var pn = pidpnamearr[1];
					var pparent_id= pidpnamearr[2];
					if(pid>proid){
						if(!isadd){
						   str = str +s+"_";
						   isadd=true;
						}
						str = str + pid+","+pn+","+pparent_id+"_";
					}else{
						str = str + pid+","+pn+","+pparent_id+"_";
						if(i==len-1){
						   str = str + s+"_";
						}
					}
				}
			}else{
			   str = s+"_";
			}
			if(str!=""){
			   str = str.substring(0,str.length-1);
			}
		    proidobj.value = str;
	   }
   }else{
       if(isin){
	       var s = "";
	       for(i=0;i<len;i++){
			 var pidpnames = pidarr[i];
			 var pidpnamearr = pidpnames.split(",");
			 var pid = pidpnamearr[0];
			 var pn = pidpnamearr[1];
			 var prid = pidpnamearr[2];
			 if(pid!=proid){
			     s = s+pid+","+pn+","+prid+"_";
			 }
		  }
		  if(s!=""){
		      s = s.substring(0,s.length-1);
			  proidobj.value = s;
		  }else{
		      proidobj.value = "";
		  }
	   }
   }
   parrLst = new ArrayList();
   setPropPrice(proidobj.value);
}

var pidpnames = "<?php echo $pidpnames; ?>";
parrLst = new ArrayList();
setPropPrice(pidpnames);


function setPropPrice(pidpnames){
    if(pidpnames!=""){
       var pidarr = pidpnames.split("_");
	   var len = pidarr.length;
	   var pp_parent_id=-1;
	   subLst = new ArrayList();
	   
	   for(var i=0;i<len; i++){
		  var pitem = pidarr[i];
		  var pidpnamearr = pitem.split(",");
		  var pid = pidpnamearr[0];
		  var pn = pidpnamearr[1];
		  var parent_id = pidpnamearr[2];
		  if(pp_parent_id==-1){
		      subLst.add(pitem);
			  pp_parent_id = parent_id;
			  if(i==len-1){
			    parrLst.add(subLst); 
			  }
			  continue;
		  }
		  
		  if(pp_parent_id!=parent_id){
		     parrLst.add(subLst);
		     subLst= new ArrayList();
			 pp_parent_id = parent_id;
			 subLst.add(pitem);
			 if(i==len-1){
			    parrLst.add(subLst); 
			 }
			 
		  }else{
		     subLst.add(pitem);
			 if(i==len-1){
			    parrLst.add(subLst); 
			 }
		  }
       }
	}
	var str = "";
	str = str +" 原价:￥<input type=\"text\" name=\"orgin_price\" id=\"orgin_price\" value=\"<?php echo $product_orgin_price; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
	str = str +" 现价:￥<input type=\"text\" name=\"now_price\" id=\"now_price\" value=\"<?php echo $product_now_price; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
	str = str +" 成本:<input type=\"text\" name=\"cost_price\" id=\"cost_price\" value=\"<?php echo $product_cost_price; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
    str = str +" 所需积分:<input type=\"text\" name=\"need_score\" id=\"need_score\" value=\"<?php echo $product_need_score; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
	str = str +" 库存: <input type=\"text\" name=\"storenum\" id=\"storenum\" value=\"<?php echo $product_storenum; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
	str = str +" 外部标识: <input type=\"text\" name=\"foreign_mark\" id=\"foreign_mark\" value=\"<?php echo $product_foreign_mark; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\"><br/><br/>";
	str = str +"<font style=\"font-weight:bold\">属性价格</font>:<br/>";
	for(var i=0;i<parrLst.size();i++){
	
		var pitemLst = parrLst.get(i);
		for(var j=0;j<pitemLst.size();j++){
		    var pitem = pitemLst.get(j);
			var pidpnamearr = pitem.split(",");
			var pid = pidpnamearr[0];
			var pn = pidpnamearr[1];
			var parent_id = pidpnamearr[2];
			var orgin_price="";
			var now_price="";
			var storenum=1;
			var need_score="";
			var cost_price="";
			var foreign_mark="";
			if(ppriceHash.contains(pid)){
			    var onprice = ppriceHash.items(pid);
				onprices =  onprice.split("_");
				orgin_price = onprices[0];
				now_price = onprices[1];
				storenum = onprices[2];
				need_score = onprices[3];
				cost_price = onprices[4];
				foreign_mark = onprices[5];
			}
			str = str +""+pn+"  原价:￥<input type=\"text\" name=\"pro_orgin_price\" value=\""+orgin_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 现价:￥<input type=\"text\" name=\"pro_now_price\" value=\""+now_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 成本:￥<input type=\"text\" name=\"pro_cost_price\" value=\""+cost_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 所需积分: <input type=\"text\" name=\"pro_need_score\" value=\""+need_score+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 库存: <input type=\"text\" name=\"pro_storenum\" value=\""+storenum+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 外部标识: <input type=\"text\" name=\"pro_foreign_mark\" value=\""+foreign_mark+"\" class=\"form_input\" size=\"5\" maxlength=\"10\"><br/><br/>";
			str = str +" <input type=hidden name=\"proids\" value=\""+pid+"\" />";
			
			str = str+ getsubstr2(i+1,pn,pid,parent_id);
		}
	}
	var divp = document.getElementById("div_proprices");
	divp.innerHTML = str;
}

function getsubstr2(pos,parent_pn,parent_pid,parent_parent_id){
   var str = "";
  
   for(var n=pos;n<parrLst.size();n++){
        var pitemLst = parrLst.get(n);
		for(var m=0;m<pitemLst.size();m++){
		    var pitem = pitemLst.get(m);
			var pidpnamearr = pitem.split(",");
			var pid = pidpnamearr[0];
			var pn = parent_pn+"_"+pidpnamearr[1];
			var parent_id = pidpnamearr[2];
			var ppid = parent_pid+"_"+pid;
			var orgin_price="";
			var now_price="";
			var storenum=1;
			var need_score="";
			var cost_price="";
			var foreign_mark="";
			if(ppriceHash.contains(ppid)){
			    var onprice = ppriceHash.items(ppid);
				onprices =  onprice.split("_");
				orgin_price = onprices[0];
				now_price = onprices[1];
				storenum = onprices[2];
				need_score = onprices[3];
				cost_price = onprices[4];
				foreign_mark = onprices[5];
			}
			str = str +""+pn+"  原价:￥<input type=\"text\" name=\"pro_orgin_price\" value=\""+orgin_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 现价:￥<input type=\"text\" name=\"pro_now_price\" value=\""+now_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 成本:￥<input type=\"text\" name=\"pro_cost_price\" value=\""+cost_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 所需积分: <input type=\"text\" name=\"pro_need_score\" value=\""+need_score+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 库存: <input type=\"text\" name=\"pro_storenum\" value=\""+storenum+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 外部标识: <input type=\"text\" name=\"pro_foreign_mark\" value=\""+foreign_mark+"\" class=\"form_input\" size=\"5\" maxlength=\"10\"><br/><br/>";
			str = str +" <input type=hidden name=\"proids\" value=\""+ppid+"\" />";
			str = str + getsubstr2(n+1,pn,ppid,parent_id);
		}
   }
   return str;
}
function setParentDefaultimgurl(default_imgurl){

    document.getElementById("default_imgurl").value=default_imgurl;
}
</script>
<?php 

mysql_close($link);
?>
</div></div></body></html>
