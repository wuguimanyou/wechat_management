<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
$customer_id = passport_decrypt($customer_id);
require('../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../../proxy_info.php');

$keyid = 0;
$del = "";
$pagenum = 1;
if(!empty($_GET["keyid"])){
	$keyid = $configutil->splash_new($_GET["keyid"]);
}
if(!empty($_GET["del"])){
	$del = $configutil->splash_new($_GET["del"]);
}
if(!empty($_GET["pagenum"])){
	$pagenum = $configutil->splash_new($_GET["pagenum"]);
}

if($keyid>0){
	$query = "select snapup_time from snapup_time_t where id=".$keyid;
	$result = mysql_query($query) or die("Query failed:".mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$snapup_time =  $row->snapup_time ;
	}
}
/*删除时间点*/
if($del=="isok"){
	$query = "update snapup_time_t set isvalid=false where id=".$keyid;
	mysql_query($query) or die("Query failed1:".mysql_error());
	mysql_close($link);
	
	header('Location:snapup_time.php?customer_id='.passport_encrypt((string)$customer_id).'&pagenum='.$pagenum);
}
/*删除时间点*/
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../js/tis.js"></script>
<script type="text/javascript" src="../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../js/WdatePicker.js"></script><!--添加时间插件-->
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>

<script>
  
  function submitV(){
      var snapup_time = document.getElementById("snapup_time").value;
	  if(snapup_time==""){
	      alert('请选择时间点！');
	      return;
	  }
	 
	  document.getElementById("upform").submit();
  }
</script>
<body>
<div class="div_new_content">
<form action="save_snapup_time.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&pagenum=<?php echo $pagenum;?>" id="upform" name="upform" method="post">
<input type="hidden" name="keyid" value="<?php echo $keyid;?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox WSY_list">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">添加时间点</a> 
				</div>
			</div>
			<div class="WSY_data">
					<dl class="WSY_member">					
						<div>
							<dt>时间点</dt>
							<dd class="spa">
								<input type="text" style="width:250px" id="snapup_time" name="snapup_time" value="<?php echo $snapup_time; ?>"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'2015-10-25 10:00',maxDate:'2019-10-25 21:30'});" >
							</dd>
						
						</div>
					</dl>
					<div class="WSY_text_input01">
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV();" style="cursor:pointer;"/></div>
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
					</div>
			
			</div>
	
		</div>
	</div>
 </form>
 <div style="width:100%;height:20px;">
 </div>
</div>
<?php
	mysql_close($link);
?>
</body>
</html>