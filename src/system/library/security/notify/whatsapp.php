<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Extension\FS_Monitor\System\Library\Security\Notify;
class WhatsApp {

	public function send($data) {

		$api_endpoint = 'https://graph.facebook.com/v20.0/' + $data['security_fs_w_business_account_id'] + '/messages';
		
		$query = [
			'phone' => $data['security_fs_w_phone_number'],
			'body' => $data['message']
		];
		$ch = curl_init($api_endpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $data['security_fs_w_api_token']));
		$result = curl_exec($ch);
		curl_close($ch);	
	}
}