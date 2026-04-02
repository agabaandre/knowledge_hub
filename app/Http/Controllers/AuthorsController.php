<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;

class AuthorsController extends Controller
{
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
        $data['authors'] = $this->authorsRepo->get($request);

        $siteName = settings()->site_name ?? 'Africa CDC Knowledge Hub';
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');

        $authors = $data['authors'];
        $total = method_exists($authors, 'total') ? (int) $authors->total() : $authors->count();
        $currentPage = method_exists($authors, 'currentPage') ? max(1, (int) $authors->currentPage()) : 1;
        $perPage = method_exists($authors, 'perPage') ? (int) $authors->perPage() : 24;

        $data['pageTitle'] = $currentPage > 1
            ? 'Contributors — Page '.$currentPage.' — '.$siteName
            : 'Contributors & Authors — '.$siteName;

        $data['pageDescription'] = Str::limit(
            'Browse '.$total.' knowledge contributors—researchers, clinicians, and institutions sharing public health resources and publications on '.$siteName.'.',
            160
        );

        $data['pageKeywords'] = Str::limit(trim(
            'authors, contributors, researchers, publications, public health, Africa CDC, '.
            (settings()->seo_keywords ?? 'knowledge hub, Africa')
        ), 300);

        $canonicalQuery = [];
        if ($currentPage > 1) {
            $canonicalQuery['page'] = $currentPage;
        }
        $data['canonicalUrl'] = route('browse.authors', $canonicalQuery);

        $logoRaw = settings()->logo ?? '';
        $data['pageImage'] = $logoRaw && filter_var($logoRaw, FILTER_VALIDATE_URL)
            ? $logoRaw
            : ($logoRaw ? asset(ltrim($logoRaw, '/')) : asset('assets/images/logo.png'));
        $data['ogType'] = 'website';

        $jsonLdFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

        $offset = ($currentPage - 1) * $perPage;
        $itemList = [];
        $pos = $offset + 1;
        foreach ($authors as $author) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'item' => [
                    '@type' => 'Person',
                    'name' => $author->name,
                    'url' => url('authors/publications?author='.$author->id),
                ],
            ];
        }

        $data['authorsIndexJsonLd'] = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $currentPage > 1 ? 'Contributors — Page '.$currentPage : 'Contributors',
            'description' => $data['pageDescription'],
            'url' => $data['canonicalUrl'],
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $siteUrl,
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $total,
                'itemListElement' => $itemList,
            ],
        ];

        $breadcrumbItems = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Contributors', 'item' => route('browse.authors')],
        ];
        if ($currentPage > 1) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => 'Page '.$currentPage,
                'item' => $data['canonicalUrl'],
            ];
        }

        $data['authorsBreadcrumbJsonLd'] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];
        $data['jsonLdFlags'] = $jsonLdFlags;

        return view('authors.index', $data);
    }
}
