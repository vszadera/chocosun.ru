<?php
	// Получение параметров, переданных методом POST (данные запроса хранятся в 'php://input');
	$_POST = json_decode(file_get_contents('php://input'),true);	
	$mail_from = !empty($_POST['mail_from']) ? $_POST['mail_from'] : 'zakaz@chocosun.ru';
	$mail_content_type = !empty($_POST['mail_content_type']) ? $_POST['mail_content_type'] : 'text/plain';
	$mail_to = !empty($_POST['mail_to']) ? $_POST['mail_to'] : null;
	$mail_subject = !empty($_POST['mail_subject']) ? $_POST['mail_subject'] : 'ChocoSun.ru';
	$mail_charset = !empty($_POST['mail_charset']) ? $_POST['mail_charset'] : 'utf-8';
	$mail_message = !empty($_POST['mail_message']) ? $_POST['mail_message'] : null;

	// Подготовка переменных
	$mail_headers = 'Content-type:%s;Charset=%s;From:%s';
	$mail_headers = sprintf($mail_headers, $mail_content_type, $mail_charset, $mail_from);
	
	$is_error = false;
	$error_message = array();
	$data = array();
	$response = array();

	if(is_null($mail_to)) { 
		$is_error = true;
		array_push($error_message, 'Не передан получатель сообщения');
	}  

	// Отправляем сообщение адресату	
	if (!$is_error) {
		try {
			$result = mail($mail_to, $mail_subject, $mail_message, $mail_headers);
			
			if(!$result) {
				$is_error = true;
				array_push($error_message, 'Не удалось отправить сообщение адресату');
			}
		}	
		catch (Exception $e){
			$is_error = true;
			array_push($error_message, $e->getMessage());
		}
	}

	// Формируем ответ	
	if ($is_error) {
		$response['state'] = 'error';
		$response['data'] = $error_message;
	} else {
		$response['state'] = 'ok';
	}

	// Возвращаем результат
	echo json_encode($response);
?>