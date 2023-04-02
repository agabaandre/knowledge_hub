<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use App\Repositories\PublicationsRepository;
use Illuminate\Http\Request;
class AccountController extends Controller
{
    private $publicationsRepo;

    public function __construct( PublicationsRepository $publicationsRepo)
    {
        $this->publicationsRepo       = $publicationsRepo;
    }


    public function profile(Request $request){

        $data['profile'] = current_user();
        
        return view('account.profile',$data);
    }


    public function favourites(Request $request){

        $data['favourites'] = $this->publicationsRepo->favourites($request);
        return view('account.favourites',$data);
    }


    public function publications(Request $request){

        $data['publications'] = $this->publicationsRepo->my_publications($request);
        return view('account.mypublications',$data);
    }


    public function publish(Request $request){

        return view('account.create');
    }

    public function create_version(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        
        return view('account.create_version',$data);
    }


    public function submit_publication(Request $request){

        $val_rules =[
            'cover'=>'required',
            'file_type'=>'required',
            'sub_theme'=>'required',
            'description'=>'required'
        ];

        if($request->original_id):
            unset($val_rules['sub_theme']);
            unset($val_rules['title']);
        endif;

        $request->validate($val_rules);

        $saved = $this->publicationsRepo->save($request);

        $message = ($saved)?'Resource submitted successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        if($request->ajax())
           return response($data);

        return redirect()->route('account.publications')->with($data);

    }

}
