<?php
require_once("l2_delivery_core.php");

//������������ � �� � ������� L2 �������
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
		//������ ��� ���������
		require_once("../partner/partner.php");
		//������ ID ��������
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
	//��� ������ ����
	echo "no char";
}

mysql_close();
?>            
