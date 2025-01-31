<?php
// 
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
        $this->Host = 'smtp.gmail.com'; // SMTP server
        $this->SMTPAuth = true; // Enable SMTP authentication
        $this->Username = 'societeagrisahel@gmail.com'; // SMTP username
        $this->Password = 'xwtjkdsecwvqqxpg'; // SMTP password
        $this->SMTPSecure = 'tls'; // Enable TLS encryption
        $this->Port = 587; // TCP port to connect to
        // Set default sender
        $this->setFrom('senpaimato5@gmail.com', 'affrah dz');
    }


    public function sendmail($sendTo, $message)
    {
        try {
            $this->addAddress($sendTo);

            $this->isHTML(true); // Set email format to HTML
            $this->Subject = 'Forget Password'; // Subject of the email
            $this->Body = "THE OTP number is :".$message; // HTML message body
            $this->AltBody = strip_tags($message); // Plain text version of the message


            $this->send();
            //
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
