<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../config.php');
  require('../back_init.php');   
 
  $user_id = $_GET["user_id"];
  $parent_id = $_GET["parent_id"];
  $status = $_GET["status"];
  
  $search_name="";
  if(!empty($_GET["search_name"])){
      $search_name=$_GET["search_name"];
  }

  $account_type = 1;
  $account = "";
  $remark="";

  $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
  mysql_select_db(DB_NAME) or die('Could not select database');
  mysql_query("SET NAMES UTF8");
  $bank_open="";
  $query = "select id,account_type,account,bank_open from weixin_card_members where isvalid=true and user_id = ".$user_id." limit 0,1";
  $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
  while ($row = mysql_fetch_object($result)) {
  
	$account = $row->account;
	$account_type = $row->account_type;
	$card_member_id = $row->id;
	$bank_open = $row->bank_open;
	break;
 }
 
 
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

<form action="save_qrsell_account.php?customer_id=<?php echo $customer_id ?>&user_id=<?php echo $user_id; ?>"  id="keywordFrm" method="post">
    <div class="add_content_one">
	    推广员收款账户
	</div>
	<div id="products" class="r_con_wrap">
	 <div class="r_con_form" >
		<div class="rows">
			<label>账户类型</label>
			<span class="input">
			  <select name="account_type" id="account_type" onchange="changeType(this.value);">
			     <option value="1" <?php if($account_type==1){ ?>selected<?php } ?>>支付宝</option>
				 <option value="2" <?php if($account_type==2){ ?>selected<?php } ?>>财付通</option>
				 <option value="3" <?php if($account_type==3){ ?>selected<?php } ?>>银行卡</option>
			  </select>
			</span>
			<div class="clear"></div>
		</div>
	
		<div class="rows">
			<label>收款账户</label>
			<span class="input">
			<input type=text value="<?php echo $account ?>" name="account" id="account" style="width:300px;" />		
			</span>
			<div class="clear"></div>
		</div>
		
		<div class="rows" id="div_bank_address" <?php if($account_type==3){ ?> style="display:block"<?php }else{ ?>style="display:none"<?php } ?>>
			<label>开户银行</label>
			<span class="input">
			<input type=text value="<?php echo $bank_open ?>" name="bank_open" id="bank_open" style="width:300px;" />		
			</span>
			<div class="clear"></div>
		</div>
		
		
		<?php if($status>0){ ?>
			<div class="rows">
				<label>我的上线</label>
				<span class="input">
				  <select name="parent_id" id="parent_id">
				     <option value=-1>请选择一个上线</option>
					  <?php 
						 $query="select distinct(wq.id) as id,qr_info_id,wq.reason as reason,wu.id as user_id,wu.name as name,wu.weixin_name as weixin_name,wu.phone as phone,wu.parent_id as parent_id ,imgurl_qr,wq.status,reward_score,reward_money,wq.createtime from weixin_qrs wq inner join weixin_qr_infos wqi inner join weixin_users wu inner join promoters promoter  on wq.qr_info_id=wqi.id and promoter.user_id=wu.id and promoter.isvalid=true and wq.isvalid=true and wqi.isvalid=true and  wqi.foreign_id = wu.id and wu.isvalid=true and  wq.isvalid=true and wq.type=1 and wq.customer_id=".$customer_id;
						 if(!empty($search_name)){
						     $query=$query." and (wu.name like '%".$search_name."%' or wu.weixin_name like '%".$search_name."%')";
						 }
						 $query = $query." and wq.status=1 order by id desc";
						 
						 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
						 $rcount_q = mysql_num_rows($result);
						 
						 while ($row = mysql_fetch_object($result)) {
						 
							$p_parent_id = $row->user_id;
							$p_name = $row->name;
							$p_weixin_name = $row->weixin_name;
							
							$p_name = $p_name."(".$p_weixin_name.")";	
							?>
							<option value="<?php echo $p_parent_id; ?>" <?php if($parent_id==$p_parent_id){?> selected<?php } ?>><?php echo $p_name; ?></option>
						 <?php 
						 }
					  ?>
				  </select>&nbsp;<input type=text value="" id="search_name" name="search_name" /><input type=button value="搜索" style="padding:5px 5px 5px 5px;" onclick="search_qrname();"> (<span style="color:red;">请慎重操作，以免引起数据混乱！</span>)
				</span>
				<div class="clear"></div>
			</div>
	  <?php } ?>
	  
	  <div class="rows">
			<label> </label>
			<span class="input">
				<input type=button class="button"  value="提交" onclick="submitV();" />
		&nbsp;	   
				<input type=button class="button"  value="取消" onclick="document.location='qrsell.php?customer_id=<?php echo $customer_id ?>';" />
      		</span>
			 <div class="clear"></div>
		</div>
		
	<input type=hidden name="user_id" value="<?php echo $user_id ?>" />
	<input type=hidden name="card_member_id" value="<?php echo $card_member_id ?>" />
	</div>
</div>
</form>
<div style="width:100%;height:20px;">
</div>
<script>
function changeType(v){
   if(v==3){
      document.getElementById("div_bank_address").style.display="block";
   }else{
      document.getElementById("div_bank_address").style.display="none";
   }
}

function search_qrname(){
    var search_name = document.getElementById("search_name").value;
	if(search_name!=""){
	   document.location="add_qrsell_account2.php?user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&status=<?php echo $status; ?>&search_name="+search_name;
	}
}
<?php 

mysql_close($link);

?>
</script>
</div>
</body>
</html>


