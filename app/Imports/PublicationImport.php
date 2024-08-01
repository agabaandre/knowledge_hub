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
    // public function model(array $row)
    // {
    //     $row = array_values($row);

    //     $sub_theme_id = SubThemeticArea::where('description',trim($row[8]))->first();
    //     //dd($sub_theme_id);
    //     $file_type    = get_file_type(null,$row[10]);

    //     // Define how to create a model from the Excel row data
    //     return new Publication([
    //         'title' => $row[0],
    //         'publication' => $row[1],
    //         'cover' => $row[5],
    //         'description' => $row[4] || ' ',
    //         'citation_link' => $row[10],
    //         'associated_authors' => $row[11],
    //         'sub_thematic_area_id'=>$sub_theme_id->id ?? null,
    //         'is_active'=>'InActive',
    //         'publication_catgory_id'=>7,
    //         'author_id'=>current_user()->author_id,
    //         'geographical_coverage_id'=>current_user()->country_id,
    //         'file_type_id'=>$file_type->id,
    //         'cover_is_exteranl'=>1
    //     ]);
    // }
    public function model(array $row)
    {
        $row = array_values($row);

        // Trim the sub-thematic description
        $subt = trim($row[8]);

        // Build the query to find the sub-thematic area ID
        $query = SubThemeticArea::where('description', 'LIKE', $subt);

        // Print out the query for debugging purposes
        //dd($query->toSql());

        // Execute the query
        $sub_theme = $query->first();

        // Retrieve the sub-thematic area ID
        $sub_theme_id = $sub_theme ? $sub_theme->id : null;

        // Get the file type using a helper function (ensure get_file_type is defined elsewhere)
        $file_type = get_file_type(null, $row[10]);
        $file_type_id = $file_type ? $file_type->id : null;

        // Ensure description has a default value if empty
        $description = !empty($row[4]) ? $row[4] : ' ';

        // Define how to create a model from the Excel row data
        return new Publication([
            'title' => $row[0],
            'publication' => $row[1],
            'cover' => $row[5],
            'description' => $description,
            'citation_link' => $row[10],
            'associated_authors' => $row[11],
            'sub_thematic_area_id' => 4,
            'is_active' => 'InActive',
            'publication_catgory_id' => 9,
            'author_id' => current_user()->author_id,
            'geographical_coverage_id' => current_user()->country_id,
            'file_type_id' => $file_type_id,
            'cover_is_exteranl' => 1,
            'is_approved'=>1
        ]);
    }

}