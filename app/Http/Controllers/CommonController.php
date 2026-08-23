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

    public function favicon()
    {
        return redirect()->away(site_favicon_url(), 301);
    }

    public function userManual()
    {
        $hide_search = true;
        $bodyHtml = $this->renderMarkdownGuide(base_path('docs/user-guide.md'));

        return view('user_manual.index', compact('hide_search', 'bodyHtml'));
    }

    public function administratorGuide()
    {
        $hide_search = true;
        $bodyHtml = $this->renderMarkdownGuide(base_path('docs/administrator-guide.md'));

        return view('user_manual.administrator', compact('hide_search', 'bodyHtml'));
    }

    private function renderMarkdownGuide(string $path): string
    {
        if (! is_file($path)) {
            return '<p>This guide is not available on the server.</p>';
        }

        $markdown = (string) file_get_contents($path);
        $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $this->rewriteGuideLinkUrls(
            $this->rewriteGuideImageUrls($converter->convert($markdown)->getContent())
        );
    }

    /**
     * Point manual screenshots at the public /manual files under the app URL.
     */
    private function rewriteGuideImageUrls(string $html): string
    {
        return (string) preg_replace_callback(
            '#src="(?:\.\./public)?/manual/([^"]+)"#',
            function (array $matches): string {
                return 'src="'.e(asset('manual/'.$matches[1])).'"';
            },
            $html
        );
    }

    /**
     * Root-relative markdown links such as /administrator-guide must include the
     * subdirectory app URL (e.g. /knowledge_hub/administrator-guide).
     */
    private function rewriteGuideLinkUrls(string $html): string
    {
        return (string) preg_replace_callback(
            '#href="(/[^"]*)"#',
            function (array $matches): string {
                $path = $matches[1];
                if (str_starts_with($path, '//')) {
                    return $matches[0];
                }

                $parts = parse_url($path) ?: [];
                $pathOnly = ltrim((string) ($parts['path'] ?? ''), '/');
                $href = url($pathOnly);
                if (! empty($parts['query'])) {
                    $href .= '?'.$parts['query'];
                }
                if (! empty($parts['fragment'])) {
                    $href .= '#'.$parts['fragment'];
                }

                return 'href="'.e($href).'"';
            },
            $html
        );
    }

}
