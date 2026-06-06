<?php

namespace App\Http\Controllers\Admin;

use App\Services\ChatGPTService;
use App\Services\HealthTopicSourceFetcher;
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

        $data['all_tags'] = $this->tagsRepo->get($request,false);
        $data['allTagsForMapping'] = $this->tagsRepo->allTagsForMapping();
        $data['search']    = (Object) $request->all();
        $data['healthTopicSources'] = HealthTopicSourceCatalog::sources();
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
}
