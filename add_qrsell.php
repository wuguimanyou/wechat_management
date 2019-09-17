<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../config.php');
  require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
 require('../back_init.php');   
 
  $user_id = $configutil->splash_new($_GET["user_id"]);

  $account_type = 1;
  $account = "";
  $remark="";

  $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
  mysql_select_db(DB_NAME) or die('Could not select database');
  mysql_query("SET NAMES UTF8");
 $query = 'SELECT id,account,account_type,remark FROM promoters where status=1 and isvalid=true and user_id='.$user_id.' and customer_id='.$customer_id;
 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
  while ($row = mysql_fetch_object($result)) {
	$account = $row->account;
	$account_type = $row->account_type;
	$remark = $row->remark;
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

<form action="save_qrsell_account.php?customer_id=<?php echo $customer_id_en ?>"  id="keywordFrm" method="post">
    <div class="add_content_one">
	    推广员收款账户
	</div>
	<div id="products" class="r_con_wrap">
	 <div class="r_con_form" >
		<div class="rows">
			<label>账户类型</label>
			<span class="input">
			  <select name="account_type" id="account_type">
			     <option value="1" <?php if($account_type==1){ ?>selected<?php } ?>>支付宝</option>
				 <option value="2" <?php if($account_type==2){ ?>selected<?php } ?>>财付通</option>
			  </select>
			</span>
			<div class="clear"></div>
		</div>
	
		<div class="rows">
			<label>收款账户</label>
			<span class="input">
			<input type=text value="<?php echo $account ?>" name="account" id="account" />		
			</span>
			<div class="clear"></div>
		</div>
		
		<div class="rows">
			<label>备注</label>
			<span class="input">
			  <textarea name="remark" rows=5 cols=20 ><?php echo $remark; ?></textarea>
			</span>
			<div class="clear"></div>
		</div>
	 
		
		<div class="rows">
			<label> </label>
			<span class="input">
				<input type=button class="button"  value="提交" onclick="submitV();" />
		&nbsp;	   
				<input type=button class="button"  value="取消" onclick="document.location='qrsell.php?customer_id=<?php echo $customer_id_en ?>';" />
      		</span>
			 <div class="clear"></div>
		</div>
	<input type=hidden name="user_id" value="<?php echo $user_id ?>" />
	</div>
</div>
</form>
<div style="width:100%;height:20px;">
</div>

</div>
</body>
</html>

