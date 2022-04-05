<?php

namespace app\lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../config/config.php';


class MailSend
{
    protected string $to_email = '';
    protected string $from = '';
    protected string $subject = '';

    public function __construct()
    {
        $this->from = EMAIL_USER;
        $this->subject = '회원 가입 이메일';
    }


    public function sendRegisterEmail($userData)
    {
        try {
            $this->to_email = $userData['userEmail'];
            $link = $this->getLink($userData);
            $mail = new PHPMailer(true);
            $mail -> CharSet = "UTF-8";

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = EMAIL_HOST;                             //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = EMAIL_USER;                             //SMTP username
            $mail->Password   = EMAIL_PASSWORD;                         //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(EMAIL_USER, 'JeongGuk');
            $mail->addReplyTo(EMAIL_USER, 'JeongGuk');

            // 받는 사람
            $mail->addAddress($this->to_email);               //Name is optional
//            $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

            //Attachments
            // 첨부 파일
//            $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $this->subject;
            $mail->Body    = "
                            <div>
                                <div>
                                    <h3>회원 가입을 위한 이메일 인증 입니다.</h3>
                                    <p>아래의 버튼을 클릭 하면 정상적인 인증이 완료 됩니다.</p>
                                </div>
                                <div style='white-space: normal'>
                                    <a style='white-space: normal' href='$link' target='_blank'>이곳 링크</a>
                                </div>
                            </div>";
//            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function getLink($userData)
    {
        $crypt = password_hash($userData['userPw'], PASSWORD_BCRYPT);
        return APP_SITE.'/view/account.php?training='.$userData['userId'].'&hash='.$crypt;
    }


}