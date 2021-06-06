<?php
    /* ****************************************** /
	##############################################
	# Магазин личного кабинета ,сделано FG-TEAM  #
	##############################################
    /* ***************************************** */

class Shop
{
	private $_shop = array();
	private $_shopItems = array();
	private $_shopPoints = 0;
	private $_accountUsername = null;
	private $_shopLink = null;
	private $_charName = null;

	public function __construct($shop_link, $shop_items, $shop_points, $account_username)
	{
		$this->_shopItems = $shop_items;

		$this->_shopPoints = $shop_points;

		$this->_accountUsername = $account_username;

		$this->_shopLink = $shop_link;
	}

	public function load()
	{

		$this->_shop = array();

		$this->_shop['account_name'] = $this->_accountUsername;

		$this->_shop['points'] = $this->_shopPoints;

		$char_selected = Account::character_selected($this->_accountUsername);

		$this->_charName = Account::char_name($_SESSION['dragon_eye_character']);

		$this->_shop['character'] = $char_selected ? $this->_charName : '<a href="?page=account&select_char">Выбрать персонажа</a>';

		$this->_shop['reselect'] = $char_selected ? '  <a href="?page=account&select_char">Другой персонаж</a>' : null;

		$this->_shop['buy_status'] = '';

		if(isset($_GET['id']) && $this->_itemExists($_GET['id']))
		{

			$validate_rnum = rand(9, 9999);

			$item_data = $this->_itemData($_GET['id']);

			if(isset($_GET['confirm']))
			{

				if(!$char_selected)
					$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_CHAR_SELECT'];
				elseif($_GET['confirm'] != $_SESSION['dragon_eye_shop_validate'])
					$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_ALREADY'];
				elseif($this->_shopPoints < $item_data['item_price'])
					$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_NO_POINTS'];
				elseif($GLOBALS['CONFIG_SERVER_TYPE'] != '1' && $this->_isOffline())
					$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_CHAR_ONLINE'];
				elseif($this->_addReward($item_data))
					$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_SUCCED'];
				else
					$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_FAILED'];

			}
			elseif(isset($_GET['cancel']))
				$this->_shop['buy_status'] = $GLOBALS['LANG_BUY_CANCEL'];
			else
				$this->_shop['buy_status'] = sprintf($GLOBALS['LANG_BUY_AGREE'], $item_data['item_count'], $item_data['item_name'], $item_data['item_price'], $this->_shopLink, $_GET['id'], $validate_rnum, $this->_shopLink, $_GET['id']);

			$_SESSION['dragon_eye_shop_validate'] = $validate_rnum;

		}
		$this->_shop['items_list'] = $this->items_list();
		return $this->_shop;
	}

	private function _isOffline()
	{
		$query = Main::db_query(sprintf($GLOBALS['DBQUERY_1_1'], $GLOBALS['DBSTRUCT_L2J_CHARS_ONLINE'], $GLOBALS['DBSTRUCT_L2J_CHARS_TABLE'], $GLOBALS['DBSTRUCT_L2J_CHARS_ID'], Main::db_escape_string($_SESSION['dragon_eye_character'], $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
		if(Main::db_result($query, 0) == 1)
			return true;
	}

	private function _cached($fp, $data)
	{
		fwrite($fp, pack('s', (strlen($data) + 2)).$data);
		$l = unpack('v', fread($fp, 2));
		$r = 'x';
		$idr = unpack('c', fread($fp, 1));
		for($i=0;$i<(($l[1] - 4)/4);++$i)
		{
			$rd = unpack('i', fread($fp, 4));
			$r .= $rd[1];
		}
		return $r;
	}

	private function _addReward($item_data)
{
	$r22 = Main::db_query("SELECT MAX(object_id) FROM items", $GLOBALS['DB_LOGIN_SERVER']);
	$ultra = mysql_fetch_array($r22);

	foreach($item_data['item_id'] as $id)
    {
	$query = Main::db_query(sprintf($GLOBALS['DBQUERY_SUPAPUPA'], Main::db_escape_string($ultra[0]+1, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($_SESSION['dragon_eye_character'], $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($id, $GLOBALS['DB_LOGIN_SERVER']), Main::db_escape_string($item_data['item_count'], $GLOBALS['DB_LOGIN_SERVER']), $item_data['item_enchant'], 4, Main::db_escape_string(1, $GLOBALS['DB_LOGIN_SERVER'])), $GLOBALS['DB_LOGIN_SERVER']);
    }
    $this->_removePoints($item_data['item_price']);	
	mysql_close($query);
	return true;
}

	private function _removePoints($price)
	{

		$new_points = $this->_shopPoints - $price;

		if($this->_shopLink == 'donate')
			$new_points = round($new_points, 5);

		if($GLOBALS['CONFIG_SERVER_TYPE'] == '1')
			Main::db_query(sprintf($GLOBALS['DBQUERY_REMOVE_POINTS'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_TABLE'], $GLOBALS['DBSTRUCT_L2OFF_USERACC_'.strtoupper($this->_shopLink).'_POINTS'], Main::db_escape_string($new_points, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2OFF_USERACC_ACCOUNT'], Main::db_escape_string($this->_accountUsername, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);
		else
			Main::db_query(sprintf($GLOBALS['DBQUERY_REMOVE_POINTS'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_TABLE'], $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_'.strtoupper($this->_shopLink).'_POINTS'], Main::db_escape_string($new_points, $GLOBALS['DB_GAME_SERVER']), $GLOBALS['DBSTRUCT_L2J_ACCOUNTS_NAME'], Main::db_escape_string($this->_accountUsername, $GLOBALS['DB_GAME_SERVER'])), $GLOBALS['DB_GAME_SERVER']);

		$this->_shop['points'] = $new_points;

		return true;

	}

	private function _itemExists($item_id)
	{

		if(isset($this->_shopItems[$item_id]))
			return true;

	}

	private function _itemData($item_id)
	{
		$data = array();

		$data['item_name'] = $this->_shopItems[$item_id][0];
		$data['item_id'] = $this->_shopItems[$item_id][1];
		$data['item_count'] = $this->_shopItems[$item_id][2];
		$data['item_enchant'] = $this->_shopItems[$item_id][4];
		$data['item_price'] = $this->_shopItems[$item_id][3];

		return $data;
	}

	public function items_list()
	{
		$shop_items_vars = array();

		$items_list = '';

		$i = 1;

		foreach($this->_shopItems as $key => $item)
		{

			$shop_items_vars['res_num'] = $i;

			$shop_items_vars['item_name'] = $item[0];

			$shop_items_vars['item_count'] = $item[2];

			$shop_items_vars['item_enchant'] = $item[4];

			$shop_items_vars['item_price'] = $item[3];

			$shop_items_vars['item_id'] = $key;

			$shop_items_vars['link'] = $this->_shopLink;

			$items_list .= Template::load('styles/shop_items.html', $shop_items_vars, 0);

			++$i;
		}
		return $items_list;
	}
}