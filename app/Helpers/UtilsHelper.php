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
use App\Jobs\PushNotificationJob;

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

if(!function_exists('strip_leading_overview_heading_from_summary_html')){
	/**
	 * Remove a leading "Overview" / "Overview of the Document" heading from AI summary HTML so the description
	 * starts with substantive content. Other section headings later in the HTML are left unchanged.
	 */
	function strip_leading_overview_heading_from_summary_html($html){
		if (empty($html) || !is_string($html)) {
			return $html;
		}
		$overview = 'Overview(?:\s+of\s+the\s+Document)?';
		$out = $html;
		for ($i = 0; $i < 8; $i++) {
			$prev = $out;
			// Optional opening div(s), then h3/h4 with Overview
			$out = preg_replace(
				'#^\s*(?:<div\b[^>]*>\s*)*<h[34]\b[^>]*>\s*' . $overview . '\s*</h[34]>\s*#iu',
				'',
				$out,
				1
			);
			// Paragraph with optional strong/b
			$out = preg_replace(
				'#^\s*(?:<div\b[^>]*>\s*)*<p\b[^>]*>\s*(?:<(?:strong|b)\b[^>]*>\s*)?' . $overview . '\s*(?:</(?:strong|b)>)?\s*</p>\s*#iu',
				'',
				$out,
				1
			);
			// Standalone strong/b line
			$out = preg_replace(
				'#^\s*(?:<div\b[^>]*>\s*)*<(?:strong|b)\b[^>]*>\s*' . $overview . '\s*</(?:strong|b)>\s*#iu',
				'',
				$out,
				1
			);
			if ($out === $prev) {
				break;
			}
		}
		return $out;
	}
}

if(!function_exists('strip_leading_subheadings_before_main_paragraph')){
	/**
	 * Remove leading h3/h4/h5 blocks and faux-title paragraphs (bold-only / short teal-styled lines)
	 * so the summary starts with a real body paragraph. Headings later in the HTML are unchanged.
	 */
	function strip_leading_subheadings_before_main_paragraph($html){
		if (empty($html) || !is_string($html)) {
			return $html;
		}
		$out = $html;
		for ($i = 0; $i < 25; $i++) {
			$prev = $out;
			$out = preg_replace('#^\s*(?:<br\s*/?>\s*|&nbsp;\s*|\xc2\xa0\s*)+#i', '', $out);
			$out = preg_replace(
				'#^\s*(?:<div\b[^>]*>\s*)*<h[345]\b[^>]*>.*?</h[345]>\s*#is',
				'',
				$out,
				1
			);
			$out = preg_replace(
				'#^\s*(?:<div\b[^>]*>\s*)*<p\b[^>]*>\s*(?:<span\b[^>]*>\s*)?<(?:strong|b)\b[^>]*>\s*[^<]{1,180}\s*</(?:strong|b)>\s*(?:</span>\s*)?</p>\s*#is',
				'',
				$out,
				1
			);
			$out = preg_replace(
				'#^\s*(?:<div\b[^>]*>\s*)*<p\b[^>]*style\s*=\s*["\'][^"\']*teal[^"\']*["\'][^>]*>\s*(?:<(?:strong|b)\b[^>]*>\s*)?[^<]{1,180}\s*(?:</(?:strong|b)>\s*)?</p>\s*#is',
				'',
				$out,
				1
			);
			if ($out === $prev) {
				break;
			}
		}
		return $out;
	}
}

