<?php
	// Получение параметров, переданных методом POST
	$type = !empty($_GET['type']) ? strtolower($_GET['type']) : null;
	$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
	
	// Подготовка флага, сигнулизирующего подключение к БД
	$is_exit = true;
	$is_exit = ($type == 'topbar' || $type == 'menu' || $type == 'group' || $type == 'cart' || $type == 'delivery') ? false : $is_exit;
	
	if ($is_exit) { exit(); }

	// Подключение класса 
	include_once($_SERVER['DOCUMENT_ROOT'] . '/core/class/cls_MySQL.php');

	// Параметры соединения
	$config = require_once($_SERVER['DOCUMENT_ROOT'] . '/core/config.php');

	// Подготовка переменных
	$is_error = false;
	$error_message = array();
	$query = NULL;
	$param = NULL;
	$response = array();
	$data = array();

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


	// Тип: Строка статусбара	
	if (($type == 'topbar') && !$is_error) {
		// Достаем данные о товарах, находящихся в корзине cookie
		$cart = array();
		$cart = json_decode($_COOKIE[$config['cart_cookie']['name']], true)['product'];

		// Формируем количество товара в корзине
		$data['cartcount'] = count($cart);
	}


	// Тип: Меню	
	if (($type == 'menu') && !$is_error) {
		// Формируем запрос на построение меню
		$query = "SELECT gr_id, gr_name FROM v_groups WHERE IFNULL(gr_parrent_id, 0) = ?";
		$param = array(0);
		
		$result = $db->query($query, $param);
		if (!is_null($db->$error)) {
			$is_error = true;
			array_push($error_message, $db->$error);	
		}	
		
		if (!$is_error && !empty($result)) {
			$tmp_menu = array();
			if (count($result) > 0) {
				foreach ($result as $key => $value) {
					array_push($tmp_menu, array(
						'id' => intval($value['gr_id']),
						'name' => $value['gr_name']
					));			
				}
			}
			$data['menu'] = $tmp_menu;
		}
	}

	// Тип: Группа
	if (($type == 'group') && !$is_error) {
		// Достаем данные о товарах, находящихся в корзине cookie
		$cart = array();
		$cart = json_decode($_COOKIE[$config['cart_cookie']['name']], true)['product'];
		
		// Формируем заголовок
		if ($id == 0) {
			$tmp_title = json_decode('{}');
			$data['title'] = $tmp_title;
		}			
		else {	
			$query = "SELECT gr_id, IFNULL(gr_parrent_id, 0) AS gr_parrent_id, gr_name, gr_description FROM v_groups WHERE gr_id = ? LIMIT 1";
			$param = array($id);

			$result = $db->query($query, $param);
			if (!is_null($db->$error)) {
				$is_error = true;
				array_push($error_message, $db->$error);
			}	
	
			$tmp_title = array();
			if (!$is_error && !empty($result)) {
				if (count($result) > 0) {
					$tmp_title['id'] = intval($result[0]['gr_id']);
					$tmp_title['parrent_id'] = intval($result[0]['gr_parrent_id']);
					$tmp_title['name'] = $result[0]['gr_name'];
					$tmp_title['description'] = $result[0]['gr_description'];
				}
			}
			$data['title'] = $tmp_title;
		}

		// Формируем хлебные крошки
		if ($id == 0) {
			$tmp_breadcrumb = json_decode('{}');
			$data['breadcrumb'] = $tmp_breadcrumb;
		}			
		else {	
			$query = "SELECT gr_id, IFNULL(gr_parrent_id, 0) AS gr_parrent_id, gr_name, gr_description FROM v_groups_level WHERE gr_child_id = ?";
			$param = array($id);
		
			$result = $db->query($query, $param);
			if (!is_null($db->$error)) {
				$is_error = true;
				array_push($error_message, $db->$error);
			}	

			$tmp_breadcrumb = array();
			if (!$is_error && !empty($result)) {
				if (count($result) > 0) {
					foreach ($result as $key => $value) {
						array_push($tmp_breadcrumb, array(
							'id' => intval($value['gr_id']),
							'parrent_id' => intval($value['gr_parrent_id']),
							'name' => $value['gr_name'], 
							'description' => $value['gr_description']
						));
					}
				}
			}
			$data['breadcrumb'] = $tmp_breadcrumb;
		}

		// Формируем список вложенных групп (при наличии)
		// с вложенными группами второго уровня (так же при  наличии)
		$query = "SELECT gr_id, IFNULL(gr_parrent_id, 0) AS gr_parrent_id, gr_name, gr_description, IFNULL(gr_fl_id, 0) AS gr_fl_id, vpra_count FROM v_groups_products_count WHERE IFNULL(gr_parrent_id, 0) = ?";
		$param = array($id);

		$result = $db->query($query, $param);
		if (!is_null($db->$error)) {
			$is_error = true;
			array_push($error_message, $db->$error);
		}	

		$tmp_group = array();
		if (!$is_error && !empty($result)) {
			if (count($result) > 0) {
				foreach ($result as $key => $value) {
					// достаем для каждрой записи список подгрупп
					$subgroup_query = "SELECT gr_id, IFNULL(gr_parrent_id, 0) AS gr_parrent_id, gr_name, gr_description, vpra_count FROM v_groups_products_count WHERE IFNULL(gr_parrent_id, 0) = ?";
					$subgroup_param = array(intval($value['gr_id']));

					$subgroup_result = $db->query($subgroup_query, $subgroup_param);
					if (!is_null($db->$error)) {
						$is_error = true;
						array_push($error_message, $db->$error);
					}	

					$tmp_subgroup = array();
					if (!$is_error && !empty($subgroup_result)) {
						if (count($subgroup_result) > 0) {
							foreach ($subgroup_result as $subgroup_key => $subgroup_value) {
								array_push($tmp_subgroup, array(
									'id' => intval($subgroup_value['gr_id']),
									'parrent_id' => intval($subgroup_value['gr_parrent_id']),
									'name' => $subgroup_value['gr_name'], 
									'description' => $subgroup_value['gr_description'],
									'pr_count' => intval($subgroup_value['vpra_count'])
								));
							}	
						}	
					}

					// формируем карточку вложенных и подгрупп
					array_push($tmp_group, array(
						'id' => intval($value['gr_id']),
						'parrent_id' => intval($value['gr_parrent_id']),
						'name' => $value['gr_name'], 
						'description' => $value['gr_description'],
						'image' => intval($value['gr_fl_id']),
						'pr_count' => intval($value['vpra_count']),
						'subgroup' => $tmp_subgroup
					));
				}
			}
		}
		$data['group'] = $tmp_group;

		// Формируем список вложенных продуктов (при наличии)
		$query = "SELECT pr_id, pr_name, IFNULL(pr_count, 0) AS pr_count, IFNULL(pr_count_measure_name, '') pr_count_measure_name, pr_price, pr_weight, pr_weight_measure_name, pr_structure FROM v_products_actual WHERE IFNULL(gr_id, 0) = ?";
		$param = array($id);

		$result = $db->query($query, $param);
		if (!is_null($db->$error)) {
			$is_error = true;
			array_push($error_message, $db->$error);
		}	

		$tmp_product = array();
		if (!$is_error && !empty($result)) {
			if (count($result) > 0) {
				foreach ($result as $key => $value) {
					// достаем для каждрой записи пакет изображений
					$img_query = "SELECT fl_id FROM v_products_files WHERE pr_id = ?"; 
					$img_param = array(intval($value['pr_id']));

					$img_result = $db->query($img_query, $img_param);
					if (!is_null($db->$error)) {
						$is_error = true;
						array_push($error_message, $db->$error);
					}	
						
					$tmp_image_product = array();
					if (!$is_error && !empty($img_result)) {
						if (count($img_result) > 0) {
							foreach ($img_result as $img_key => $img_value) {
								array_push($tmp_image_product, intval($img_value['fl_id']));
							}	
						}	
					}

					// достаем для каждой записи данные из корзины
					$tmp_cart_item['is_cart'] = false;  
					$tmp_cart_item['count'] = 1;
					if (isset($cart[intval($value['pr_id'])])) {
						$tmp_cart_item['is_cart'] = true; 
						$tmp_cart_item['count'] = intval($cart[intval($value['pr_id'])]);
					}
					$tmp_cart_item['price'] = number_format(floatval($value['pr_price']) * intval($tmp_cart_item['count']), 2);

					// формируем карточку продукта
					array_push($tmp_product, array(
						'id' => intval($value['pr_id']),
						'name' => $value['pr_name'], 
						'count' => $value['pr_count'], 
						'count_measure_name' => $value['pr_count_measure_name'], 
						'price' => $value['pr_price'],
						'weight' => $value['pr_weight'],
						'weight_measure_name' => $value['pr_weight_measure_name'],
						'structure' => $value['pr_structure'],
						'cart' => $tmp_cart_item,
						'image' => $tmp_image_product
					));
					
				}
			}
		}
		$data['product'] = $tmp_product;
	}

	// Тип: Продукт
	if ($type == 'product') {
		// Формируем запрос на получение списка продуктов.
		$query = "SELECT gr_name, gr_description FROM v_groups WHERE gr_id = ?";		
		$param = array($id);
	}

	// Тип: Корзина
	if ($type == 'cart') {
		// Подготовка переменных
		$tmp_cookie_product = array();
		$tmp_cookie_personal = array();
		
		
		// Достаем данные о товарах, находящихся в корзине cookie
		$tmp_cookie = json_decode($_COOKIE[$config['cart_cookie']['name']], true);
		$tmp_cookie_product = $tmp_cookie['product'];
		$tmp_cookie_personal = $tmp_cookie['personal'];
		

		// Формируем перечень товаров, лежащих в корзине	
		$tmp_product = array();
		if (count($tmp_cookie_product) > 0) {
			foreach ($tmp_cookie_product as $pr_id => $cart_count) {
				// достаем для каждого продукта весь пакет данных
				$query = "SELECT pr_id, pr_name, pr_count, pr_count_measure_name, pr_price, pr_weight, pr_weight_measure_name, pr_is_actual, pr_image_id FROM v_products_cart WHERE pr_id = ?";
				$param = array(intval($pr_id));

				$result = $db->query($query, $param);
				if (!is_null($db->$error)) {
					$is_error = true;
					array_push($error_message, $db->$error);
				}	

				if (!$is_error && !empty($result)) {
					if (count($result) > 0) {
						foreach ($result as $key => $value) {
							// формируем карточку продукта
							array_push($tmp_product, array(
								'id' => intval($value['pr_id']),
								'name' => $value['pr_name'], 
								'count' => $value['pr_count'], 
								'count_measure_name' => $value['pr_count_measure_name'], 
								'price' => ($value['pr_is_actual'] == 0) ? 0 : $value['pr_price'],
								'cart' => array(
									'is_cart' => true,
									'count' => ($value['pr_is_actual'] == 0) ? 0 : intval($cart_count),
									'price' => ($value['pr_is_actual'] == 0) ? 0 : (intval($cart_count) * $value['pr_price'])
								),
								'weight' => $value['pr_weight'],
								'weight_measure_name' => $value['pr_weight_measure_name'],
								'is_actual' => ($value['pr_is_actual'] == 0) ? false : true,
								'image_id' => $value['pr_image_id']
							));
						}
					}
				}
			}
		}
		$data['product'] = $tmp_product;

		// Достаем из cookie персональные данные, оставшиеся от предыдущего заказа, если таковые существуют
		$tmp_personal = array();
		$tmp_personal['name'] = (array_key_exists('name', $tmp_cookie_personal)) ? $tmp_cookie_personal['name']: '';
		$tmp_personal['phone'] = (array_key_exists('phone', $tmp_cookie_personal)) ? $tmp_cookie_personal['phone']: '';
		$tmp_personal['address'] = (array_key_exists('address', $tmp_cookie_personal)) ? $tmp_cookie_personal['address']: '';
		
		$data['personal'] = $tmp_personal;


		// Формируем перечень доступных видов доставки
		$tmp_delivery = array();
		if (count($tmp_cookie_product) > 0) {
			$query = "SELECT dl_id, dl_name, dl_description, dl_is_address FROM v_delivery";
			$param = array();

			$result = $db->query($query, $param);
			if (!is_null($db->$error)) {
				$is_error = true;
				array_push($error_message, $db->$error);
			}	

			if (!$is_error && !empty($result)) {
				foreach ($result as $key => $value) {
					// Считываем суммы, доступные для текущего вида доставки
					// Выбор в порядке возрастания минимальной суммы заказа 
					$price_query = "SELECT dl_pr_id, dl_pr_price, dl_pr_min_summ, next_min_summ FROM v_delivery_price WHERE dl_id = ?"; 
					$price_param = array(intval($value['dl_id']));

					$price_result = $db->query($price_query, $price_param);
					if (!is_null($db->$error)) {
						$is_error = true;
						array_push($error_message, $db->$error);
					}	
						
					$tmp_price_delivery = array();
					if (!$is_error && !empty($price_result)) {
						if (count($price_result) > 0) {
							foreach ($price_result as $price_key => $price_value) {
								array_push($tmp_price_delivery, array(
									'id' => intval($price_value['dl_pr_id']),
									'price' => $price_value['dl_pr_price'],
									'this_min_summ' => $price_value['dl_pr_min_summ'],
									'next_min_summ' => $price_value['next_min_summ']
								));
							}	
						}	
					}

					array_push($tmp_delivery, array(
						'id' => intval($value['dl_id']),
						'name' => $value['dl_name'], 
						'description' => $value['dl_description'], 
						'is_address' => ($value['dl_is_address'] == 0) ? false : true,
						'prices' => $tmp_price_delivery
					));
				}
			}
			
		}	
		$data['delivery'] = $tmp_delivery;

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

	// Закрываем соединение	
	$db = NULL;

	// Возвращаем результат
	echo json_encode($response);
?>