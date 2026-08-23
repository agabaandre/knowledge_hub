<?php

namespace Tests\Unit;

use App\Support\HealthEmergencyTags;
use Tests\TestCase;

class HealthEmergencyTagsTest extends TestCase
{
    public function test_only_tags_declared_as_emergencies_are_kept(): void
    {
        $tags = [
            (object) ['id' => 1, 'tag_text' => 'Cholera', 'is_health_emergency' => 1],
            (object) ['id' => 2, 'tag_text' => 'Asthma', 'is_health_emergency' => 0],
            (object) ['id' => 3, 'tag_text' => 'Mpox', 'is_health_emergency' => '1'],
            (object) ['id' => 4, 'tag_text' => 'Allergies', 'is_health_emergency' => '0'],
            (object) ['id' => 5, 'tag_text' => 'Unset'],
        ];

        $emergencies = HealthEmergencyTags::from($tags);

        $this->assertSame([1, 3], $emergencies->pluck('id')->all());
        $this->assertTrue(HealthEmergencyTags::from([])->isEmpty());
        $this->assertTrue(HealthEmergencyTags::from(null)->isEmpty());
    }
}
