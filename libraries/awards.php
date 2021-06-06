<?php 

$user = "Ultra";
$pass = "31852792";
$data = "l2fgt";
$host = "192.168.1.2";


mysql_connect($host1,$user1,$pass1) or die("Coudn't connect to MySQL Server"); 
mysql_select_db($data1) or die("Coudn't connect to MySQL Database");

$result = mysql_query("SELECT * FROM `characters` WHERE `pkkills` < '1'") or die(mysql_error());
$row = mysql_num_rows($result);

if($row <= '0') {

echo "No Player Kills.<br>";

}

else {

$result1 = mysql_query("SELECT `char_name`,`level`,`pkkills`,`clanid` FROM `characters` WHERE `pkkills` > '0' AND `accesslevel` = '0' ORDER by `pkkills` DESC limit $pk_limit");

echo "<table border='0'><tr>";
echo "<th ><center>Ü</center></th>";
echo "<th ><center>Character Name</center></th>";
echo "<th ><center>Clan</center></th>";
echo "<th ><center>PK</center></th>";
echo "</tr>";

$i=1;

while ($row1 = mysql_fetch_row($result1)) 
{

if($row1[3] >= '1') {
$result2 = mysql_query("SELECT `clan_name` FROM `clan_data` WHERE `clanid` = '$row1[3]'") or die(mysql_error());
$row2 = mysql_fetch_row($result2);
$clan_name = "<a href='clan_info.php?id=$row1[3]' target='_self'>$row2[0]</a>"; 
} else {
$clan_name = 'No Clan';
}

echo "<tr>";
echo "<td ><center>$i</center></td>";
echo "<td ><center>$row1[0]</center></td>";
echo "<td ><center>$clan_name</center></td>";
echo "<td ><center>$row1[2]</center></td>";
echo "</tr>";

$i++;

}
echo "</table>";

mysql_close();

}
echo "<input value='Go Back' name='Go Back' onclick=\"location.href = 'javascript:history.back()'\" class='submit' type='button'>";