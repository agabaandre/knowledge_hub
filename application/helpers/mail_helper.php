<?php
use PHPMailer\PHPMailer\PHPMailer;



if (!function_exists('fire_email')) {

  function fire_email ($title, $message_body, $receiver) {
      $mail = new PHPMailer(true);
      try {
         //Server settings
         $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;                      //Enable verbose debug output
         $mail->isSMTP();                                            //Send using SMTP
         $mail->Host       = 'precisionconceptsug.com';                     //Set the SMTP server to send through
         $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
         $mail->Username   = 'demo@precisionconceptsug.com';                     //SMTP username
         $mail->Password   = 'Microinfo@12';                               //SMTP password
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    
         //$mail->SMTPAutoTLS = false;
         //$mail->SMTPSecure = false;        //Enable implicit TLS encryption
         $mail->Port       = 465; //587; //465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

         //Recipients
         $mail->setFrom('demo@precisionconceptsug.com', 'Zeal Pesa');
         $mail->addAddress($receiver);     //Add a recipient
         $mail->addReplyTo('demo@precisionconceptsug.com', 'Zeal Pesa');
         // $mail->addCC('cc@example.com');
         // $mail->addBCC('bcc@example.com');

         //Attachments
         // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
         // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

         //Content
         $mail->isHTML(true);                                  //Set email format to HTML
         $mail->Subject = $title;
         $mail->Body    = $message_body;
         $mail->AltBody = $message_body;

         $mail->send();
         return 'Message has been sent';
      } catch (Exception $e) {
         return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
  }


  if (!function_exists('save_email')) {

   function save_email ($client_id, $title, $body) {
      $title = base64_encode($title);
      $body = base64_encode($body);

      // save msg content first
      $ci =& get_instance();
      $ci->db->query("INSERT INTO `message_contents` SET `title`='{$title}', 
         `message_body` = '{$body}'");
         
      $content_id = (int) $ci->db->insert_id();
      
      $ci->db->query("INSERT INTO `messages` SET `message_content_id`='{$content_id}', 
         `client_id` = '{$client_id}', `message_type_id` = 3, shows_inbox=0, 
         status='Queued', user_id='0' ");

      $messages_id = (int) $ci->db->insert_id();

      return $messages_id > 0;

   }

  }

  if (!function_exists('process_emails')) {
   
      function process_emails () {
         $ci =& get_instance();
         $results =  $ci->db->query("SELECT `messages`.`id`, `client_id`
            ,`message_contents`.`title`, `message_contents`.`message_body`
            ,(SELECT `email` FROM `client` WHERE `client`.`id` =  `messages`.`client_id` ) AS `email`
            FROM `messages` 
            JOIN `message_contents` ON `message_contents`.`id`=`messages`.`message_content_id`
            WHERE `status` LIKE 'Queued' AND `message_type_id`=3 LIMIT 1")->result();

         foreach ($results as $key => $result) {
            $result->email = "sendimarvin1@gmail.com";
            fire_email (base64_decode($result->title), base64_decode($result->message_body), $result->email);
            $ci->db->query("UPDATE `messages` SET `status` = 'Sent' WHERE id='{$result->id}' ");
         }
      }
  }

}