if(!function_exists('normalize_ai_publication_summary_html')){
	/**
	 * Post-process AI HTML for publication descriptions: overview heading strip, then leading subheading strip.
	 */
	function normalize_ai_publication_summary_html($html){
		if (empty($html) || !is_string($html)) {
			return $html;
		}
		$out = strip_leading_overview_heading_from_summary_html($html);
		return strip_leading_subheadings_before_main_paragraph($out);
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

if (!function_exists('normalize_title_case_local')) {
    /**
     * Deterministic title-case formatter with common minor-word rules.
     */
    function normalize_title_case_local($title) {
        $title = clean_unicode(strip_tags((string) $title));
        if ($title === '') {
            return $title;
        }

        $title = preg_replace('/\s+/u', ' ', trim($title));
        $minorWords = [
            'a', 'an', 'and', 'as', 'at', 'but', 'by', 'for', 'from', 'in', 'into',
            'nor', 'of', 'on', 'onto', 'or', 'per', 'so', 'the', 'to', 'up', 'via',
            'with', 'yet', 'vs', 'v',
        ];
        $minorMap = array_fill_keys($minorWords, true);

        $tokens = preg_split('/(\s+)/u', $title, -1, PREG_SPLIT_DELIM_CAPTURE);
        $wordTokenIndexes = [];
        foreach ($tokens as $i => $token) {
            if (!preg_match('/^\s+$/u', $token)) {
                $wordTokenIndexes[] = $i;
            }
        }
        if (empty($wordTokenIndexes)) {
            return $title;
        }

        $firstWordIndex = $wordTokenIndexes[0];
        $lastWordIndex = $wordTokenIndexes[count($wordTokenIndexes) - 1];

        foreach ($wordTokenIndexes as $tokenIndex) {
            $isEdgeWord = ($tokenIndex === $firstWordIndex || $tokenIndex === $lastWordIndex);
            $tokens[$tokenIndex] = _normalize_title_case_word($tokens[$tokenIndex], $minorMap, $isEdgeWord);
        }

        return implode('', $tokens);
    }
}

if (!function_exists('_normalize_title_case_word')) {
    function _normalize_title_case_word($word, $minorMap, $isEdgeWord) {
        if ($word === '') {
            return $word;
        }

        if (!preg_match('/^([("“\'\[]*)(.*?)([)\]"”\',.!?:;]*)$/u', $word, $m)) {
            return $word;
        }

        $prefix = $m[1] ?? '';
        $core = $m[2] ?? '';
        $suffix = $m[3] ?? '';

        if ($core === '') {
            return $word;
        }

        $parts = preg_split('/([\-\/])/u', $core, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $i => $part) {
            if ($part === '-' || $part === '/') {
                continue;
            }

            $trimmed = trim($part);
            if ($trimmed === '') {
                continue;
            }

            $lower = mb_strtolower($trimmed);
            $isAcronym = preg_match('/^[A-Z0-9][A-Z0-9\-&]{1,}$/u', $trimmed)
                && mb_strlen($trimmed) <= 12
                && !isset($minorMap[$lower])
                && (preg_match('/[0-9&]/u', $trimmed) || mb_strlen($trimmed) === 3);
            if ($isAcronym) {
                $parts[$i] = $trimmed;
                continue;
            }

            if (!$isEdgeWord && isset($minorMap[$lower])) {
                $parts[$i] = $lower;
                continue;
            }

            $parts[$i] = mb_strtoupper(mb_substr($lower, 0, 1)) . mb_substr($lower, 1);
        }

        return $prefix . implode('', $parts) . $suffix;
    }
}

if (!function_exists('format_title_with_ai_fallback')) {
    /**
     * Try AI title normalization first (when enabled), fallback to local deterministic formatter.
     */
    function format_title_with_ai_fallback($title) {
        $source = clean_unicode(strip_tags((string) $title));
        if ($source === '') {
            return $source;
        }

        $local = normalize_title_case_local($source);
        $useAi = filter_var(env('TITLE_CASE_AI_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        if (!$useAi) {
            return $local;
        }

        try {
            $chatGpt = app('chatgpt');
            if (is_object($chatGpt) && method_exists($chatGpt, 'formatTitleCase')) {
                $aiTitle = $chatGpt->formatTitleCase($source);
                if (is_string($aiTitle) && trim($aiTitle) !== '') {
                    // Normalize AI output as final guardrail.
                    return normalize_title_case_local($aiTitle);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Title case AI formatting failed; using fallback.', [
                'error' => $e->getMessage(),
            ]);
        }

        return $local;
	}
}

if (!function_exists('format_view_count')) {
    /**
     * Compact view count: up to 999 as-is; 1,000+ as 1.0k / 1.5m / 2.3b (one decimal).
     */
    function format_view_count($count): string
    {
        $n = max(0, (int) $count);

        if ($n <= 999) {
            return (string) $n;
        }

        if ($n < 1_000_000) {
            return number_format($n / 1_000, 1, '.', '') . 'k';
        }

        if ($n < 1_000_000_000) {
            return number_format($n / 1_000_000, 1, '.', '') . 'm';
        }

        return number_format($n / 1_000_000_000, 1, '.', '') . 'b';
    }
}

if (!function_exists('publication_content_updated_at')) {
    /**
     * When the publication content was last meaningfully changed (not view counts).
     */
    function publication_content_updated_at($publication)
    {
        if (! $publication) {
            return null;
        }
        if (! empty($publication->content_updated_at)) {
            return $publication->content_updated_at;
        }
        if (! empty($publication->date_created)) {
            return $publication->date_created;
        }
        if (! empty($publication->created_at)) {
            return $publication->created_at;
        }

        return null;
    }
}

if (!function_exists('publication_content_updated_ago')) {
    function publication_content_updated_ago($publication)
    {
        $at = publication_content_updated_at($publication);

        return $at ? time_ago($at) : '—';
    }
}

if (!function_exists('publication_last_visited_at')) {
    function publication_last_visited_at($publication)
    {
        if (! $publication || empty($publication->last_visited_at)) {
            return null;
        }

        return $publication->last_visited_at;
    }
}

if (!function_exists('publication_touch_last_visited')) {
    function publication_touch_last_visited(int $publicationId): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasColumn('publication', 'last_visited_at')) {
            return;
        }
        $update = ['last_visited_at' => now()];
        if (\Illuminate\Support\Facades\Schema::hasColumn('publication', 'updated_at')) {
            $update['updated_at'] = \Illuminate\Support\Facades\DB::raw('`updated_at`');
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('publication', 'content_updated_at')) {
            $update['content_updated_at'] = \Illuminate\Support\Facades\DB::raw('`content_updated_at`');
        }
        \Illuminate\Support\Facades\DB::table('publication')->where('id', $publicationId)->update($update);
    }
}

if (!function_exists('publication_record_visit_metrics')) {
  function publication_record_visit_metrics(int $publicationId, bool $incrementMonthlyView = true): void
    {
        if ($incrementMonthlyView) {
            \App\Models\PublicationView::incrementView($publicationId);
        }
        $totalViews = \App\Models\PublicationView::getTotalViews($publicationId);
        $update = ['visits' => $totalViews];
        if (\Illuminate\Support\Facades\Schema::hasColumn('publication', 'last_visited_at')) {
            $update['last_visited_at'] = now();
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('publication', 'updated_at')) {
            $update['updated_at'] = \Illuminate\Support\Facades\DB::raw('`updated_at`');
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('publication', 'content_updated_at')) {
            $update['content_updated_at'] = \Illuminate\Support\Facades\DB::raw('`content_updated_at`');
        }
        \Illuminate\Support\Facades\DB::table('publication')->where('id', $publicationId)->update($update);
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
        $file_path = trim((string) $file_path);
        if ($file_path === '') {
            return '';
        }
        // Legacy double prefix: "uploads/https://…" (e.g. admin views concatenating uploads/ + already-absolute path)
        if (preg_match('#^uploads/(https?://)#i', $file_path)) {
            return storage_link(substr($file_path, strlen('uploads/')));
        }
        // If file_path already contains full URL, return as-is
        if (strpos($file_path, 'http://') === 0 || strpos($file_path, 'https://') === 0) {
            return $file_path;
        }
        try {
            return hub_storage()->url($file_path);
        } catch (\Throwable $e) {
            $storageUrl = Storage::disk('public')->url($file_path);
            if (strpos($storageUrl, 'http://') === 0 || strpos($storageUrl, 'https://') === 0) {
                return $storageUrl;
            }

            return url('/').$storageUrl;
        }
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

    // Normalize email data - handle both 'title' and 'subject' fields; recipient from 'email' or 'to'
    $subject = $request->subject ?? $request->title ?? 'Knowledge Resource Center Email';
    $email = $request->email ?? $request->to ?? null;
    $body = $request->body ?? '';

    if (!$email) {
        \Log::error('send_email called without email address', ['request' => (array)$request]);
        return array('success'=>false,'message'=>"Email address is required.");
    }
    $email = is_string($email) ? trim($email) : $email;
    \Log::info('send_email: sending to recipient', ['to' => $email, 'subject' => $subject]);

    // When driver is 'exchange' but Exchange is not configured, do NOT fall back to SMTP
    if ($emailDriver === 'exchange' && !$exchangeConfigured) {
        \Log::error('Email driver is exchange but Exchange is not configured. Set EXCHANGE_TENANT_ID, EXCHANGE_CLIENT_ID, EXCHANGE_CLIENT_SECRET in .env or set EMAIL_DRIVER=smtp to use SMTP.');
        return array('success'=>false,'message'=>"Email is set to Exchange but Exchange credentials are missing. Configure EXCHANGE_TENANT_ID, EXCHANGE_CLIENT_ID, EXCHANGE_CLIENT_SECRET in .env or set EMAIL_DRIVER=smtp (or MAIL_MAILER=smtp) to use SMTP.");
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
        
        $resolvedFromEmail = $fromEmail ?: \App\Support\EmailConfig::resolve('MAIL_FROM_ADDRESS', 'mail_from_address');
        $resolvedFromName = $fromName ?: \App\Support\EmailConfig::resolve('MAIL_FROM_NAME', 'mail_from_name', 'Africa CDC Knowledge Hub');

        \Log::info('Sending email via Exchange OAuth', [
            'to' => is_array($to) ? implode(', ', $to) : $to,
            'subject' => $subject,
            'from' => $resolvedFromEmail
        ]);
        
        $result = $oauth->sendEmail(
            $to,
            $subject,
            $body,
            true, // HTML email
            $resolvedFromEmail,
            $resolvedFromName,
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
    $mime_type = null;

    if (!empty($file_path) && is_string($file_path) && file_exists($file_path)) {
   $mime_type = getFileMimeType($file_path);
        if ($mime_type && strtolower($mime_type) !== 'file not found') {
            $mime_type = strtolower($mime_type);
            $mime_type = str_replace('application/', '', $mime_type);
            $mime_type = str_replace('images/', '', $mime_type);
        } else {
            $mime_type = null;
        }
    }

    if (!$mime_type && !empty($pub_url) && is_string($pub_url)) {
        $url = strtolower(trim($pub_url));
        if (is_video_platform_url($url)) {
            $mime_type = 'video';
        } else {
            $path = parse_url($url, PHP_URL_PATH) ?: $url;
            $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
            if ($ext !== '') {
                $mime_type = $ext;
            } else {
                $mime_type = $url;
            }
        }
    }

    if (!$mime_type) {
        $mime_type = 'other';
    }

    $type = PublicationType::where('mime_types', 'like', '%' . strtolower($mime_type) . '%')->first();
    if (!$type && strpos((string) $mime_type, 'video') !== false) {
        $type = PublicationType::where('name', 'like', '%video%')->first();
    }
    if (!$type)
        $type = PublicationType::where('name','like','%other%')->first();
 
    return $type;
}

if (!function_exists('is_video_platform_url')) {
    /**
     * Detect common video platform links and direct video files.
     */
    function is_video_platform_url($url)
    {
        if (!$url || !is_string($url)) {
            return false;
        }

        $u = strtolower(trim($url));
        $host = parse_url($u, PHP_URL_HOST) ?: '';
        $path = parse_url($u, PHP_URL_PATH) ?: '';

        $videoHosts = [
            'youtube.com', 'www.youtube.com', 'youtu.be',
            'vimeo.com', 'www.vimeo.com', 'player.vimeo.com',
            'dailymotion.com', 'www.dailymotion.com', 'dai.ly',
            'wistia.com', 'www.wistia.com', 'fast.wistia.net',
            'loom.com', 'www.loom.com',
        ];
        foreach ($videoHosts as $vh) {
            if ($host === $vh || (substr($host, -strlen('.'.$vh)) === '.'.$vh)) {
                return true;
            }
        }

        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
        $directVideoExt = ['mp4', 'm4v', 'mov', 'webm', 'ogg', 'ogv', 'mpeg', 'mpg', 'avi'];
        return in_array($ext, $directVideoExt, true);
    }
}

if (!function_exists('is_direct_video_file_url')) {
    function is_direct_video_file_url($url)
    {
        if (!$url || !is_string($url)) {
            return false;
        }
        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'm4v', 'mov', 'webm', 'ogg', 'ogv', 'mpeg', 'mpg', 'avi'], true);
    }
}

if (!function_exists('get_video_embed_url')) {
    /**
     * Convert known video platform links to embeddable URLs.
     */
    function get_video_embed_url($url)
    {
        if (!$url || !is_string($url)) {
            return null;
        }

        $trimmed = trim($url);
        $parts = parse_url($trimmed);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        parse_str($parts['query'] ?? '', $query);

        if ($host === 'youtu.be') {
            $id = trim($path, '/');
            return $id ? 'https://www.youtube.com/embed/' . $id : null;
        }

        if ($host === 'youtube.com' || $host === 'www.youtube.com' || $host === 'm.youtube.com') {
            if (!empty($query['v'])) {
                return 'https://www.youtube.com/embed/' . $query['v'];
            }
            if (strpos($path, '/embed/') === 0) {
                return 'https://www.youtube.com' . $path;
            }
            if (strpos($path, '/shorts/') === 0) {
                $id = trim(substr($path, strlen('/shorts/')), '/');
                return $id ? 'https://www.youtube.com/embed/' . $id : null;
            }
        }

        if ($host === 'vimeo.com' || $host === 'www.vimeo.com') {
            $id = trim($path, '/');
            if ($id && preg_match('/^\d+$/', $id)) {
                return 'https://player.vimeo.com/video/' . $id;
            }
        }
        if ($host === 'player.vimeo.com') {
            return $trimmed;
        }

        if (($host === 'dailymotion.com' || $host === 'www.dailymotion.com') && strpos($path, '/video/') === 0) {
            $id = trim(substr($path, strlen('/video/')), '/');
            return $id ? 'https://www.dailymotion.com/embed/video/' . $id : null;
        }
        if ($host === 'dai.ly') {
            $id = trim($path, '/');
            return $id ? 'https://www.dailymotion.com/embed/video/' . $id : null;
        }

        return null;
    }
}

if (!function_exists('get_video_platform_thumbnail_url')) {
    /**
     * Resolve thumbnail URL for known video platforms.
     */
    function get_video_platform_thumbnail_url($url)
    {
        if (!$url || !is_string($url)) {
            return null;
        }

        $trimmed = trim($url);
        $parts = parse_url($trimmed);
        $host = strtolower($parts['host'] ?? '');

        $embed = get_video_embed_url($trimmed);
        if ($embed && (strpos($embed, 'youtube.com/embed/') !== false)) {
            $id = basename(parse_url($embed, PHP_URL_PATH));
            return $id ? 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg' : null;
        }

        // Vimeo / Dailymotion via oEmbed
        if (strpos($host, 'vimeo.com') !== false || strpos($host, 'dailymotion.com') !== false || $host === 'dai.ly') {
            $oembed = null;
            if (strpos($host, 'vimeo.com') !== false) {
                $oembed = 'https://vimeo.com/api/oembed.json?url=' . urlencode($trimmed);
            } else {
                $oembed = 'https://www.dailymotion.com/services/oembed?url=' . urlencode($trimmed);
            }

            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true,
                    'header' => "User-Agent: KHUB/1.0\r\n",
                ],
            ]);
            $raw = @file_get_contents($oembed, false, $ctx);
            if ($raw) {
                $json = json_decode($raw, true);
                $thumb = $json['thumbnail_url'] ?? null;
                if (is_string($thumb) && $thumb !== '') {
                    return $thumb;
                }
            }
        }

        return null;
    }
}

