<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
$customer_id = passport_decrypt($customer_id);//print_r($customer_id);die();
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
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../../../proxy_info.php');
//error_reporting(0);
  /* 删除快递模板 */
  if($del=="isok"){
     $query = 'update  weixin_expresses set isvalid=false where id='.(int)$keyid;
	 mysql_query($query);
	 mysql_close($link);
	 echo "<script>location.href='express.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	 return;
  }
	$name 		   = "";	//快递名称   
	$type		   =  1;	//计价方式。1：按件数；2：按重量
	$FreeNum       =  0;	//免邮设置。按件免邮/按重免邮
	$FirstNum      =  1;	//首件件数/首重重量
	$ContinueNum   =  1;	//续件件数/续重重量
	$price 		   =  0;	//首件/首重费用
	$ContinuePrice =  0;	//续件费用/续重费用
	$is_include	   =  1;	//运送范围:0.范围之内 1.范围之外
	$region 	   = "";	//是否允许快递的地区
	$cost 		   =  0;	//快递所需商品总费用
	$expressCode   = "";	//快递100代码
	$region_array  = array();	//快递地区数组
	$print_temp_id = 0; 	//快递打印模板ID
  if($keyid>0){
	$query = 'SELECT id,name,is_include,region,cost,expressCode,type,FirstNum,ContinueNum,price,ContinuePrice,FreeNum,print_temp_id FROM weixin_expresses where id='.$keyid;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
	    $name		   = $row->name;
	    $type		   = $row->type;			//计价方式
		$FirstNum      = $row->FirstNum;	    //首件件数/首重重量
		$ContinueNum   = $row->ContinueNum;		//续件件数/续重重量
		$price         = $row->price;			//首件费用/首重费用
		$ContinuePrice = $row->ContinuePrice;	//续件费用/续重费用
		$FreeNum       = $row->FreeNum;
		$is_include    = $row->is_include;
		$region        = $row->region;
		$cost          = $row->cost;
		$expressCode   = $row->expressCode;
		$print_temp_id = $row->print_temp_id;
		if(!empty($region)){$region_array  = explode(",",$region);}else{$region_array = array();} //这里不加判断，字段为空的话，会出现警告提示；
		
	}
	
	
	
	
  }
	$sql_print_temp = "SELECT id,print_name from weixin_print_temp WHERE isvalid=1 AND is_supply=0 AND customer_id=".$customer_id;
	$obj_print_temp = mysql_query($sql_print_temp); $array_print_temp = array();
	while ($row_print_temp = mysql_fetch_object($obj_print_temp)){
		$array_print_temp[] = $row_print_temp;
	}
	
	
//list in select 检测是否等于$val值是否相等，如果等于就返回$selected值，默认为selected
function l_s($val_0, $val_1, $selected='selected'){
	//if(in_array($array_val,$array)){return $selected;}else{return '';}
	if($val_0==$val_1){return $selected;}else{return '';}
}	
  
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../css/chosen.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>

.add_content_one{
  width:90%;
  padding-left: 20px;
  margin:0;
  margin-top:10px;
  height:30px;
  line-height:30px;
  font-size:13px;
  text-align:left;
  font-weight:bolder;
}
.split_line{
	width:100%;
	/*border-bottom: 1px solid  #d4d4d4;
	border-top:none;*/
	border-bottom:1px solid #ccc;
	border-top:1px solid #fff;
}
.add_content_con_one{
   width:90%;
   margin:0 auto;
   margin-top:10px;
   height:40px;
   line-height:20px;
}
.add_content_con_l{

   width:10%;
   float:left;
   heigth:100%;
   text-align:right;
   
}
.add_content_con_r{

   width:90%;
   float:left;
   heigth:100%;
   text-align:left;
}

.add_content_con_two{
   width:90%;
   margin:0 auto;
   margin-top:10px;
   height:auto;
}
.add_content_con_four{
   width:90%;
   margin:0 auto;
   margin-top:30px;
   height:250px;
   
}
.add_content_con_three{

  width:90%;
  margin:0 auto;
  text-align:left;
  height:100px;;
}

