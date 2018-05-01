<?php


class Tatva_Checkcron_Model_Testmail
{

public function checkmail()
{
        $general_contact_email = "vaibhav.munjpara@tatvasoft.com";
        $general_contact_name = "vaibhav.munjpara";

        if ($general_contact_email != "") {
            $to_email = $general_contact_email;
            $to_name = $general_contact_name;
            $subject = "Test Cron Mail Run";
            $Body = "Cron Run Successfully";
            $sender_email =  $general_contact_email;
            $sender_name =  $general_contact_name;

            $mail = new Zend_Mail(); //class for mail
            $mail->setBodyHtml($Body); //for sending message containing html code
            $mail->setFrom($sender_email, $sender_name);
            $mail->addTo($to_email, $to_name);
            //$mail->addCc($cc, $ccname);    //can set cc
            //$mail->addBCc($bcc, $bccname);    //can set bcc
            $mail->setSubject($subject);
            try {
                if ($mail->send()) {

                }
            } catch (Exception $ex) {
            }
        }



}




}

?>