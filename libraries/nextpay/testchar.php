<?php
require_once("l2_delivery_core.php");

//Подключаемся к БД с данными L2 сервера
openItemsDBConnection();

$charname = @$_REQUEST['character'];
$charname = mysql_real_escape_string($charname);

$query = "SELECT account_name FROM characters WHERE char_name='$charname'";
$res = mysql_query($query) or die (mysql_error());
$numRows = mysql_num_rows($res);

if ($numRows == 1)
{
	if(ENABLE_PARTNERSHIP_PROGRAMM)
	{
		//Скрипт для партнерки
		require_once("../partner/partner.php");
		//Узнаем ID партнера
		$row = mysql_fetch_array($res);
		$accountName = $row['account_name'];
		$partnerId = npp_getPartnerId($accountName);
		if($partnerId == null)
		{
			echo "ok";
		}
		else
		{
			echo npp_buildOKResponse($partnerId);
		}
	}
	else
	{
		echo "ok";
	}
}
else
{
	//Нет такого чара
	echo "no char";
}

mysql_close();
?>            
