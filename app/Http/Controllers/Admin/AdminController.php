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
        $data['users_count'] = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'ADMIN');
        })->count();

    


        return view('admin.dashboard.index',$data);
    }

    public function rccdashboards(Request $request){
        return view('admin.dashboard.rcc');
    }

}
