<?php
	@session_start();
	@ob_start();

	@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE);
	@ini_set('display_errors', true);
	@ini_set('html_errors', false);
	@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);
	
	define('ROOT_DIR', dirname( __FILE__ ));
	define('ENGINE', true);
	
	require_once ROOT_DIR . '/functions/insales_api.php';
	require_once ROOT_DIR . '/functions/database.php';
	require_once ROOT_DIR . '/data/database.php';	
	require_once ROOT_DIR . '/data/config.php';	

	// Входящие данные
		$insales_shop = trim(addslashes(htmlspecialchars($_GET['shop'], ENT_QUOTES, $conf['charset'])));
		$insales_token = trim(addslashes(htmlspecialchars($_GET['token'], ENT_QUOTES, $conf['charset'])));
		$insales_id = abs(intval($_GET['insales_id']));
		
		$insales_api = insales_api_client($shop['shop_url'], $conf['app_api_key'], $shop['password']);
		
	// Проверяем, есть ли такой магазин в БД
		if($insales_id AND $insales_shop AND $insales_token){
			$shop = $db->super_query('SELECT * FROM '. DBPREFIX .'shops WHERE insales_id="'. $insales_id .'" AND password="'. $insales_token .'"');
			if($shop['id']){
				// Удаляем магазин и все данные магазина из БД
					$db->query('DELETE FROM '. DBPREFIX .'shops WHERE insales_id="'. $insales_id .'"');
					$db->query('DELETE FROM '. DBPREFIX .'views WHERE insales_id="'. $insales_id .'"');
			}else{
				echo '{"error": "The app for this store is not installed"}';
				exit;
			}
		}else{
			echo '{"error": "Invalid insales_id"}';
			exit;
		}

	// Работаем с API
		try{
			// Удаляем скрипты
			//	$response = $insales_api('DELETE', '/admin/js_tags/'. $insales_id .'.json');
				
		}catch (InsalesApiException $e){
			/* $e->getInfo() вернет массив со следующими ключами:
				* method
				* path
				* params (third parameter passed to $insales_api)
				* response_headers
				* response
				* shops_myinsales_domain
				* shops_token
			*/
		}catch (InsalesCurlException $e){
			// $e->getMessage() возвращает содержимое curl_errno(), $e->getCode() возвращает содержимое curl_ error()
		}
?>