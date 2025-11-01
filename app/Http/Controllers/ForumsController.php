<?php

namespace App\Http\Controllers;

use App\Repositories\ForumsRepository;
use Illuminate\Http\Request;
use Biscolab\ReCaptcha\Facades\ReCaptcha;

class ForumsController extends Controller
{
    private $forumsRepo;

    public function __construct(ForumsRepository $forumsRepo)
    {
        $this->forumsRepo = $forumsRepo;
    }

    public function index(Request $request)
    {

        $data['forums']    = $this->forumsRepo->get($request);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $data['search']    = (object) $request->all();

        return view('forums.index', $data);
    }

    public function myForums(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $data['forums'] = $this->forumsRepo->getByUser($userId, $request);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $data['search'] = (object) $request->all();

        return view('forums.index', $data);
    }

    public function thread(Request $request)
    {

        $data['forum']     = $this->forumsRepo->find($request->id);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $request['rows']   = 6;
        $data['search']    = (object) $request->all();
        $data['forums']    = $this->forumsRepo->get($request);

        return view('forums.show', $data);
    }

    public function join(Request $request)
    {
        if(!@current_user()->id)
         return redirect('login');
       
        $this->forumsRepo->join_forum($request);

       return redirect('forums/thread?id='.$request->id);
    }

    public function create(Request $request)
    {
        return view('forums.create');
    }

    
    public function publish(Request $request)
    {
       $saved = $this->forumsRepo->save($request);
   
        $message = ($saved)?'Forum submitted for approval':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert'] = $message;
        $data['status']      = 200;
        return back();
    }

    public function comment(Request $request)
    {
        // Validate reCAPTCHA if not on localhost
        $recaptchaSiteKey = config('recaptcha.api_site_key');
        $isLocalhost = in_array($request->getHost(), ['localhost', '127.0.0.1']) || 
                       app()->environment('local', 'testing');
        
        if ($recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost) {
            // Check if reCAPTCHA response is provided
            if (!$request->filled('g-recaptcha-response')) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'Please complete the CAPTCHA to proceed.',
                ])->withInput();
            }
            
            // Validate the reCAPTCHA response
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!ReCaptcha::validate($recaptchaResponse)) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'CAPTCHA verification failed. Please try again.',
                ])->withInput();
            }
        }

        $saved =$this->forumsRepo->save_comment($request);

        $message = ($saved)?'Comment saved successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert'] = $message;
        $data['status']      = 200;
        return back()->with($data);
    }


}
