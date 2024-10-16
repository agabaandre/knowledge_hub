<?php
namespace App\Imports;

use App\Models\Country;
use App\Models\Publication;
use App\Models\PublicationCategory;
use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class PublicationImport implements ToModel,WithHeadingRow
{

    public function model(array $row)
{
    $row = array_values($row);

    // Trim the sub-thematic description from the Excel row
    $title = $row[0];
    $link = $row[1];
    $description = !empty($row[4]) ? $row[4] : ' ';
    $cover = $row[5];
    $cat = trim($row[7]);
    $subt = trim($row[8]);
    $country = trim($row[9]);
    $citation_link = $row[10];
    $associated_authors = $row[11];
    $sub_theme_id = $this->get_subtheme($subt);
    $pub_cat_id = $this->get_category($cat);
    $member_state_id = $this->get_memberstate($country);

    // Get the file type using a helper
    $file_type = get_file_type(null, $link);
    $file_type_id = $file_type ? $file_type->id : null;



    // Define how to create a model from the Excel row data
    // if cover is null implement the cover from the db
    return new Publication([
        'title' => $title,
        'publication' => $link,
        'cover' => $cover,
        'description' => $description,
        'citation_link' => $citation_link,
        'associated_authors' => $associated_authors,
        'sub_thematic_area_id' => $sub_theme_id,
        'is_active' => 'Active',
        'publication_catgory_id' => $pub_cat_id,
        'author_id' => current_user()->author_id,
        'geographical_coverage_id' => $member_state_id,
        'file_type_id' => $file_type_id,
        'cover_is_exteranl' => 1,
        'is_approved'=>1,
    ]);
}
public function get_subtheme($subt){
        $query = SubThemeticArea::where(DB::raw('TRIM(description)'), 'LIKE', "%$subt%");
        $sub_theme = $query->first();
       return  $sub_theme ? $sub_theme->id : null;
}
    public function get_category($cat)
    {
        $query = PublicationCategory::where(DB::raw('TRIM(category_name)'), 'LIKE', "%$cat%");
        $cat = $query->first();
        return  $cat ? $cat->id : null;
    }
    public function get_memberstate($country)
    {
        $query = Country::where(DB::raw('TRIM(name)'), 'LIKE', "$country");
        $country = $query->first();
        return $country ? $country->id : null;
    }
}