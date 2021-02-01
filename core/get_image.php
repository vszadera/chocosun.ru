<?php
	// Подключение класса 
	include_once('class/cls_MySQL.php');

	// Параметры соединения
	$config = require_once('config.php');

	// Получение параметров, переданных методом POST
	$id_file = !empty($_GET['id']) ? ($_GET['id']) : 0;
	if ($id_file == 0) {
		// Идентификатор не передан.
		exit();
	}

	// Создаем экземпляр класса и получаем путь к изображению и его MIME-типа
	$db = new cls_MySQL(
		$config['db_connect']['host'], 
		$config['db_connect']['port'], 
		$config['db_connect']['database'],
		$config['db_connect']['charset'],
		$config['db_connect']['user'],
		$config['db_connect']['password']
	);

	$query = "SELECT fl_filename, fl_mime_type FROM v_files WHERE fl_id=? LIMIT 1";		
	$param = array($id_file);

	$result = $db->query($query, $param);
	if (count($result) != 1) {
		exit();
	}
	else {
		$image = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $result[0]['fl_filename']);
		header(sprintf('Content-type: %s', $result[0]['fl_mime_type']));
		echo $image;	
	}
?>