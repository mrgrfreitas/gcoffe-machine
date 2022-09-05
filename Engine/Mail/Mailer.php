<?php

namespace app\Machine\Engine\Mail;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use stdClass;

class Mailer
{

    /** @var PHPMailer */
    private $mail;

    /** @var stdClass */
    private $data;

    /** @var Exception */
    private $Error;

    function __construct() {
        $this->mail = new PHPMailer(true);
        $this->data = new stdClass;

        $this->mail->isSMTP();
        $this->mail->isHTML();
        $this->mail->setLanguage("pt_br");

        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = "tls";
        $this->mail->CharSet = "utf-8";

        $this->mail->Host       = SMTP_MAIL_CONFIG['host'];
        $this->mail->Port       = SMTP_MAIL_CONFIG['port'];
        $this->mail->Username   = SMTP_MAIL_CONFIG['user'];
        $this->mail->Password   = SMTP_MAIL_CONFIG['pass'];
    }

    public function add(string $subject, string $body, string $receiver_name, string $receiver_email): Mailer
    {
        $this->data->subject = $subject;
        $this->data->body = $body;
        $this->data->receiver_name = $receiver_name;
        $this->data->receiver_email = $receiver_email;

        return $this;
    }
    public function attach(string $filePath, string $fileName): Mailer
    {
        $this->data->attach[$filePath] = $fileName;
        return $this;
    }

    public function send( string $from_name = SMTP_MAIL_CONFIG['from_name'], string $from_email = SMTP_MAIL_CONFIG['from_email']): bool
    {
        try {

            $this->mail->Subject = $this->data->subject;
            $this->mail->msgHTML($this->data->body);
            $this->mail->addAddress($this->data->receiver_email, $this->data->receiver_name);
            $this->mail->setFrom($from_email, $from_name);

            if(!empty($this->data->attach)){
                foreach($this->data->attach as $path => $name){
                    $this->mail->addAttachment($path, $name);
                }
            }

            $this->mail->send();
            return true;

        }catch (Exception $exception){
            $this->Error = $exception;
            return false;
        }
    }

    public function error(): ?Exception
    {
        return $this->Error;
    }

}