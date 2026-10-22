<?php

namespace Database\Seeders;

use App\Models\LearningResource;
use Illuminate\Database\Seeder;

class LearningResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            [
                'title' => 'Free courses for technical and professional skills',
                'provider' => 'SWAYAM',
                'url' => 'https://swayam.gov.in/',
                'skill_tags' => ['Python', 'Web Development', 'Database', 'SQL', 'Data Analysis', 'Data Management', 'Electrical', 'Solar Tech', 'Maintenance', 'Machinery', 'Automation', 'AutoCAD', 'Textile Tech'],
                'format' => 'Courses and learning materials',
            ],
            [
                'title' => 'Free self-paced technology training',
                'provider' => 'Microsoft Learn',
                'url' => 'https://learn.microsoft.com/en-us/training/browse/',
                'skill_tags' => ['Python', 'Web Development', 'Database', 'SQL', 'Data Analysis', 'Data Management', 'Cloud', 'Cyber Security', 'Networking', 'Automation', 'MS Office', 'Computer Fundamentals'],
                'format' => 'Self-paced modules and learning paths',
            ],
            [
                'title' => 'Free networking, cybersecurity and IT courses',
                'provider' => 'Cisco Networking Academy',
                'url' => 'https://www.cisco.com/site/us/en/learn/training-certifications/training/netacad/index.html',
                'skill_tags' => ['Cyber Security', 'Networking', 'Cloud', 'Computer Fundamentals'],
                'format' => 'Self-paced online courses',
            ],
            [
                'title' => 'Free digital and technology learning',
                'provider' => 'IBM SkillsBuild',
                'url' => 'https://skillsbuild.org/',
                'skill_tags' => ['Python', 'Data Analysis', 'Cloud', 'Cyber Security', 'Computer Fundamentals', 'Automation'],
                'format' => 'Online courses and projects',
            ],
            [
                'title' => 'Free foundational HTML, CSS and JavaScript courses',
                'provider' => 'SWAYAM Plus',
                'url' => 'https://swayamplus.education.gov.in/catalog?free=1',
                'skill_tags' => ['Web Development', 'Computer Fundamentals'],
                'format' => 'Online courses',
            ],
            [
                'title' => 'Free practical coding lessons and projects',
                'provider' => 'freeCodeCamp',
                'url' => 'https://www.freecodecamp.org/learn/',
                'skill_tags' => ['Python', 'Web Development', 'Database', 'SQL', 'Data Analysis'],
                'format' => 'Interactive lessons and projects',
            ],
        ];

        foreach ($resources as $resource) {
            LearningResource::updateOrCreate(
                ['url' => $resource['url']],
                $resource + [
                    'cost' => 'free',
                    'language' => 'English',
                    'is_active' => true,
                    'verified_at' => now(),
                ],
            );
        }
    }
}
