<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Extension\FS_Monitor\System\Library\Security\Notify;
class Email {

	public function send($data) {

		$mail_option = [
			'parameter'     => $data['config_mail_parameter'],
			'smtp_hostname' => $data['config_mail_smtp_hostname'],
			'smtp_username' => $data['config_mail_smtp_username'],
			'smtp_password' => html_entity_decode($data['config_mail_smtp_password'], ENT_QUOTES, 'UTF-8'),
			'smtp_port'     => $data['config_mail_smtp_port'],
			'smtp_timeout'  => $data['config_mail_smtp_timeout']
		];

		$mail = new \Opencart\System\Library\Mail($data['config_mail_engine'], $mail_option);

		foreach (explode(',', $data['security_fs_e_emails']) as $email) {
			$mail->setTo(trim($email));
			$mail->setFrom($data['config_email']);
			$mail->setSender(html_entity_decode($data['config_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($data['text_mail_subject'], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(nl2br($data['message']));
			$mail->setText(strip_tags($data['message']));
			$mail->send();
		}
	}
}