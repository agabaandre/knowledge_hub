<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\AccessLog;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class LogsRepository{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        $areas      = AccessLog::with('user')->orderBy('id','desc');

        if($request->term){
            $t = trim($request->term);
            $areas->where(function($q) use ($t){
                $q->where('country','like','%'.$t.'%')
                  ->orWhere('city','like','%'.$t.'%')
                  ->orWhere('ip_address','like','%'.$t.'%');
            });
        }
        if($request->filled('user_id')){
            $areas->where('user_id', $request->user_id);
        }

        $result = $areas->paginate($rows_count);
        return $result;
    }
    
    public function count(){
        return count(AccessLog::all());
    }

    public function audit_trail($request){

        $userId = $request->user_id;
        $start  = $request->start;
        $end    = $request->end;
        $action = $request->action;

        
        return AuditTrail::when($userId, 
        function ($query, $userId) {
            return $query->where('user_id', $userId);
        })
        ->when($start, 
        function ($query, $start) {
            $start = date('Y-m-d',(strtotime('+0 day',strtotime($start))));
            return $query->where('created_at','>=', $start);
        })
        ->when($end, 
        function ($query, $end) {
            $end = date('Y-m-d',(strtotime('+1 day',strtotime($end))));
            return $query->where('created_at','<=', $end);
        })
        ->when($action, 
        function ($query, $action) {
            return $query->where('action','like', "%$action%");
        })
        ->orderBy('id','desc')->paginate(25);
    }

}