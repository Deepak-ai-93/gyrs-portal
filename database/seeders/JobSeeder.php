<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Job;
use App\Models\Skill;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * The six seeded vacancies, ported from prototype/js/app.js.
     *
     * @var array<int, array<string, mixed>>
     */
    public const JOBS = [
        [
            'title' => 'Junior Software Engineer',
            'department' => 'Gujarat Informatics Ltd (GIL)',
            'district' => 'Gandhinagar',
            'salary_text' => '₹35,000 - ₹45,000 / month',
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'employment_type' => 'Full-Time',
            'skills' => ['Python', 'Web Development', 'Database'],
            'match_score' => 96,
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'official_website_url' => 'https://gil.gujarat.gov.in/',
            'notification_pdf_path' => 'https://ojas.gujarat.gov.in/pdf/gil-junior-software-engineer-2026.pdf',
            'advertisement_number' => 'GIL/2026/04',
            'total_posts' => 48,
            'posted_at' => '02 Oct 2026',
            'last_date' => '02 Nov 2026',
            'age_limit' => '21 - 35 Years (relaxation as per Govt rules)',
            'fees' => 'General: ₹500 | SC/ST/SEBC/EWS: ₹250 | PwD: Nil',
            'vacancy_breakup' => 'UR: 20 | SEBC: 12 | SC: 6 | ST: 7 | EWS: 3',
            'eligibility' => [
                'B.E./B.Tech in Computer / IT / MCA with minimum 55%',
                'Knowledge of Python, Web Development and SQL / Database',
                'Gujarati / Hindi language proficiency desirable',
            ],
            'responsibilities' => [
                'Development of state e-governance web modules',
                'API integration and database management',
                'Testing, documentation and support at GIFT City centre',
            ],
            'selection_steps' => ['Online Application Scrutiny', 'Written / Technical Test', 'Personal Interview', 'Document Verification'],
            'application_steps' => [
                'Read the official notification PDF carefully before applying.',
                'Click Apply Now below to open the OJAS application portal.',
                'Register with One-Time Registration, fill the form and upload photo / signature.',
                'Pay the fee online and note the confirmation number for tracking in GYRS dashboard.',
            ],
            'description' => 'Recruitment for state e-governance software initiatives and AI web platform development at GIFT City, Gandhinagar.',
        ],
        [
            'title' => 'Data Entry & Systems Operator',
            'department' => 'Ahmedabad Municipal Corp (AMC)',
            'district' => 'Ahmedabad',
            'salary_text' => '₹22,000 - ₹28,000 / month',
            'qualification' => '12th Pass / Graduate',
            'employment_type' => 'Govt Contract',
            'skills' => ['Gujarati Typing', 'MS Office', 'Data Entry'],
            'match_score' => 91,
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'official_website_url' => 'https://ahmedabadcity.gov.in/',
            'notification_pdf_path' => 'https://ojas.gujarat.gov.in/pdf/amc-data-entry-operator-2026.pdf',
            'advertisement_number' => 'AMC/EST/2026/11',
            'total_posts' => 120,
            'posted_at' => '28 Sep 2026',
            'last_date' => '27 Oct 2026',
            'age_limit' => '18 - 34 Years',
            'fees' => 'All Categories: ₹200 (online only)',
            'vacancy_breakup' => 'UR: 50 | SEBC: 30 | SC: 15 | ST: 18 | EWS: 7',
            'eligibility' => [
                '12th Pass / Graduate from recognized board / university',
                'Gujarati + English typing speed 25 wpm',
                'CCC / computer certificate desirable',
            ],
            'responsibilities' => [
                'Digital record digitization at civic centres',
                'Citizen service desk data operations',
                'Document verification and MIS entry',
            ],
            'selection_steps' => ['Application Scrutiny', 'Typing / Skill Test', 'Merit List', 'Document Verification'],
            'application_steps' => [
                'Download and read the AMC notification PDF.',
                'Click Apply Now to go to OJAS / AMC portal.',
                'Fill personal, education details and upload documents.',
                'Submit before last date and save acknowledgement.',
            ],
            'description' => 'Digital record digitization, citizen service desk operations, and document management for AMC civic centers.',
        ],
        [
            'title' => 'Solar Panel Maintenance Engineer',
            'department' => 'GEDA - Renewable Energy Dept',
            'district' => 'Kutch',
            'salary_text' => '₹30,000 - ₹40,000 / month',
            'qualification' => 'ITI / Diploma (Mechanical/Electrical)',
            'employment_type' => 'Full-Time',
            'skills' => ['Electrical', 'Solar Tech', 'Maintenance'],
            'match_score' => 88,
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'official_website_url' => 'https://geda.gujarat.gov.in/',
            'notification_pdf_path' => 'https://ojas.gujarat.gov.in/pdf/geda-solar-engineer-2026.pdf',
            'advertisement_number' => 'GEDA/HR/2026/07',
            'total_posts' => 65,
            'posted_at' => '05 Oct 2026',
            'last_date' => '04 Nov 2026',
            'age_limit' => '19 - 36 Years',
            'fees' => 'General: ₹400 | Reserved: ₹200',
            'vacancy_breakup' => 'UR: 28 | SEBC: 16 | SC: 8 | ST: 9 | EWS: 4',
            'eligibility' => [
                'ITI / Diploma in Electrical / Mechanical',
                'Knowledge of PV systems and safety norms',
                'Willingness for Khavda site posting',
            ],
            'responsibilities' => [
                'PV array maintenance and grid commissioning',
                'Substation supervision and fault reporting',
                'Daily generation logs and safety compliance',
            ],
            'selection_steps' => ['Shortlisting', 'Trade Test', 'Interview', 'Medical + Document Verification'],
            'application_steps' => [
                'Read GEDA notification PDF for site conditions.',
                'Apply Now via OJAS link below.',
                'Attach ITI / Diploma certificates and experience if any.',
                'Track status in GYRS Candidate Dashboard.',
            ],
            'description' => 'Grid commissioning, photovoltaic array maintenance, and substation supervision at Khavda Renewable Energy Park.',
        ],
        [
            'title' => 'Textile Automation Technician',
            'department' => 'Gujarat Industrial Dev Corp (GIDC)',
            'district' => 'Surat',
            'salary_text' => '₹25,000 - ₹32,000 / month',
            'qualification' => 'ITI / Diploma (Mechanical/Electrical)',
            'employment_type' => 'Full-Time',
            'skills' => ['Automation', 'Textile Tech', 'Machinery'],
            'match_score' => 85,
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'official_website_url' => 'https://gidc.gujarat.gov.in/',
            'notification_pdf_path' => 'https://ojas.gujarat.gov.in/pdf/gidc-textile-technician-2026.pdf',
            'advertisement_number' => 'GIDC/TECH/2026/09',
            'total_posts' => 80,
            'posted_at' => '01 Oct 2026',
            'last_date' => '30 Oct 2026',
            'age_limit' => '18 - 33 Years',
            'fees' => 'General: ₹300 | SC/ST: ₹150',
            'vacancy_breakup' => 'UR: 34 | SEBC: 20 | SC: 10 | ST: 11 | EWS: 5',
            'eligibility' => [
                'ITI / Diploma in Mechanical / Electrical / Textile',
                'PLC / automation basics preferred',
                'Shift duty willingness at Surat GIDC',
            ],
            'responsibilities' => [
                'Automated loom operations and PLC upkeep',
                'Preventive maintenance scheduling',
                'Spare management and breakdown support',
            ],
            'selection_steps' => ['Scrutiny', 'Practical Trade Test', 'Interview', 'Document Verification'],
            'application_steps' => [
                'Verify eligibility in notification PDF.',
                'Click Apply Now for OJAS form.',
                'Upload trade certificates.',
                'Save application number for interview call letter.',
            ],
            'description' => 'Automated loom machinery operations, programmable logic maintenance, and industrial equipment servicing at Surat GIDC.',
        ],
        [
            'title' => 'Apprentice Mechanical Supervisor',
            'department' => 'Vadodara ITI Apprenticeship',
            'district' => 'Vadodara',
            'salary_text' => '₹18,000 - ₹22,000 / month',
            'qualification' => 'ITI / Diploma (Mechanical/Electrical)',
            'employment_type' => 'Apprenticeship',
            'skills' => ['Machinery', 'AutoCAD', 'Maintenance'],
            'match_score' => 82,
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'official_website_url' => 'https://apprenticeshipindia.gov.in/',
            'notification_pdf_path' => 'https://ojas.gujarat.gov.in/pdf/vadodara-apprentice-2026.pdf',
            'advertisement_number' => 'DETVAD/APP/2026/03',
            'total_posts' => 150,
            'posted_at' => '25 Sep 2026',
            'last_date' => '25 Oct 2026',
            'age_limit' => '18 - 28 Years',
            'fees' => 'No Application Fee (Stipend under Mukhyamantri Apprentice Yojana)',
            'vacancy_breakup' => 'Mechanical: 90 | Electrical: 60 (district-wise merit)',
            'eligibility' => [
                'ITI pass in relevant trade / Diploma fresher',
                'Gujarat domicile',
                'Aadhaar + bank details for DBT stipend',
            ],
            'responsibilities' => [
                '1-year on-job training in manufacturing belt',
                'AutoCAD drawings and shop-floor assistance',
                'Monthly logbook and assessment',
            ],
            'selection_steps' => ['Merit (ITI marks)', 'Document Verification', 'Medical Fitness'],
            'application_steps' => [
                'Read apprentice notification PDF.',
                'Apply Now on OJAS / Apprenticeship portal.',
                'Select Vadodara cluster and trade.',
                'Stipend credited monthly via DBT after joining.',
            ],
            'description' => '1-year industrial apprenticeship program with monthly stipend under Mukhyamantri Apprentice Yojana in Vadodara manufacturing belt.',
        ],
        [
            'title' => 'Cyber Security Analyst',
            'department' => 'Gujarat Police Cyber Cell',
            'district' => 'Rajkot',
            'salary_text' => '₹40,000 - ₹55,000 / month',
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'employment_type' => 'Govt Direct',
            'skills' => ['Networking', 'Cyber Security', 'Python'],
            'match_score' => 94,
            'apply_url' => 'https://ojas.gujarat.gov.in/',
            'official_website_url' => 'https://police.gujarat.gov.in/',
            'notification_pdf_path' => 'https://ojas.gujarat.gov.in/pdf/cyber-cell-analyst-2026.pdf',
            'advertisement_number' => 'GP/CYBER/2026/02',
            'total_posts' => 36,
            'posted_at' => '08 Oct 2026',
            'last_date' => '07 Nov 2026',
            'age_limit' => '21 - 37 Years + physical standards as per rules',
            'fees' => 'General: ₹600 | SC/ST/SEBC/EWS: ₹300',
            'vacancy_breakup' => 'UR: 15 | SEBC: 9 | SC: 4 | ST: 5 | EWS: 3',
            'eligibility' => [
                'B.E./B.Tech (CS/IT) / MCA with 55%',
                'Networking + security fundamentals, Python scripting',
                'Clean record; police verification required',
            ],
            'responsibilities' => [
                'Digital forensics and threat monitoring',
                'State network protection and incident response',
                'Awareness and reporting support',
            ],
            'selection_steps' => ['Written Exam', 'Technical / Practical Test', 'Interview', 'Physical + Document Verification'],
            'application_steps' => [
                'Read cyber cell notification PDF in detail.',
                'Click Apply Now to open OJAS form.',
                'Fill exam centre preference (Rajkot / Ahmedabad).',
                'Download hall ticket when announced and track here.',
            ],
            'description' => 'Digital forensic investigations, state data network protection, and cyber threat monitoring for Rajkot Cyber Crime Division.',
        ],
    ];

    public function run(): void
    {
        $districts = District::query()->pluck('id', 'name');

        foreach (self::JOBS as $data) {
            $job = Job::updateOrCreate(
                ['slug' => str($data['title'])->slug()->value()],
                [
                    'title' => $data['title'],
                    'department' => $data['department'],
                    'district_id' => $districts[$data['district']] ?? null,
                    'location' => $data['district'],
                    'salary_text' => $data['salary_text'],
                    'qualification' => $data['qualification'],
                    'employment_type' => $data['employment_type'],
                    'description' => $data['description'],
                    'match_score' => $data['match_score'],
                    'advertisement_number' => $data['advertisement_number'],
                    'total_posts' => $data['total_posts'],
                    'posted_at' => $this->date($data['posted_at']),
                    'last_date' => $this->date($data['last_date']),
                    'age_limit' => $data['age_limit'],
                    'fees' => $data['fees'],
                    'vacancy_breakup' => $data['vacancy_breakup'],
                    'official_website_url' => $data['official_website_url'],
                    'apply_url' => $data['apply_url'],
                    'notification_pdf_path' => $data['notification_pdf_path'],
                    'status' => Job::STATUS_PUBLISHED,
                ],
            );

            $job->skills()->sync(
                Skill::query()
                    ->whereIn('name', $data['skills'])
                    ->pluck('id')
                    ->all()
            );

            $this->syncSteps($job->eligibility(), $data['eligibility']);
            $this->syncSteps($job->responsibilities(), $data['responsibilities']);
            $this->syncSteps($job->selectionSteps(), $data['selection_steps']);
            $this->syncSteps($job->applicationSteps(), $data['application_steps']);
        }
    }

    private function date(string $value): string
    {
        return CarbonImmutable::createFromFormat('d M Y', $value)->toDateString();
    }

    /**
     * @param  HasMany  $relation
     * @param  array<int, string>  $rows
     */
    private function syncSteps($relation, array $rows): void
    {
        $relation->getRelated()->newQuery()
            ->where($relation->getForeignKeyName(), $relation->getParentKey())
            ->delete();

        foreach ($rows as $index => $body) {
            $relation->create([
                'body' => $body,
                'sort_order' => $index,
            ]);
        }
    }
}