if (!function_exists('extract_video_frame_cover')) {
    /**
     * Extract frame at ~2s from local video using ffmpeg.
     * Returns stored filename (relative in uploads/publications) or null.
     */
    function extract_video_frame_cover($localVideoPath, $seed = 'video')
    {
        if (empty($localVideoPath) || !is_string($localVideoPath) || !file_exists($localVideoPath)) {
            return null;
        }

        $outputDir = hub_storage_path('uploads/publications').'/';
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }
        if (!is_dir($outputDir)) {
            return null;
        }

        $hash = md5($seed . '|' . $localVideoPath . '|' . @filemtime($localVideoPath));
        $filename = $hash . '_frame.jpg';
        $outputPath = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        // If already generated, reuse it.
        if (file_exists($outputPath) && filesize($outputPath) > 0) {
            return $filename;
        }

        $ffmpeg = trim((string) @shell_exec('command -v ffmpeg'));
        if ($ffmpeg === '') {
            return null;
        }

        $cmd = escapeshellcmd($ffmpeg)
            . ' -y -ss 00:00:02 -i ' . escapeshellarg($localVideoPath)
            . ' -frames:v 1 -q:v 2 ' . escapeshellarg($outputPath)
            . ' 2>&1';
        @exec($cmd, $out, $exitCode);

        if ($exitCode === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
            return $filename;
        }

        return null;
    }
}

