<?php

class Dragon_Eye_Exception extends Exception
{
	public function errorMSG()
	{

		return sprintf('
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru"> 
<head>
	<title>Страница не найдена</title>
	<link href="/templates/default/css/error.css" rel="stylesheet" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
</head>
<body>
<div class="error-block">
	<div class="error-text">
		<h1>Ой, какая незадача.</h1>
		<p>Произошла ошибка. Страница, которую вы запросили, не найдена.</p>
		<p>Попробуйте вернуться <a href="/">на главную страницу</a>.</p>
	</div>
</div></body>
</html>', $this->getMessage());
	}
}