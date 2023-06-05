
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdatesController extends Controller
{

    public function __construct()
    {
      
    }

    public function index(){
          
        $output['git pull] = shell_exec('sudo git pull -X theirs');
        $output['migration] = shell_exec('php artisan migrate');
         echo "<pre>$output</pre>";
       return view('admin.updates.index',$output);
    }
