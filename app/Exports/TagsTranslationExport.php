<?php

namespace App\Exports;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagsTranslationExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection(): Collection
    {
        return Tag::query()
            ->orderBy('tag_text')
            ->orderBy('id')
            ->get(['id', 'tag_text', 'overview', 'is_health_topic', 'is_health_emergency']);
    }

    public function headings(): array
    {
        return [
            'id',
            'tag_text',
            'tag_text_translated',
            'overview',
            'overview_translated',
            'is_health_topic',
            'is_health_emergency',
        ];
    }

    /**
     * @param  Tag  $tag
     */
    public function map($tag): array
    {
        $overview = normalize_multilingual_text(strip_tags((string) ($tag->overview ?? '')), false);

        return [
            (int) $tag->id,
            normalize_multilingual_text((string) ($tag->tag_text ?? '')),
            '', // translator fills
            $overview,
            '', // translator fills
            (int) ($tag->is_health_topic ?? 1),
            (int) ($tag->is_health_emergency ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
