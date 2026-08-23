<?php

namespace Tests\Unit;

use App\Models\DataCategory;
use App\Repositories\DataRecordsRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DataRecordsRepositoryCategoryTest extends TestCase
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
        ]);

        Schema::dropIfExists('data_category_publication_category');
        Schema::dropIfExists('publication_categories');
        Schema::dropIfExists('data_categories');

        Schema::create('data_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('url_path')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('show_on_menu')->default(false);
            $table->string('required_permission')->nullable();
            $table->boolean('is_special')->default(false);
            $table->boolean('is_dashboard')->default(false);
            $table->boolean('is_restricted')->default(false);
            $table->timestamps();
        });

        Schema::create('publication_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::create('data_category_publication_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('data_category_id');
            $table->unsignedBigInteger('publication_category_id');
            $table->timestamps();
        });
    }

    public function test_update_persists_name_and_access_flags(): void
    {
        $category = new DataCategory();
        $category->forceFill([
            'category_name' => 'Old name',
            'url_path' => '/old',
            'slug' => 'old-name',
            'show_on_menu' => false,
            'is_restricted' => false,
        ])->save();

        $repo = new DataRecordsRepository();
        $request = Request::create('/admin/datarecords/categories/update', 'POST', [
            'id' => $category->id,
            'name' => 'Workforce',
            'url' => '/workforce',
            'show_menu' => '1',
            'is_restricted' => '1',
            'required_permission' => 'view_workforce',
        ]);

        $this->assertTrue($repo->update_category($request));

        $fresh = $category->fresh();
        $this->assertSame('Workforce', $fresh->category_name);
        $this->assertSame('/workforce', $fresh->url_path);
        $this->assertTrue((bool) $fresh->show_on_menu);
        $this->assertTrue((bool) $fresh->is_restricted);
        $this->assertSame('view_workforce', $fresh->required_permission);
    }

    public function test_restricted_without_permission_defaults_to_view_workforce(): void
    {
        $category = new DataCategory();
        $category->forceFill([
            'category_name' => 'HR',
            'slug' => 'hr',
        ])->save();

        $repo = new DataRecordsRepository();
        $request = Request::create('/admin/datarecords/categories/update', 'POST', [
            'id' => $category->id,
            'name' => 'HR',
            'is_restricted' => '1',
        ]);

        $this->assertTrue($repo->update_category($request));
        $this->assertSame('view_workforce', $category->fresh()->required_permission);
    }
}
