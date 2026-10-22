<?php

namespace Database\\Seeds;

use App\\Models\\LearningResource;
use Illuminate\\Database\\Eloquent\\Factories\\Factory;
use Illuminate\\Support\\Str;

class LearningResourceSeeder extends Factory
{
    /** @var array<int, array{id: int, title: string, provider: string, url: string}> */
    private static $resources = [];

    /** @var array<string, array<int, array{id: int, title: string, provider: string, url: string}>> */
    private static $resourcesBySkill = [];

    public function definition(): array
    {
        return [];
    }

    /**
     * Seed learning resources for each major skill.
     * All resources are free or India-friendly.
     */
    public function run(): void
    {
        // Clear existing resources
        LearningResource::query()->delete();

        // Free/India-friendly learning resources grouped by skill
        $allResources = [
            'Python' => [
                ['title': 'W3Schools Python Course', 'provider': 'W3Schools', 'url': 'https://www.w3schools.com/python/'],
                ['title': 'FreeCodeCamp Python', 'provider': 'FreeCodeCamp', 'url': 'https://www.freecodecamp.org/learn/python/'],
                ['title': 'GeeksforGeeks Python', 'provider': 'GeeksforGeeks', 'url': 'https://www.geeksforgeeks.org/python-tutorial/'],
            ],
            'Web Development' => [
                ['title': 'HTML5 + CSS3 Course', 'provider': 'freeCodeCamp', 'url': 'https://www.freecodecamp.org/learn/responsive-web-design/'],
                ['title': 'JavaScript Algorithms', 'provider': 'freeCodeCamp', 'url': 'https://www.freecodecamp.org/learn/javascript-algorithms/'],
                ['title': 'Responsive Design Guide', 'provider': 'MDN Web Docs', 'url': 'https://developer.mozilla.org/en-US/docs/Learn/CSS'],
            ],
            'Database' => [
                ['title': 'SQLZoo Tutorials', 'provider': 'SQLZoo', 'url': 'https://sqlzoo.net/'],
                ['title': 'W3Schools SQL', 'provider': 'W3Schools', 'url': 'https://www.w3schools.com/sql/'],
                ['title': 'DataCamp Intro to SQL', 'provider': 'DataCamp', 'url': 'https://www.datacamp.com/intro-to-sql/'],
            ],
            'SQL' => [
                ['title': 'SQLCourse', 'provider': 'SQLCourse', 'url': 'https://www.sqlcourse.com/'],
                ['title': 'Mode Analytics SQL Guide', 'provider': 'Mode Analytics', 'url': 'https://mode.com/sql-tutorial/'],
                ['title': 'Khan Academy SQL', 'provider': 'Khan Academy', 'url': 'https://www.khanacademy.org/computing/computer-programming/sql'],
            ],
            'Data Analysis' => [
                ['title': 'Google Data Analytics', 'provider': 'Google Career Certificates', 'url': 'https://grow.google/data-analytics/'],
                ['title': 'FreeCodeCamp Data Analysis', 'provider': 'FreeCodeCamp', 'url': 'https://www.freecodecamp.org/learn/data-analysis/'],
                ['title': 'Kaggle Micro-courses', 'provider': 'Kaggle', 'url': 'https://www.kaggle.com/learn'],
            ],
            'Data Entry' => [
                ['title': 'MS Office Training', 'provider': 'GCFGlobal', 'url': 'https://edu.gcfglobal.org/en/msoffice/'],
                ['title': 'TypingClub', 'provider': 'TypingClub', 'url': 'https://www.typingclub.com/'],
                ['title': 'GST e-Learning', 'provider': 'GST Network', 'url': 'https://gst.gov.in/education-and-outreach'],
            ],
            'Cloud' => [
                ['title': 'AWS Cloud Practitioner', 'provider': 'AWS', 'url': 'https://aws.amazon.com/training/cal/cloud-practitioner/'],
                ['title': 'Google Cloud Basics', 'provider': 'Google Cloud', 'url': 'https://cloud.google.com/learn'],
                ['title': 'Azure Fundamentals', 'provider': 'Microsoft Azure', 'url': 'https://learn.microsoft.com/en-us/training/modules/describe-cloud-concepts/'],
            ],
            'Cyber Security' => [
                ['title': 'Cybrary Intro to Cyber', 'provider': 'Cybrary', 'url': 'https://www.cybrary.it/courses/introduction-to-cyber-security/'],
                ['title': 'Google Cybersecurity Certificate', 'provider': 'Google Career Certificates', 'url': 'https://grow.google/cybersecurity/'],
                ['title': 'CISA Learning', 'provider': 'CISA', 'url': 'https://www.cisa.gov/learning/'],
            ],
            'Networking' => [
                ['title': 'Cisco Networking Basics', 'provider': 'Cisco', 'url': 'https://www.netacad.com/courses/infrastructure/introduction-to-internet-and-networks'],
                ['title': 'Network+ Guide', 'provider': 'CompTIA', 'url': 'https://www.comptia.org/certifications/network+'],
                ['title': 'Google Cloud Networking', 'provider': 'Google Cloud', 'url': 'https://cloud.google.com/networking'],
            ],
            'Electrical' => [
                ['title': 'Electrical Engineering Basics', 'provider': 'Khan Academy', 'url': 'https://www.khanacademy.org/science/electrical-engineering'],
                ['title': 'Eaton Power Training', 'provider': 'Eaton', 'url': 'https://www.eaton.com/en-in/training/'],
                ['title': 'IEEE Xplore Articles', 'provider': 'IEEE', 'url': 'https://ieeexplore.ieee.org/'],
            ],
            'Solar Tech' => [
                ['title': 'Solar Energy Fundamentals', 'provider': 'Coursera', 'url': 'https://www.coursera.org/learn/renewable-energy-specialization'],
                ['title': 'National Solar Job Training', 'provider': 'US DOE', 'url': 'https://www.energy.gov/solar/training-and-resources'],
                ['title': 'MNRE India Solar Resources', 'provider': 'MNRE', 'url': 'https://mnre.gov.in/'],
            ],
            'Maintenance' => [
                ['title': 'Maintenance Management', 'provider': 'Alison', 'url': 'https://alison.com/c/maintenance-management'],
                ['title': 'Total Productive Maintenance', 'provider': 'GCFGlobal', 'url': 'https://edu.gcfglobal.org/en/maintenance/'],
                ['title': 'Reliability Engineering', 'provider': 'NPTEL', 'url': 'https://nptel.ac.in/courses/112107118/'],
            ],
            'Machinery' => [
                ['title': 'Machining Tips', 'provider': 'Practical Machinist', 'url': 'https://www.practicalmachinist.com/'],
                ['title': 'Engineering ToolBox', 'provider': 'Engineering ToolBox', 'url': 'https://www.engineeringtoolbox.com/'],
                ['title': 'NPTEL Manufacturing', 'provider': 'NPTEL', 'url': 'https://nptel.ac.in/courses/112105255/'],
            ],
            'AutoCAD' => [
                ['title': 'AutoCAD Tutorial', 'provider': 'AutoDesk', 'url': 'https://www.autodesk.com/tutor/autocad'],
                ['title': 'CAD Geeks', 'provider': 'CAD Geeks', 'url': 'https://www.cadgeeks.com/'],
                ['title': 'Engineering Drawing', 'provider': 'NPTEL', 'url': 'https://nptel.ac.in/courses/112105255/'],
            ],
            'Automation' => [
                ['title': 'Python Automation', 'provider': 'FreeCodeCamp', 'url': 'https://www.freecodecamp.org/learn/automation-with-python/'],
                ['title': 'AutoMate Tutorials', 'provider': 'AutoMate', 'url': 'https://www.automate.biz/learning/'],
                ['title': 'UiPath Academy', 'provider': 'UiPath', 'url': 'https://academy.uipath.com/'],
            ],
            'Textile Tech' => [
                ['title': 'Textile Wet Processing', 'provider': 'Textile Institute', 'url': 'https://www.textileindustryfinder.com/'],
                ['title': 'Fabric Manufacturing', 'provider': 'NCDEX', 'url': 'https://www.ncdex.com/'],
                ['title': 'Gujarat Textile Resources', 'provider': 'GSETI', 'url': 'https://gseti.org/'],
            ],
            'Typing' => [
                ['title': 'Typing Test & Practice', 'provider': 'TypingTest.com', 'url': 'https://www.typingtest.com/'],
                ['title': 'Speed Typing Online', 'provider': 'SpeedTypingOnline', 'url': 'https://www.speedtypingonline.com/'],
                ['title': 'GCFGlobal Typing', 'provider': 'GCFGlobal', 'url': 'https://edu.gcfglobal.org/en/typing/'],
            ],
            'MS Office' => [
                ['title': 'MS Office Official Training', 'provider': 'Microsoft', 'url': 'https://support.microsoft.com/en-us/training'],
                ['title': 'Excel Easy Tutorials', 'provider': 'Excel Easy', 'url': 'https://www.exceleasy.com/'],
                ['title': 'GCFGlobal MS Office', 'provider': 'GCFGlobal', 'url': 'https://edu.gcfglobal.org/en/msoffice/'],
            ],
            'GST Compliance' => [
                ['title': 'GST Course', 'provider': 'ICAI', 'url': 'https://www.icai.org/gst-course.html'],
                ['title': 'GST Practitioner', 'provider': 'TaxGuru', 'url': 'https://www.taxguru.in/gst/'],
                ['title': 'GST Portal Training', 'provider': 'GSTN', 'url': 'https://www.gstindia.gov.in/'],
            ],
            'Computer Fundamentals' => [
                ['title': 'GCFGlobal Computer Basics', 'provider': 'GCFGlobal', 'url': 'https://edu.gcfglobal.org/en/computerbasics/'],
                ['title': 'Khan Academy Computing', 'provider': 'Khan Academy', 'url': 'https://www.khanacademy.org/computing/computer-programming'],
                ['title': 'Digital Literacy', 'provider': 'Microsoft', 'url': 'https://digiliteracy.org/'],
            ],
        ];

        // Insert resources
        $resourceIndex = 0;
        foreach ($allResources as $skillName => $resources) {
            $skill = App\Models\Skill::where('name', $skillName)->first();

            if (! $skill) {
                // Create skill if it doesn't exist
                $skill = App\Models\Skill::firstOrCreate(
                    ['name' => $skillName],
                    ['slug' => Str::slug($skillName), 'category' => 'technical']
                );
            }

            foreach ($resources as $resource) {
                $resourceIndex++;
                $resourceData = [
                    'skill_id' => $skill->id,
                    'title' => $resource['title'],
                    'provider' => $resource['provider'],
                    'url' => $resource['url'],
                    'skill_tags' => explode(',', $skillName),
                    'is_active' => true,
                    'format' => 'online',
                    'cost' => 0,
                    'language' => 'en',
                ];

                LearningResource::create($resourceData);
            }
        }

        echo "Seeded {$resourceIndex} learning resources across ".count($allResources)." skills.\n";
    }
}