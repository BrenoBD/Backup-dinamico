<?php
	date_default_timezone_set('America/Sao_Paulo');

	define('HOST', 'localhost');
	define('DB', 'test');
	define('USER','root');
	define('PASS', '');

	function backup(){
		$db = new PDO('mysql:host='.HOST.';dbname='.DB,USER,PASS);

		$f = fopen(date('Y-m-d').'.tex','wt');

		$tables = $db->query('SHOW TABLES');
		foreach ($tables as $table){
			$sql = '--TABLES: '.$table[0].PHP_EOL;
			$create = $db->query('SHOW CREATE TABLE `'.$table[0].'`')->fetch();
			$sql.=$create['Create Table'].';'.PHP_EOL;
			fwrite($f, $sql);

			$rows = $db->query('SELECT * FROM `'.$table[0].'`');
			$rows->setFetchMode(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				$row = array_map(array($db,'quote'),$row);
				$sql = 'INSERT INTO `'.$table[0].'` (`'.implode('`, `',array_keys($row)).'`) VALUES ('.(implode(', ',$row)).');'.PHP_EOL;
				fwrite($f, $sql);
			}
			$sql = PHP_EOL;
			$result = fwrite($f, $sql);

			if($result !== false){
				echo 'BACKUP feito com sucesso!';
			}else{
				echo 'OH não, falhou';
			}
			flush();		
		}	
		fclose($f);

	}

	backup();

?>
