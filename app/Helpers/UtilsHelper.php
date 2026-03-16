<?php

use App\Models\PublicationType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;
use App\Notifications\SendPushNotification;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageTarget;
use App\Notifications\AccountActivated;
use App\Models\PushNotification;
use App\Services\ExchangeEmailService;

if(!function_exists('truncate')){
	function truncate($str,$limit){

		return Str::of($str)->limit($limit);
	}
}

if(!function_exists('publication_description_for_list')){
	/**
	 * Strip "Overview of the Document" from publication description using string replace (leaves a single space).
	 */
	function publication_description_for_list($description){
		if (empty($description)) {
			return $description;
		}
		$text = str_ireplace('Overview of the Document', ' ', $description);
		return trim(preg_replace('/\s+/', ' ', $text));
	}
}

if(!function_exists('clean_unicode')){
	/**
	 * Remove hidden Unicode characters and control characters from text
	 * This includes zero-width spaces, directional marks, and other invisible characters
	 */
	function clean_unicode($text){
		if (empty($text)) {
			return $text;
		}
		
		// Remove zero-width characters
		$text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
		
		// Remove left-to-right and right-to-left marks
		$text = preg_replace('/[\x{200E}\x{200F}]/u', '', $text);
		
		// Remove zero-width joiner and non-joiner
		$text = preg_replace('/[\x{200C}\x{200D}]/u', '', $text);
		
		// Remove other invisible Unicode characters (control characters)
		$text = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}]/u', '', $text);
		
		// Remove other problematic Unicode ranges
		$text = preg_replace('/[\x{2060}-\x{206F}]/u', '', $text); // Word joiner, invisible plus, etc.
		$text = preg_replace('/[\x{202A}-\x{202E}]/u', '', $text); // Directional formatting
		$text = preg_replace('/[\x{2066}-\x{2069}]/u', '', $text); // Directional isolates
		
		// Remove soft hyphen (optional, but often unwanted)
		$text = preg_replace('/[\x{00AD}]/u', '', $text);
		
		return trim($text);
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
        // If file_path already contains full URL, return as-is
        if (strpos($file_path, 'http://') === 0 || strpos($file_path, 'https://') === 0) {
            return $file_path;
        }
        // Get storage URL
        $storageUrl = Storage::disk('local')->url($file_path);
        // If storage URL already contains domain, return as-is, otherwise prepend site URL
        if (strpos($storageUrl, 'http://') === 0 || strpos($storageUrl, 'https://') === 0) {
            return $storageUrl;
        }
        return url('/').$storageUrl;
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

    $emailDriver = config('emails.driver', 'smtp');
    $exchangeConfig = config('exchange-email');
    $exchangeConfigured = !empty($exchangeConfig['tenant_id']) && !empty($exchangeConfig['client_id']) && !empty($exchangeConfig['client_secret']);
    $useExchange = ($emailDriver === 'exchange') && $exchangeConfigured;

    // Normalize email data - handle both 'title' and 'subject' fields
    $subject = $request->subject ?? $request->title ?? 'Knowledge Resource Center Email';
    $email = $request->email ?? null;
    $body = $request->body ?? '';
    
    if (!$email) {
        \Log::error('send_email called without email address', ['request' => (array)$request]);
        return array('success'=>false,'message'=>"Email address is required.");
    }
    
    // Use Exchange when driver is 'exchange' and Exchange is configured
    if ($useExchange) {
        try {
            $result = sendEmailWithExchange(
                $email, 
                $subject, 
                $body
            );
            
            if ($result) {
                return array('success'=>true,'message'=>"Email has been sent via Exchange.");
            } else {
                // Exchange failed - but since it's configured, DON'T use SMTP
                \Log::error('Exchange email failed but Exchange is configured. SMTP fallback disabled.', [
                    'email' => $email,
                    'subject' => $subject
                ]);
                return array('success'=>false,'message'=>"Email sending failed. Exchange OAuth is configured but authentication failed. Please check Exchange configuration.");
            }
        } catch (\Exception $e) {
            // Exchange exception - DON'T fall back to SMTP
            \Log::error('Exchange email exception: ' . $e->getMessage(), [
                'email' => $email,
                'subject' => $subject,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return array('success'=>false,'message'=>"Email sending failed via Exchange: " . $e->getMessage());
        }
    }

    // Use PHPMailer/SMTP when driver is 'smtp' or when Exchange is not configured
    \Log::info('Using SMTP for sending (driver: ' . $emailDriver . ')');
    
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
        $mail->Port       = config('emails.port');  
        $mail->FromName = config('emails.sender');                // port - 587/465

        $mail->setFrom(config('emails.username'), config('emails.sender'),true);
        $mail->addAddress($email);
      //  $mail->addCC($request->emailCc);
      //  $mail->addBCC($request->emailBcc);

        $mail->addReplyTo(config('emails.username'), config('emails.sender'));

        // if(isset($_FILES['emailAttachments'])) {
        //     for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
        //         $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
        //     }
        // }


        $mail->isHTML(true);                // Set email content format to HTML

       
        $mail->Subject = $subject;
        $mail->Body    = $body;

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

/**
 * Send email using Exchange service (Microsoft Graph API)
 * 
 * @param string|array $to Email address(es)
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $fromEmail From email address
 * @param string $fromName From name
 * @param array $cc CC recipients
 * @param array $bcc BCC recipients
 * @param array $attachments Attachments
 * @return bool
 */
function sendEmailWithExchange($to, $subject, $body, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
{
    try {
        $config = config('exchange-email');
        
        // Check if Exchange is configured
        if (empty($config['tenant_id']) || empty($config['client_id']) || empty($config['client_secret'])) {
            \Log::warning('Exchange service not configured. Missing tenant_id, client_id, or client_secret.');
            return false;
        }
        
        // Use Exchange Email Service
        $oauth = new \App\Services\ExchangeEmailService(
            $config['tenant_id'],
            $config['client_id'],
            $config['client_secret'],
            $config['redirect_uri'],
            $config['scope'],
            $config['auth_method']
        );
        
        if (!$oauth->isConfigured()) {
            \Log::warning('Exchange service not properly configured. OAuth object validation failed.');
            return false;
        }
        
        // Get client credentials token (or refresh if needed)
        if (!$oauth->hasValidToken()) {
            \Log::info('Getting new Exchange OAuth token...');
            $tokenResult = $oauth->getClientCredentialsToken();
            
            if (!$tokenResult) {
                \Log::error('Failed to obtain Exchange OAuth token. Check your Exchange credentials.');
                return false;
            }
        }
        
        \Log::info('Sending email via Exchange OAuth', [
            'to' => is_array($to) ? implode(', ', $to) : $to,
            'subject' => $subject,
            'from' => $fromEmail ?: env('MAIL_FROM_ADDRESS')
        ]);
        
        $result = $oauth->sendEmail(
            $to,
            $subject,
            $body,
            true, // HTML email
            $fromEmail ?: env('MAIL_FROM_ADDRESS'),
            $fromName ?: env('MAIL_FROM_NAME', 'Africa CDC Knowledge Hub'),
            $cc,
            $bcc,
            $attachments
        );
        
        if ($result) {
            \Log::info('Email sent successfully via Exchange OAuth');
            return true;
        } else {
            \Log::error('Exchange sendEmail returned false. Check Exchange OAuth configuration and permissions.');
            return false;
        }
        
    } catch (\Exception $e) {
        \Log::error('Exchange email failed with exception: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
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
    // Avoid recursive calls - just use the photo passed in
    if (!empty($photo)) {
        $user_photo = $photo;
        // If photo is already a full URL, return it as-is
        if (filter_var($user_photo, FILTER_VALIDATE_URL)) {
            return $user_photo;
        }
        // If photo is already processed by storage_link, return as-is
        if (strpos($user_photo, '/storage/') !== false || strpos($user_photo, url('/')) !== false) {
            return $user_photo;
        }
        $image_link = asset('storage/uploads/users/' . $user_photo);
    } else {
        // Only check current_user if no photo provided and avoid recursion
        $auth_user = Auth::user();
        if ($auth_user && !empty($auth_user->getRawOriginal('photo'))) {
            $user_photo = $auth_user->getRawOriginal('photo');
            $image_link = asset('storage/uploads/users/' . $user_photo);
        } else {
            $image_link = asset('assets/images/user.jpg');
        }
    }

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

  /**
   * Admin layout to use: Nifty (theme1) or default. Pass 'tabular' for list layouts.
   */
  function admin_layout($type = 'main'){
    if ((settings()->site_theme ?? '') === 'theme1.') {
      return 'admin.layouts.main_nifty';
    }
    return $type === 'tabular' ? 'admin.layouts.tabular' : 'admin.layouts.main';
  }

function fix_text_encoding($text) {
    // Convert the text from a problematic encoding (ISO-8859-1) to UTF-8
        // Define replacements for a wide range of common misinterpreted characters
        $replacements = [
            'â€“' => '–',    // en dash
            'â€”' => '—',    // em dash
            'â€˜' => '‘',    // left single quotation mark
            'â€™' => '’',    // right single quotation mark / apostrophe
            'â€œ' => '“',    // left double quotation mark
            'â€' => '”',    // right double quotation mark
            'â€¢' => '•',    // bullet point
            'Ã©' => 'é',     // e with acute accent
            'Ã¨' => 'è',     // e with grave accent
            'Ã€' => 'À',     // A with grave accent
            'Ã¡' => 'á',     // a with acute accent
            'Ã¢' => 'â',     // a with circumflex
            'Ã£' => 'ã',     // a with tilde
            'Ãª' => 'ê',     // e with circumflex
            'Ã«' => 'ë',     // e with diaeresis
            'Ã¯' => 'ï',     // i with diaeresis
            'Ã´' => 'ô',     // o with circumflex
            'Ã¶' => 'ö',     // o with diaeresis
            'Ã¹' => 'ù',     // u with grave accent
            'Ã»' => 'û',     // u with circumflex
            'Ã¼' => 'ü',     // u with diaeresis
            'Ã§' => 'ç',     // c with cedilla
            'Ã‘' => 'Ñ',     // N with tilde
            'Ã±' => 'ñ',     // n with tilde
            'Â' => '',       // remove extra 'Â' characters often found before symbols
            '**' => '',      // remove double asterisks
        ];

        // Replace each misinterpreted character in the text
        return strtr($text, $replacements);
    

  
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
            $tempPdfPath = 'uploads/publications/cover_' . uniqid() . '.pdf';
            file_put_contents(Storage::path($tempPdfPath), $pdfContent);
            //Storage::disk('local')->put($tempPdfPath, $pdfContent);

            // Create an Imagick object
            $imagick = new Imagick();

            Log::info('PDF Path: ' . Storage::path($tempPdfPath));
            if (!file_exists(Storage::path($tempPdfPath))) {
                throw new Exception("File does not exist at path: " . Storage::path($tempPdfPath));
            }

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
           // Storage::disk('local')->delete($tempPdfPath);

            return $imagePath;
        } catch (Exception $e) {
            throw new Exception("Failed to extract the first page as a JPG: " . $e->getMessage());
        }
        
    }

    function is_image($path){
            return strpos($path,'.jpg') || strpos($path,'.png') 
            || strpos($path,'.jpeg') 
            || strpos($path,'.gif')
             || strpos($path,'.bmp') 
             || strpos($path,'.tiff') 
             || strpos($path,'.ico');
    }
}

if (!function_exists('detect_and_embed_video_links')) {
    /**
     * Detect video links in text and convert them to embedded previews
     * Also converts regular URLs to clickable links that open in a new tab
     * Supports YouTube, Vimeo, and direct video file URLs
     * 
     * @param string $text The text content to process
     * @param int $width Width for video preview (default: 80px)
     * @param int $height Height for video preview (default: 80px)
     * @return string Text with video links converted to embedded previews and URLs as clickable links
     */
    function detect_and_embed_video_links($text, $width = 80, $height = 80) {
        if (empty($text)) {
            return $text;
        }
        
        // First, mark all URLs to prevent double processing
        // We'll use placeholders for URLs that are already inside HTML tags (like existing links, images, etc.)
        $placeholders = [];
        $placeholderIndex = 0;
        
        // Protect existing HTML tags and their content
        $text = preg_replace_callback('/<[^>]+>/i', function($matches) use (&$placeholders, &$placeholderIndex) {
            $placeholder = '___HTML_PLACEHOLDER_' . $placeholderIndex . '___';
            $placeholders[$placeholder] = $matches[0];
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // YouTube patterns
        $youtubePattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w|-]{11})(?:(?:[\?&]t=)(\S+))?/i';
        
        // Vimeo patterns
        $vimeoPattern = '/https?:\/\/(?:player\.)?vimeo\.com\/(?:video\/)?([0-9]+)(?:\?.*)?/i';
        
        // Direct video file patterns (mp4, webm, ogg, etc.)
        $directVideoPattern = '/(https?:\/\/[^\s<>"\'{}|\\^`\[\]]+\.(mp4|webm|ogg|ogv|mov|avi|wmv|flv|m4v)(?:\?[^\s<>"\'{}|\\^`\[\]]*)?)/i';
        
        // Replace YouTube links
        $text = preg_replace_callback($youtubePattern, function($matches) use ($width, $height) {
            $videoId = $matches[1];
            $startTime = isset($matches[2]) ? '?start=' . $matches[2] : '';
            return '<div class="video-preview-inline" style="display: inline-block; margin: 4px; vertical-align: middle;">
                        <iframe width="' . $width . '" height="' . $height . '" 
                                src="https://www.youtube.com/embed/' . $videoId . $startTime . '" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                style="border-radius: 4px; max-width: 100%;">
                        </iframe>
                    </div>';
        }, $text);
        
        // Replace Vimeo links
        $text = preg_replace_callback($vimeoPattern, function($matches) use ($width, $height) {
            $videoId = $matches[1];
            return '<div class="video-preview-inline" style="display: inline-block; margin: 4px; vertical-align: middle;">
                        <iframe width="' . $width . '" height="' . $height . '" 
                                src="https://player.vimeo.com/video/' . $videoId . '" 
                                frameborder="0" 
                                allow="autoplay; fullscreen; picture-in-picture" 
                                allowfullscreen
                                style="border-radius: 4px; max-width: 100%;">
                        </iframe>
                    </div>';
        }, $text);
        
        // Replace direct video file links
        $text = preg_replace_callback($directVideoPattern, function($matches) use ($width, $height) {
            $videoUrl = $matches[1];
            return '<div class="video-preview-inline" style="display: inline-block; margin: 4px; vertical-align: middle;">
                        <video width="' . $width . '" height="' . $height . '" 
                               controls 
                               preload="metadata"
                               style="border-radius: 4px; max-width: 100%;">
                            <source src="' . htmlspecialchars($videoUrl) . '" type="video/' . $matches[2] . '">
                            Your browser does not support the video tag.
                        </video>
                    </div>';
        }, $text);
        
        // Restore HTML placeholders before processing URLs
        foreach ($placeholders as $placeholder => $original) {
            $text = str_replace($placeholder, $original, $text);
        }
        
        // Convert remaining URLs to clickable links (only plain URLs, not already processed videos)
        // Match URLs that are not already inside HTML tags
        // This pattern matches http/https URLs that are standalone (not in existing tags)
        $urlPattern = '/(?<!href=["\'])(?<!src=["\'])(?<!<[^>]*>)(?<!["\'>])(https?:\/\/[^\s<>"\'{}|\\^`\[\]\.]+(?:[^\s<>"\'{}|\\^`\[\]])*?)(?![^<]*>)(?![^\s]*[.,;:!?](?=\s|$|[<]))/i';
        
        // More reliable approach: split text into parts and process each part
        // First, extract all existing HTML tags and their positions
        $parts = preg_split('/(<[^>]+>)/i', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        
        foreach ($parts as $part) {
            // Skip HTML tags
            if (preg_match('/^<[^>]+>$/i', $part)) {
                $result .= $part;
                continue;
            }
            
            // Process plain text parts for URLs
            $part = preg_replace_callback('/(https?:\/\/[^\s<>"\'{}|\\^`\[\]]+)/i', function($matches) {
                $url = trim($matches[1]);
                // Clean trailing punctuation
                $url = rtrim($url, '.,;:!?)');
                
                // Skip if it's already an embedded video (starts with video preview div)
                if (strpos($url, '<div class="video-preview-inline"') !== false) {
                    return $url;
                }
                
                // Extract domain for display text
                $displayText = $url;
                if (preg_match('/https?:\/\/(?:www\.)?([^\/?#]+)/i', $url, $domainMatch)) {
                    $displayText = $domainMatch[1];
                }
                
                return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: underline; word-break: break-all;">' . htmlspecialchars($displayText) . '</a>';
            }, $part);
            
            $result .= $part;
        }
        
        $text = $result;
        
        return $text;
    }
}

?>
