<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\RozgarMela;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class RozgarMelaSeeder extends Seeder
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public const MELAS = [
        [
            'title' => 'Ahmedabad Mega Rozgar Mela 2026',
            'district' => 'Ahmedabad',
            'venue' => 'Gujarat University Convention Centre, Ahmedabad',
            'starts_at' => '2026-10-20 09:00:00',
            'ends_at' => '2026-10-20 17:00:00',
            'registration_url' => 'https://rojgar.gujarat.gov.in/mela/ahmedabad-2026',
        ],
        [
            'title' => 'Saurashtra Regional Rozgar Mela',
            'district' => 'Rajkot',
            'venue' => 'Race Course Ground, Rajkot',
            'starts_at' => '2026-10-26 09:30:00',
            'ends_at' => '2026-10-26 17:30:00',
            'registration_url' => 'https://rojgar.gujarat.gov.in/mela/rajkot-2026',
        ],
        [
            'title' => 'South Gujarat Job Fair',
            'district' => 'Surat',
            'venue' => 'Surat International Exhibition Centre',
            'starts_at' => '2026-11-03 09:00:00',
            'ends_at' => '2026-11-03 18:00:00',
            'registration_url' => 'https://rojgar.gujarat.gov.in/mela/surat-2026',
        ],
        [
            'title' => 'Kutch Renewable Energy Job Mela',
            'district' => 'Kutch',
            'venue' => 'Kutch District Employment Exchange, Bhuj',
            'starts_at' => '2026-11-12 10:00:00',
            'ends_at' => '2026-11-12 16:00:00',
            'registration_url' => 'https://rojgar.gujarat.gov.in/mela/kutch-2026',
        ],
    ];

    public function run(): void
    {
        $districts = District::query()->pluck('id', 'name');

        foreach (self::MELAS as $data) {
            RozgarMela::updateOrCreate(
                ['title' => $data['title']],
                [
                    'district_id' => $districts[$data['district']] ?? null,
                    'venue' => $data['venue'],
                    'starts_at' => CarbonImmutable::parse($data['starts_at']),
                    'ends_at' => CarbonImmutable::parse($data['ends_at']),
                    'registration_url' => $data['registration_url'],
                    'status' => RozgarMela::STATUS_SCHEDULED,
                ],
            );
        }
    }
}
