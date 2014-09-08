<?php
namespace Amplus\Mail;

use Phalcon\Mvc\User\Component;

class Mail extends Component {

	/**
	 * Sends e-mails via AmazonSES based on predefined templates
	 *
	 * @param string $to
	 * @param array $email_fields
	 */
	public function send($to, $email_fields)	{
	    // Settings
	    $mailSettings = $this->config->mail;	    
	    $mail = new \PHPMailer;

	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = $mailSettings->smtp->server;  // Specify main and backup SMTP servers
	    $mail->SMTPAuth = true;                               // Enable SMTP authentication
	    $mail->Username = $mailSettings->smtp->username;                  // SMTP username
	    $mail->Password = $mailSettings->smtp->password;                            // SMTP password
	    $mail->SMTPSecure = $mailSettings->smtp->security;                            // Enable encryption, 'ssl' also accepted

	    $mail->From = $mailSettings->fromEmail;
	    $mail->FromName = $mailSettings->fromName;	    
	    $mail->addAddress($to, $email_fields['subject']);               // Name is optional

	    $mail->Subject = $subject;
	    $mail->Body    = $email_fields['content'];
	    $mail->SMTPDebug = $mailSettings->smtp->debug; 

	    if(!$mail->send()) {
	        $this->flash->notice('Message could not be sent.');
	        $this->flash->error('Mailer Error: ' . $mail->ErrorInfo);
	    } else {
	        $this->flash->success('Message has been sent');
	    }
	}
}