if (!function_exists('get_video_cover_source')) {
    /**
     * Pick best cover source for videos: platform thumbnail first, else local frame extraction.
     *
     * @return array{cover: string, is_external: bool}|null
     */
    function get_video_cover_source($videoUrl = null, $localVideoPath = null, $seed = 'video')
    {
        $platformThumb = get_video_platform_thumbnail_url($videoUrl);
        if ($platformThumb) {
            return ['cover' => $platformThumb, 'is_external' => true];
        }

        $frameFile = extract_video_frame_cover($localVideoPath, $seed);
        if ($frameFile) {
            return ['cover' => $frameFile, 'is_external' => false];
        }

        return null;
    }
}

if (!function_exists('resolve_publication_card_cover')) {
    /**
     * Resolve a safe card image URL for publication cards.
     * Supports image covers and video platform thumbnails.
     */
    function resolve_publication_card_cover($publication)
    {
        $default = asset('assets/images/cover.png');
        if (!$publication) {
            return $default;
        }

        $publicationUrl = isset($publication->publication) ? (string) $publication->publication : '';
        $isVideo = !empty($publication->is_video)
            || is_video_platform_url($publicationUrl)
            || is_direct_video_file_url($publicationUrl);

        // For videos, platform thumbnail can be more reliable if cover is missing.
        if ($isVideo) {
            $thumb = get_video_platform_thumbnail_url($publicationUrl);
            if ($thumb && filter_var($thumb, FILTER_VALIDATE_URL)) {
                return $thumb;
            }
        }

        $candidate = null;
        if (!empty($publication->cover)) {
            $candidate = (string) $publication->cover;
        } elseif (!empty($publication->image_url)) {
            $candidate = (string) $publication->image_url;
        }

        if (!$candidate) {
            return $default;
        }

        if (filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $candidate;
        }

        if (strpos($candidate, 'storage/') !== false || strpos($candidate, 'uploads/') !== false) {
            return asset($candidate);
        }

        if (strpos($candidate, '/') === 0) {
            return url($candidate);
        }

        return $default;
    }
}

if (!function_exists('normalize_publication_attachment_extension')) {
    /**
     * Normalize extension for display/preview. ".pd" is treated as a truncated ".pdf" (common upload mistake).
     */
    function normalize_publication_attachment_extension(string $extension): string
    {
        $ext = strtolower(trim($extension));

        return $ext === 'pd' ? 'pdf' : $ext;
    }
}

