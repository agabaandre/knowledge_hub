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
use Illuminate\Support\Facades\Schema;

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

        if ($request->ajax() && $request->boolean('datatable')) {
            return response()->json($this->publicationsRepo->adminDashboardRecentDatatable($request));
        }

        $data['search'] = (Object) $request->all();

        $data['publications_count'] = Publication::count();
        $data['authors_count'] = Author::count();
        $data['experts_count'] = Expert::count();
        $data['forums_count'] = Forum::count();

        $data['states_count'] = Country::count();
        // $data['visits_count'] = AccessLog::whereDate('created_at', now())->count();

        $total_visits = Schema::hasTable('access_logs') ? (int) DB::table('access_logs')->count() : 0;
        $earliest_date = $total_visits > 0 ? DB::table('access_logs')->min('created_at') : null;
        $data['visits_count'] = self::averageDailyVisits($total_visits, $earliest_date);

        try {
            $data['admin_units_count'] = Schema::hasTable((new AdministrativeUnit)->getTable())
                ? AdministrativeUnit::count()
                : 0;
        } catch (\Throwable $e) {
            $data['admin_units_count'] = 0;
        }
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

        return view('admin.dashboard.list', ['adminOnlyDashboards' => $dashboards]);
    }

    public static function averageDailyVisits(int $totalVisits, $earliestDate): int
    {
        if ($totalVisits < 1 || $earliestDate === null || $earliestDate === '') {
            return 0;
        }

        try {
            $days = Carbon::parse($earliestDate)->diffInDays(Carbon::now()) + 1;
        } catch (\Throwable $e) {
            return 0;
        }

        return (int) round($totalVisits / max(1, $days));
    }

}
