<?php
	/*
	Входящие данные:
		type - тип обращения:
		данные имеют разное значение в зависимости от типа обращения
			cart - внесение изменения в корзину заказа (COOKIE)
				id - идентификатор продукта, добавляемого/изменяемого/удаляемого из корзины товаров
				count - количество товара: 
					- если количество товара равно нулю, то товар из корзины удаляется
					- если количество товара более нуля, то товар в корзине создается (при его отсутствии),
					либо количество товара изменяется на новое значение (если товар в корзине уже присутствует)
			personal - внесение изменений в персональные данные (COOKIE)
				пока не реализованная функциональность
			zakaz - оформление заказа
	*/
	
	
	// Получение параметров, переданных методом POST (данные запроса хранятся в 'php://input');
	$_POST = json_decode(file_get_contents('php://input'),true);	
	// Определение типа обращения
	$type = !empty($_POST['type']) ? strtolower($_POST['type']) : null;

	// Подключение класса 
	include_once($_SERVER['DOCUMENT_ROOT'] . '/core/class/cls_MySQL.php');

	// Загрузка конфигурации
	$config = require_once($_SERVER['DOCUMENT_ROOT'] . '/core/config.php');

	// Подготовка переменных
	$is_error = false;
	$error_message = array();

	$query = NULL;
	$param = NULL;
	$data = array();
	$response = array();

	// Управление корзиной товаров
	if ($type == 'cart') {
		// Чтение параметров управления продуктовой корзиной
		$id = !empty($_POST['id']) ? $_POST['id'] : null;
		$count = !empty($_POST['count']) ? $_POST['count'] : 0;

		if (is_null($id)) {
			$is_error = true;
			array_push($error_message, 'Не передан идентификатор продукта');
		} else {
			// Подготовка параметров
			$cookie_name = $config['cart_cookie']['name'];
			$cookie_expire_day = (is_numeric($config['cart_cookie']['expire_day'])) ? $config['cart_cookie']['expire_day'] : 0;
			$cookie_time = time() + (60 * 60 * 24 * $cookie_expire_day);

			try {
				// Проверим наличие cookie, если такового нет, создадим его
				if (!isset($_COOKIE[$cookie_name])) {
					$product_value = array();
					$cookie_value = ['product' => $product_value];
					setcookie($cookie_name, $cookie_value, $cookie_time);
				} 
	
				// Получим значение cookie
				$cookie_value = json_decode($_COOKIE[$cookie_name], true);
				$product_value = $cookie_value['product'];

				// Если задано count, то изменим данные в массиве
				// Если не задано count, удалим данные из массива
				if ($count > 0) {
					$product_value[$id] = intval($count);
				} else {
					if (isset($product_value[$id])) {
						unset($product_value[$id]);
					}
				}
			
				// Установим новое значение cookie
				$cookie_value['product'] = $product_value;
				setcookie($cookie_name, json_encode($cookie_value), $cookie_time);
			}	
			catch (Exception $e){
				$is_error = true;
				array_push($error_message, $e->getMessage());
			}
		}	
	}	

	// Управление персональной информацией
	if ($type == 'personal') {
		// Чтение параметров управления персональными данными
		$name = !empty($_POST['name']) ? $_POST['name'] : null;
		$phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
		$address = !empty($_POST['address']) ? $_POST['address'] : null;

		// Подготовка параметров
		$cookie_name = $config['cart_cookie']['name'];
		$cookie_expire_day = (is_numeric($config['cart_cookie']['expire_day'])) ? $config['cart_cookie']['expire_day'] : 0;
		$cookie_time = time() + (60 * 60 * 24 * $cookie_expire_day);

		try {
			// Проверим наличие cookie, если такового нет, создадим его
			if (!isset($_COOKIE[$cookie_name])) {
				$personal_value = array();
				$cookie_value = ['personal' => $personal_value];
				setcookie($cookie_name, $cookie_value, $cookie_time);
			} 
	
			// Получим значение cookie
			$cookie_value = json_decode($_COOKIE[$cookie_name], true);
			$personal_value = $cookie_value['personal'];

			$personal_value['name'] = !is_null($name) ? $name: $personal_value['name'];
			$personal_value['phone'] = !is_null($phone) ? $phone: $personal_value['phone'];
			$personal_value['address'] = !is_null($address) ? $address: $personal_value['address'];

			// Установим новое значение cookie
			$cookie_value['personal'] = $personal_value;
			setcookie($cookie_name, json_encode($cookie_value), $cookie_time);
		}	
		catch (Exception $e){
			$is_error = true;
			array_push($error_message, $e->getMessage());
		}
	}

	// Оформление заказа
	if ($type == 'zakaz') {
		try {
			// Чтение параметров управления персональными данными
			$cart = !empty($_POST['cart']) ? $_POST['cart'] : array();
			$calculate = !empty($_POST['calculate']) ? $_POST['calculate'] : null;
			$zakaz = !empty($_POST['zakaz']) ? $_POST['zakaz'] : null;
			
			// Подготовка параметров
			$tmp_mail_message = '';
			
			// Создаем экземпляр класса
			$db = new cls_MySQL(
				$config['db_connect']['host'], 
				$config['db_connect']['port'], 
				$config['db_connect']['database'],
				$config['db_connect']['charset'],
				$config['db_connect']['user'],
				$config['db_connect']['password']
			);
	
			if (!is_null($db->$error)) {
				$is_error = true;
				array_push($error_message, $db->$error);
			}	

			// Получим идентификатор статуса из таблицы статусов
			$tmp_zakaz_state_id = null;
			if (!$is_error) {
				$query = "SELECT zs_id FROM t_zakaz_state WHERE zs_code = ?";
				$param = array('new');
		
				$result = $db->query($query, $param);
				if (!is_null($db->$error)) {
					$is_error = true;
					array_push($error_message, $db->$error);	
				}	
		
				if (!$is_error && !empty($result)) {
					if (count($result) > 0) {
						$tmp_zakaz_state_id = intval($result[0]['zs_id']);
					}
				}
			}

			// Получим тип доставки
			$tmp_delivery_name = null;
			if (!$is_error) {
				$query = "SELECT dl_name FROM t_delivery WHERE dl_id = ?";
				$param = array(intval($zakaz['delivery_id']));
		
				$result = $db->query($query, $param);
				if (!is_null($db->$error)) {
					$is_error = true;
					array_push($error_message, $db->$error);	
				}	
		
				if (!$is_error && !empty($result)) {
					if (count($result) > 0) {
						$tmp_delivery_name = strval($result[0]['dl_name']);
					}
				}
			}

			// Добавим запись в таблицу заказов		
			$tmp_zakaz_id = NULL;
			if (!$is_error && !is_null($tmp_zakaz_state_id)) {
				$query = "
					INSERT INTO t_zakaz(
						zk_create_datetime,
						zs_id,
						zk_client_name,
						zk_client_phone,
						zk_client_is_callback,
						zk_client_address,
						zk_comment,
						dl_id,
						zk_delivery_price,
						zk_product_price
					) VALUES (
						NOW(),
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?
					)";
				$param = array(
					$tmp_zakaz_state_id,
					$cart['personal']['name'],
					$cart['personal']['phone'],
					intval($zakaz['is_callback']),
					$cart['personal']['address'],
					$zakaz['comment'],
					intval($zakaz['delivery_id']),
					floatval($calculate['delivery']),
					floatval($calculate['product'])
				);

				$result = $db->insert($query, $param);
				if (!is_null($db->$error)) {
					$is_error = true;
					array_push($error_message, $db->$error);	
				}	
		
				// Получим индентификатор последней добавленной записи
				if (!$is_error && !empty($result)) {
					$tmp_zakaz_id = intval($result);
				}
			}

			// Сообщение. Информация о заказе
			if (!$is_error && !is_null($tmp_zakaz_id)) {
				$tmp_mail_message .= 'Заказ № ' . strval($tmp_zakaz_id) . "\r\n";
				$tmp_mail_message .= 'Получен: ' . strval(date('d.m.Y H:i:s')) . "\r\n";
				$tmp_mail_message .= "\r\n";
				$tmp_mail_message .= 'Заказчик: ' . ((strval($cart['personal']['name']) != '') ? strval($cart['personal']['name']) : '')  . "\r\n";
				$tmp_mail_message .= 'Номер телефона: ' . strval($cart['personal']['phone']) . "\r\n";
				$tmp_mail_message .= 'Перезвонить: ' . ((intval($zakaz['is_callback']) == 1) ? 'Да': 'Нет') . "\r\n";
				$tmp_mail_message .= 'Комментарий: ' . ((strval($zakaz['comment']) != '') ? strval($zakaz['comment']): '') . "\r\n";
				$tmp_mail_message .= "\r\n";
				$tmp_mail_message .= 'Тип доставки: ' . strval($tmp_delivery_name) . "\r\n";
				$tmp_mail_message .= 'Адрес доставки: ' . strval($cart['personal']['address']) . "\r\n";
				$tmp_mail_message .= "\r\n";
			}

			// Добавим все продукты из корзины в заказ
			if (!$is_error && !is_null($tmp_zakaz_id)) {
				$query = "
					INSERT INTO t_zakaz_products(
						zk_id,
						pr_id,
						zp_count,
						zp_price
					) VALUES (
						?,
						?,
						?,
						?
					)";

				if (count($cart.product) > 0) {
					$tmp_item = 0;
					$tmp_mail_message .= 'Перечень товаров: ' . "\r\n";
					$tmp_mail_message .= '--------------------------' . "\r\n";
					foreach ($cart['product'] as $item_cart) {
						// Добавляем каждый продукта, при условии что:
						// is_actual = true - он актуален
						// is_cart = true - он не удален из корзины
						if ($item_cart['is_actual'] && $item_cart['cart']['is_cart']) {
							$param = array(
								$tmp_zakaz_id,
								intval($item_cart['id']),
								intval($item_cart['cart']['count']),
								floatval($item_cart['cart']['price'])
							);
							
							$db->exec($query, $param);
							if (!is_null($db->$error)) {
								$is_error = true;
								array_push($error_message, $db->$error);	
							}	

							// Сообщение. Перечень товаров
							$tmp_item = $tmp_item + 1;
							$tmp_mail_message .= strval($tmp_item) . '. ' . strval($item_cart['name']) . ', ' . strval($item_cart['weight']) . ' ' . strval($item_cart['weight_measure_name']) . ',' . "\r\n";
							$tmp_mail_message .= 'цена: ' . strval(number_format(floatval($item_cart['price']), 2)) . ' руб., ' . 'количество: ' . strval($item_cart['cart']['count']) . ' ед., ' . 'стоимость: ' . strval(number_format(floatval($item_cart['cart']['price']), 2)) . ' руб.;' . "\r\n";
							$tmp_mail_message .= "\r\n";
						}
					}
					$tmp_mail_message .= '--------------------------' . "\r\n";
					$tmp_mail_message .= "\r\n";
				}
			}

			// Сообщение. Стоимость заказа
			if (!$is_error && !is_null($tmp_zakaz_id)) {
				$tmp_mail_message .= 'Стоимость товаров: ' . strval(number_format(floatval($calculate['product']), 2)) . ' руб.' . "\r\n";
				$tmp_mail_message .= 'Стоимость доставки: ' . strval(number_format(floatval($calculate['delivery']), 2)) . ' руб.' . "\r\n";
				$tmp_mail_message .= 'ИТОГО к оплате: ' . strval(number_format(floatval($calculate['total']), 2)) . ' руб.' . "\r\n";
			}
			
			// Закрываем соединение	
			$db = NULL;
			
			// Очищаем корзину 
			$cookie_name = $config['cart_cookie']['name'];
			$cookie_expire_day = (is_numeric($config['cart_cookie']['expire_day'])) ? $config['cart_cookie']['expire_day'] : 0;
			$cookie_time = time() + (60 * 60 * 24 * $cookie_expire_day);

			// Получим данные из cookie	
			$cookie_value = json_decode($_COOKIE[$cookie_name], true);

			// Установим новое значение cookie
			// обнуляем только корзину
			$cookie_value['product'] = array();
			setcookie($cookie_name, json_encode($cookie_value), $cookie_time);

			// Возвращаем идентификатиор добавленного заказа
			$data['zakaz_id'] = $tmp_zakaz_id;
			
			// Возвращаем тело сообщения заказа, для отправки оператору
			$data['mail'] = $tmp_mail_message;
			
			
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
	}
	else {
		$response['state'] = 'ok';
		$response['data'] = $data;
	}

	// Возвращаем результат
	echo json_encode($response);

?>