.add_content_con_three_con{
  height:40px;
  width:100%; 
}
.button_blue{font-size: 14px;display: block;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:5px;color: #fff;}
.r_con_form{border:0 none;}
.r_con_form .rows{border: 0 none;}
.r_con_form .rows .input{border: 0 none;}
.r_con_form .rows .input2{border: 0 none;}
.r_con_wrap{background: rgb(251, 251, 251);}
.border_left{border: 0 none;}
.r_con_form .rows > label{line-height:24px;text-align: left;width: 11%;font-size:14px;}
.r_con_form .rows:hover{background:#fbfbfb;}
.rows input[type="text"]{padding-left:5px;height:24px;border-radius:2px;}
</style>
</head>

<script>     
function submitV(){
  
  var name =$('#name').val();
  if(name==""){
	  alert('请输入快递公司名称');
	  return;
  }
 var type =$("input[name='type'][checked]").val(); 	//计价方式
 var FirstNum =$('#FirstNum').val();				//首件件数/首重重量
 var ContinueNum =$('#ContinueNum').val();			//续件件数/续重重量
 var price =$('#price').val();						//首件费用/首重费用
 var ContinuePrice =$('#ContinuePrice').val();		//续件费用/续重费用
 var cost =$('#cost').val();						//所需金额
 var Costtype_str1 = $('#Costtype_str1').text();
 var Costtype_str2 = $('#Costtype_str2').text();
 var Costtype_str3 = $('#Costtype_str3').text();
 var Costtype_str4 = $('#Costtype_str4').text();
 var  re =   new RegExp("^[0-9]*[1-9][0-9]*$"); 
  
  if(FirstNum.match(re)==null && type==1){ 
	  alert(Costtype_str1+"请输入大于零的整数!"); 
	  return;
  } 
  if(FirstNum==""){
	  alert('请输入'+Costtype_str1);
	  return;
  }
  if(isNaN(FirstNum) || FirstNum<0){
	  alert('请输入正确'+Costtype_str1);
	  return;
  }
  if(ContinueNum.match(re)==null && type==1){ 
	  alert(Costtype_str2+"请输入大于零的整数!"); 
	  return;
  } 
  if(ContinueNum==""){
	  alert('请输入'+Costtype_str2);
	  return;
  }
  if(isNaN(ContinueNum) || ContinueNum<0){
	  alert('请输入正确'+Costtype_str2);
	  return;
  }
  
  if(price==""){
	  alert('请输入'+Costtype_str3);
	  return;
  } 
  if(isNaN(price) || price<0){
	 alert('请输入正确'+Costtype_str3);
	  return;
  }
  
  if(ContinuePrice==""){
	  alert('请输入'+Costtype_str4);
	  return;
  }
  if(isNaN(ContinuePrice) || ContinuePrice<0){
	  alert('请输入正确'+Costtype_str4);
	  return;
  }
  
  if(cost==""){
	  alert('请输入所需金额');
	  return;   
  }
  if(isNaN(cost) || cost<0){
	  alert('所需金额请输入正确金额');
	  return;
  }
  //if($('#print_temp_id').val() == '0'){ alert('请选择要关联的打印模板'); return;}
  
  document.getElementById("keywordFrm").submit();
}
</script>

<body>

<div >  
    <div class="WSY_content">
		<div class="WSY_columnbox">
		<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">添加运费规则</a>   
				</div>
		</div>  
<form action="saveexpress.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" id="keywordFrm" method="post">
    <div class="add_content_one">
	    添加运费规则
	</div>
	
	<div id="products" class="r_con_wrap">
	  <div class="r_con_form" >
		
		<div class="rows">
			<label>区域模式</label>
			<span class="input">
			   <label><input type="radio" name="include" value="0" <?php if($is_include==0){?> checked="checked" <?php } ?>>区域之内&nbsp;</label>
			   <label><input type="radio" name="include" value="1" <?php if($is_include==1){?> checked="checked" <?php } ?>>区域之外&nbsp;</label>
			</span>
			<div class="clear"></div>
		</div>
		<?php //$region_array = array();//print_r($region_array);die(); ?>
		<div class="rows">
			<label>区域选择</label>
			<span class="input2">
				<select data-placeholder="区域" multiple class="chosen-select" tabindex="8" name="region[]">
					<option value=""></option>
					<option value="北京市" <?php if(in_array("北京市",$region_array)){echo "selected";} ?>>北京市</option>
					<option value="天津市" <?php if(in_array("天津市",$region_array)){echo "selected";} ?>>天津市</option>
					<option value="河北省"<?php if(in_array("河北省",$region_array)){echo "selected";} ?>>河北省</option>
					<option value="山西省" <?php if(in_array("山西省",$region_array)){echo "selected";} ?>>山西省</option>
					<option value="内蒙古自治区" <?php if(in_array("内蒙古自治区",$region_array)){echo "selected";} ?>>内蒙古自治区</option>
					<option value="辽宁省" <?php if(in_array("辽宁省",$region_array)){echo "selected";} ?>>辽宁省</option>
					<option value="吉林省" <?php if(in_array("吉林省",$region_array)){echo "selected";} ?>>吉林省</option>
					<option value="黑龙江省" <?php if(in_array("黑龙江省",$region_array)){echo "selected";} ?>>黑龙江省</option>
					<option value="上海市" <?php if(in_array("上海市",$region_array)){echo "selected";} ?>>上海市</option>
					<option value="江苏省" <?php if(in_array("江苏省",$region_array)){echo "selected";} ?>>江苏省</option>
					<option value="浙江省" <?php if(in_array("浙江省",$region_array)){echo "selected";} ?>>浙江省</option>
					<option value="安徽省" <?php if(in_array("安徽省",$region_array)){echo "selected";} ?>>安徽省</option>
					<option value="福建省" <?php if(in_array("福建省",$region_array)){echo "selected";} ?>>福建省</option>
					<option value="江西省" <?php if(in_array("江西省",$region_array)){echo "selected";} ?>>江西省</option>
					<option value="山东省" <?php if(in_array("山东省",$region_array)){echo "selected";} ?>>山东省</option>
					<option value="河南省" <?php if(in_array("河南省",$region_array)){echo "selected";} ?>>河南省</option>
					<option value="湖北省" <?php if(in_array("湖北省",$region_array)){echo "selected";} ?>>湖北省</option>
					<option value="湖南省" <?php if(in_array("湖南省",$region_array)){echo "selected";} ?>>湖南省</option>
					<option value="广东省" <?php if(in_array("广东省",$region_array)){echo "selected";} ?>>广东省</option>
					<option value="广西壮族自治区" <?php if(in_array("广西壮族自治区",$region_array)){echo "selected";} ?>>广西壮族自治区</option>
					<option value="海南省" <?php if(in_array("海南省",$region_array)){echo "selected";} ?>>海南省</option>
					<option value="重庆市" <?php if(in_array("重庆市",$region_array)){echo "selected";} ?>>重庆市</option>
					<option value="四川省" <?php if(in_array("四川省",$region_array)){echo "selected";} ?>>四川省</option>
					<option value="贵州省" <?php if(in_array("贵州省",$region_array)){echo "selected";} ?>>贵州省</option>
					<option value="云南省" <?php if(in_array("云南省",$region_array)){echo "selected";} ?>>云南省</option>
					<option value="西藏自治区" <?php if(in_array("西藏自治区",$region_array)){echo "selected";} ?>>西藏自治区</option>
					<option value="陕西省" <?php if(in_array("陕西省",$region_array)){echo "selected";} ?>>陕西省</option>
					<option value="甘肃省" <?php if(in_array("甘肃省",$region_array)){echo "selected";} ?>>甘肃省</option>
					<option value="青海省" <?php if(in_array("青海省",$region_array)){echo "selected";} ?>>青海省</option>
					<option value="宁夏回族自治区" <?php if(in_array("宁夏回族自治区",$region_array)){echo "selected";} ?>>宁夏回族自治区</option>
					<option value="新疆维吾尔自治区" <?php if(in_array("新疆维吾尔自治区",$region_array)){echo "selected";} ?>>新疆维吾尔自治区</option>
					<option value="香港特别行政区" <?php if(in_array("香港特别行政区",$region_array)){echo "selected";} ?>>香港特别行政区</option>
					<option value="澳门特别行政区" <?php if(in_array("澳门特别行政区",$region_array)){echo "selected";} ?>>澳门特别行政区</option>
					<option value="台湾省" <?php if(in_array("台湾省",$region_array)){echo "selected";} ?>>台湾省</option>
					<option value="其它" <?php if(in_array("其它",$region_array)){echo "selected";} ?>>其它</option>
				</select> 
			</span>
			<div class="clear"></div>
		</div>	 
		
		<div class="rows">
			<label>运费规则名称</label>
			<span class="input">
			<input type=text value="<?php echo $name ?>" name="name" id="name" />			
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>关联打印模板</label>
			<span class="input">
			<select name="print_temp_id" id="print_temp_id">
				<option value="0">选择模板绑定</option>
				<?php foreach($array_print_temp as $val){ ?>
				<option value="<?php echo $val->id ?>" <?php echo l_s($val->id,$print_temp_id) ?>><?php echo $val->print_name ?></option>
				<?php } ?>
			</select>
			
			<!--<a href="javascript:select_relist();">刷新列表</a>--> 
			<a href="add_delivery_temp.php?customer_id=<?php echo $customer_id ?>">添加运单模板</a> 
			<a id="edit_print_temp_url" style="display:none;" href="">编辑运单模板</a>
			<a id="del_print_temp_url" style="display:none;" href="" onClick="return confirm('确认要删除吗？');">删除运单模板</a>
			</span>
			<div class="clear"></div>
		</div>
		<!-- <div class="rows">
			<label>快递100编码</label>
			<span class="input">
			<input type=text value="<?php echo $expressCode ?>" name="kuaiDiName" id="kuaiDiName" />
			<a style="color:blue" href="http://www.kuaidi100.com/download/api_kuaidi100_com(20140729).doc" class="aco" target="_blank">API URL 所支持的快递公司及参数说明</a>
			</span>
			<div class="clear"></div>
		</div> -->
		<div class="rows">
			<label>计价方式</label>
			<span class="input">
			   <label><input type="radio" name="type" id="type1" value="1" <?php if($type==1){?> checked="checked" <?php } ?>  onClick="chktype(1);">按件数&nbsp;</label>
			   <label><input type="radio" name="type" id="type2" value="2" <?php if($type==2){?> checked="checked" <?php } ?>  onClick="chktype(2);">按重量&nbsp;</label>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows" >
			<label>快递件数设置(<span id="unit">件</span>)</label>
			<span class="input_bx border_left"><span id="Costtype_str1">首件件数</span>：
				<input type="text" name="FirstNum" id="FirstNum" value="<?php echo $FirstNum;?>" >
			</span>
			<span class="input_bx"><span id="Costtype_str2">续件件数</span>：
				<input type="text" name="ContinueNum" id="ContinueNum" value="<?php echo $ContinueNum;?>" >
			</span>
			<span class="input_bx"><span id="Costtype_Free">按件免邮</span>：
				<input type="text" name="FreeNum" id="FreeNum" value="<?php echo $FreeNum;?>" >
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows" >
			<label>快递配送费用(元)</label>
			<span class="input_bx border_left"><span id="Costtype_str3">首件费用</span>：
				<input type="text" name="price" id="price" value="<?php echo $price;?>" >
			</span>
			<span class="input_bx"><span id="Costtype_str4">续件费用</span>：
				<input type="text" name="ContinuePrice" id="ContinuePrice" value="<?php echo $ContinuePrice;?>" >&nbsp;(0表示无运费)
			</span>
			<div class="clear"></div>
		</div>
	    
		<!--
		<div class="rows">
			<label>运费</label>
			<span class="input">
			<input type=text value="<!?php echo $price ?>" name="price" id="price" />元&nbsp;(0表示无运费)
			</span>
			<div class="clear"></div>
		</div>	
		-->
		<div class="rows">
			<label>选择此快递所需金额(元)</label>
			<span class="input">
			<input type=text value="<?php echo $cost ?>" name="cost" id="cost" />(0表示无限制)
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label> </label>
			<span class="input">
			<input type=button class="WSY_button"  value="提交" onclick="submitV();" style="border:0 none;border-radius: 3px;float:left;margin-right: 10px;"/>
			<input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="border:0 none;border-radius: 3px;float:left;"/>
			</span>
			<div class="clear"></div>
		</div>
		
	  </div>
			<div class="add_content_con_four">
			</div>
	</div>
	
<input type=hidden name="keyid" value="<?php echo $keyid ?>" />
</form>
<div style="width:100%;height:20px;">
</div>
</div>
</div>
</div>
<script type="text/javascript" src="../../../js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../../js/chosen.jquery.min.js" ></script>
<script>
$(function() {
	$('.chosen-select').chosen({
	  no_results_text: '没有找到匹配的区域!',
	  width: '250px'
	});
	var v = $("input[name='type'][checked]").val();
	chktype(v);
	
	$("#print_temp_id").change( function() {
		if($("#print_temp_id").val() == '0'){
			$('#edit_print_temp_url').hide();
			$('#del_print_temp_url').hide();
		}else{
			show_url($("#print_temp_id").val());
		}
	});
	
	<?php if($print_temp_id>0){?>
	show_url($("#print_temp_id").val());
	<?php } ?>
	//select_relist();
	
  
});

function show_url(print_temp_id){
	$('#edit_print_temp_url').show();
	$('#edit_print_temp_url').attr('href','add_delivery_temp.php?id='+print_temp_id);
	$('#del_print_temp_url').show();
	$('#del_print_temp_url').attr('href','save_delivery.php?do=del&id='+print_temp_id);
}


 function chktype(v){
	    v = parseInt(v);
       switch(v){
			case 1:
				$('#unit').text("件");
				$('#Costtype_str1').text("首件件数");
				$('#Costtype_str2').text("续件件数");
				$('#Costtype_str3').text("首件费用");
				$('#Costtype_str4').text("续件费用");
				$('#Costtype_Free').text("按件免邮");
				$('#type1').attr("checked",true);
				$('#type2').removeAttr("checked");
			break;
			
			case 2:
				$('#unit').text("千克");
				$('#Costtype_str1').text("首重重量");
				$('#Costtype_str2').text("续重重量");
				$('#Costtype_str3').text("首重费用");
				$('#Costtype_str4').text("续重费用");
				$('#Costtype_Free').text("续重免邮");
				$('#type2').attr("checked",true);
				$('#type1').removeAttr("checked");
			break;
	   }
}
		   
</script>
<?php mysql_close($link); ?>
</body>
</html>

