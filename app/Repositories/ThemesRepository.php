<?php
namespace App\Repositories;

use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThemesRepository
{
    private const FA_VERSION = '5.3.1';

    private function forgetThematicAreaViewCaches(): void
    {
        Cache::forget('themes');
        Cache::forget('themes_form_alphabetical');
        Cache::forget('subthemes');
    }

    public function get(Request $request)
    {

        $rows_count = ($request->rows) ? $request->rows : 24;

        // Homepage + public browse: follow admin "Display order", then name.
        $themes = ThemeticArea::query()
            ->orderBy('display_order', 'asc')
            ->orderBy('description', 'asc')
            ->orderBy('id', 'asc');

        if ($request->term)
            $themes->where('description', 'like', '%' . $request->term . '%');
        $result = $themes->paginate($rows_count);

        return $result;
    }

    public function get_subthemes(Request $request,$return_array=false)
    {

        $rows_count = ($request->rows) ? $request->rows : 24;
        $qry = SubThemeticArea::query()->orderBy('description', 'asc')->orderBy('id', 'asc');

      if($request->theme_id)
       $qry= $qry->where('thematic_area_id', $request->theme_id);
        
       
       if($return_array)
        return $qry->get();

       $themes = $qry->paginate($rows_count);
        return $themes;
    }

    public function get_all_subthemes(Request $request)
    {

        $rows_count = ($request->rows) ? $request->rows : 24;
        $themes = SubThemeticArea::with('theme')->orderBy('description', 'asc')->orderBy('id', 'asc');
        if ($request->filled('theme_id')) {
            $themes->where('thematic_area_id', (int) $request->theme_id);
        } elseif ($request->filled('thematic_area_id')) {
            $themes->where('thematic_area_id', (int) $request->thematic_area_id);
        }
        if ($request->term)
            $themes->where('description', 'like', '%' . $request->term . '%');
        $result = $themes->paginate($rows_count);
        $result->appends($request->only(['term', 'theme_id', 'thematic_area_id']));

        return $result;
    }

    public function save(Request $request)
    {
        // Find the record by 'id' if present, otherwise instantiate a new one
        $theme = ThemeticArea::findOrNew($request->id);

        // Update fields
        $theme->description = $request->description;
        $theme->detailed_description = $request->filled('detailed_description')
            ? (string) $request->detailed_description
            : null;
        $theme->icon = $request->icon;
        $theme->display_order = (int) ($request->display_order ?? 0);

        // Save the record
        $theme->save();
        $this->forgetThematicAreaViewCaches();

        return $theme;
    }

    public function find($id)
    {

        return ThemeticArea::find($id);
    }

    public function delete($id)
    {
        $row = ThemeticArea::find($id);
        if (! $row) {
            return false;
        }
        $ok = $row->delete();
        if ($ok) {
            $this->forgetThematicAreaViewCaches();
        }

        return $ok;
    }

    public function allForMapping()
    {
        return ThemeticArea::query()
            ->orderBy('description', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'description', 'display_order']);
    }

    public function deleteWithMapping(int $id, int $replacementThemeId): array
    {
        if ($id === $replacementThemeId) {
            return ['status' => 'failure', 'message' => 'Please select a different theme to map data to.'];
        }

        $oldTheme = ThemeticArea::find($id);
        $newTheme = ThemeticArea::find($replacementThemeId);
        if (! $oldTheme || ! $newTheme) {
            return ['status' => 'failure', 'message' => 'Selected theme was not found.'];
        }

        $result = DB::transaction(function () use ($oldTheme, $newTheme) {
            $subThemeIds = SubThemeticArea::where('thematic_area_id', $oldTheme->id)->pluck('id');
            $mappedSubThemes = 0;
            $mappedPublications = 0;

            if ($subThemeIds->isNotEmpty()) {
                $mappedSubThemes = SubThemeticArea::whereIn('id', $subThemeIds)->update([
                    'thematic_area_id' => $newTheme->id,
                ]);

                $mappedPublications = Publication::whereIn('sub_thematic_area_id', $subThemeIds)->count();

                // Keep legacy denormalized column aligned if it exists in this deployment.
                if (Schema::hasColumn('publication', 'thematic_area_id')) {
                    Publication::whereIn('sub_thematic_area_id', $subThemeIds)->update([
                        'thematic_area_id' => $newTheme->id,
                    ]);
                }
            }

            $oldTheme->delete();

            return [
                'status' => 'success',
                'message' => 'Theme deleted and data mapped successfully.',
                'data' => [
                    'mapped_subthemes' => $mappedSubThemes,
                    'mapped_publications' => $mappedPublications,
                    'deleted_theme_id' => $oldTheme->id,
                    'replacement_theme_id' => $newTheme->id,
                ],
            ];
        });

        if (($result['status'] ?? '') === 'success') {
            $this->forgetThematicAreaViewCaches();
        }

        return $result;
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
        $theme->description = $request->description;
        $theme->detailed_description = $request->filled('detailed_description')
            ? (string) $request->detailed_description
            : null;
        $theme->icon = $request->icon;
        $theme->thematic_area_id = $request->thematic_area_id;

        // Save changes
        $theme->save();
        $this->forgetThematicAreaViewCaches();

        return $theme;
    }

    public function delete_subtheme($id)
    {
        $row = SubThemeticArea::find($id);
        if (! $row) {
            return false;
        }
        $ok = $row->delete();
        if ($ok) {
            $this->forgetThematicAreaViewCaches();
        }

        return $ok;
    }

    public function allSubthemesForMapping()
    {
        return SubThemeticArea::with('theme')
            ->orderBy('description', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'description', 'thematic_area_id']);
    }

    public function deleteSubthemeWithMapping(int $id, int $replacementSubthemeId): array
    {
        if ($id === $replacementSubthemeId) {
            return ['status' => 'failure', 'message' => 'Please select a different subtheme to map data to.'];
        }

        $oldSubtheme = SubThemeticArea::find($id);
        $newSubtheme = SubThemeticArea::find($replacementSubthemeId);
        if (! $oldSubtheme || ! $newSubtheme) {
            return ['status' => 'failure', 'message' => 'Selected subtheme was not found.'];
        }

        $result = DB::transaction(function () use ($oldSubtheme, $newSubtheme) {
            $mappedPublications = Publication::where('sub_thematic_area_id', $oldSubtheme->id)->count();
            if ($mappedPublications > 0) {
                $updates = ['sub_thematic_area_id' => $newSubtheme->id];
                if (Schema::hasColumn('publication', 'thematic_area_id')) {
                    $updates['thematic_area_id'] = $newSubtheme->thematic_area_id;
                }
                Publication::where('sub_thematic_area_id', $oldSubtheme->id)->update($updates);
            }

            $oldSubtheme->delete();

            return [
                'status' => 'success',
                'message' => 'Subtheme deleted and publications mapped successfully.',
                'data' => [
                    'mapped_publications' => $mappedPublications,
                    'deleted_subtheme_id' => $oldSubtheme->id,
                    'replacement_subtheme_id' => $newSubtheme->id,
                ],
            ];
        });

        if (($result['status'] ?? '') === 'success') {
            $this->forgetThematicAreaViewCaches();
        }

        return $result;
    }

    public function count()
    {
        return count(ThemeticArea::all());
    }

    public function fontAwesomeVersion(): string
    {
        return self::FA_VERSION;
    }

    public function fontAwesomeCheatsheetUrl(): string
    {
        return 'https://fontawesome.com/v'.self::FA_VERSION.'/icons?d=gallery&m=free';
    }

    /**
     * Returns icon values in the stored format used by this project (e.g. "fa-eye").
     *
     * @return array<int, string>
     */
    public function fontAwesomeIconOptions(): array
    {
        $base = public_path('assets/plugins/fontawesome-free/svgs');
        $paths = [
            $base.'/solid/*.svg',
            $base.'/regular/*.svg',
            $base.'/brands/*.svg',
        ];
        $icons = [];
        foreach ($paths as $pattern) {
            foreach (glob($pattern) ?: [] as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($name === '') {
                    continue;
                }
                $icons['fa-'.$name] = true;
            }
        }
        if ($icons === []) {
            return ['fa-eye', 'fa-book', 'fa-folder', 'fa-file', 'fa-heart'];
        }
        $list = array_keys($icons);
        sort($list);

        return $list;
    }


}
