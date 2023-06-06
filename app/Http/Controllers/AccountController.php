<?php

namespace App\Http\Controllers;

use App\Repositories\PublicationsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
class AccountController extends Controller
{
    private $publicationsRepo,$usersRepo;

    public function __construct( PublicationsRepository $publicationsRepo,UsersRepository $usersRepo)
    {
        $this->publicationsRepo       = $publicationsRepo;
        $this->usersRepo            = $usersRepo;
    }


    public function profile(Request $request){

        $data['profile'] = current_user();
        $data['preferences'] = [];

        foreach(current_user()->preferences as $pref){
            $data['preferences'][] = $pref->tag_id;
        }

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

    public function edit_publication(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->ref);
        return view('account.editpub',$data);
    }

    public function create_version(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        
        return view('account.create_version',$data);
    }


    public function submit_publication(Request $request){

        $val_rules = [
            'cover'=>'required',
            'file_type'=>'required',
            'sub_theme'=>'required',
            'description'=>'required'
        ];

        if($request->original_id):
            unset($val_rules['sub_theme']);
            unset($val_rules['title']);
        endif;

        if($request->id):
            unset($val_rules['cover']);
        endif;

        $request->validate($val_rules);

        $saved = $this->publicationsRepo->save($request);

        $message = ($saved)?'Publication saved successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        if($request->ajax())
           return response($data);

        return redirect()->route('account.publications')->with($data);

    }

    public function create_summary(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        
        return view('account.create_summary',$data);
    }


    public function submit_summary(Request $request){

        $val_rules = [
            'file_type'=>'required',
            'summary'=>'required',
            'title'=>'required'
        ];

        $request->validate($val_rules);

        $saved = $this->publicationsRepo->save_summary($request);

        $message = ($saved)?'Resource summary submitted successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        if($request->ajax())
           return response($data);

        return redirect()->url('records/show?id='.$request->original_id)->with($data);

    }

    public function delete_publication(Request $request)
    {
        $this->publicationsRepo->delete($request->id);
    }



}
