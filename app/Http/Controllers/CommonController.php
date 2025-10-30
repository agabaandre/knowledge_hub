<?php

namespace App\Http\Controllers;

use App\Repositories\FaqsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class CommonController extends Controller
{
    private $faqsRepo;

    public function __construct( FaqsRepository $faqsRepo)
    {
        $this->faqsRepo       = $faqsRepo;
    }

    public function privacy(Request $request){

        $data['policy'] = file_get_contents(storage_link('uploads/privacy/privacy_policy.md'));
        return view('privacy.index',$data);
    }

    
    public function endtour(Request $request){
        $action = $request->input('action', 'finished'); // 'finished', 'skipped', 'declined'
        $dontShowAgain = $request->input('dont_show_again', false);

        // Set cookie to prevent tour from showing again
        set_cookie('CDC_Tour_Finished',"YES09983kjfiejk");
        set_cookie('CDC_Tour_Action', $action);
        
        // If user doesn't want to see it again, set additional flag
        if ($dontShowAgain || $action === 'declined') {
            set_cookie('CDC_Tour_Declined', "true");
        }

        // Support both GET (backward compatibility) and POST requests
        if ($request->isMethod('GET')) {
            return 'Finished';
        }

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'message' => $action === 'skipped' || $action === 'declined' 
                ? 'Tour preference saved. You can restart it anytime from your account settings.' 
                : 'Tour completed!'
        ]);
    }

    public function imageUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $file = $request->file('file');
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/editor/' . $filename;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $url = asset('storage/' . $path);

        return response()->json(['url' => $url]);
    }

}
