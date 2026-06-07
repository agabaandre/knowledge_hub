<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FederatedContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FederatedContentAdminController extends Controller
{
    public function pending(Request $request)
    {
        if (! Schema::hasTable('federated_content_items')) {
            return redirect()->route('admin.federation.index')
                ->with('alert-danger', 'Run database migrations to enable federated content review.');
        }

        $query = FederatedContentItem::query()
            ->with('hub.mappedCountry')
            ->pendingReview()
            ->orderByDesc('updated_at');

        if ($request->filled('hub')) {
            $query->where('federated_knowledge_hub_id', (int) $request->input('hub'));
        }

        if ($request->filled('type')) {
            $query->where('content_type', $request->input('type'));
        }

        $items = $query->paginate(25)->withQueryString();

        return view('admin.federation.pending_content', [
            'items' => $items,
            'hubs' => \App\Models\FederatedKnowledgeHub::orderBy('name')->get(),
        ]);
    }

    public function bulkReview(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:federated_content_items,id',
        ]);

        $approved = $data['action'] === 'approve';

        FederatedContentItem::query()
            ->whereIn('id', $data['item_ids'])
            ->pendingReview()
            ->update([
                'central_approved' => $approved,
                'central_rejected' => ! $approved,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

        $verb = $approved ? 'approved' : 'rejected';
        $count = count($data['item_ids']);

        return redirect()->route('admin.federation.pending-content')
            ->with('alert-success', "{$count} federated item(s) {$verb} for the central portal.");
    }
}
