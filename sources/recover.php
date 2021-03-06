<?php


// Check if this page it's accessed in the right way
if (!defined('FGT')) die('Sorry, but you are not allowed to access this page from this location!');

$template_location[] = 'header.html';

$template_vars['page_title'] .= ' - Recover';

if(!$this->logged)
{

	$template_location[] = 'recover.html';

	$template_vars['val_user'] = null;
	$template_vars['val_code'] = null;
	$template_vars['status'] = null;

	if(isset($_GET['uname']))
		$template_vars['val_user'] = htmlspecialchars($_GET['uname']);
	if(isset($_GET['rid']))
		$template_vars['val_code'] = htmlspecialchars($_GET['rid']);

	if(isset($_GET['uname']) && isset($_GET['rid']))
	{

		$rec_user = htmlspecialchars(trim($_GET['uname']));
		$rec_rid = htmlspecialchars(trim($_GET['rid']));

		if($acc->validate_user($rec_user) && $acc->validate_code($rec_rid))
		{

			if(Account::recover_check($rec_user))
			{

				$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_CHECK'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($rec_rid, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string(USER_IP, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

				if(Main::db_rows($query) == 1)
				{

					$query = Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_DATA'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($rec_rid, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string(USER_IP, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

					$data = Main::db_fetch_row($query);

					$new_pass = @Main::encrypt($data[2]);

					if($GLOBALS['CONFIG_SERVER_TYPE'] == 1)
						Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_PASSWORD'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERAUT_PASS'], 'CONVERT(binary, '.$new_pass.')', $GLOBALS['DBSTRUCT_L2OFF_USERAUT_ACCOUNT'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
					else
						Main::db_query(sprintf($GLOBALS['DBQUERY_CHANGE_PASSWORD'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_PASS'], '\''.$new_pass.'\'', $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

					Main::db_query(sprintf($GLOBALS['DBQUERY_MCHECK_DELETE'], Main::db_escape_string($rec_user, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);

					$mail = new Mail();

					$mail->Send($data[1], $GLOBALS['CONFIG_ADMIN_MAIL'], sprintf($GLOBALS['LANG_RECOVER_PASS_MAIL_SUBJECT'], $GLOBALS['CONFIG_WEBSITE_NAME']), sprintf($GLOBALS['LANG_RECOVER_PASS_MAIL'], $data[0], $data[2], $GLOBALS['CONFIG_WEBSITE_NAME']));

					$GLOBALS['the_status'] = $GLOBALS['LANG_RECOVER_SUCCEDED'];

				}
				else
					$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_ACT_SESSION'];

			}
			else
				$GLOBALS['the_status'] = $GLOBALS['LANG_ERROR_ACT_EXPIRED'];

		}

	}

	$template_vars['status'] = $GLOBALS['the_status'];

}
else
{

	$template_vars['the_error'] = $GLOBALS['LANG_PAGE_RESTRICTED'];

	$template_location[] = 'errors.html';	

}

$template_location[] = 'footer.html';
