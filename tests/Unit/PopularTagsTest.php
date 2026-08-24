<?php

namespace Tests\Unit;

use App\Models\Publication;
use App\Models\Tag;
use App\View\Composers\TagsViewComposer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PopularTagsTest extends TestCase
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
                'foreign_key_constraints' => false,
            ],
            'cache.default' => 'array',
            'scout.driver' => 'null',
        ]);

        Schema::dropIfExists('publication_tags');
        Schema::dropIfExists('publication_views');
        Schema::dropIfExists('favourites');
        Schema::dropIfExists('publication');
        Schema::dropIfExists('tags');

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_text')->nullable();
            $table->string('slug')->nullable();
        });

        Schema::create('publication', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('is_active')->nullable();
            $table->integer('is_approved')->default(0);
            $table->integer('is_version')->default(0);
            $table->integer('is_admin_only_access')->default(0);
            $table->timestamps();
        });

        Schema::create('publication_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('publication_id');
        });

        Schema::create('publication_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publication_id');
            $table->unsignedInteger('views')->default(0);
        });

        Schema::create('favourites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publication_id');
        });

        Cache::flush();
    }

    public function test_empty_and_unpublished_tags_are_excluded_from_popular_list(): void
    {
        $empty = $this->makeTag('Allergies');
        $draftTag = $this->makeTag('Arthritis');
        $versionTag = $this->makeTag('Asthma');
        $liveTag = $this->makeTag('Malaria');

        $this->attachTag($draftTag, $this->makePublication(['is_approved' => 0]));
        $this->attachTag($versionTag, $this->makePublication(['is_version' => 1]));
        $this->attachTag($liveTag, $this->makePublication());

        $popular = Tag::popularByEngagement(10);

        $this->assertSame(['Malaria'], $popular->pluck('tag_text')->all());
        $this->assertNotContains($empty->id, $popular->pluck('id'));
    }

    public function test_admin_only_publications_do_not_make_a_tag_popular(): void
    {
        $publicTag = $this->makeTag('Mpox');
        $adminTag = $this->makeTag('Internal');

        $this->attachTag($publicTag, $this->makePublication());
        $this->attachTag($adminTag, $this->makePublication(['is_admin_only_access' => 1]));

        $popular = Tag::popularByEngagement(10);

        $this->assertSame(['Mpox'], $popular->pluck('tag_text')->all());
    }

    public function test_popular_tags_rank_by_engagement_then_publication_count(): void
    {
        $malaria = $this->makeTag('Malaria');
        $cholera = $this->makeTag('Cholera');
        $zika = $this->makeTag('Zika');

        $malariaPub = $this->makePublication();
        $this->attachTag($malaria, $malariaPub);
        $this->addViews($malariaPub, 50);

        $choleraPubA = $this->makePublication();
        $choleraPubB = $this->makePublication();
        $this->attachTag($cholera, $choleraPubA);
        $this->attachTag($cholera, $choleraPubB);
        $this->addViews($choleraPubA, 10);
        $this->addViews($choleraPubB, 10);

        $zikaPubA = $this->makePublication();
        $zikaPubB = $this->makePublication();
        $zikaPubC = $this->makePublication();
        $this->attachTag($zika, $zikaPubA);
        $this->attachTag($zika, $zikaPubB);
        $this->attachTag($zika, $zikaPubC);
        $this->addViews($zikaPubA, 20);
        $this->addViews($zikaPubB, 15);
        $this->addViews($zikaPubC, 15);

        $popular = Tag::popularByEngagement(10);

        $this->assertSame(['Zika', 'Malaria', 'Cholera'], $popular->pluck('tag_text')->all());
    }

    public function test_composer_keeps_alphabetical_tags_and_exposes_popular_tags(): void
    {
        $this->makeTag('Allergies');
        $malaria = $this->makeTag('Malaria');
        $this->attachTag($malaria, $this->makePublication());

        $view = app('view')->make('user_manual.partials.styles');
        (new TagsViewComposer())->compose($view);

        $this->assertSame(['Allergies', 'Malaria'], $view['tags']->pluck('tag_text')->all());
        $this->assertSame(['Malaria'], $view['popular_tags']->pluck('tag_text')->all());
    }

    public function test_forget_tag_list_cache_clears_popular_tags(): void
    {
        Cache::put(TagsViewComposer::CACHE_KEY_TAGS_ALPHABETICAL, collect(['stale-alpha']));
        Cache::put(TagsViewComposer::CACHE_KEY_POPULAR_TAGS, collect(['stale-popular']));

        TagsViewComposer::forgetTagListCache();

        $this->assertFalse(Cache::has(TagsViewComposer::CACHE_KEY_TAGS_ALPHABETICAL));
        $this->assertFalse(Cache::has(TagsViewComposer::CACHE_KEY_POPULAR_TAGS));
    }

    public function test_footers_render_popular_tags_instead_of_alphabetical_slice(): void
    {
        $i18n = file_get_contents(resource_path('views/layouts/partials/footer_i18n_row.blade.php'));
        $theme1 = file_get_contents(resource_path('views/layouts/theme1/partials/footer.blade.php'));

        $this->assertStringContainsString('$footerPopularTags = $popular_tags', $i18n);
        $this->assertStringContainsString('$footerPopularTags->take(5)', $i18n);
        $this->assertStringNotContainsString('$tags->take(5)', $i18n);

        $this->assertStringContainsString('$footerPopularTags = $popular_tags', $theme1);
        $this->assertStringContainsString('$footerPopularTags->take(8)', $theme1);
        $this->assertStringNotContainsString('$tags->take(8)', $theme1);

        $home = file_get_contents(resource_path('views/home/partials/tags.blade.php'));
        $this->assertStringContainsString('$homePopularTags = $popular_tags', $home);
        $this->assertStringNotContainsString('$tags->take(40)', $home);
    }

    private function makeTag(string $text): Tag
    {
        $tag = new Tag();
        $tag->forceFill(['tag_text' => $text])->save();

        return $tag->fresh();
    }

    private function makePublication(array $overrides = []): Publication
    {
        $publication = new Publication();
        $publication->forceFill(array_merge([
            'title' => 'Resource '.$this->nextTitleSuffix(),
            'is_active' => 'Active',
            'is_approved' => 1,
            'is_version' => 0,
            'is_admin_only_access' => 0,
        ], $overrides))->save();

        return $publication->fresh();
    }

    private function attachTag(Tag $tag, Publication $publication): void
    {
        DB::table('publication_tags')->insert([
            'tag_id' => $tag->id,
            'publication_id' => $publication->id,
        ]);
    }

    private function addViews(Publication $publication, int $views): void
    {
        DB::table('publication_views')->insert([
            'publication_id' => $publication->id,
            'views' => $views,
        ]);
    }

    private function nextTitleSuffix(): string
    {
        static $i = 0;

        return (string) (++$i);
    }
}
