<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../config.php');
  require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
  require('../../../back_init.php');   
  $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
  mysql_select_db(DB_NAME) or die('Could not select database');
  mysql_query("SET NAMES UTF8");
  require('../../../proxy_info.php');
  $user_id = $configutil->splash_new($_GET["user_id"]);
  $parent_id = $configutil->splash_new($_GET["parent_id"]);
  $pre_parent_id = $configutil->splash_new($_GET["parent_id"]);
  $status = $configutil->splash_new($_GET["status"]);
  
  $pagenum = $configutil->splash_new($_GET["pagenum"]);
  $isAgent=0;
  if(!empty($_GET["isAgent"])){
	  $isAgent = $configutil->splash_new($_GET["isAgent"]);
  }
  $search_name="";
  if(!empty($_GET["search_name"])){
      $search_name=$configutil->splash_new($_GET["search_name"]);
  }
  $search_user_id="";
  if(!empty($_GET["search_user_id"])){
      $search_user_id=$configutil->splash_new($_GET["search_user_id"]);
  }
  $account_type = 1;
  $account = "";
  $remark="";

  $bank_open="";
  
   $query_u = "select name,weixin_name,generation,parent_id from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
  $result_u=mysql_query($query_u)or die('Query failed'.mysql_error());
  while($row=mysql_fetch_object($result_u)){
  	$u_name = $row->name;
  	$u_weixin_name = $row->weixin_name;
  	$u_generation = $row->generation;
  	$u_parent_id = $row->parent_id;
  }
  $query = "select id,account_type,account,bank_open from weixin_card_members where isvalid=true and user_id = ".$user_id." limit 0,1";
  $result = mysql_query($query) or die('Query failed: ' . mysql_error());  	
  while ($row = mysql_fetch_object($result)) {
  
	$account = $row->account;
	$account_type = $row->account_type;
	$card_member_id = $row->id;
	$bank_open = $row->bank_open;
	break;
 }



 $count_arr=array();	
$is_alreadyRelat=0;
$p_user_id=$user_id;
$curr_num=0;
while(true){
$query="select user_id from promoters where isvalid=true and status=1 and parent_id=".$p_user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$p_parent_id=-1;
while ($row = mysql_fetch_object($result)) {			//查找下线
  $p_parent_id = $row->user_id;
  break;
}
	array_push($count_arr,$p_parent_id);
	if($p_parent_id==-1){
	   $is_alreadyRelat=1;
	   break;
	}
	$p_user_id = $p_parent_id;
	$curr_num++;
	if($curr_num>20){
	  //查找20层关系
	  break;
	}
} 
//echo $customer_id;
//var_dump($count_arr);
//print_r($count_arr) ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">	
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/allinpay_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<title>编辑推广员</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">

