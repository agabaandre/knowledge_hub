<?php

use App\Models\PublicationType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Cache;
use Smalot\PdfParser\Parser;
use App\Notifications\SendPushNotification;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageTarget;
use App\Notifications\AccountActivated;
use App\Models\PushNotification;
use App\Jobs\PushNotificationJob;
use Imagick;

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
        $date_now = new \DateTime();
        $date2    = new \DateTime($date);
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
        if (Storage::disk('local')->exists($image)) {
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

        if(is_array($data)){
            return $data;
        }
        
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

function export_excel($records,$heading=false) {

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
        $mail->Host       = config('emails.host');             //  smtp host
        $mail->SMTPAuth   = true;
        $mail->Username   = config('emails.username');   //  sender username
        $mail->Password   = config('emails.password');       // sender password
        $mail->SMTPSecure = config('emails.smtp_secure');                  // encryption - ssl/tls
        $mail->Port       = config('emails.port');                          // port - 587/465

        $mail->setFrom(config('emails.username'), config('emails.sender'));
        $mail->addAddress($request->email);
      //  $mail->addCC($request->emailCc);
      //  $mail->addBCC($request->emailBcc);

        $mail->addReplyTo(config('emails.username'), 'No Reply');

        // if(isset($_FILES['emailAttachments'])) {
        //     for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
        //         $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
        //     }
        // }


        $mail->isHTML(true);                // Set email content format to HTML

       
        $mail->Subject = $request->subject ?? 'Knowledge Resource Center Email';
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
function isValidWebLink($link) {
    // Define a regular expression pattern to match web links
    $pattern = '/^(http|https|ftp|ftps):\/\/.+/i';
    
    // Check if the link matches the pattern
    if (preg_match($pattern, $link)) {
        return true; // Link is valid
    } else {
        return false; // Link is not valid
    }
}


function getFileMimeType($file_path)
{
    if (!file_exists($file_path)) {
        return "File not found";
    }

    $finfo     = finfo_open(FILEINFO_MIME_TYPE); // Open fileinfo extension
    $mime_type = finfo_file($finfo, $file_path); // Get MIME type
    finfo_close($finfo); // Close fileinfo extension

    return $mime_type;
}

function get_file_type($file_path=null,$pub_url=null){


   $mime_type = getFileMimeType($file_path);

   if($mime_type){
        $mime_type = str_replace('application/','',$mime_type);
        $mime_type = str_replace('images/','',$mime_type);
    }

   //sdd($mime_type);
  
    $mime_type = ($mime_type)?$mime_type: $pub_url;

    $type = PublicationType::where('mime_types','like','%'.strtolower($mime_type).'%')->first();
    
    if(!$type)
        $type = PublicationType::where('name','like','%other%')->first();
 
    return $type;
}

function html_to_text($html) {
    // Remove HTML tags
    $text = strip_tags($html);
    
    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Convert special characters to plain text
    $text = htmlspecialchars_decode($text, ENT_QUOTES | ENT_HTML5);
    
    // Convert multiple spaces into single spaces
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Trim leading and trailing spaces
    $text = trim($text);
    
    return $text;
}

function clear_cache(){
  Cache::flush();
}

function user_profile_photo($photo=null){


    if (!empty(@current_user()->photo) || $photo):
        $user_photo = ($photo)?$photo:current_user()->photo;
        $image_link = asset('storage/uploads/users/' . $user_photo);
    else:
        $image_link = asset('assets/images/user.jpg');
    endif;

    return $image_link;		 
}

function get_publication_state($approved,$rejected){
    if($rejected)
        return 'Rejected';
    if($approved)
        return 'Active';
    if(!$approved && ! $rejected)
        return "Pending Approval";
}


function pdfToText($pdf_url){

if(strpos($pdf_url,'.pdf')== false)
 return '';

// Create a new instance of the PDF parser
$parser = new Parser();

// Parse the PDF file
$pdf = $parser->parseFile($pdf_url);

// Extract text from the PDF
$text = $pdf->getText();

// Output the extracted text
return htmlspecialchars($text);

}
function cleanHtmlContent($htmlContent)
{
    // Use DOMDocument to parse and clean the HTML content
    $dom = new \DOMDocument();

    // Suppress errors due to invalid HTML
    libxml_use_internal_errors(true);

    // Load the HTML content
    $dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));

    // Get all the div elements
    $divs = $dom->getElementsByTagName('div');

    // Loop through each div
    foreach ($divs as $div) {
        // Check if the div has the style attribute you want to remove
        if ($div->hasAttribute('style')) {
            $style = $div->getAttribute('style');
            if (strpos($style, 'position: absolute') !== false) {
                // Remove the style attribute or modify it as needed
                $div->removeAttribute('style');
            }
        }
    }

    // Save the cleaned HTML content
    $cleanedHtmlContent = $dom->saveHTML();

    // Return the cleaned content
    return $cleanedHtmlContent;
}


