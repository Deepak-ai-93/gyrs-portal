<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Skill vocabulary used across the seeded jobs and candidate fixtures.
     *
     * @var array<int, string>
     */
    public const SKILLS = [
        'Python',
        'Web Development',
        'Database',
        'SQL',
        'Data Analysis',
        'Data Management',
        'Data Entry',
        'Cloud',
        'Cyber Security',
        'Networking',
        'Electrical',
        'Solar Tech',
        'Maintenance',
        'Machinery',
        'Automation',
        'AutoCAD',
        'Textile Tech',
        'Typing',
        'Gujarati Typing',
        'MS Office',
        'Tally',
        'GST Compliance',
        'Computer Fundamentals',
    ];

    public function run(): void
    {
        foreach (self::SKILLS as $name) {
            Skill::updateOrCreate(
                ['slug' => str($name)->slug()->value()],
                ['name' => $name],
            );
        }
    }
}
