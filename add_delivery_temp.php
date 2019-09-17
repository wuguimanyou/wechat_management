<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../back_init.php');
$keyid = 0;
$len = count($_GET);
$del = "";
if($len>0){
 if(!empty($_GET["keyid"])){
   $keyid = $configutil->splash_new($_GET["keyid"]);
 }
 if($len>1){
   if(!empty($_GET["del"])){
	  $del = $configutil->splash_new($_GET["del"]);
   }
 }
}


$is_add = true;
$ary_temp = array(); //模板数据数组
if(isset($_GET['id']) && (is_numeric($_GET['id']))){
	
	$is_add = false;
	$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	mysql_query("SET NAMES UTF8");
	require('../../../proxy_info.php');
	
	$temp_id = $_GET['id']; 
	$sql_select_print_temp = 'SELECT * FROM weixin_print_temp where id='.$temp_id;
	$obj_select_print_temp = mysql_query($sql_select_print_temp);
	$row_select_print_temp = mysql_fetch_array($obj_select_print_temp,MYSQL_ASSOC);
	if($row_select_print_temp !== false){ $ary_temp = $row_select_print_temp; }
	else{echo '<script language="javascript">alert("未知错误");location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';die();}	
	
}else{
	
}

//isset to empty 如果数组不存在就返回空值
function i2e($array, $array_key){ if(isset($array[$array_key])){ return $array[$array_key]; }else{return '';} }
//isset to select 检测数组是否存在，如果存在，就检测是否等于$selected_val值，如果等于就返回selected
function i2s($array, $array_key, $selected_val){
	if(isset($array[$array_key])){
		if($array[$array_key] == $selected_val){return 'selected';}
	}else{return '';}
}
//isset to urlencode 检测数组是否存在。如果存在，就将$array_key内数组的JSON值传成数组；再将对应的$json_key值内换成URL编码
function i2u($array, $array_key, $json_id_val){
	if(isset($array[$array_key])){		
		$array_json = json_decode($array[$array_key], true); 
		foreach($array_json as $val){
			if($val['id'] == $json_id_val){
				//print_r($array_json);die();
				$str_json = json_encode($val);//print_r($str_json);die();
				return urlencode($str_json);break;
				
			}
		}		
		return '';	
	}else{return '';}
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />    
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title>添加快递单模板</title>
<link rel="stylesheet" href="css/component-min.css">    
<link rel="stylesheet" href="css/jbox-min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/diy-min.css">
<link rel="stylesheet" href="css/colorpicker.css">
<link rel="stylesheet" type="text/css" href="css/jquery.ui.resizable-min.css">
<link rel="stylesheet" href="css/delivery.css">
<link rel="stylesheet" type="text/css" href="uploadify/uploadify.css"/>
<style>

</style>
</head>

<script>     


</script>

<body class="cp-bodybox">

<div class="container">
	<div class="inner clearfix">

	<!-- end content-left -->

	<div class="content-right fl">
	 

		<h1 class="content-right-title">编辑快递单模板</h1>
		
		
		<div class="alert alert-danger" id="hack_fixie67" style="display:none;"><strong>提示</strong>系统检测到您使用的浏览器版本过低，如果功能无法正常使用，请尝试升级您的浏览器。</div>
		<div class="express-settingbox">
			<form id="J_FormExpress" action="save_delivery.php" method="post">
				<div class="formitems" style="vertical-align: top; display: inline-block; margin-left: 0;">
					<label class="fi-name" style="width: 80px;"><span class="colorRed"> </span>物&nbsp;流&nbsp;公&nbsp;司：</label>
					<div class="form-controls" style="margin-left: 90px;">
						<select class="select small" name="shipping_company_id" id="J_ShippingCompanyId">
							<option value="">--请选择--</option>
							<option value="1" >顺丰</option><option value="2" >申通</option><option value="3" >圆通</option><option value="4" >EMS</option><option value="5" >中通</option><option value="6" >韵达</option><option value="7" >中国邮政</option><option value="8" >宅急送</option><option value="9" >天天</option><option value="11" >百世汇通</option><option value="10" >其它</option>                    </select>
						<span class="fi-help-text hide"></span>
					</div>
				</div>
				<div class="formitems" style="vertical-align: top; display: inline-block; margin-left: 10px;">
					<label class="fi-name" style="width: 80px;"><span class="colorRed">*</span>模&nbsp;板&nbsp;名&nbsp;称：</label>
					<div class="form-controls" style="margin-left: 90px;">
						<input type="text" placeholder="" name="print_temp_name" id="J_PrintTempName" class="input" value="<?php echo i2e($ary_temp,'print_name'); ?>"/>
						<span class="fi-help-text hide"></span>
					</div>
				</div>
				<div class="formitems">
					<label class="fi-name" style="width: 80px;"><span class="colorRed">*</span>快递单尺寸：</label>
					<div class="form-controls" style="vertical-align: top; display: inline-block; margin-left: 10px;">
						<label>长</label>
						<input type="text" placeholder="" class="input mini" name="printing_paper_width" value="<?php echo i2e($ary_temp,'paper_width'); ?>" id="printing_paper_width" />
						<span class="fi-help-text hide"></span>
					</div>
					<div class="form-controls" style="vertical-align: top; display: inline-block; margin-left: 0;">
						<label>宽</label>
						<input type="text" placeholder="" class="input mini" name="printing_paper_height" value="<?php echo i2e($ary_temp,'paper_height'); ?>" id="printing_paper_height" />
						<span class="fi-help-text hide"></span>
					</div>
					<div class="form-controls" style="vertical-align: top; display: inline-block; margin-left: 0; overflow: hidden;">
						<input type="hidden" name="img_url" id="J_ImgUrl" value="<?php echo i2e($ary_temp,'base_temp_img'); ?>" />
						<a href="javascript:;" class="btn btn-primary" id="upload_picture">上传底图</a>
						
						
					</div>
				<span class="fi-help-text">设置【快递单尺寸】的实际尺寸，单位mm</span>
					
				
				</div>
				<input type="hidden" name="act" value="submit" />
				<?php 
					//$re_url = ''; if(isset($_GET['re_url'])){$re_url = $_GET['re_url'];}					
				?>
				<?php 
				$re_url = ''; if(isset($_SERVER['HTTP_REFERER'])){$re_url = $_SERVER['HTTP_REFERER'];}			 
				//if($re_url){ $re_url = urlencode($re_url); }
				?>
				<input type="hidden" name="re_url" value="<?php echo $re_url; ?>" />
				<input type="hidden" name="temp_id" id="temp_id" value="<?php echo i2e($ary_temp,'id'); ?>" />
				<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $_GET['customer_id']; ?>" />
			</form>
			<select id="slt_opts" class="select">
				<option value="">请选择打印项</option>
				<option value="ordersn">订单 - 订单号</option><option value="realname2">收货人 - 姓名</option><option value="province2">收货人 - 省份</option><option value="address2">收货人 - 地址</option><option value="zipcode2">收货人 - 邮编</option><option value="mobile2">收货人 - 手机</option><option value="sitename">网店名称</option><option value="address">发货人 - 地址</option><option value="tel">发货人 - 电话</option><option value="year">年</option><option value="month">月</option><option value="day">日</option><option value="city2">收货人 - 城市</option><option value="district2">收货人 - 地区</option><option value="itemname">商品名称</option><option value="itemnum">商品数量</option>        </select>
			<select id="slt_fontsize" class="select small">
				<option value="">字体大小</option>
				<option value="10">10px</option>
				<option value="12">12px</option>
				<option value="14">14px</option>
				<option value="16">16px</option>
				<option value="18">18px</option>
				<option value="20">20px</option>
				<option value="22">22px</option>
				<option value="24">24px</option>
			</select>

			<select id="slt_letterSpacing" class="select small">
				<option value="">文字间距</option>
				<option value="0">0px</option>
				<option value="2">2px</option>
				<option value="4">4px</option>
				<option value="6">6px</option>
				<option value="8">8px</option>
				<option value="10">10px</option>
				<option value="12">12px</option>
				<option value="14">14px</option>
				<option value="16">16px</option>
				<option value="18">18px</option>
			</select>

			<span>右偏移：</span><input type="text" class="input" id="ipt_posLeft" style="width:50px;"><span>px</span>
			<span>下偏移：</span><input type="text" class="input" id="ipt_posTop" style="width:50px;"><span>px</span>
			<a href="javascript:;" id="ckb_fontbold" class="ckb-font" title="加粗"><i class="expicon bold"></i></a>
			<a href="javascript:;" id="ckb_fontitalic" class="ckb-font" title="斜体"><i class="expicon italic"></i></a>
		</div>
		<div id="express-editor">
			<div class="textarea-item J_PrintItem" id="ordersn">
			<textarea name="ordersn" data-tip_value="订单 - 订单号" data-item_config="<?php echo i2u($ary_temp,'items_params', 'ordersn'); ?>">订单 - 订单号</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="realname2">
			<textarea name="realname2" data-tip_value="收货人 - 姓名" data-item_config="<?php echo i2u($ary_temp,'items_params', 'realname2'); ?>">收货人 - 姓名</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="province2">
			<textarea name="province2" data-tip_value="收货人 - 省份" data-item_config="<?php echo i2u($ary_temp,'items_params', 'province2'); ?>">收货人 - 省份</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="address2">
			<textarea name="address2" data-tip_value="收货人 - 地址" data-item_config="<?php echo i2u($ary_temp,'items_params', 'address2'); ?>">收货人 - 地址</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="zipcode2">
			<textarea name="zipcode2" data-tip_value="收货人 - 邮编" data-item_config="<?php echo i2u($ary_temp,'items_params', 'zipcode2'); ?>">收货人 - 邮编</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="mobile2">
			<textarea name="mobile2" data-tip_value="收货人 - 手机" data-item_config="<?php echo i2u($ary_temp,'items_params', 'mobile2'); ?>">收货人 - 手机</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="sitename">
			<textarea name="sitename" data-tip_value="网店名称" data-item_config="<?php echo i2u($ary_temp,'items_params', 'sitename'); ?>">网店名称</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="address">
			<textarea name="address" data-tip_value="发货人 - 地址" data-item_config="<?php echo i2u($ary_temp,'items_params', 'address'); ?>">发货人 - 地址</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="tel">
			<textarea name="tel" data-tip_value="发货人 - 电话" data-item_config="<?php echo i2u($ary_temp,'items_params', 'tel'); ?>">发货人 - 电话</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="year">
			<textarea name="year" data-tip_value="年" data-item_config="<?php echo i2u($ary_temp,'items_params', 'year'); ?>">年</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="month">
			<textarea name="month" data-tip_value="月" data-item_config="<?php echo i2u($ary_temp,'items_params', 'month'); ?>">月</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="day">
			<textarea name="day" data-tip_value="日" data-item_config="<?php echo i2u($ary_temp,'items_params', 'day'); ?>">日</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="city2">
			<textarea name="city2" data-tip_value="收货人 - 城市" data-item_config="<?php echo i2u($ary_temp,'items_params', 'city2'); ?>">收货人 - 城市</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			<div class="textarea-item J_PrintItem" id="district2">
			<textarea name="district2" data-tip_value="收货人 - 地区" data-item_config="<?php echo i2u($ary_temp,'items_params', 'district2'); ?>">收货人 - 地区</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			
			
			
			
			<div class="textarea-item J_PrintItem" id="itemname">
			<textarea name="itemname" data-tip_value="商品名称" data-item_config="<?php echo i2u($ary_temp,'items_params', 'itemname'); ?>">商品名称</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
			
			
			
			<div class="textarea-item J_PrintItem" id="itemnum">
			<textarea name="itemnum" data-tip_value="商品数量" data-item_config="<?php echo i2u($ary_temp,'items_params', 'itemnum'); ?>">商品数量</textarea>
			<a href="javascript:;" title="移除" class="textarea-item-del"><i class="exp-ctrl-icon remove"></i></a>
			<a href="javascript:;" title="移动" class="textarea-item-move"><i class="exp-ctrl-icon move"></i></a>            </div>
					
					
			<!-- 底图开始 -->
			<div class="default-height">
				<img id="J_ExpressBG" src="<?php echo i2e($ary_temp,'base_temp_img'); ?>" />
			</div>
			<!-- 底图结束 -->
		</div>

		<div class="mgt15">
			<a href="javscript:;" class="btn btn-primary" id="btn_confirm"><i class="gicon-check white"></i>确定</a>
		</div>

	</div>
	<!-- end content-right -->
	</div>
</div>


<script src="js/lib-min.js"></script>
<script src="js/jquery.jbox-min.js"></script>
<script src="js/jquery.zclip-min.js"></script>

<script src="js/component-min.js"></script>



<script src="js/diyueditor-min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/diy-min.js"></script>

<script type="text/javascript" src="js/ui-104/jquery-ui.min.js"></script>
<script src="js/add_delivery.js"></script>
<script src="uploadify/jquery.uploadify.min.js"></script>

<script>
 $(function(){
	<?php if($is_add) {?>
	var src = 'images/source_back.png';	
	srcs = src.replace('@!w640','');
	$('.default-height').empty().append('<img src="'+srcs+'" style="width:740px;" alt="">');//console.log('012');
	<?php }else{ ?>
	var src = $('#J_ExpressBG').attr('src');	//console.log(src+'123');
	srcs = src.replace('@!w640','');
	$('.default-height').empty().append('<img src="'+srcs+'" style="width:740px;" alt="">')

	<?php } ?>
});
</script>
<script type="text/javascript">
    $("#upload_picture").uploadify({
        "height"          : 30,
        "swf"             : "uploadify/uploadify.swf",
        "fileObjName"     : "Filedata",
        "buttonText"      : "上传底图",
        "uploader"        : "uploadify.php?customer_id=10086",
        "width"           : 120,
        'removeTimeout'	  : 1,
        'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
        "onUploadSuccess" : uploadPicture,
        'onFallback' : function() {
            alert('未检测到兼容版本的Flash.');
        }
    });
    function uploadPicture(file, data){                            
        var data = $.parseJSON(data);                            
        $('.default-height > img').attr('src',data.webpath);
        $('#J_ImgUrl').val(data.webpath);
    }
</script>
<script>

</script>
<?php if(!$is_add){mysql_close($link);}  ?>
</body>
</html>

