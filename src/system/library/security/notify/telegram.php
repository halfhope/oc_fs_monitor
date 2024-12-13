<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Extension\FS_Monitor\System\Library\Security\Notify;
class Telegram {

	public function send($data) {
		
		$query = http_build_query([
			'chat_id' => $data['security_fs_t_channel_id'],
			'text' => $data['message'],
		]);
		
		$url = 'https://api.telegram.org/bot' . $data['security_fs_t_api_token'] . '/sendMessage?';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
	}
}