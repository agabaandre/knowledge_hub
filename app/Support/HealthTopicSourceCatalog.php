<?php

namespace App\Support;

class HealthTopicSourceCatalog
{
    public const SOURCE_WHO = 'who';

    public const SOURCE_MEDLINEPLUS = 'medlineplus';

    public const SOURCE_BERKELEY = 'berkeley';

    public const SOURCE_DARTMOUTH = 'dartmouth';

    /**
     * @return array<string, array{label: string, url: string}>
     */
    public static function sources(): array
    {
        return [
            self::SOURCE_WHO => [
                'label' => 'WHO Health Topics',
                'url' => 'https://www.who.int/health-topics',
            ],
            self::SOURCE_MEDLINEPLUS => [
                'label' => 'MedlinePlus Health Topics',
                'url' => 'https://medlineplus.gov/healthtopics.html',
            ],
            self::SOURCE_BERKELEY => [
                'label' => 'UC Berkeley UHS Health Topics',
                'url' => 'https://uhs.berkeley.edu/health-topics',
            ],
            self::SOURCE_DARTMOUTH => [
                'label' => 'Dartmouth General Health Topics',
                'url' => 'https://students.dartmouth.edu/health-service/primary-care/health-wellness/health-information/general-health-topics',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function defaultSourceKeys(): array
    {
        return array_keys(self::sources());
    }

    /**
     * @return list<string>
     */
    public static function fallbackTopics(string $sourceKey): array
    {
        $lists = [
            self::SOURCE_WHO => [
                'Antimicrobial Resistance', 'Cholera', 'COVID-19', 'Dengue', 'Diabetes',
                'Ebola Virus Disease', 'HIV/AIDS', 'Malaria', 'Marburg Virus Disease', 'Measles',
                'Mental Health', 'Mpox', 'Neglected Tropical Diseases', 'Polio', 'Rift Valley Fever',
                'Tuberculosis', 'Yellow Fever', 'Zika Virus', 'Cervical Cancer', 'Hypertension',
            ],
            self::SOURCE_MEDLINEPLUS => [
                'Allergies', 'Arthritis', 'Asthma', 'Cancer', 'Cholesterol',
                'Chronic Kidney Disease', 'Depression', 'Diabetes', 'Heart Disease', 'Hepatitis',
                'High Blood Pressure', 'Infectious Diseases', 'Lupus', 'Obesity', 'Pneumonia',
                'Pregnancy', 'Stroke', 'Substance Use Disorders', 'Vaccines', 'Women\'s Health',
            ],
            self::SOURCE_BERKELEY => [
                'Anxiety', 'Biofeedback Therapy', 'Colds and Respiratory Infections', 'Eating Disorders',
                'Exercise', 'Human Papillomavirus (HPV)', 'Mental Health', 'Monkeypox (Mpox)',
                'Mumps', 'Nutrition', 'Skin Cancer Prevention', 'Sleeping Soundly', 'Smoking Cessation',
                'Stress Management', 'Trauma', 'Wildfire Smoke and Air Quality', 'Ebola', 'Leptospirosis',
            ],
            self::SOURCE_DARTMOUTH => [
                'Tobacco Cessation', 'Alcohol and Other Drugs', 'Nutrition and Healthy Eating',
                'Eating Concerns', 'Depression and Anxiety', 'Hand Foot and Mouth Disease',
                'Mindfulness', 'Reproductive and Sexual Health', 'Skin Cancer Prevention',
                'Sleep', 'Stress Management', 'Suicide Prevention', 'Concussion', 'Influenza',
                'Meningitis B', 'Vaping and E-cigarettes',
            ],
        ];

        return $lists[$sourceKey] ?? [];
    }

    public static function normalizeTopicName(string $name): string
    {
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $name = preg_replace('/\s+/u', ' ', trim($name)) ?? trim($name);

        return $name;
    }

    public static function normalizeTagKey(string $name): string
    {
        return mb_strtolower(self::normalizeTopicName($name));
    }
}
