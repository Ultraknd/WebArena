<?php
require_once("l2_delivery_config.php");
$STATUS_ORDER_DELIVERED = 1;

$orderId = null;
$la2ItemId = null;
$productCount = null;
$orderHash = null;
$char = null;
$profit = null;
$volute = null;
$comment = null;


function logOrder()
{
	global $SQL_SERVER_SMS;
	openSQLConnection($SQL_SERVER_SMS);
	global $orderId, $la2ItemId, $profit, $volute, $productCount, $L2_SERVER_ID, $char;

	$orderIdSQL = mysql_escape_string($orderId);

	$query = "select 1 from nextpay_l2_order where order_id = '$orderIdSQL'";
	$res = mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($res) == 0)
	{
		$la2ItemIdSQL = mysql_escape_string($la2ItemId);
		$profitSQL = mysql_escape_string($profit);
		$voluteSQL = mysql_escape_string($volute);
		$serverSQL = mysql_escape_string($L2_SERVER_ID);
		$charSQL = mysql_escape_string($char);
		$productCountSQL = mysql_escape_string($productCount);
		global $STATUS_ORDER_DELIVERED;
		$status = $STATUS_ORDER_DELIVERED;
		$commentSQL = 'NULL';
		$query = "insert into nextpay_l2_order (order_id, date_created, product_id, profit, volute, product_count, server, char_name, comment, status)";
		$query .= " values('$orderIdSQL', now(),  '$la2ItemIdSQL', '$profitSQL', '$voluteSQL', '$productCountSQL', '$serverSQL', '$charSQL', $commentSQL, $status)";
		mysql_query($query) or die(mysql_error());
	}
	mysql_close();
}


function isOrderDelivered($orderId)
{
	global $SQL_SERVER_SMS;
	openSQLConnection($SQL_SERVER_SMS);
	$orderIdSQL = mysql_escape_string($orderId);
	$query = "select 1 from nextpay_l2_order where order_id = '$orderIdSQL' and status = 1";
	$res = mysql_query($query) or die(mysql_error());
	$ret = mysql_num_rows($res) != 0;
	mysql_close();
	return $ret;
}


function getNameById($id, $array)
{
	if($id == null)
	{
		return "";
	}
	else
	{
		if(array_key_exists($id, $array))
		{
			return $array[$id];
		}
		else
		{
			return "";
		}
	}
}


function getVoluteName($id)
{
	global $VOLUTE_NAMES;
	return getNameById($id, $VOLUTE_NAMES);
}

function openItemsDBConnection()
{
	global $SQL_SERVER_ITEMS;
	openSQLConnection($SQL_SERVER_ITEMS);
}


function openSQLConnection($data)
{
	$db_host = $data["host"];
	$db_user = $data["login"];
	$db_pass = $data["pass"];
	$db_name = $data["db"];
	mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
	mysql_select_db($db_name) or die(mysql_error());
	mysql_query("set names cp1251") or die(mysql_error());
}


function success()
{
	sendNotificationEmail("Product delivered");
	echo "ok";
}

function sendNotificationEmail($message)
{
	global $SEND_NOTIFICATION_BY_EMAIL_ENABLED;
	if($SEND_NOTIFICATION_BY_EMAIL_ENABLED)
	{
		global $EMAIL_FROM_ADDRESS;
		global $EMAIL_ADDRESS;
		global $EMAIL_SUBJECT;
		global $L2_SERVER_NAME;

		$headers = 'From: '.$EMAIL_FROM_ADDRESS.'' . "\r\n" .
		'Reply-To: '.$EMAIL_FROM_ADDRESS.'' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		$orderId = $_REQUEST["order_id"];
		$profit = $_REQUEST["profit"];
		$char =  $_REQUEST["character"];
		$count = $_REQUEST["product_count"];
		$serverName = $L2_SERVER_NAME;
		$volute = $_REQUEST["volute"];
		$voluteName = getVoluteName($volute);
		$comment = $_REQUEST["comment"];


		$msg =
		"order=$orderId
		currency=$voluteName
		sum=$profit
		server=$serverName
		charname=$char
		product count=$count
		comment=$comment
		STATUS=$message";
		mail($EMAIL_ADDRESS, $EMAIL_SUBJECT, $msg, $headers);
	}
}

function error($msg)
{
	echo "Ошибка при обработке. $msg";
	sendNotificationEmail($msg);
	die();
}


function preprocess()
{
	global $orderId, $la2ItemId, $productCount, $orderHash, $char, $profit, $volute, $SECRET_KEY, $comment;
	$orderId = $_REQUEST["order_id"];
	if($orderId == null)
	{
		error("Не передан ID заказа");
	}
	$orderId = intval($orderId);


	$la2ItemId =  $_REQUEST["seller_product_id"];
	if($la2ItemId == null)
	{
		error("Не передан ID продукта");
	}
	$la2ItemId = intval($la2ItemId);

	$productCount =  $_REQUEST["product_count"];
	if($productCount == null)
	{
		error("Не передано количество продукта");
	}
	$productCount = intval($productCount);

	if($productCount <= 0)
	{
		error("Неверное значение параметра \"количество продукта\"");
	}

	$orderHash =  $_REQUEST["hash"];
	if($orderHash == null)
	{
		error("Не передана контрольная сумма заказа");
	}

	$profit = $_REQUEST["profit"];
	if($profit == null)
	{
		error("Не передана стоимость заказа");
	}
	if($profit < 0)
	{
		error("Неверное значение параметра \"стоимость заказа\"");
	}

	$volute = $_REQUEST["volute"];
	if($volute == null)
	{
		error("Не передана валюта заказа");
	}
	$volute = intval($volute);

	$comment = $_REQUEST["comment"];

	//custom parameter
	$char = $_REQUEST["character"];
	if($char == null)
	{
		error("Не передан ник");
	}


	//Проверка контрольной суммы
	$hash = "$orderId$la2ItemId$productCount$profit$volute$SECRET_KEY";
	$hash = sha1($hash);

	if($hash != $orderHash)
	{
		error("Контрольные суммы не совпадают");
	}

	if(isOrderDelivered($orderId))
	{
		error("Данный заказ уже доставлен");
	}
}


function deliverProduct()
{
	global $char, $la2ItemId, $productCount, $PRODUCT_COUNT_FACTOR;
	if($PRODUCT_COUNT_FACTOR >= 1)
	{
		//Умножаем на фактор, если продаем продукт в пакетах
		$productCount *= $PRODUCT_COUNT_FACTOR;
	}
	openItemsDBConnection();
	$charSQL = mysql_real_escape_string($char);
	//$sql = "select login from accounts where login = $char";
	$sql = "select * from accounts where login = '$charSQL'";
	$result = mysql_query($sql) or die(mysql_error());

	if (mysql_num_rows($result) != 1)
	{
		mysql_close();
		error("Не существует аккаунта: $char");
	}


	//$userid = mysql_result($result, 0, "login");
	//$useridSQL = mysql_real_escape_string($userid);
	
	$productCount = intval($productCount);
	
    //$sql2 = "select 'donate_points' from accounts WHERE login = '$charSQL'";
	//$new_points = $sql2 + 1;
	//$productCount
	//$sql = "REPLACE accounts SET donate_points WHERE login = '$charSQL'";
	$sql = "UPDATE accounts SET donate_points = donate_points +1 WHERE login = '$charSQL'";
	mysql_query($sql) or die(mysql_error());
	mysql_close();

	logOrder();
	success();
}



?>