</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
	<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">编辑推广员</a>
			</div>
		</div>
		<form action="save_qrsell_account.php?customer_id=<?php echo $customer_id_en ?>&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>&u_parent_id=<?php echo $u_parent_id; ?>"  id="keywordFrm" method="post">
			<div class="WSY_remind_main">
				<dl class="WSY_remind_dl02"> 
					<dt>推广员收款账户</dt>
					<dd style="margin-left:10px;line-height: 24px;">
						 <a href="qrsell_account_detail.php?customer_id=<?php echo $customer_id_en ?>&user_id=<?php echo $user_id;?>"><span style="color:red;">更改关系详情 </span></a>
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>账户类型：</dt>
					<dd>
						 <select name="account_type" id="account_type" onchange="changeType(this.value);">
							 <option value="1" <?php if($account_type==1){ ?>selected<?php } ?>>支付宝</option>
							 <option value="2" <?php if($account_type==2){ ?>selected<?php } ?>>财付通</option>
							 <option value="3" <?php if($account_type==3){ ?>selected<?php } ?>>银行卡</option>
						 </select>		 		
					</dd>
				</dl>	
				<dl class="WSY_remind_dl02"> 
					<dt>收款账户：</dt>
					<dd>						
						<input type=text value="<?php echo $account ?>" name="account" id="account" style="width:300px;" />	
					</dd>
				</dl>
				<dl class="WSY_remind_dl02" id="div_bank_address" <?php if($account_type==3){ ?> style="display:block"<?php }else{ ?>style="display:none"<?php } ?>> 
					<dt>开户银行：</dt>
					<dd>						
						<input type=text value="<?php echo $bank_open ?>" name="bank_open" id="bank_open" style="width:300px;" />
					</dd>
				</dl>
			
			<?php if( $status>0 and $isAgent!=2){ ?>
				<dl class="WSY_remind_dl02"> 
					<dt>用户编号</dt>
					<dd>						
						<input type=text value="<?php echo $account ?>" name="account" id="account" style="width:300px;" />	
					</dd>
				</dl>
				<dl class="WSY_remind_dl02"> 
					<dt>我的上线：</dt>
					<dd>
						<select name="parent_id" id="parent_id">
						 <option value=-1>请选择一个上线</option>
						  <?php 
							/**旧的**/	
							/*  $query="select distinct(wq.id) as id,qr_info_id,wq.reason as reason,wu.id as user_id,wu.name as name,wu.weixin_name as weixin_name,wu.phone as phone,wu.parent_id as parent_id ,imgurl_qr,wq.status,reward_score,reward_money,wq.createtime 
							 from weixin_qrs wq 
							 inner join weixin_qr_infos wqi 
							 inner join weixin_users wu 
							 inner join promoters promoter  
							 on wq.qr_info_id=wqi.id and promoter.user_id=wu.id and promoter.isvalid=true and wq.isvalid=true and wqi.isvalid=true and  wqi.foreign_id = wu.id and wu.isvalid=true and  wq.isvalid=true and wq.type=1 and wq.customer_id=".$customer_id;
							 if(!empty($search_name)){
								 $query=$query." and (wu.name like '%".$search_name."%' or wu.weixin_name like '%".$search_name."%')";
							 }
							 $query = $query." and wu.name != '".$u_name."' and  wu.weixin_name != '".$u_weixin_name."' ";
							 $query = $query." and wu.id!=".$user_id." and wq.status=1 order by id desc"; */
							 /**旧的end**/
							 
							 $query=" select wu.id as user_id ,wu.name,wu.weixin_name,wu.gflag 
							 from weixin_users wu 
							 inner join promoters promoter 
							 on promoter.user_id=wu.id and promoter.isvalid=true 
							 where wu.isvalid=true and promoter.status=1 and wu.customer_id=".$customer_id." and wu.gflag not like '%,".$user_id.",%'";
							 
							 //排除自己的下线
							 
							 
							 if(!empty($search_name)){
								 $query=$query." and (wu.name like '%".$search_name."%' or wu.weixin_name like '%".$search_name."%')";
							 }	
							  if(!empty($search_user_id)){
								 $query=$query." and wu.id like '%".$search_user_id."%'";
							 }	
							
							//排除自己
							$query = $query." and wu.id!=".$user_id."  order by user_id desc";
							 
							//echo $query;
							$result = mysql_query($query) or die('Query failed: ' . mysql_error());
							$rcount_q = mysql_num_rows($result);
							 
							 while ($row = mysql_fetch_object($result)) {
							 
								$p_parent_id = $row->user_id;
								$p_name = $row->name;
								$p_weixin_name = $row->weixin_name;
								
								$p_name = $p_name."(".$p_weixin_name.")";
															
								?>
								<option value="<?php echo $p_parent_id; ?>" <?php if($parent_id==$p_parent_id){?> selected<?php } ?>><?php echo "[编号:".$p_parent_id."] 推广员:".$p_name; ?></option>
							 <?php 
							 }
						  ?>
						</select>	&nbsp;
						<input type=text value="" id="search_name" name="search_name" style="width:150px;margin-right: 10px;" placeholder="输入名称进行搜索" />
						<input type=text value="" id="search_user_id" name="search_user_id" style="width:150px;margin-right: 10px;" placeholder="输入用户编号进行搜索"/>
						<input type=button value="搜索" class="WSY_button" style="width: 100px;float: none;margin-top: 0;font-size: 13px;" onclick="search_qrname();"/> 
						(<span style="color:red;">不能搜索自己下级，请慎重操作，以免引起数据混乱！</span>)					  
					</dd>
				</dl>
			 <?php } ?>	
			
			</div> 
		
		<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
			<input type="button" class="WSY_button" value="取消" onclick="document.location='promoter.php?customer_id=<?php echo $customer_id_en ?>';">
		</div>
		<?php $testJson = json_encode($count_arr);//数组转JS数组
		?>
	<input type=hidden name="user_id" value="<?php echo $user_id ?>" />
	<input type=hidden name="pre_parent_id" value="<?php echo $pre_parent_id ?>" />
	<input type=hidden name="card_member_id" value="<?php echo $card_member_id ?>" />
	</form>
	</div>
</div> 	
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>

</body>
<script>
function submitV(){
	
	var status=<?php echo $status;?>;
	var isAgent=<?php echo $isAgent;?>;
	var parent_id=-1;
	var card_member_id = '<?php echo $card_member_id ;?>';
	if(status>0 && isAgent!=2){
		var parent_id = document.getElementById("parent_id").value;
		if(parent_id==-1){
			alert("请选择一个上线");
			return;
		}
		var js_json = <?php echo $testJson;?>;
		var p_user_id = <?php echo $user_id;?>;
		
		if(in_array(parent_id,js_json)){
			alert("不能选择自己的下线成为上线");
			return;
		}
		if(p_user_id==parent_id){
			alert("不能选择自己成为上线");
			return;
		}
			if(card_member_id==''){
				var i = window.confirm('此操作仅修改上线关系，如需要修改资料请领取会员卡！');
				if(i==true){
					document.getElementById("keywordFrm").submit();
				}else{
					return;
				}
			}else{
				    document.getElementById("keywordFrm").submit();
			}
	}
	
	
	  //document.getElementById("keywordFrm").submit();
}

function in_array(search,array){
    for(var i in array){
        if(array[i]==search){
            return true;
        }
    }
    return false;
}

 

function changeType(v){
   if(v==3){
      document.getElementById("div_bank_address").style.display="block";
   }else{
      document.getElementById("div_bank_address").style.display="none";
   }
}

function search_qrname(){
    var search_name = document.getElementById("search_name").value;
    var search_user_id = document.getElementById("search_user_id").value;
	if(search_name!="" || search_user_id!=""){
	   document.location="add_qrsell_account.php?user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&status=<?php echo $status; ?>&search_name="+search_name+"&search_user_id="+search_user_id;
	}	
	
}
</script>
<?php 

mysql_close($link);

?>
</html>