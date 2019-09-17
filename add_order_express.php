<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../config.php');
  require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
 require('../back_init.php');   
 
  $order_id = $configutil->splash_new($_GET["order_id"]);

  $expressname = "";
  $expressnum = "";
 
     $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
     mysql_select_db(DB_NAME) or die('Could not select database');
     mysql_query("SET NAMES UTF8");
	$query = 'SELECT id,expressname,expressnum FROM weixin_commonshop_orders where isvalid=true and id='.$order_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$expressname = $row->expressname;
		$expressnum = $row->expressnum;
		break;
	}
	mysql_close($link);

  
 
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/css2.css" media="all">
<link href="../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../common/add/css/shop.css" rel="stylesheet" type="text/css">

<meta http-equiv="content-type" content="text/html;charset=UTF-8">

</head>

<script>
 function submitV(){
    
	
    document.getElementById("keywordFrm").submit();
 }
 
 
</script>

<body>
<div class="div_new_content">

<form action="saveorderexpress.php?customer_id=<?php echo $customer_id_en ?>"  id="keywordFrm" method="post">
    <div class="add_content_one">
	    快递单信息
	</div>
	<div id="products" class="r_con_wrap">
	 <div class="r_con_form" >
		<div class="rows">
			<label>快递公司名</label>
			<span class="input">
			<input type=text value="<?php echo $expressname ?>" name="expressname" id="expressname" />		
			</span>
			<div class="clear"></div>
		</div>
	
		<div class="rows">
			<label>快递单号</label>
			<span class="input">
			<input type=text value="<?php echo $expressnum ?>" name="expressnum" id="expressnum" />		
			</span>
			<div class="clear"></div>
		</div>
	 
		
		<div class="rows">
			<label> </label>
			<span class="input">
				<input type=button class="button"  value="提交" onclick="submitV();" />
		&nbsp;	   
				<input type=button class="button"  value="取消" onclick="document.location='order.php?customer_id=<?php echo $customer_id_en ?>';" />
      		</span>
			 <div class="clear"></div>
		</div>
	<input type=hidden name="order_id" value="<?php echo $order_id ?>" />
	</div>
</div>
</form>
<div style="width:100%;height:20px;">
</div>

</div>
</body>
</html>

