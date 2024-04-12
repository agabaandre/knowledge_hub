<?php
namespace App\Repositories;

use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use Illuminate\Http\Request;

class ThemesRepository
{

    public function get(Request $request)
    {

        $rows_count = ($request->rows) ? $request->rows : 24;

        $themes = ThemeticArea::orderBy('id', 'desc');

        if ($request->term)
            $themes->where('description', 'like', '%' . $request->term . '%');
        $result = $themes->paginate($rows_count);

        return $result;
    }

    public function get_subthemes(Request $request)
    {

        $rows_count = ($request->rows) ? $request->rows : 24;
        $themes = SubThemeticArea::where('thematic_area_id', $request->id)->paginate($rows_count);

        return $themes;
    }

    public function get_all_subthemes(Request $request)
    {

        $rows_count = ($request->rows) ? $request->rows : 24;
        $themes = SubThemeticArea::with('theme')->orderBy('id', 'desc');
        if ($request->term)
            $themes->where('description', 'like', '%' . $request->term . '%');
        $result = $themes->paginate($rows_count);

        return $result;
    }

    public function save(Request $request)
    {
        // Find the record by 'id' if present, otherwise instantiate a new one
        $theme = ThemeticArea::findOrNew($request->id);

        // Update fields
        $theme->description = $request->description;
        $theme->icon = $request->icon;

        // Save the record
        $theme->save();

        return $theme;
    }

    public function find($id)
    {

        return ThemeticArea::find($id);
    }

    public function delete($id)
    {
        return ThemeticArea::find($id)->delete();
    }

    public function save_subtheme(Request $request)
    {
        // Check if request has an 'id' parameter
        if ($request->has('id')) {
            // Update existing SubThemeticArea
            $theme = SubThemeticArea::find($request->id);
            if (!$theme) {
                // Handle the case where the SubThemeticArea doesn't exist
                return null;
            }
        } else {
            // Create a new SubThemeticArea
            $theme = new SubThemeticArea();
        }

        // Update fields with new values from request
        $theme->description = $request->name;
        $theme->icon = $request->icon;
        $theme->thematic_area_id = $request->thematic_area_id;

        // Save changes
        $theme->save();

        return $theme;
    }

    public function delete_subtheme($id)
    {
        return SubThemeticArea::find($id)->delete();
    }

    public function count()
    {
        return count(ThemeticArea::all());
    }


}
