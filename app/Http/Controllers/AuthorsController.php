<?php

namespace App\Http\Controllers;

use App\Support\ContributorsSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;

class AuthorsController extends Controller
{
    public const AUTHORS_INFINITE_ROWS = 12;

    private $publicationsRepo,$authorsRepo;

    public function __construct(PublicationsRepository $publicationsRepo,
    AuthorsRepository $authorsRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
    }

    public function index(Request $request)
    {
        $data['search'] = (object) $request->all();
        $this->prepareAuthorsListingRequest($request);
        $data['authors'] = $this->authorsRepo->get($request);
        $data['authorsInfiniteScroll'] = $this->authorsInfiniteScrollEnabled();

        $authors = $data['authors'];
        $total = method_exists($authors, 'total') ? (int) $authors->total() : $authors->count();
        $currentPage = method_exists($authors, 'currentPage') ? max(1, (int) $authors->currentPage()) : 1;
        $perPage = method_exists($authors, 'perPage') ? (int) $authors->perPage() : 24;

        $searchTerm = $request->filled('term') ? trim((string) $request->term) : null;

        $data['pageTitle'] = ContributorsSeo::authorsIndexTitle($total, $currentPage, $searchTerm);

        $data['pageDescription'] = ContributorsSeo::authorsIndexDescription($total, $currentPage, $searchTerm);

        $data['pageKeywords'] = Str::limit(trim(
            'contributors, authors, researchers, institutions, publications, public health experts, Africa CDC, '.
            (settings()->seo_keywords ?? 'knowledge hub, Africa')
        ), 300);

        $canonicalQuery = array_filter([
            'page' => $currentPage > 1 ? $currentPage : null,
            'term' => $searchTerm,
        ]);
        $data['canonicalUrl'] = route('browse.authors', $canonicalQuery);

        $logoRaw = settings()->logo ?? '';
        $data['pageImage'] = $logoRaw && filter_var($logoRaw, FILTER_VALIDATE_URL)
            ? $logoRaw
            : ($logoRaw ? asset(ltrim($logoRaw, '/')) : asset('assets/images/logo.png'));
        $data['ogType'] = 'website';

        $offset = ($currentPage - 1) * $perPage;
        $data['authorsIndexJsonLd'] = ContributorsSeo::authorsIndexGraph(
            $authors,
            $total,
            $data['canonicalUrl'],
            $offset,
            $currentPage
        );
        $data['jsonLdFlags'] = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
        $data['authorsTotal'] = $total;
        $data['authorsSearchTerm'] = $searchTerm;

        return view('authors.index', $data);
    }

    public function authorsPage(Request $request)
    {
        if (! $this->authorsInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $this->prepareAuthorsListingRequest($request);
        $request->merge(['rows' => self::AUTHORS_INFINITE_ROWS]);
        $authors = $this->authorsRepo->get($request);

        $page = (int) $authors->currentPage();
        $perPage = (int) $authors->perPage();
        $loadedCount = min($authors->total(), max(0, ($page - 1) * $perPage) + $authors->count());

        return response()->json([
            'ok' => true,
            'html' => view('authors.partials.author_list_items', ['authors' => $authors])->render(),
            'current_page' => $page,
            'last_page' => (int) $authors->lastPage(),
            'has_more' => $authors->hasMorePages(),
            'total' => (int) $authors->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function prepareAuthorsListingRequest(Request $request): void
    {
        if ($this->authorsInfiniteScrollEnabled()) {
            $request->merge([
                'page' => max(1, (int) $request->input('page', 1)),
                'rows' => self::AUTHORS_INFINITE_ROWS,
            ]);
        }
    }

    protected function authorsInfiniteScrollEnabled(): bool
    {
        return (settings()->authors_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
    }
}
