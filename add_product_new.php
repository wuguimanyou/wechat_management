<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
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
$product_isout=false;
$product_isnew=false;
$product_ishot=false;
$product_tradeprices="";
$product_propertyids = "";

$imgids="";

if(!empty($_GET["product_id"])){
   $product_id = $_GET["product_id"];
   $query="select name,orgin_price,now_price,type_id,introduce,description,asort,isout,isnew,ishot,tradeprices,propertyids from weixin_commonshop_products where isvalid=true and id=".$product_id;
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
	  break;
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
<script>
ppriceHash = new Hashtable();

<?php 
$query="select proids,orgin_price,now_price from weixin_commonshop_product_prices where product_id=".$product_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {

   $proids = $row->proids;
   $orgin_price = $row->orgin_price;
   $now_price = $row->now_price;
   
?>
  var proids = "<?php echo $proids; ?>";
  var orgin_price = <?php echo $orgin_price; ?>;
  var now_price = <?php echo $now_price; ?>;
  ppriceHash.add(proids,orgin_price+"_"+now_price);
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
			<li class=""><a href="base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a></li>
			<li class=""><a href="fengge.php?customer_id=<?php echo $customer_id_en; ?>">风格设置</a></li>
			<li class=""><a href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>">首页设置</a></li>
			<li class="cur"><a href="product.php?customer_id=<?php echo $customer_id_en; ?>">产品管理</a></li>
			<li class=""><a href="order.php?customer_id=<?php echo $customer_id_en; ?>">订单管理</a></li>
			<li class=""><a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>">推广员</a></li>
		</ul>
	</div>
<div id="products" class="r_con_wrap">

<form id="frm_product" class="r_con_form" method="post" action="save_product.php?customer_id=<?php echo $customer_id_en; ?>">
	<div class="rows">
		<label>产品名称</label>
		<span class="input"><input type="text" name="name" id="name" value="<?php echo $product_name;?>" class="form_input" size="35" maxlength="100" notnull=""> <font class="fc_red">*</font></span>
		<div class="clear"></div>
	</div>
		<div class="rows">
			<label>隶属分类</label>
			<span class="input">
			<select name="type_id" id="type_id">
			<option value="-1">--请选择--</option>
			<?php 
			  $query="select id,name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=-1";
			  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			  while ($row = mysql_fetch_object($result)) {
				   $pt_id = $row->id;
				   $pt_name = $row->name;
			?>
			  <option value="<?php echo $pt_id; ?>" <?php if($pt_id==$product_type_id){?>selected <?php } ?>><?php echo $pt_name; ?></option>
			<?php 
			
			      $query2="select id,name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id." and parent_id=".$pt_id;
				  $result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
				  while ($row2 = mysql_fetch_object($result2)) {
					   $sub_pt_id = $row2->id;
					   $sub_pt_name = $row2->name;
			 ?>
			     <option value="<?php echo $sub_pt_id; ?>" <?php if($sub_pt_id==$product_type_id){?>selected <?php } ?>>&nbsp;&nbsp;<?php echo $sub_pt_name; ?></option>
			 <?php }
			 } ?>
			</select>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>产品图片</label>
			<span class="input">
				<span class="upload_file">
					<div>
						<iframe src="iframe_images.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>" height=200 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
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
								<label><input type="checkbox" value="<?php echo $p_id; ?>" <?php if($propertylst->Contains($p_id)){ $pidpnames = $pidpnames.$p_id.",".$p_name.",".$parent_id."_"; ?>checked<?php } ?> name="ptids" onclick="chkPro(this,<?php echo $p_id; ?>,'<?php echo $p_name; ?>',<?php echo $parent_id; ?>);">
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
			</span>
			<input type=hidden id="hide_proprice" name="hide_proprice" value="<?php echo $proprices; ?>" />
			<input type=hidden id="hide_selpros" name="hide_selpros" value="" />
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
				<option value="1" <?php if($product_asort==1){?>selected<?php } ?>>一级优先</option>
				<option value="2" <?php if($product_asort==2){?>selected<?php } ?>>二级优先</option>
				<option value="3" <?php if($product_asort==3){?>selected<?php } ?>>三级优先</option>
				<option value="4" <?php if($product_asort==4){?>selected<?php } ?>>四级优先</option>
				<option value="5" <?php if($product_asort==5){?>selected<?php } ?>>五级优先</option>
			</select>
		</span>
		<div class="clear"></div>
	</div>
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
	<input type=hidden name="isout" id="isout" value=0 />
	<input type=hidden name="isnew" id="isnew" value=0 />
	<input type=hidden name="ishot" id="ishot" value=0 />
	<input type=hidden name="pro_price_detail" id="pro_price_detail" />
	<input type=hidden name="tradeprices" id="tradeprices" value="<?php echo $product_tradeprices; ?>" />
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
		   propertyids = propertyids + "_"+s;
		   proidobj.value = propertyids;
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
		  }
	   }
   }
   parrLst = new ArrayList();
   //alert('pb======'+proidobj.value);
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
	str = str +" 现价:￥<input type=\"text\" name=\"now_price\" id=\"now_price\" value=\"<?php echo $product_now_price; ?>\" class=\"form_input\" size=\"5\" maxlength=\"10\"><br/><br/>";
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
			if(ppriceHash.contains(pid)){
			    var onprice = ppriceHash.items(pid);
				onprices =  onprice.split("_");
				orgin_price = onprices[0];
				now_price = onprices[1];
			}
			console.log("nowPrice======="+now_price);
			str = str +""+pn+"  原价:￥<input type=\"text\" name=\"pro_orgin_price\" value=\""+orgin_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 现价:￥<input type=\"text\" name=\"pro_now_price\" value=\""+now_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\"><br/><br/>";
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
			if(ppriceHash.contains(ppid)){
			    var onprice = ppriceHash.items(ppid);
				onprices =  onprice.split("_");
				orgin_price = onprices[0];
				now_price = onprices[1];
			}
			str = str +""+pn+"  原价:￥<input type=\"text\" name=\"pro_orgin_price\" value=\""+orgin_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\">";
			str = str +" 现价:￥<input type=\"text\" name=\"pro_now_price\" value=\""+now_price+"\" class=\"form_input\" size=\"5\" maxlength=\"10\"><br/><br/>";
			str = str +" <input type=hidden name=\"proids\" value=\""+ppid+"\" />";
			str = str + getsubstr2(n+1,pn,ppid,parent_id);
		}
   }
   return str;
}
</script>
<?php 

mysql_close($link);
?>
</div></div></body></html>
