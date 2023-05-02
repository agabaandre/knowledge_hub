<?php
namespace App\View\Composers;

use App\Models\AssetType;
use Illuminate\View\View;

class AssetTypesViewComposer{

    public function compose(View $view){

        $asset_types = AssetType::all();
        $view->with('asset_types',$asset_types);
    }

}

?>