if (!function_exists('publication_filename_is_pdf')) {
    /**
     * Whether a stored filename/path refers to a PDF, including mis-saved ".pd" extensions.
     */
    function publication_filename_is_pdf(?string $rawFilename): bool
    {
        if ($rawFilename === null || $rawFilename === '') {
            return false;
        }
        $lower = strtolower($rawFilename);
        if (strpos($lower, '.pdf') !== false) {
            return true;
        }
        if (preg_match('/\.pd$/i', $rawFilename)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('forum_attachment_display_name')) {
    /**
     * Human-readable filename for forum UI (never the hashed storage basename when `name` is set).
     */
    function forum_attachment_display_name($attachment): string
    {
        if (!$attachment instanceof \App\Models\CustomAttachment) {
            return 'Attachment';
        }
        $attrs = $attachment->getAttributes();
        $name = isset($attrs['name']) ? trim((string) $attrs['name']) : '';
        if ($name !== '') {
            return $name;
        }

        $rawPath = $attachment->getRawOriginal('path');
        if ($rawPath === null || $rawPath === '') {
            $rawPath = (string) ($attrs['path'] ?? '');
        }
        if ($rawPath !== '' && !preg_match('#^https?://#i', $rawPath)) {
            $base = basename(str_replace('\\', '/', $rawPath));
            if ($base !== '' && strpos($base, '.') !== false) {
                return $base;
            }
        }

        return 'Attachment';
    }
}

if (!function_exists('forum_comment_attachment_raw_extension')) {
    function forum_comment_attachment_raw_extension($attachment): string
    {
        if (!$attachment instanceof \App\Models\CustomAttachment) {
            return '';
        }
        $raw = $attachment->getRawOriginal('path');
        if ($raw === null || $raw === '') {
            $raw = $attachment->getAttributes()['path'] ?? '';
        }

        return strtolower(pathinfo((string) $raw, PATHINFO_EXTENSION));
    }
}

if (!function_exists('forum_comment_attachment_is_convertible_office')) {
    /**
     * Office types on forum comments or forum thread posts (may be converted to PDF on demand via the PDF route).
     */
    function forum_comment_attachment_is_convertible_office($attachment): bool
    {
        if (!$attachment instanceof \App\Models\CustomAttachment) {
            return false;
        }
        $model = (string) $attachment->getAttribute('model');
        if (! in_array($model, ['forum_comments', 'forums'], true)) {
            return false;
        }
        $ext = forum_comment_attachment_raw_extension($attachment);

        return $ext !== '' && app(\App\Services\OfficeDocumentToPdfService::class)->isConvertibleExtension($ext);
    }
}

if (!function_exists('forum_comment_attachment_effective_href')) {
    /**
     * Public URL for download/preview: convertible office types use the on-demand PDF route.
     */
    function forum_comment_attachment_effective_href($attachment): string
    {
        if (!$attachment instanceof \App\Models\CustomAttachment) {
            return '';
        }
        if (forum_comment_attachment_is_convertible_office($attachment)) {
            return route('forums.comment-attachment.pdf', ['attachment' => $attachment->id], true);
        }

        return $attachment->path;
    }
}

if (!function_exists('forum_comment_attachment_preview_extension')) {
    function forum_comment_attachment_preview_extension($attachment): string
    {
        if (forum_comment_attachment_is_convertible_office($attachment)) {
            return 'pdf';
        }

        return forum_comment_attachment_raw_extension($attachment);
    }
}

if (!function_exists('normalize_publication_stored_filename_for_public_url')) {
    /**
     * Use .pdf in public storage URLs when the DB has a mis-saved .pd filename so preview/download match the real file.
     */
    function normalize_publication_stored_filename_for_public_url(?string $storedRelativePath): ?string
    {
        if ($storedRelativePath === null || $storedRelativePath === '') {
            return $storedRelativePath;
        }
        if (preg_match('/\.pd$/i', $storedRelativePath)) {
            return preg_replace('/\.pd$/i', '.pdf', $storedRelativePath);
        }

        return $storedRelativePath;
    }
}

if (!function_exists('resolve_publication_upload_disk_path')) {
    /**
     * Resolve a file under uploads/publications when DB has .pd but disk has .pdf (or the reverse).
     */
    function resolve_publication_upload_disk_path(string $rawRelativeFilename): ?string
    {
        if ($rawRelativeFilename === '') {
            return null;
        }
        $baseDir = hub_storage_path('uploads/publications').'/';
        $candidates = [$rawRelativeFilename];
        if (preg_match('/\.pd$/i', $rawRelativeFilename)) {
            $candidates[] = preg_replace('/\.pd$/i', '.pdf', $rawRelativeFilename);
        }
        if (preg_match('/\.pdf$/i', $rawRelativeFilename)) {
            $candidates[] = preg_replace('/\.pdf$/i', '.pd', $rawRelativeFilename);
        }
        foreach (array_unique($candidates) as $name) {
            $path = $baseDir . $name;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
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

/**
 * Two-letter initials for avatar fallback on community cards, etc.
 */
/**
 * Organization / institution label for a contributor profile (user profile field, then publication affiliation).
 */
function contributor_profile_organization(?\App\Models\Author $author, ?\App\Models\User $user = null): ?string
{
    if ($user !== null) {
        $fromUser = trim((string) ($user->organization_name ?? ''));
        if ($fromUser !== '') {
            return $fromUser;
        }
    }

    if ($author === null) {
        return null;
    }

    $affiliation = \App\Models\Publication::query()
        ->where('author_id', $author->id)
        ->whereNotNull('author_affiliation')
        ->where('author_affiliation', '!=', '')
        ->orderByDesc('content_updated_at')
        ->orderByDesc('updated_at')
        ->value('author_affiliation');

    $affiliation = trim((string) ($affiliation ?? ''));

    return $affiliation !== '' ? $affiliation : null;
}

/**
 * Emoji icon for a community participant badge slug.
 */
function participant_badge_emoji(?string $slug): string
{
    switch ($slug) {
        case 'silver':
            return '🥈';
        case 'gold':
            return '🥇';
        case 'platinum':
            return '💎';
        case 'diamond':
            return '💠';
        default:
            return '🏅';
    }
}

function community_user_initials(?string $name): string
{
    $name = trim((string) $name);
    if ($name === '') {
        return '?';
    }
    $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
    if (count($parts) >= 2) {
        $a = mb_substr($parts[0], 0, 1);
        $b = mb_substr($parts[count($parts) - 1], 0, 1);

        return mb_strtoupper($a.$b);
    }

    return mb_strtoupper(mb_substr($parts[0], 0, min(2, mb_strlen($parts[0]))));
}

/**
 * Display name of the community restricted to @africacdc.org accounts on the hub.
 */
/**
 * Whether community directory cards show the participant avatars / names strip (Admin → Configure).
 */
function communities_listing_show_participants(): bool
{
    try {
        $s = settings();

        return (bool) ($s->communities_listing_show_participants ?? true);
    } catch (\Throwable $e) {
        return true;
    }
}

/**
 * Max participant faces per community card (1–24, default 8).
 */
function communities_listing_max_faces(): int
{
    try {
        $s = settings();
        $n = (int) ($s->communities_listing_max_faces ?? 8);

        return max(1, min(24, $n));
    } catch (\Throwable $e) {
        return 8;
    }
}

/**
 * Community directory cards per row on desktop (1, 2, or 3; default 2).
 */
function communities_listing_cards_per_row(): int
{
    try {
        if (! \Illuminate\Support\Facades\Schema::hasColumn('setting', 'communities_listing_cards_per_row')) {
            return 2;
        }
        $s = settings();
        $n = (int) ($s->communities_listing_cards_per_row ?? 2);

        return max(1, min(3, $n));
    } catch (\Throwable $e) {
        return 2;
    }
}

function communities_listing_grid_column_class(?int $cardsPerRow = null): string
{
    $cardsPerRow = $cardsPerRow ?? communities_listing_cards_per_row();
    if ($cardsPerRow === 1) {
        return 'col-12';
    }
    if ($cardsPerRow === 3) {
        return 'col-md-6 col-lg-4';
    }

    return 'col-md-6';
}

function communities_listing_description_char_limit(?int $cardsPerRow = null): int
{
    $cardsPerRow = $cardsPerRow ?? communities_listing_cards_per_row();
    $limits = [
        1 => 360,
        2 => 220,
        3 => 140,
    ];

    return $limits[$cardsPerRow] ?? 220;
}

function communities_listing_description_line_clamp(?int $cardsPerRow = null): int
{
    $cardsPerRow = $cardsPerRow ?? communities_listing_cards_per_row();
    $limits = [
        1 => 6,
        2 => 4,
        3 => 2,
    ];

    return $limits[$cardsPerRow] ?? 4;
}

/**
 * Max recommended communities shown (always fills complete grid rows).
 */
function communities_listing_recommended_max_count(?int $cardsPerRow = null): int
{
    $cardsPerRow = $cardsPerRow ?? communities_listing_cards_per_row();

    return match ($cardsPerRow) {
        1 => 4,
        2 => 4,
        3 => 6,
        default => 4,
    };
}

function communities_listing_recommended_fetch_limit(): int
{
    return max(communities_listing_recommended_max_count(), 9);
}

/**
 * Drop trailing items so the grid never has empty cells on the last row.
 *
 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Enumerable|array  $items
 * @return \Illuminate\Support\Collection
 */
function communities_listing_trim_to_full_rows($items, ?int $cardsPerRow = null)
{
    $items = collect($items);
    $cols = $cardsPerRow ?? communities_listing_cards_per_row();

    if ($items->isEmpty() || $cols === 1) {
        return $items->values();
    }

    $count = $items->count();
    $remainder = $count % $cols;
    if ($remainder !== 0) {
        $items = $items->take($count - $remainder);
    }

    return $items->values();
}

/**
 * Cap and trim recommended communities to full grid rows for the active layout.
 *
 * @param  \Illuminate\Support\Collection|\Illuminate\Support\Enumerable|array  $items
 * @return \Illuminate\Support\Collection
 */
function communities_listing_prepare_recommended($items)
{
    $items = collect($items)->take(communities_listing_recommended_max_count());

    return communities_listing_trim_to_full_rows($items);
}

function communities_listing_grid_wrapper_class(?int $cardsPerRow = null): string
{
    $cardsPerRow = $cardsPerRow ?? communities_listing_cards_per_row();

    return 'communities-card-grid communities-card-grid--per-row-' . $cardsPerRow;
}

function community_africa_cdc_staff_name(): string
{
    return 'Africa CDC Staff';
}

/**
 * Whether the community is the internal Africa CDC Staff community (by name).
 */
function community_is_africa_cdc_staff_restricted(\App\Models\CommunityOfPractice $community): bool
{
    return strcasecmp(trim((string) ($community->community_name ?? '')), community_africa_cdc_staff_name()) === 0;
}

/**
 * Logged-in user may see / join the Africa CDC Staff community (email domain @africacdc.org).
 */
function user_email_allows_africa_cdc_staff_community(?\Illuminate\Contracts\Auth\Authenticatable $user): bool
{
    if ($user === null || empty($user->email)) {
        return false;
    }

    return (bool) preg_match('/@africacdc\.org$/i', trim((string) $user->email));
}

/**
 * Relative path on the public disk for a stored user photo (uploads/users/...).
 */
function community_user_local_photo_storage_path(string $raw): ?string
{
    $raw = trim(str_replace('\\', '/', $raw));
    if ($raw === '') {
        return null;
    }
    // Full URL to this app’s /storage/...
    if (preg_match('#^https?://#i', $raw)) {
        $path = parse_url($raw, PHP_URL_PATH) ?? '';
        if ($path !== '' && preg_match('#/storage/(.+)$#i', $path, $m)) {
            return ltrim($m[1], '/');
        }
    }
    if (preg_match('#/(?:storage/)?uploads/users/(.+)$#i', $raw, $m)) {
        return 'uploads/users/'.$m[1];
    }
    if (str_starts_with(strtolower($raw), 'uploads/users/')) {
        return $raw;
    }

    return 'uploads/users/'.basename($raw);
}

/**
 * Job title / role line for community participant cards (User model uses job_title).
 */
function community_user_display_job_title(?\App\Models\User $user): string
{
    if ($user === null) {
        return '';
    }

    return trim((string) ($user->job_title ?? ''));
}

/**
 * System / seed placeholder filenames (not a real uploaded portrait). DB default is avatar.jpg.
 */
function community_user_profile_photo_is_system_placeholder(string $raw): bool
{
    $raw = trim(str_replace('\\', '/', $raw));
    if ($raw === '') {
        return true;
    }
    $path = $raw;
    if (preg_match('#^https?://#i', $raw)) {
        $path = (string) (parse_url($raw, PHP_URL_PATH) ?? '');
        if ($path === '' || $path === '/') {
            return false;
        }
    }
    $base = strtolower(basename($path));
    if ($base === '' || $base === '.' || $base === '..') {
        return false;
    }
    static $placeholders = [
        'avatar.jpg', 'avatar.jpeg', 'avatar.png',
        'user.jpg', 'user.png',
        'default.jpg', 'default.png',
        'placeholder.jpg', 'placeholder.png',
        'no-photo.png', 'nophoto.png',
    ];

    return in_array($base, $placeholders, true);
}

/**
 * True if the user has a profile image that is usable: external URL, or local file that exists on disk.
 */
function community_user_has_profile_image(\App\Models\User $user): bool
{
    $attrs = $user->getAttributes();
    $raw = $attrs['photo'] ?? null;
    if ($raw === null || trim((string) $raw) === '') {
        return false;
    }
    $raw = trim((string) $raw);

    if (community_user_profile_photo_is_system_placeholder($raw)) {
        return false;
    }

    if (! empty($attrs['is_photo_external']) && (int) $attrs['is_photo_external'] === 1) {
        return true;
    }

    // Same-site URL: verify file exists under public storage (avoids broken /storage/ links)
    if (preg_match('#^https?://#i', $raw)) {
        $path = parse_url($raw, PHP_URL_PATH) ?? '';
        if ($path !== '' && preg_match('#/storage/(.+)$#i', $path, $m)) {
            $rel = ltrim($m[1], '/');

            return community_user_public_storage_file_usable($rel);
        }

        return true;
    }

    $rel = community_user_local_photo_storage_path($raw);
    if ($rel === null) {
        return false;
    }

    return community_user_public_storage_file_usable($rel);
}

/**
 * True if the file exists on the public disk and is a non-empty readable file.
 */
function community_user_public_storage_file_usable(string $relativePath): bool
{
    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
    if ($relativePath === '') {
        return false;
    }

    try {
        if (Storage::disk('public')->exists($relativePath)) {
            return (int) Storage::disk('public')->size($relativePath) > 0;
        }
    } catch (\Throwable $e) {
        // fall through to public_path check
    }

    $full = public_path('storage/'.$relativePath);

    return is_file($full) && is_readable($full) && filesize($full) > 0;
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
if (!function_exists('trim_rich_text_input')) {
    /**
     * Trim rich text input before sanitization/rendering.
     */
    function trim_rich_text_input($html)
    {
        if ($html === null) {
            return '';
        }

        return trim((string) $html);
    }
}

if (! function_exists('plain_text_excerpt_from_html')) {
    /**
     * Decode entities, strip markup, and return a short plain-text excerpt.
     */
    function plain_text_excerpt_from_html($html, int $limit = 200): string
    {
        $text = trim((string) $html);
        if ($text === '') {
            return '';
        }

        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $text) {
                break;
            }
            $text = $decoded;
        }

        $text = preg_replace('/<(br\s*\/?>|\/(p|div|h[1-6]|li|tr|td|th|blockquote))>/i', ' ', $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        if (function_exists('clean_unicode')) {
            $text = clean_unicode($text);
        }

        return \Illuminate\Support\Str::limit($text, $limit);
    }
}

if (!function_exists('sanitize_rich_text_for_display')) {
    /**
     * Sanitize Summernote/rich HTML for safe on-site rendering.
     * Keeps intended rich text but removes risky/unstable styling and classes.
     */
    function sanitize_rich_text_for_display($html)
    {
        $html = trim_rich_text_input($html);
        if ($html === '') {
            return '';
        }

        $decoded = $html;
        for ($i = 0; $i < 3; $i++) {
            $prev = $decoded;
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $prev) {
                break;
            }
        }

    libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $wrapper = '<div id="rich-root">' . $decoded . '</div>';
        $loaded = @$dom->loadHTML('<?xml encoding="UTF-8">' . $wrapper);
        libxml_clear_errors();

        if (! $loaded) {
            return sanitize_rich_text_for_display_fallback($decoded);
        }

        $root = $dom->getElementById('rich-root');
        if (! $root) {
            return sanitize_rich_text_for_display_fallback($decoded);
        }

        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query('//comment()') as $comment) {
            if ($comment->parentNode) {
                $comment->parentNode->removeChild($comment);
            }
        }

        foreach ($xpath->query('//*[@class]') as $el) {
            /** @var \DOMElement $el */
            $class = $el->getAttribute('class');
            if ($class === '') {
                continue;
            }
            $parts = preg_split('/\s+/', trim($class), -1, PREG_SPLIT_NO_EMPTY);
            if (! $parts) {
                $el->removeAttribute('class');
                continue;
            }

            $filtered = array_values(array_filter($parts, function ($c) {
                return stripos($c, 'note-float') === false
                    && stripos($c, 'note-image') === false;
            }));

            if (count($filtered) === 0) {
                $el->removeAttribute('class');
            } else {
                $el->setAttribute('class', implode(' ', $filtered));
            }
        }

        foreach ($xpath->query('//*[@style]') as $el) {
            /** @var \DOMElement $el */
            $style = $el->getAttribute('style');
            $style = preg_replace('/\b(float|clear|text-wrap-mode)\s*:\s*[^;]+;?/i', '', $style);
            $style = preg_replace('/\bposition\s*:\s*(absolute|fixed|sticky)\s*;?/i', '', $style);
            $style = preg_replace('/\b(z-index|transform)\s*:\s*[^;]+;?/i', '', $style);
            $style = preg_replace('/\bbackground(?:-color)?\s*:\s*rgba\([^)]+\)\s*;?/i', '', $style);
            $style = trim(preg_replace('/\s*;\s*/', ';', $style), " ;\t\n\r\0\x0B");

            if ($style === '') {
                $el->removeAttribute('style');
            } else {
                $el->setAttribute('style', $style);
            }
        }

        foreach ($xpath->query('//img') as $img) {
            /** @var \DOMElement $img */
            $img->removeAttribute('class');
            $style = $img->getAttribute('style');
            $style = preg_replace('/\b(float|clear|vertical-align)\s*:\s*[^;]+;?/i', '', $style);
            $style = preg_replace('/\bwidth\s*:\s*[^;]+;?/i', '', $style);
            $style = preg_replace('/\bheight\s*:\s*[^;]+;?/i', '', $style);
            $style = trim($style . ' max-width:100%; height:auto; display:block; margin:8px 0;');
            $style = preg_replace('/\s*;\s*/', '; ', trim($style));
            $img->setAttribute('style', $style);

            $src = $img->getAttribute('src');
            if ($src !== '' && !preg_match('#^https?://#i', $src)) {
                $base = rtrim((string) (config('app.url') ?: ''), '/');
                if ($base !== '') {
                    if (strpos($src, '//') === 0) {
                        $img->setAttribute('src', (strpos($base, 'https') === 0 ? 'https:' : 'http:') . $src);
                    } elseif (strpos($src, '/') === 0) {
                        $img->setAttribute('src', $base . $src);
                    } else {
                        $img->setAttribute('src', $base . '/' . ltrim($src, '/'));
                    }
                }
            }
        }

        $inner = '';
        foreach ($root->childNodes as $child) {
            $inner .= $dom->saveHTML($child);
        }

        $inner = trim($inner);
        if ($inner === '') {
            return '';
        }

        return '<div class="rich-text-content html-content" style="margin:8px 0;text-align:left;overflow:visible;">' . $inner . '</div>';
    }
}

if (!function_exists('sanitize_rich_text_for_display_fallback')) {
    /**
     * Regex fallback for malformed HTML fragments.
     */
    function sanitize_rich_text_for_display_fallback($html)
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $out = preg_replace('/<!--.*?-->/s', '', $html);
        $out = preg_replace('/\bclass\s*=\s*"(?:[^"]*\bnote-float[^"]*)"/i', '', $out);
        $out = preg_replace('/\bclass\s*=\s*\'(?:[^\']*\bnote-float[^\']*)\'/i', '', $out);
        $out = preg_replace_callback(
            '/<img\b[^>]*(?:\/)?>/i',
            function ($m) {
                $tag = $m[0];
                $tag = preg_replace('/\sstyle\s*=\s*"[^"]*"/i', '', $tag);
                $tag = preg_replace("/\sstyle\s*=\s*'[^']*'/i", '', $tag);
                $tag = preg_replace('/\sclass\s*=\s*"[^"]*"/i', '', $tag);
                if (preg_match('/\/\s*>$/', $tag)) {
                    return preg_replace('/\/\s*>$/', ' style="max-width:100%;height:auto;display:block;margin:8px 0;" />', $tag);
                }
                return preg_replace('/>$/', ' style="max-width:100%;height:auto;display:block;margin:8px 0;">', $tag);
            },
            $out
        );

        $out = trim($out);
        if ($out === '') {
            return '';
        }

        return '<div class="rich-text-content html-content" style="margin:8px 0;text-align:left;">' . $out . '</div>';
    }
}

if (!function_exists('sanitize_rich_text_for_storage')) {
    /**
     * Sanitize rich text before saving to DB (API/web inputs).
     * Keeps meaningful HTML while removing unstable Summernote artifacts.
     */
    function sanitize_rich_text_for_storage($html)
    {
        $html = trim_rich_text_input($html);
        if ($html === '') {
            return '';
        }

        $renderSafe = sanitize_rich_text_for_display($html);
        if ($renderSafe === '') {
            return '';
        }

        // Remove the outer wrapper injected by sanitize_rich_text_for_display().
        $inner = preg_replace('/^<div\b[^>]*>/', '', $renderSafe);
        $inner = preg_replace('/<\/div>\s*$/', '', (string) $inner);

        return trim((string) $inner);
    }
}

if (! function_exists('forum_body_for_wysiwyg_editor')) {
    /**
     * Prepare stored forum HTML for a WYSIWYG (Summernote, etc.).
     * Decodes HTML entities so real markup is edited visually (handles double-encoded paste).
     */
    function forum_body_for_wysiwyg_editor(?string $html): string
    {
        $html = (string) $html;
        if ($html === '') {
            return '';
        }

        $decoded = $html;
        for ($i = 0; $i < 3; $i++) {
            $prev = $decoded;
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $prev) {
                break;
            }
        }

        return $decoded;
    }
}

function cleanHtmlContent($htmlContent)
{
    // Backward-compatible alias used widely in views
    return sanitize_rich_text_for_display($htmlContent);
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
    if ($value === null) {
        return '';
    }

    $value = mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8');
    // Remove only problematic control chars; preserve multilingual letters.
    $value = preg_replace('/[\x{0000}-\x{0008}\x{000B}\x{000C}\x{000E}-\x{001F}\x{007F}]/u', '', $value);

    return trim($value);
}


 function sendPushNotification($title, $message, $fcmTokens, $isTopic = false)
{
    try {
        if (empty($fcmTokens) || empty($message)) {
            \Log::warning('Push notification skipped: Missing tokens or message.');
            return false; // Indicate failure or no action taken
        }

        // Dispatch the job
        PushNotificationJob::dispatch($title, $message, $fcmTokens, $isTopic)->onQueue('default');

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
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" 
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
                               controls playsinline webkit-playsinline
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

if (! function_exists('hub_storage')) {
    function hub_storage(): \App\Services\HubStorageService
    {
        return app(\App\Services\HubStorageService::class);
    }
}

if (! function_exists('hub_storage_path')) {
    function hub_storage_path(string $relative = ''): string
    {
        return hub_storage()->absolutePath($relative);
    }
}

if (! function_exists('active_ui_locale')) {
    function active_ui_locale(): string
    {
        return \App\Support\LocaleDirection::activeLocale();
    }
}

if (! function_exists('locale_is_rtl')) {
    function locale_is_rtl(?string $locale = null): bool
    {
        return \App\Support\LocaleDirection::isRtl($locale ?? active_ui_locale());
    }
}

if (! function_exists('locale_direction')) {
    function locale_direction(?string $locale = null): string
    {
        return \App\Support\LocaleDirection::direction($locale ?? active_ui_locale());
    }
}

?>
