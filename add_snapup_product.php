<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
$customer_id = passport_decrypt($customer_id);
require('../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../../proxy_info.php');

$keyid = -1;

if(!empty($_GET["keyid"])){
	$keyid = $configutil->splash_new($_GET["keyid"]);
}

if($keyid>0){
	$query = "select snapup_time from snapup_time_t where id=".$keyid;
	$result = mysql_query($query) or die("Query failed:".mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$snapup_time =  $row->snapup_time ;
	}
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../js/tis.js"></script>
<script type="text/javascript" src="../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../common/js/layer/layer.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<style>
.WSY_member dt{
	width: auto;
	float: none;
	margin-bottom: 10px;
	font-size: 16px;
}
.WSY_member ul{
	width: 97%;
    margin-left: 20px;
    border: 1px solid #ccc;
    overflow: hidden;
}
.WSY_member ul dd{
	width: 170px;
	margin: 20px;
}
</style>
<script>
  
  function submitV(){	 
	  document.getElementById("upform").submit();
  }
</script>
<body>
<div class="div_new_content">
<form action="save_snapup_product.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>" id="upform" name="upform" method="post">
<input type="hidden" name="keyid" value="<?php echo $keyid;?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox WSY_list">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">关联产品</a> 
				</div>
			</div>
			<div class="WSY_data">
					<dl class="WSY_member">					
						<div>
							<dt>抢购产品列表：</dt>
							<dd style="margin-bottom:15px;margin-left:15px;"><input type="checkbox" id="chk_all"><label for="chk_all">选择全部</label></dd>
							<ul>
							<?php
							$query2 = "select id,name from weixin_commonshop_products where isvalid=true and isout=0 and issnapup=1 and customer_id=".$customer_id." and buystart_time='".$snapup_time."'";
							 
							$result2 = mysql_query($query2) or die('Query failed2'.mysql_error());
							$pid = -1;
							$name = '';
							while($row2 = mysql_fetch_object($result2)){
								$pid  = $row2->id;
								$name = $row2->name;
								
								$query3 = "select count(1) as pcount from snapup_product_t where isvalid=true and customer_id=".$customer_id." and pid=".$pid." and time_id=".$keyid;
								$result3 = mysql_query($query3) or die('Query failed3'.mysql_error());
								$pcount = 0;
								while($row3 = mysql_fetch_object($result3)){
									$pcount = $row3->pcount;
								}
							?>
								<dd>
									<input type="checkbox" id="pid<?php echo $pid;?>" name="product[]" <?php if($pcount>0){echo "checked";}?> data-id="<?php echo $pid;?>" value="<?php echo $pid;?>">
									<label for="pid<?php echo $pid;?>"><?php echo $name;?></label>
								</dd>
							<?php
							}
							?>
							</ul>
						
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
<script>
$('#chk_all').click(function(){
	var chk_all=$(this).prop("checked"); 
	$("input[name='product[]']").prop("checked", chk_all); 
})
</script>
</html>