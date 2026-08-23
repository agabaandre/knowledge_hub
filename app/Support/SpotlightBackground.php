<?php

namespace App\Support;

final class SpotlightBackground
{
    /**
     * @return array{
     *     has_banner: bool,
     *     banner_url: ?string,
     *     gradient_start: string,
     *     gradient_end: string,
     *     inline_style: string,
     *     animation: string,
     *     animation_speed: string,
     *     animation_class: string
     * }
     */
    public static function resolve(?object $settings = null): array
    {
        $settings = $settings ?? settings();
        $gradientStart = (string) ($settings->gradient_start_color ?? '#119A48');
        $gradientEnd = (string) ($settings->gradient_end_color ?? '#16c653');
        $bannerUrl = self::bannerUrl($settings);

        if ($bannerUrl !== null) {
            $overlayOpacity = self::overlayOpacity($settings);
            $startRgba = self::hexToRgba($gradientStart, $overlayOpacity);
            $endRgba = self::hexToRgba($gradientEnd, $overlayOpacity);
            $escapedUrl = self::escapeCssUrl($bannerUrl);
            $inlineStyle = sprintf(
                'background-color: transparent !important; background-image: linear-gradient(135deg, %s 0%%, %s 100%%), url(%s) !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important;',
                $startRgba,
                $endRgba,
                self::cssUrl($escapedUrl)
            );
        } else {
            $inlineStyle = sprintf(
                'background: linear-gradient(135deg, %s 0%%, %s 100%%) !important;',
                $gradientStart,
                $gradientEnd
            );
        }

        $animation = self::animationType($settings);
        $speed = self::animationSpeed($settings);

        return [
            'has_banner' => $bannerUrl !== null,
            'banner_url' => $bannerUrl,
            'gradient_start' => $gradientStart,
            'gradient_end' => $gradientEnd,
            'inline_style' => $inlineStyle,
            'animation' => $animation,
            'animation_speed' => $speed,
            'animation_class' => $animation === 'none'
                ? ''
                : 'home-spotlight--anim-'.$animation.' home-spotlight--speed-'.$speed,
        ];
    }

    /**
     * @return list<array{value: string, label: string, hint: string}>
     */
    public static function animationOptions(): array
    {
        return [
            ['value' => 'none', 'label' => 'None (static)', 'hint' => 'No motion. Best for accessibility and low-power devices.'],
            ['value' => 'kenburns', 'label' => 'Ken Burns zoom', 'hint' => 'Slow cinematic zoom and pan, like documentary stills.'],
            ['value' => 'pan', 'label' => 'Gentle pan', 'hint' => 'Background drifts slowly across the frame.'],
            ['value' => 'gradient', 'label' => 'Living gradient', 'hint' => 'Brand colors shift and flow — works with or without a photo.'],
            ['value' => 'aurora', 'label' => 'Aurora glow', 'hint' => 'Soft moving color blobs, common on modern SaaS heroes.'],
            ['value' => 'shimmer', 'label' => 'Light shimmer', 'hint' => 'A diagonal highlight sweeps across the banner.'],
            ['value' => 'pulse', 'label' => 'Breathing overlay', 'hint' => 'Overlay opacity gently pulses for a live feel.'],
        ];
    }

    public static function cssBackgroundRule(?object $settings = null): string
    {
        $resolved = self::resolve($settings);

        return $resolved['inline_style'];
    }

    private static function bannerUrl(?object $settings): ?string
    {
        if ($settings === null || empty($settings->spotlight_banner)) {
            return null;
        }

        $raw = trim((string) $settings->spotlight_banner);
        if ($raw === '') {
            return null;
        }

        if (function_exists('branding_image_url')) {
            $resolved = branding_image_url($raw);

            return $resolved !== '' ? $resolved : null;
        }

        if (str_starts_with($raw, 'http') || str_starts_with($raw, '//')) {
            return $raw;
        }

        return asset(ltrim($raw, '/'));
    }

    private static function overlayOpacity(?object $settings): float
    {
        $percent = (int) ($settings->spotlight_overlay_opacity ?? 35);

        return max(0, min(100, $percent)) / 100;
    }

    private static function hexToRgba(string $hex, float $alpha): string
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $rgb = sscanf($hex, '%02x%02x%02x');
        if (! is_array($rgb) || count($rgb) !== 3) {
            return 'rgba(17, 154, 72, '.round($alpha, 2).')';
        }

        return sprintf(
            'rgba(%d, %d, %d, %.2f)',
            (int) $rgb[0],
            (int) $rgb[1],
            (int) $rgb[2],
            $alpha
        );
    }

    private static function escapeCssUrl(string $url): string
    {
        // Single-quoted url() is used because inline styles sit inside HTML style="..." attributes.
        return str_replace(['\\', "'"], ['\\\\', "\\'"], $url);
    }

    private static function cssUrl(string $escapedUrl): string
    {
        return "'".$escapedUrl."'";
    }

    public static function animationType(?object $settings = null): string
    {
        $settings = $settings ?? settings();
        $value = strtolower(trim((string) ($settings->spotlight_animation ?? 'none')));
        $allowed = array_column(self::animationOptions(), 'value');

        return in_array($value, $allowed, true) ? $value : 'none';
    }

    public static function animationSpeed(?object $settings = null): string
    {
        $settings = $settings ?? settings();
        $value = strtolower(trim((string) ($settings->spotlight_animation_speed ?? 'medium')));

        return in_array($value, ['slow', 'medium', 'fast'], true) ? $value : 'medium';
    }
}
