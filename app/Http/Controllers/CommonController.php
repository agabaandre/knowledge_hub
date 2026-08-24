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

    /**
     * Render a markdown file from docs/ in the same in-app guide chrome.
     * Related-documentation links in the administrator guide point here.
     */
    public function documentation(string $path)
    {
        $relative = $this->safeDocsRelativePath($path);
        if ($relative === null) {
            abort(404);
        }

        if ($relative === 'user-guide.md') {
            return redirect()->to(url('user_manual'));
        }
        if ($relative === 'administrator-guide.md') {
            return redirect()->to(url('administrator-guide'));
        }

        $absolute = base_path('docs/'.$relative);
        $hide_search = true;
        $bodyHtml = $this->renderMarkdownGuide($absolute);
        $title = $this->guideTitleFromFile($absolute);

        return view('user_manual.document', compact('hide_search', 'bodyHtml', 'title'));
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

        return $this->rewriteGuideDisplayedPaths(
            $this->rewriteGuideLinkUrls(
                $this->rewriteGuideImageUrls($converter->convert($markdown)->getContent())
            )
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
     * Turn guide hrefs into in-app URLs: root paths use APP_URL, and relative
     * .md files (which 404 on /administrator-guide) map to portal pages.
     */
    private function rewriteGuideLinkUrls(string $html): string
    {
        return (string) preg_replace_callback(
            '#href="([^"]*)"#',
            function (array $matches): string {
                $href = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $rewritten = $this->rewriteGuideHref($href);
                if ($rewritten === null) {
                    return $matches[0];
                }

                return 'href="'.e($rewritten).'"';
            },
            $html
        );
    }

    private function rewriteGuideHref(string $href): ?string
    {
        $href = trim($href);
        if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:')) {
            return null;
        }
        if (str_starts_with($href, '//') || preg_match('#^[a-z][a-z0-9+.-]*:#i', $href)) {
            return null;
        }
        if (str_starts_with($href, '/')) {
            return $this->guideAbsoluteUrl($href);
        }
        if (preg_match('/\.md(?:[?#].*)?$/i', $href)) {
            return $this->guideMarkdownUrl($href);
        }

        return null;
    }

    private function guideMarkdownUrl(string $href): string
    {
        $parts = parse_url($href) ?: [];
        $path = ltrim(str_replace('\\', '/', (string) ($parts['path'] ?? '')), './');
        $path = (string) preg_replace('#/+#', '/', $path);
        $file = strtolower(basename($path));

        $portal = [
            'user-guide.md' => 'user_manual',
            'administrator-guide.md' => 'administrator-guide',
        ];
        $base = $portal[$file] ?? 'docs/'.$path;
        $url = url($base);
        if (! empty($parts['query'])) {
            $url .= '?'.$parts['query'];
        }
        if (! empty($parts['fragment'])) {
            $url .= '#'.$parts['fragment'];
        }

        return $url;
    }

    private function safeDocsRelativePath(string $path): ?string
    {
        $path = rawurldecode(str_replace('\\', '/', $path));
        $path = ltrim($path, '/');
        if ($path === '' || str_contains($path, '..') || ! preg_match('/\.md$/i', $path)) {
            return null;
        }
        if (! preg_match('#^[A-Za-z0-9][A-Za-z0-9_./\-]*\.md$#', $path)) {
            return null;
        }

        $docsRoot = realpath(base_path('docs'));
        $absolute = realpath(base_path('docs/'.$path));
        if ($docsRoot === false || $absolute === false || ! str_starts_with($absolute, $docsRoot.DIRECTORY_SEPARATOR)) {
            return null;
        }
        if (! is_file($absolute)) {
            return null;
        }

        return $path;
    }

    private function guideTitleFromFile(string $absolute): string
    {
        $markdown = (string) file_get_contents($absolute);
        if (preg_match('/^#\s+(.+)$/m', $markdown, $matches)) {
            return trim($matches[1]);
        }

        return 'Documentation';
    }

    /**
     * Show APP_URL-based addresses in the rendered guide instead of `/countries`.
     */
    private function rewriteGuideDisplayedPaths(string $html): string
    {
        return (string) preg_replace_callback(
            '#<code>(/[^<]+)</code>#',
            function (array $matches): string {
                $path = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if (! preg_match('#^/[A-Za-z0-9]#', $path) || str_starts_with($path, '/manual/')) {
                    return $matches[0];
                }

                $absolute = $this->guideAbsoluteUrl($path);
                $encoded = e($absolute);
                if (preg_match('/\{|…|\.\.\./u', $path)) {
                    return '<code>'.$encoded.'</code>';
                }

                return '<a href="'.$encoded.'"><code>'.$encoded.'</code></a>';
            },
            $html
        );
    }

    private function guideAbsoluteUrl(string $path): string
    {
        $parts = parse_url($path) ?: [];
        $pathOnly = ltrim((string) ($parts['path'] ?? ''), '/');
        $href = url($pathOnly);
        if (! empty($parts['query'])) {
            $href .= '?'.$parts['query'];
        }
        if (! empty($parts['fragment'])) {
            $href .= '#'.$parts['fragment'];
        }

        return $href;
    }

}
