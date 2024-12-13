<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Security\Notify;
class Email {

	public function send($mail, $data) {

		$mail->protocol      = $data['config_mail_protocol'];
		$mail->parameter     = $data['config_mail_parameter'];
		$mail->smtp_hostname = $data['config_mail_smtp_hostname'];
		$mail->smtp_username = $data['config_mail_smtp_username'];
		$mail->smtp_password = html_entity_decode($data['config_mail_smtp_password'], ENT_QUOTES, 'UTF-8');
		$mail->smtp_port     = $data['config_mail_smtp_port'];
		$mail->smtp_timeout  = $data['config_mail_smtp_timeout'];

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