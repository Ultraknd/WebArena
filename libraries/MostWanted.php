<?php
$user = "root";
$pass = "31852792";
$data = "fgt";
$host = "46.147.158.122";

mysql_connect($host,$user,$pass) or die("Coudn't connect to MySQL Server"); 
mysql_select_db($data) or die("Coudn't connect to MySQL Database");


$result = mysql_query("SELECT * FROM `accounts` WHERE `access_level` = '0'") or die(mysql_error());
$row = mysql_num_rows($result);

if($row <= '0') {

echo "err";

}

else {

$result6 = mysql_query("SELECT `obj_id` FROM `server_variables` WHERE `name` = 'isWanted'") or die(mysql_error());
$row6 = mysql_fetch_row($result6);

$result1 = mysql_query("SELECT `char_name`,`obj_Id`,`race`,`sex` FROM `characters` WHERE `obj_Id` = '$row6[0]' LIMIT 1 ");

while ($row1 = mysql_fetch_row($result1)) 
{

if($row1[4] >= '1') {
$result2 = mysql_query("SELECT `obj_id`,`name`,`value` FROM `server_variables` WHERE `name` = 'isWanted' AND `value` = '1' ORDER by `name` DESC LIMIT 1 ");
$row2 = mysql_fetch_row($result2);
}

$race = array('0' => "<img src=\"images/race/0.gif\">", '1' => "<img src=\"images/race/1.gif\">", '2' => "<img src=\"images/race/2.gif\">", '3' => "<img src=\"images/race/3.gif\">", '4' => "<img src=\"images/race/4.gif\">", '5' => "<img src=\"images/race/5.gif\">") ;
$sex = array('0' => "<img src=\"images/gender/man.gif\">", '1' => "<img src=\"images/gender/women.gif\">");
$sex1 = array('0' => "His", '1' => "Her");
$sex2 = array('0' => "He", '1' => "She");


echo "<li id='wanted'>
<br><br><br><br>
<img src='templates/default/images/char/$row1[2]$row1[3].jpg'width='60' height='65'><br>
<h5>$row1[0]<h5>
</li>";     

$i++;
};

mysql_close();
}
echo "";