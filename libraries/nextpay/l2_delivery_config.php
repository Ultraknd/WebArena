<?php
$SECRET_KEY = "90917dcd5447df81844eff7366ff4f8a";

//???? ? ??? ????????? l2 ???????? ? ???? ???? ??????? ??????????? ???? ????????, ?????
//????????? ???????? ? ?? ???????
$L2_SERVER_ID = 1;

//???? ? ??? ????????? l2 ???????? ??????????? ???? ????????, ?????
//????????? ??????? ??? ?????????? ????? ??????????? ?????
$L2_SERVER_NAME = "name";

//?????? ? ?? ???????
$SQL_SERVER_ITEMS = array("host"=>"46.147.150.55", "db"=>"impulse", "login"=>"Ultra", "pass"=>"31852792");

//?????? ? ?? l2 ???????
$SQL_SERVER_SMS = array("host"=>"46.147.150.55", "db"=>"impulse", "login"=>"Ultra", "pass"=>"31852792");

//?????????/?????????? ??????????? ?????????
define('ENABLE_PARTNERSHIP_PROGRAMM', false);

//???? ?? ???????? ?????? (???????? 1?? ???? ? ??????) ???? ???????? ????????? ???????
//???????? ????????? ? ??????.
//????????, 2 ?????? ?? 1?? ???? == 2?? ????, PRODUCT_COUNT_FACTOR = 1000000.
//???? ?? ?? ???????? ??????? ? ???????, ???????????  PRODUCT_COUNT_FACTOR == 1.
//???? ???? ???????? < 1, ?? ??? ???????? ?? ????? ????????? ?????? 1
$PRODUCT_COUNT_FACTOR = 1;


//???????? ??? ??? ?????????? ?? ?????
$SEND_NOTIFICATION_BY_EMAIL_ENABLED = false;
$EMAIL_FROM_ADDRESS = "from";
$EMAIL_ADDRESS = "to";
$EMAIL_SUBJECT = "webmoney";


//?? ??????? ???!
//???????????? ??? ??????????? ?????? ?????? ??? ?????????? ????? ??????????? ?????
$VOLUTE_NAMES = array(2=>"WMR", 3=>"WMZ", 6=>"WMU", 7=>"WME");

?>