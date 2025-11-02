<?php
namespace App\Repositories;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        
        $authors = Author::with(['user.badges.badgeType', 'user.communities', 'publications']);

        if($request->term) {
            $searchTerm = '%'.$request->term.'%';
            $authorIds = Author::leftJoin('users', 'author.id', '=', 'users.author_id')
                ->where(function($query) use ($searchTerm) {
                    $query->where('author.name', 'like', $searchTerm)
                          ->orWhere('users.name', 'like', $searchTerm)
                          ->orWhere('users.job_title', 'like', $searchTerm)
                          ->orWhere('users.organization_name', 'like', $searchTerm);
                })
                ->distinct()
                ->pluck('author.id');
            
            $authors->whereIn('id', $authorIds);
        }

        //Access levels effect to query results
        $this->access_filter($authors);

        $result = $authors->orderBy('id','desc')->paginate($rows_count);

        return  $result;
    }
    
    public function save($name){
        
        $author = new Author();
        $author->name = $name;
        $author->save();

        return $author;
    }

    public function find($id){

        return Author::find($id);
    }

    public function delete($id){

        return Author::find($id)->delete();
    }

    public function count(){
        return count(Author::all());
    }


}