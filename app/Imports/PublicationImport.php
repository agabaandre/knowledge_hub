<?php
namespace App\Imports;

use App\Models\Publication;
use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PublicationImport implements ToModel,WithHeadingRow
{
    // private $sub_theme_id;
    // public function __construct($subtheme)
    // {
    //     $this->sub_theme_id = $subtheme;

        
    // }
    public function model(array $row)
    {
        $row = array_values($row);

        $sub_theme_id = SubThemeticArea::where('description',trim($row[8]))->first();
        $file_type    = get_file_type(null,$row[10]);

        // Define how to create a model from the Excel row data
        return new Publication([
            'title' => $row[0],
            'publication' => $row[1],
            'cover' => $row[5],
            'description' => $row[4] || ' ',
            'citation_link' => $row[10],
            'associated_authors' => $row[11],
            'sub_thematic_area_id'=>$sub_theme_id->id ?? null,
            'is_active'=>'InActive',
            'publication_catgory_id'=>7,
            'author_id'=>current_user()->author_id,
            'geographical_coverage_id'=>current_user()->country_id,
            'file_type_id'=>$file_type->id,
            'cover_is_exteranl'=>1
        ]);
    }
}