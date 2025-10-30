<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use App\Models\AdministrativeUnit;
use App\Models\Author;
use App\Models\Country;
use App\Models\Expert;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\User;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ExpertsRepository;
use App\Repositories\ForumsRepository;

class AdminController extends Controller
{
    private $publicationsRepo,$authorsRepo,$expertsRepo,$forumsRepo;

    public function __construct(PublicationsRepository $publicationsRepo,
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo,
    ExpertsRepository $expertsRepo,ForumsRepository $forumsRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->expertsRepo      = $expertsRepo;
        $this->forumsRepo       = $forumsRepo;
    }

    public function index(Request $request){

        $request['rows']      = 10;
        $request['order_by_visits'] = true;
        $request['is_admin']  = 1;

        $data['search'] = (Object) $request->all();

		$data['publications'] = $this->publicationsRepo->get($request);
        $data['authors'] = $this->authorsRepo->get($request);
        $data['experts'] = $this->expertsRepo->get($request);
        $data['forums']  = $this->forumsRepo->get($request);

        $data['publications_count'] = Publication::count();
        $data['authors_count'] = Author::count();
        $data['experts_count'] = Expert::count();
        $data['forums_count'] = Forum::count();

        $data['states_count'] = Country::count();
        // $data['visits_count'] = AccessLog::whereDate('created_at', now())->count();

        $total_visits = DB::table('access_logs')->count();
        $earliest_date = DB::table('access_logs')->min('created_at');
        $days_diff = Carbon::parse($earliest_date)->diffInDays(Carbon::now()) + 1;
        $data['visits_count'] = round($total_visits / $days_diff);

        $data['admin_units_count'] = AdministrativeUnit::count();
        // Show total registered users for clarity on the dashboard
        $data['users_count'] = User::count();

    
        // Admin-only dashboards list: publications whose data category is marked as dashboard
        try{
            $data['dashboards'] = \App\Models\Publication::where(function($q){
                $q->whereHas('data_category', function($dq){
                    $dq->where('is_dashboard', 1);
                })
                ->orWhere('is_admin_only_access', 1);
            })
            ->orderBy('id','desc')
            ->get();
        }catch(\Throwable $e){ $data['dashboards'] = collect(); }

        return view('admin.dashboard.index',$data);
    }

    public function rccdashboards(Request $request){
        return view('admin.dashboard.rcc');
    }

    public function dashboards(Request $request){
        // List admin-only content (all categories), with search and pagination
        try{
            $query = \App\Models\Publication::with(['author','data_category'])
                ->where('is_admin_only_access', 1);
            if ($term = trim((string) $request->input('term', ''))) {
                $query->where(function($q) use ($term){
                    $q->where('title', 'like', '%'.$term.'%')
                      ->orWhere('description', 'like', '%'.$term.'%');
                });
            }
            $dashboards = $query->orderBy('id','desc')
                ->paginate($request->input('rows', 20));
        }catch(\Throwable $e){ $dashboards = collect(); }

        return view('admin.dashboard.list', ['dashboards' => $dashboards]);
    }

}
