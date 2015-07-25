<?php 
//require("config.inc.php");

$shop_ID_counter = 0;
$shop_name_counter = 0;
$friend_ID_counter = 0;
$friend_name_counter = 0;

if(!empty($_POST)){
	if(empty($_POST['username']) || empty($_POST['password'])){
		$response["success"] = 0;
		$response["message"] = "Please enter your username and password!";
		die(json_encode($response));
	}
	
	$mysql_query_member = "SELECT * FROM Member WHERE Member_Name = :user";
	$mysql_query_params = array(
		':user' => $_POST['username']
	);
	$mysql_query_shop = "SELECT * FROM Shop";
	$mysql_query_friend = "Select Friend.Friend_ID, Member.Member_Name FROM Friend INNER JOIN Member ON Friend.Friend_ID = Member.Member_ID WHERE Friend.Member_ID = 1 AND Friend.Friend_Status = 1";
	
	try{
		$mysql_stmt = $mysql_conn->prepare($mysql_query_member);
		$mysql_result = $mysql_stmt->execute($mysql_query_params);
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error! Please Try Again!";
		die(json_encode($response));
	}
	
	$validated_info = false;
	
	$member_row = $mysql_stmt->fetch();
	if($member_row){
		if($_POST['password'] == $member_row['Member_Password']){
			$login_ok = true;
		}
	}
	
	if($login_ok){
		
		$response["success"] = 1;
		$response["message"] = "Login successful";
		$response["member_id"] = $member_row['Member_ID'];
		$response["member_name"] = $member_row['Member_Name'];
		$response["member_point"] = $member_row['Member_Point'];
		try{
			$mysql_stmt = $mysql_conn->prepare($mysql_query_shop);
			$mysql_result = $mysql_stmt->execute();
		}
		catch (PDOException $ex) {
			$response["success"] = 0;
			$response["message"] = "Database Error! Please Try Again!";
			die(json_encode($response));
		}
		while($shop_row = $mysql_stmt->fetch()){
			$response['shop_ID_' . (++$shop_ID_counter)] = $shop_row[Shop_ID];
			$response['shop_name_' . (++$shop_name_counter)] = $shop_row[Shop_Name];
		}
		try{
			$mysql_stmt = $mysql_conn->prepare($mysql_query_friend);
			$mysql_result = $mysql_stmt->execute();
		}
		catch (PDOException $ex) {
			$response["success"] = 0;
			$response["message"] = "Database Error! Please Try Again!";
			die(json_encode($response));
		}
		while($friend_row = $mysql_stmt->fetch()){
			$response['friend_ID_' . (++$friend_ID_counter)] = $friend_row[Friend_ID];
			$response['friend_name_' . (++$friend_name_counter)] = $friend_row[Member_Name];
		}
		$response['shop_ID_' . (++$shop_ID_counter)] = null;
		$response['friend_ID_' . (++$friend_ID_counter)] = null;		
		die(json_encode($response));
	} else {
		$response["success"] = 0;
		$response["message"] = "Invalid credential";
		die(json_encode($response));
	}
} else {
?>

<h1>Login</h1>
<form action = "login.php" method = "post">
Username: <br />
<input type = "text" name = "username" placeholder = "username" />
<br /><br />
Password: <br/>
<input type = "password" name="password" placeholder = "password" value = "" />
<br /><br />
<input type = "submit" value="Login" />
</form>
<?php
	}
	?>

	
