<?php

namespace Tests\Unit;

use App\Models\AdministrativeUnit;
use App\Repositories\AdminUnitsRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminUnitsRepositorySaveTest extends TestCase
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

        Schema::dropIfExists('administrative_units');
        Schema::create('administrative_units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300);
            $table->string('description', 300)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('code', 50)->nullable();
            $table->string('alternate_code', 50)->nullable();
            $table->string('logo', 50)->nullable();
            $table->string('icon', 50)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('iso_code', 2)->nullable();
            $table->string('iso3_code', 3)->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_an_administrative_unit(): void
    {
        $repo = new AdminUnitsRepository();
        $request = Request::create('/admin/adminunits/save', 'POST', [
            'unit_name' => 'Ashanti Region',
            'description' => 'Test unit',
            'code' => 'ASH',
            'alt_code' => 'AS',
            'icon' => 'fa-building',
            'parent_id' => null,
        ]);

        $saved = $repo->save($request);

        $this->assertNotFalse($saved);
        $this->assertDatabaseHas('administrative_units', [
            'name' => 'Ashanti Region',
            'description' => 'Test unit',
            'code' => 'ASH',
            'alternate_code' => 'AS',
            'icon' => 'fa-building',
        ]);
    }

    public function test_it_updates_an_administrative_unit(): void
    {
        $unit = new AdministrativeUnit();
        $unit->forceFill([
            'name' => 'Old Name',
            'description' => 'Old',
            'icon' => 'fa-building',
            'parent_id' => null,
        ])->save();

        $repo = new AdminUnitsRepository();
        $request = Request::create('/admin/adminunits/save', 'POST', [
            'id' => $unit->id,
            'unit_name' => 'Updated Name',
            'description' => 'Updated desc',
            'code' => 'UPD',
            'icon' => 'fa-map',
        ]);

        $saved = $repo->save($request);

        $this->assertNotFalse($saved);
        $this->assertDatabaseHas('administrative_units', [
            'id' => $unit->id,
            'name' => 'Updated Name',
            'description' => 'Updated desc',
            'code' => 'UPD',
            'icon' => 'fa-map',
        ]);
        $this->assertSame(1, AdministrativeUnit::count());
    }
}
