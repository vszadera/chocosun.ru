<?php
	class cls_MySQL {
		private static $myPDO; // Идентификатор соединения
		private static $connection_string = 'mysql:host=%s;port=%s;dbname=%s;charset=%s'; 
		private static $connection_options = array (
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_ERRMODE, 
			PDO::ERRMODE_EXCEPTION
		);
		public $error = null;

		// Конструктор класса, подключающийся к базе данных, устанавливающий локаль и кодировку соединения
		public function __construct($host, $port, $dbname, $charset, $user, $password) {
			// формируем строку подключения
			self::$connection_string = sprintf(self::$connection_string, $host, $port, $dbname, $charset);

			// соединяемся с базой
			try {
				self::$myPDO = new PDO(self::$connection_string, $user, $password, self::$connection_options);
			}
			catch (PDOException $e){
				$this->$error = $e->getMessage();
			}
		}

		// Метод для получения данных, на основании запроса	
		public function query($query, $parametrs) {
			try {
				// Избавляемся от SQL инъекции
				$stmt = self::$myPDO->prepare($query);
				
				// Выполняем запрос с переданными параметрами
				$stmt->execute($parametrs);		
				
				// Получаем результат
				$result = $stmt->fetchAll();
				if ($result) {
					return $result;
				} 
				else {
					return null;	
				}	
			}
			catch (PDOException $e){
				$this->$error = $e->getMessage();
			}
		}
	
		// Метод для добавления/изменения/удаления данных, на основании запроса	
		public function exec($query, $parametrs) {
			try {
				// Избавляемся от SQL инъекции
				$stmt = self::$myPDO->prepare($query);
				
				// Выполняем запрос с переданными параметрами
				$stmt->execute($parametrs);		
			}
			catch (PDOException $e){
				$this->$error = $e->getMessage();
			}
		}

		// Метод для добавления данных, с получением id добавленной записи
		public function insert($query, $parametrs) {
			try {
				// Избавляемся от SQL инъекции
				$stmt = self::$myPDO->prepare($query);
				
				// Выполняем запрос с переданными параметрами
				$stmt->execute($parametrs);		

				// Получаем идентификатор добавленной записи
				$result = self::$myPDO->lastInsertId();
				if ($result) {
					return $result;
				} else {
					return null;	
				}	
			}
			catch (PDOException $e){
				$this->$error = $e->getMessage();
			}
		}


		// Деструктор класса
		public function __destruct() {
			if (self::$myPDO) self::$myPDO = null;
		}		
		
	}
?>