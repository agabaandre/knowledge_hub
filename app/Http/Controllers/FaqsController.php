<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use App\Repositories\FaqsRepository;
use Illuminate\Http\Request;
class FaqsController extends Controller
{
    public const FAQS_INFINITE_ROWS = 6;

    private $faqsRepo;

    public function __construct(FaqsRepository $faqsRepo)
    {
        $this->faqsRepo = $faqsRepo;
    }

    public function index(Request $request)
    {
        $this->prepareFaqsListingRequest($request);
        $data['faqs'] = $this->faqsRepo->get($request);
        $data['faqsInfiniteScroll'] = $this->faqsInfiniteScrollEnabled();

        return view('faqs.index', $data);
    }

    public function faqsPage(Request $request)
    {
        if (! $this->faqsInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $this->prepareFaqsListingRequest($request);
        $request->merge(['rows' => self::FAQS_INFINITE_ROWS]);
        $faqs = $this->faqsRepo->get($request);

        $page = (int) $faqs->currentPage();
        $perPage = (int) $faqs->perPage();
        $listOffset = max(0, ($page - 1) * $perPage);
        $loadedCount = min($faqs->total(), $listOffset + $faqs->count());

        return response()->json([
            'ok' => true,
            'html' => view('faqs.partials.faq_list_items', [
                'faqs' => $faqs,
                'listOffset' => $listOffset,
            ])->render(),
            'current_page' => $page,
            'last_page' => (int) $faqs->lastPage(),
            'has_more' => $faqs->hasMorePages(),
            'total' => (int) $faqs->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function prepareFaqsListingRequest(Request $request): void
    {
        if ($this->faqsInfiniteScrollEnabled()) {
            $request->merge([
                'page' => max(1, (int) $request->input('page', 1)),
                'rows' => self::FAQS_INFINITE_ROWS,
            ]);
        }
    }

    protected function faqsInfiniteScrollEnabled(): bool
    {
        return (settings()->faqs_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
    }
}
