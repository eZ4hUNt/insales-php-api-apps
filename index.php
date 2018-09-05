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
		$insales_id = abs(intval($_GET['insales_id']));
		
	// Проверяем, есть ли такой магазин в БД
		if($insales_id AND $insales_id != '0'){
			$shop = $db->super_query('SELECT * FROM '. DBPREFIX .'shops WHERE insales_id="'. $insales_id .'"');
			if($shop['id']){
				$insales_api = insales_api_client($shop['shop_url'], $conf['app_api_key'], $shop['password']);
			}else{
				echo '{"error": "The app for this store is not installed"}';
				exit;
			}
		}else{
			echo '{"error": "Invalid insales_id"}';
			exit;
		}

		// Работаеv с API
			try{
				// Компилируем шаблон info.tpl
					$tpl->load_template('info.tpl');
					$tpl->set('{info}', 'Приложение настроено и работает!');
					$tpl->compile('info');
					$tpl->clear();
				
				// Компилируем шаблон основной страницы
					$tpl->load_template('_main.tpl');
					$tpl->set('{info}', $tpl->result['info']);
					$tpl->set('{content}', $content);
					$tpl->compile('content');
					echo $tpl->result['content'];
					$tpl->clear();
					
			}catch (InsalesApiException $e){
				/* $e->getInfo() вернет массив со следующими ключами:
					* method
					* path
					* params (third parameter passed to $insales_api)
					* response_headers
					* response
					* shops_myinsales_domain
					* shops_token */
					
			}catch (InsalesCurlException $e){
				// $e->getMessage() возвращает содержимое curl_errno(), $e->getCode() возвращает содержимое curl_ error()
			}

?>