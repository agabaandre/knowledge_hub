<?php

namespace App\Http\Controllers\Admin;

use App\Services\ChatGPTService;
use App\Services\HealthTopicSourceFetcher;
use App\Services\WhoFactsheetFetcher;
use App\Support\HealthTopicSourceCatalog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TagsRepository;

class TagsController extends Controller
{
    private $tagsRepo;

    public function __construct(TagsRepository $tagsRepo)
    {
        $this->tagsRepo = $tagsRepo;
    }

    public function index(Request $request){

        $request->merge(['datatable' => true]);
        $data['all_tags'] = $this->tagsRepo->get($request, true);
        $data['allTagsForMapping'] = $this->tagsRepo->allTagsForMapping();
        $data['search']    = (Object) $request->all();
        $data['healthTopicSources'] = HealthTopicSourceCatalog::sources();
        $data['missingOverviewCount'] = $this->tagsRepo->countTagsNeedingOverview();
        return view('admin.tags.index',$data);
    }
    
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'overview' => 'nullable|string',
        ]);

        $saved = $this->tagsRepo->save($request);

        if($saved):
            $data = ['message'=>'File type saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'tag_text' => 'required|string|max:255',
            'tag_id' => 'required|integer|exists:tags,id',
            'overview' => 'nullable|string',
        ]);

        $updated = $this->tagsRepo->update($request, $request->input('tag_id'));

        $data = $updated
            ? ['message' => 'Tag saved successfully', 'status' => 'success', 'data' => $updated]
            : ['message' => 'Tag could not be saved', 'status' => 'failure', 'data' => null];

        if($request->ajax()){
            return response($data,200);
        }

        notify()->success('Laravel Notify is awesome!');

        return back()->with($data);
    }



    public function destroy(Request $request){
        $user = auth()->user();
        if (! $user || (! $user->can('delete_publication_metadata') && ! $user->can('delete_meta_data'))) {
            return response()->json(['status' => 'failure', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:tags,id',
            'replacement_tag_id' => 'required|integer|exists:tags,id|different:id',
        ]);

        $result = $this->tagsRepo->deleteTagWithMapping((int) $request->id, (int) $request->replacement_tag_id);
        $statusCode = ($result['status'] ?? 'failure') === 'success' ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    public function aiGenerate(Request $request, HealthTopicSourceFetcher $fetcher, ChatGPTService $chatGpt)
    {
        $request->validate([
            'count' => 'nullable|integer|min:5|max:40',
            'sources' => 'nullable|array',
            'sources.*' => 'string|in:'.implode(',', HealthTopicSourceCatalog::defaultSourceKeys()),
        ]);

        $count = (int) $request->input('count', 15);
        $sourceKeys = $request->input('sources', HealthTopicSourceCatalog::defaultSourceKeys());
        if (! is_array($sourceKeys) || $sourceKeys === []) {
            $sourceKeys = HealthTopicSourceCatalog::defaultSourceKeys();
        }

        $referenceBySource = $fetcher->collectReferenceTopics($sourceKeys);
        $existingKeys = $this->tagsRepo->existingTagKeyMap();
        $referenceTopics = $fetcher->collectUniqueReferenceTopics($sourceKeys, $existingKeys);

        $existingNames = $this->tagsRepo->allTagsForMapping()->pluck('tag_text')->all();
        $ai = $chatGpt->generateHealthTopics($existingNames, $referenceTopics, $count);
        if (! ($ai['ok'] ?? false)) {
            return response()->json([
                'status' => 'failure',
                'message' => $ai['error'] ?? 'AI generation failed.',
            ], 422);
        }

        $marked = $this->tagsRepo->markDuplicateTopics($ai['topics'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Generated '.count($marked).' topic(s). Review and import unique items.',
            'topics' => $marked,
            'reference_topic_count' => count($referenceTopics),
            'sources_used' => array_keys($referenceBySource),
        ]);
    }

    public function aiImport(Request $request)
    {
        $request->validate([
            'topics' => 'required|array|min:1',
            'topics.*.tag_text' => 'required|string|max:255',
            'topics.*.overview' => 'required|string',
            'is_health_topic' => 'nullable|boolean',
            'is_health_emergency' => 'nullable|boolean',
        ]);

        $isHealthTopic = $request->boolean('is_health_topic', true) ? 1 : 0;
        $isHealthEmergency = $request->boolean('is_health_emergency', false) ? 1 : 0;

        $payload = [];
        foreach ($request->input('topics', []) as $row) {
            $payload[] = [
                'tag_text' => (string) ($row['tag_text'] ?? ''),
                'overview' => (string) ($row['overview'] ?? ''),
                'is_health_topic' => $isHealthTopic,
                'is_health_emergency' => $isHealthEmergency,
            ];
        }

        $result = $this->tagsRepo->importHealthTopics($payload);

        return response()->json([
            'status' => 'success',
            'message' => "Imported {$result['imported']} tag(s). Skipped {$result['skipped_duplicates']} duplicate(s).",
            'data' => $result,
        ]);
    }

    public function deduplicate(Request $request)
    {
        $result = $this->tagsRepo->deduplicateTags();

        return response()->json($result, 200);
    }

    public function aiDescribe(Request $request, WhoFactsheetFetcher $whoFetcher, ChatGPTService $chatGpt)
    {
        $request->validate([
            'tag_id' => 'nullable|integer|exists:tags,id',
            'tag_ids' => 'nullable|array|max:10',
            'tag_ids.*' => 'integer|exists:tags,id',
            'only_missing' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        $tagIds = [];
        if ($request->filled('tag_id')) {
            $tagIds[] = (int) $request->input('tag_id');
        }
        if ($request->filled('tag_ids')) {
            foreach ($request->input('tag_ids', []) as $id) {
                $tagIds[] = (int) $id;
            }
        }

        $explicitSelection = $request->filled('tag_id') || $request->filled('tag_ids');
        $onlyMissing = $request->boolean('only_missing', ! $explicitSelection);
        $limit = (int) $request->input('limit', 5);

        if ($tagIds === []) {
            $tags = $this->tagsRepo->tagsNeedingOverview(120, $limit);
            $payload = $tags->map(fn ($tag) => [
                'tag_id' => (int) $tag->id,
                'tag_text' => (string) $tag->tag_text,
                'overview' => (string) ($tag->overview ?? ''),
            ])->all();
        } else {
            $payload = [];
            foreach (array_unique($tagIds) as $id) {
                $full = $this->tagsRepo->find((int) $id);
                if (! $full) {
                    continue;
                }
                if ($onlyMissing && ! $this->tagsRepo->tagNeedsOverview($full->overview ?? null)) {
                    continue;
                }
                $payload[] = [
                    'tag_id' => (int) $full->id,
                    'tag_text' => (string) $full->tag_text,
                    'overview' => (string) ($full->overview ?? ''),
                ];
            }
        }

        if ($payload === []) {
            return response()->json([
                'status' => 'failure',
                'message' => 'No tags need descriptions (or selected tags already have content).',
            ], 422);
        }

        $result = $chatGpt->generateHealthTopicOverviewsForTags($payload, $whoFetcher);
        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'status' => 'failure',
                'message' => $result['error'] ?? 'Description generation failed.',
                'items' => $result['items'] ?? [],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Generated '.count($result['items']).' description preview(s) from WHO factsheets.',
            'items' => $result['items'],
        ]);
    }

    public function aiApplyOverviews(Request $request)
    {
        $request->validate([
            'updates' => 'required|array|min:1',
            'updates.*.tag_id' => 'required|integer|exists:tags,id',
            'updates.*.overview' => 'required|string',
            'updates.*.force' => 'nullable|boolean',
            'only_if_longer' => 'nullable|boolean',
        ]);

        $updates = [];
        foreach ($request->input('updates', []) as $row) {
            $updates[] = [
                'tag_id' => (int) ($row['tag_id'] ?? 0),
                'overview' => (string) ($row['overview'] ?? ''),
                'force' => (bool) ($row['force'] ?? false),
            ];
        }

        $result = $this->tagsRepo->applyTagOverviews(
            $updates,
            $request->boolean('only_if_longer', true)
        );

        return response()->json([
            'status' => 'success',
            'message' => "Updated {$result['updated']} tag description(s). Skipped {$result['skipped']}.",
            'data' => $result,
        ]);
    }
}
