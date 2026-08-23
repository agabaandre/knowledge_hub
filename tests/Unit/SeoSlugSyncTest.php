<?php

namespace Tests\Unit;

use App\Console\Commands\RegenerateSeoSlugsCommand;
use App\Models\Publication;
use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use App\Repositories\ThemesRepository;
use App\Support\SeoSlugSync;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SeoSlugSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'cache.default' => 'array',
            'scout.driver' => 'null',
        ]);

        Schema::dropIfExists('publication');
        Schema::dropIfExists('sub_thematic_area');
        Schema::dropIfExists('thematic_area');
        Schema::dropIfExists('country');

        Schema::create('thematic_area', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->text('detailed_description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('sub_thematic_area', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->text('detailed_description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('thematic_area_id')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('publication', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->integer('is_version')->default(0);
            $table->timestamps();
        });

        Schema::create('country', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
        });
    }

    public function test_apply_regenerates_theme_slug_when_name_changes(): void
    {
        $theme = new ThemeticArea();
        $theme->forceFill(['description' => 'Old Theme Name', 'slug' => 'old-theme-name'])->save();

        $theme->description = 'Updated Theme Name';
        $changed = SeoSlugSync::apply($theme, 'themes');

        $this->assertTrue($changed);
        $this->assertSame('updated-theme-name', $theme->slug);
    }

    public function test_apply_keeps_slug_when_name_is_unchanged(): void
    {
        $theme = new ThemeticArea();
        $theme->forceFill(['description' => 'Stable Theme', 'slug' => 'custom-kept-slug'])->save();
        $theme->icon = 'fa-star';

        $this->assertFalse(SeoSlugSync::apply($theme, 'themes'));
        $this->assertSame('custom-kept-slug', $theme->slug);
    }

    public function test_saving_a_theme_or_subtheme_regenerates_slug_on_rename(): void
    {
        $theme = new ThemeticArea();
        $theme->forceFill(['description' => 'Climate', 'slug' => 'climate', 'icon' => 'fa-sun'])->save();

        $repo = new ThemesRepository();
        $repo->save(Request::create('/admin/themes/save', 'POST', [
            'id' => $theme->id,
            'description' => 'Climate Change and Health',
            'icon' => 'fa-sun',
            'display_order' => 1,
        ]));

        $this->assertSame('climate-change-and-health', $theme->fresh()->slug);

        $sub = new SubThemeticArea();
        $sub->forceFill([
            'description' => 'Heat',
            'slug' => 'heat',
            'thematic_area_id' => $theme->id,
            'icon' => 'fa-fire',
        ])->save();

        $repo->save_subtheme(Request::create('/admin/subthemes/save', 'POST', [
            'id' => $sub->id,
            'description' => 'Extreme Heat',
            'thematic_area_id' => $theme->id,
            'icon' => 'fa-fire',
        ]));

        $this->assertSame('extreme-heat', $sub->fresh()->slug);
    }

    public function test_command_regenerates_publication_and_theme_slugs(): void
    {
        $theme = new ThemeticArea();
        $theme->forceFill(['description' => 'One Health', 'slug' => 'stale-theme'])->save();

        $sub = new SubThemeticArea();
        $sub->forceFill([
            'description' => 'Zoonoses',
            'slug' => 'stale-sub',
            'thematic_area_id' => $theme->id,
        ])->save();

        $pub = new Publication();
        $pub->forceFill(['title' => 'National Lab Strategy', 'slug' => 'stale-pub', 'is_version' => 0])->save();

        $code = Artisan::call('slugs:regenerate', ['type' => 'themes,subthemes,publications']);

        $this->assertSame(0, $code);
        $this->assertSame('one-health', $theme->fresh()->slug);
        $this->assertSame('zoonoses', $sub->fresh()->slug);
        $this->assertSame('national-lab-strategy', $pub->fresh()->slug);
        $this->assertTrue(class_exists(RegenerateSeoSlugsCommand::class));
    }

    public function test_publication_save_assigns_slug_from_updated_title(): void
    {
        $pub = new Publication();
        $pub->forceFill(['title' => 'Old Title', 'slug' => 'old-title', 'is_version' => 0])->save();
        $pub->title = 'New Publication Title';

        $this->assertTrue(SeoSlugSync::apply($pub, 'publications'));
        $this->assertSame('new-publication-title', $pub->slug);
    }

    public function test_regenerate_updates_country_slugs_without_timestamp_columns(): void
    {
        $country = new \App\Models\Country();
        $country->timestamps = false;
        $country->forceFill(['name' => 'Western Sahara', 'slug' => 'stale-country'])->save();

        $result = SeoSlugSync::regenerate('countries');

        $this->assertSame(1, $result['updated']);
        $this->assertSame('western-sahara', $country->fresh()->slug);
        $this->assertFalse(Schema::hasColumn('country', 'updated_at'));
    }
}
