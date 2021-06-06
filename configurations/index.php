<?php
// Check if this page it's accessed in the right way
if (!defined('FGT')) die('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru"> 
<head>
	<title>Страница не найдена</title>
	<link href="/templates/default/css/error.css" rel="stylesheet" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
</head>
<body>
<div class="error-block">
	<div class="error-text">
		<h1>Ay-ay-ay, shame on you...</h1>
		<p>Sorry, but you are not allowed access to this page!</p>
		<p>Back <a href="/">to main page</a>.</p>
		<p>Dont try to cheat please.</p>
	</div>
</div></body>
</html>');