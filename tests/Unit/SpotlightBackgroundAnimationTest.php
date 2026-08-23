<?php

namespace Tests\Unit;

use App\Support\SpotlightBackground;
use Tests\TestCase;

class SpotlightBackgroundAnimationTest extends TestCase
{
    public function test_animation_options_include_modern_motion_types(): void
    {
        $values = array_column(SpotlightBackground::animationOptions(), 'value');

        $this->assertContains('none', $values);
        $this->assertContains('kenburns', $values);
        $this->assertContains('aurora', $values);
        $this->assertContains('shimmer', $values);
    }

    public function test_unknown_animation_falls_back_to_none(): void
    {
        $settings = (object) [
            'spotlight_animation' => 'explode',
            'spotlight_animation_speed' => 'ludicrous',
            'gradient_start_color' => '#119A48',
            'gradient_end_color' => '#16c653',
            'spotlight_banner' => null,
        ];

        $resolved = SpotlightBackground::resolve($settings);

        $this->assertSame('none', $resolved['animation']);
        $this->assertSame('medium', $resolved['animation_speed']);
        $this->assertSame('', $resolved['animation_class']);
    }

    public function test_resolve_adds_animation_classes(): void
    {
        $settings = (object) [
            'spotlight_animation' => 'aurora',
            'spotlight_animation_speed' => 'fast',
            'gradient_start_color' => '#119A48',
            'gradient_end_color' => '#16c653',
            'spotlight_banner' => null,
        ];

        $resolved = SpotlightBackground::resolve($settings);

        $this->assertSame('aurora', $resolved['animation']);
        $this->assertStringContainsString('home-spotlight--anim-aurora', $resolved['animation_class']);
        $this->assertStringContainsString('home-spotlight--speed-fast', $resolved['animation_class']);
    }
}
