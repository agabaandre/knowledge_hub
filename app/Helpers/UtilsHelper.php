<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;

if(!function_exists('truncate')){
	function truncate($str,$limit){

		return Str::of($str)->limit($limit);
	}
}


if (!function_exists('time_ago')) {

    function time_ago($timestamp)
    {
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;

        $minutes = round($seconds / 60);           // value 60 is seconds
        $hours = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec
        $days = round($seconds / 86400);          //86400 = 24 * 60 * 60;
        $weeks = round($seconds / 604800);          // 7*24*60*60;
        $months = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
        $years = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60

        if ($seconds <= 60) {
            return "Just now";
        } else if ($minutes <= 60) {
            if ($minutes == 1) {
                return "1 " . "Minute" . " " . "ago";
            } else {
                return $minutes . " " . "Minutes" . " ago";
            }
        } else if ($hours <= 24) {
            if ($hours == 1) {
                return "1 " . "hour" . " " . "ago";
            } else {
                return $hours . " " . "hours" . " " . "ago";
            }
        } else if ($days <= 30) {
            if ($days == 1) {
                return "1 " . "day" . " " . "ago";
            } else {
                return $days . " " . "days" . " " . "ago";
            }
        } else if ($months <= 12) {
            if ($months == 1) {
                return "1 " . "month" . " " . "ago";
            } else {
                return $months . " " . "months" . " " . "ago";
            }
        } else {
            if ($years == 1) {
                return "1 " . "year" . " " . "ago";
            } else {
                return $years . " " . "years" . " " . "ago";
            }
        }
    }
}


if (!function_exists('is_past')) {

    function is_past($date)
    {
        $date_now = new DateTime();
        $date2    = new DateTime($date);
        return ($date_now > $date2);
    }
}

if (!function_exists('text_date')) {

    function text_date($date)
    {
        return date("M jS, Y", strtotime($date));;
    }
}

if (!function_exists('share_buttons')) {
	function share_buttons($link,$subject="Checkout this  Africa CDC  resource"){

		$data['link']    = $link;
        $data['subject'] = $subject;

		return view('common.share_buttons',$data);
	}
}

if (!function_exists('is_valid_image')) {

    function is_valid_image($image)
    {
        if (file_exists($image)) {
            return TRUE;
        } else {
            return FALSE;

        }
    }
}

if(!function_exists('storage_link')){

    function storage_link($file_path){

       return url('/').Storage::disk('local')->url($file_path);
       
     }
   
   }

   if(!function_exists('form_edit')){

    function form_edit($field,$data=null,$data_field=null){

        $field = str_replace('[','',$field);
        $field = str_replace(']','',$field);
        
        return ($data)?$data->{$data_field}:old($field);
     }
   
   }

   if(!function_exists('current_url')){
	function current_url(){

	$current = URL::full();
	$appendable = (strpos($current,'?')>-1)?"&":"?";
	return $current.$appendable ;

	}
}

function export_excel($records) {
	$heading = false;
		if(!empty($records))
		  foreach($records as $row) {
			if(!$heading) {
			  // display field/column names as a first row
			  echo implode("\t", array_keys($row)) . "\n";
			  $heading = true;
			}
			echo implode("\t", array_values($row)) . "\n";
		}
	exit;
}


function send_email($request){

    $mail = new PHPMailer(true);     // Passing `true` enables exceptions

    try {
        // Email server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');             //  smtp host
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USERNAME');   //  sender username
        $mail->Password = env('MAIL_PASSWORD');       // sender password
        $mail->SMTPSecure = env('MAIL_ENCRYPTION','ssl');                  // encryption - ssl/tls
        $mail->Port = env('MAIL_PORT','465');                          // port - 587/465

        $mail->setFrom(env('MAIL_USERNAME'), env('MAIL_SENDERNAME'));
        $mail->addAddress($request->email);
      //  $mail->addCC($request->emailCc);
      //  $mail->addBCC($request->emailBcc);

        $mail->addReplyTo(env('MAIL_USERNAME'), 'SenderReplyName');

        // if(isset($_FILES['emailAttachments'])) {
        //     for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
        //         $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
        //     }
        // }


        $mail->isHTML(true);                // Set email content format to HTML

        $mail->Subject = $request->subject;
        $mail->Body    = $request->body;

        // $mail->AltBody = plain text version of email body;

        if( !$mail->send() ) {
             
            return  (Object) array('success'=>false,'message'=>$mail->ErrorInfo);
        }
        else {

            return array('success'=>true,'message'=>"Email has been sent.");
        }

    } catch (Exception $e) {

          return array('success'=>false,'message'=>$e->getMessage());
    }
    
}





?>