// Now you can pass $cleanedContent to your view
if (!function_exists('extract_first_page_as_image')) {
    /**
     * Extract the first page of a PDF as an image
     *
     * @param string $pdfPath Path to the uploaded PDF or PDF URL
     * @param string $outputPath Output path for the image
     * @return string Image path or URL
     * @throws Exception if extraction fails
     */
    function extract_first_page_as_image(string $pdfPath, string $outputPath): string
    {
        try {
            // Create an instance of the PDF to Image converter
            $pdf = new Pdf($pdfPath);
            
            // Set the page to be converted (Page 1)
            $pageNumber = 1;

            // Specify the image format (e.g., jpeg, png)
            $imageFormat = 'png';

            // Generate the image from the first page
            $imagePath = $outputPath . '/' . basename($pdfPath, '.pdf') . '_page1.' . $imageFormat;
            $pdf->setPage($pageNumber)->saveImage($imagePath);

            // Return the generated image path
            return $imagePath;
        } catch (Exception $e) {
            throw new Exception("Failed to extract the first page as an image: " . $e->getMessage());
        }
    }
}

function cleanUTF8($value){

    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    $value = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', '', $value);
    $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

    return $value;
}


 function sendPushNotification($title, $message, $fcmTokens, $isTopic = false)
{
    try {
        if (empty($fcmTokens) || empty($message)) {
            \Log::warning('Push notification skipped: Missing tokens or message.');
            return false; // Indicate failure or no action taken
        }

        // Dispatch the job
        PushNotificationJob::dispatch($title, $message, $fcmTokens, $isTopic);

        return true; // Indicate success
    } catch (Exception $ex) {
        \Log::error('Push Error: ' . $ex->getMessage());
        return false; // Indicate failure
    }
}

function updateUSerPushToken($request,$user=null){

    $auth_user = ($user)?$user:auth()->user();
    
    if($auth_user && $request->header('x-fcm-token')){

        $existing_token = cache()->get('fcm_token_'.$auth_user->id);
        $header_token = $request->header('x-fcm-token');
      
        //update if tokens are different
        if(!$existing_token || $existing_token !== $header_token){

        $user = User::find($auth_user->id);
        $user->fcm_token = $header_token;
        $user->save();

        cache()->put('fcm_token_'.$auth_user->id,$header_token);
    
      }
    }
  }

  function site_theme(){
    return settings()->site_theme;
  }

function fix_text_encoding($text) {
    // Convert the text from a problematic encoding (ISO-8859-1) to UTF-8
    $fixed_text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
    
    return $fixed_text;
}

if (!function_exists('extract_pdf_as_image')) {
    /**
     * Extract the first page of a PDF from a URL and save it as a JPG image
     *
     * @param string $pdfUrl URL of the PDF
     * @param string $outputPath Directory to save the JPG image
     * @return string Path to the saved JPG image
     * @throws Exception if extraction fails
     */
    function extract_pdf_as_image(string $pdfUrl, string $outputPath): string
    {
        try {
            // Download the PDF from the URL
            $pdfContent = file_get_contents($pdfUrl);
            if ($pdfContent === false) {
                throw new Exception("Failed to download PDF from URL: $pdfUrl");
            }

            // Create a temporary file in Laravel's storage
            $tempPdfPath = 'publications/' . uniqid() . '.pdf';
            Storage::disk('local')->put($tempPdfPath, $pdfContent);

            // Create an Imagick object
            $imagick = new Imagick();

            // Read the PDF file
            $imagick->readImage(Storage::path($tempPdfPath) . '[0]'); // [0] to read the first page

            // Set the image format to JPG
            $imagick->setImageFormat('jpg');

            // Define the output image path
            $imagePath = $outputPath . '/' . basename($tempPdfPath, '.pdf') . '_page1.jpg';

            // Save the image
            $imagick->writeImage($imagePath);

            // Clean up
            $imagick->clear();
            $imagick->destroy();

            // Delete the temporary PDF file
            Storage::disk('local')->delete($tempPdfPath);

            return $imagePath;
        } catch (Exception $e) {
            throw new Exception("Failed to extract the first page as a JPG: " . $e->getMessage());
        }
    }
}

?>
