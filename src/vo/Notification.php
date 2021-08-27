<?
	class Notification{
		function sendByPlayerId(array $playerIds, $message, array $data, Properties $settings, Logger $log){
			$responses = array();
			if(is_array($playerIds) && count($playerIds)>0){
				$content = array(
					"en" => $message
				);
				
				$fields = array(
					'app_id'=>$settings->prop("onesignal.config.appid"),
					'include_player_ids' => $playerIds,
					//'data' => array("foo" => "bar"),
					'data'=>$data,
					'contents' => $content
				);
				
				$fields = json_encode($fields);
		    	$log->debug('Json a enviar');
		    	$log->debug($fields);
				$ch = curl_init();
				//curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
				curl_setopt($ch, CURLOPT_URL, $settings->prop("onesignal.config.notification.url"));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
														   'Authorization: Basic '.$settings->prop("onesignal.config.restapikey")));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

				$responses = curl_exec($ch);
				//$log->debug(json_encode($response));
				curl_close($ch);					
			}else{
				$log->error('Se debe especificar el(los) device_id de el(los) dispositivo(s)');
			}
			return $responses;			
		}
		
		/*$response = sendMessage();
		$return["allresponses"] = $response;
		$return = json_encode( $return);
		
		print("\n\nJSON received:\n");
		print($return);
		print("\n");*/
	}
?>