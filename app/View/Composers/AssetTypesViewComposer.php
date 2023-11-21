<?php
namespace App\View\Composers;

use App\Models\AssetType;
use Illuminate\View\View;

class AssetTypesViewComposer{

    public function compose(View $view){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $asset_types = cache()->remember('asset_types',$minutes, function () {
            return  AssetType::all();
        });
    
        $view->with('asset_types',$asset_types);
    }

}

?>