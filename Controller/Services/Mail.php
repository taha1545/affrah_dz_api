<?php
//  mail to send to user
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Mail extends PHPMailer
{
    public function __construct()
    {
        parent::__construct(true);
        // 
        $this->isSMTP();
        $this->Host = 'smtp.gmail.com';
        $this->SMTPAuth = true;
        $this->Username = 'societeagrisahel@gmail.com';
        $this->Password = 'xwtjkdsecwvqqxpg';
        $this->SMTPSecure = 'tls';
        $this->Port = 587;
        //
        $this->setFrom('senpaimato5@gmail.com', 'affrah dz');
    }


    public function sendmail($sendTo, $message)
    {
        try {
            $this->addAddress($sendTo);

            $this->isHTML(true);
            $this->Subject = 'Forget Password';
            $this->Body = "THE OTP number is :" . $message;
            $this->AltBody = strip_tags($message);
            //
            $this->send();
            //
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
