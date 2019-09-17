<?php 
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../config.php');
  require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
  require('../../../back_init.php');
   $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
   mysql_select_db(DB_NAME) or die('Could not select database');
   mysql_query("SET NAMES UTF8");
   require('../../../proxy_info.php');
 // $theme='blue';
  
  $charity_id = -1;
  $op = "";


  if(!empty($_GET["charity_id"])){
    $charity_id = $configutil->splash_new($_GET["charity_id"]);
  }
	
  if($charity_id>0){
    
    $query = 'SELECT charity_name FROM charitable_charity_t where isvalid=true and id='.$charity_id;
	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		
		$charity_name = $row->charity_name;	
		
	}
  }	

 if(!empty($_GET["op"])){
	  
  $op = $configutil->splash_new($_GET["op"]);
  
  
   if($op=="del"){
    
     
     $query = 'update charitable_charity_t set isvalid=false where id='.(int)$charity_id;
	 mysql_query($query);
	 $error =mysql_error();
	 mysql_close($link);
	 //echo $error;
	 echo "<script>location.href='charity.php?customer_id=".$customer_id_en."';</script>";
	 return;
  }
}   
	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<script type="text/javascript" src="../../../js/WdatePicker.js"></script> 

</head>
<script>
	
	function submitV(a){
	
	var  charity_name = $('#charity_name').val();
	if(charity_name == ''){
		alert('请输入机构名！');
		return false;
	}
	
	document.getElementById("upform").submit();	 
 } 




</script>
<style type="text/css">
.WSY_member textarea {
width: 350px;
height: 150px;
}
dt{
	margin-top:6px;
}
.spa{
  position: relative;
  right: 32px;
  padding-left: 105px;
}
.WSY_member div {
    width: 50%;
}
.WSY_member dd {	
    float: none!important;
}
.WSY_member dt {
	width: 200px;
}

</style>
<body>
<div class="div_new_content">
<form action="save_charity.php?customer_id=<?php echo $customer_id_en ?>" method="post" id="upform" name="upform">
<input type="hidden"  name="charity_id" value="<?php echo $charity_id;?>"/>
    <div class="WSY_content">
		<div class="WSY_columnbox WSY_list">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">机构信息</a> 
				</div>
			</div>

			<div class="WSY_data">
					<dl class="WSY_member">					
						<div>
							<dt>机构名</dt>
							<dd class="spa">
								<input type="text" value="<?php echo $charity_name;?>" name="charity_name" id="charity_name" style="width:250px; ">
							</dd>
						
						</div>
					</dl>
					<div class="WSY_text_input01">
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
					</div>
			
			</div>
	
		</div>
	</div>
 </form>

<div style="width:100%;height:20px;">
</div>
</div>	

</body>

<?php mysql_close($link);?>	
</html>