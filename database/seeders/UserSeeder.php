<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Prototype job id => seeded job slug (order preserved from js/app.js).
     *
     * @var array<int, string>
     */
    private const JOB_SLUGS_BY_LEGACY_ID = [
        1 => 'junior-software-engineer',
        2 => 'data-entry-systems-operator',
        3 => 'solar-panel-maintenance-engineer',
        4 => 'textile-automation-technician',
        5 => 'apprentice-mechanical-supervisor',
        6 => 'cyber-security-analyst',
    ];

    /**
     * @var array<int, array<string, mixed>>
     */
    private const CANDIDATES = [
        [
            'enrollment_id' => 'GYRS-2026-001',
            'name' => 'Jagan Trivedi',
            'email' => 'jagan.trivedi@gyrs.gov.in',
            'phone' => '9876543210',
            'district' => 'Ahmedabad',
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'cgpa' => '8.7',
            'status' => User::STATUS_VERIFIED,
            'skills' => ['Python', 'Web Development', 'Data Analysis', 'SQL'],
            'applied' => [1, 2, 6],
        ],
        [
            'enrollment_id' => 'GYRS-2026-002',
            'name' => 'Priya Patel',
            'email' => 'priya.patel@gujarat.ac.in',
            'phone' => '9825012345',
            'district' => 'Gandhinagar',
            'qualification' => 'MCA / Software Engineering',
            'cgpa' => '9.1',
            'status' => User::STATUS_PLACED,
            'skills' => ['Python', 'Cloud', 'Cyber Security', 'SQL'],
            'applied' => [1, 6],
        ],
        [
            'enrollment_id' => 'GYRS-2026-003',
            'name' => 'Rahul Solanki',
            'email' => 'rahul.solanki@mail.com',
            'phone' => '9712345678',
            'district' => 'Vadodara',
            'qualification' => 'ITI / Diploma (Mechanical/Electrical)',
            'cgpa' => '7.9',
            'status' => User::STATUS_VERIFIED,
            'skills' => ['AutoCAD', 'Machinery', 'Maintenance'],
            'applied' => [5],
        ],
        [
            'enrollment_id' => 'GYRS-2026-004',
            'name' => 'Anjali Mehta',
            'email' => 'anjali.mehta@gmail.com',
            'phone' => '9909011223',
            'district' => 'Surat',
            'qualification' => 'ITI / Diploma (Mechanical/Electrical)',
            'cgpa' => '8.4',
            'status' => User::STATUS_SHORTLISTED,
            'skills' => ['Automation', 'Textile Tech', 'Machinery'],
            'applied' => [4],
        ],
        [
            'enrollment_id' => 'GYRS-2026-005',
            'name' => 'Bhavik Shah',
            'email' => 'bhavik.shah@gyrs.gov.in',
            'phone' => '9426098765',
            'district' => 'Rajkot',
            'qualification' => 'B.Tech / B.E. (Computer / IT)',
            'cgpa' => '8.9',
            'status' => User::STATUS_VERIFIED,
            'skills' => ['Networking', 'Cyber Security', 'Python'],
            'applied' => [6],
        ],
        [
            'enrollment_id' => 'GYRS-2026-006',
            'name' => 'Nisha Desai',
            'email' => 'nisha.desai@outlook.com',
            'phone' => '9638054321',
            'district' => 'Kutch',
            'qualification' => 'ITI / Diploma (Mechanical/Electrical)',
            'cgpa' => '8.1',
            'status' => User::STATUS_PENDING,
            'skills' => ['Electrical', 'Solar Tech', 'Maintenance'],
            'applied' => [3],
        ],
        [
            'enrollment_id' => 'GYRS-2026-007',
            'name' => 'Hardik Prajapati',
            'email' => 'hardik.prajapati@gmail.com',
            'phone' => '9898012399',
            'district' => 'Ahmedabad',
            'qualification' => '12th Pass / Graduate',
            'cgpa' => '7.5',
            'status' => User::STATUS_VERIFIED,
            'skills' => ['Typing', 'MS Office', 'Data Entry'],
            'applied' => [2],
        ],
        [
            'enrollment_id' => 'GYRS-2026-008',
            'name' => 'Kavita Vaghela',
            'email' => 'kavita.vaghela@gujarat.in',
            'phone' => '9724087654',
            'district' => 'Gandhinagar',
            'qualification' => 'B.Com / Accounting',
            'cgpa' => '8.0',
            'status' => User::STATUS_UNDER_REVIEW,
            'skills' => ['Tally', 'MS Office', 'GST Compliance'],
            'applied' => [],
        ],
    ];

    public function run(): void
    {
        $password = $this->devPassword();
        $districts = District::query()->pluck('id', 'name');
        $skills = Skill::query()->pluck('id', 'name');
        $jobs = Job::query()->pluck('id', 'slug');

        $admin = User::updateOrCreate(
            ['email' => config('gyrs.admin_email', 'admin.gyrs@gujarat.gov.in')],
            [
                'name' => 'Super Administrator',
                'phone' => null,
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_VERIFIED,
                'approved_at' => now(),
                'enrollment_id' => null,
            ],
        );

        foreach (self::CANDIDATES as $data) {
            $candidate = User::updateOrCreate(
                ['enrollment_id' => $data['enrollment_id']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => $password,
                    'role' => User::ROLE_CANDIDATE,
                    'district_id' => $districts[$data['district']] ?? null,
                    'qualification' => $data['qualification'],
                    'cgpa' => $data['cgpa'],
                    'status' => $data['status'],
                    'approved_at' => in_array($data['status'], [User::STATUS_VERIFIED, User::STATUS_PLACED, User::STATUS_SHORTLISTED], true) ? now() : null,
                ],
            );

            $candidate->skills()->sync(
                collect($data['skills'])
                    ->map(fn (string $name) => $skills[$name] ?? null)
                    ->filter()
                    ->values()
                    ->all()
            );

            $this->seedApplications($candidate, $data, $jobs, $admin);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  Collection<string, string>  $jobs
     */
    private function seedApplications(User $candidate, array $data, $jobs, User $admin): void
    {
        foreach ($data['applied'] as $index => $legacyId) {
            $jobId = $jobs[self::JOB_SLUGS_BY_LEGACY_ID[$legacyId]] ?? null;

            if (! $jobId) {
                continue;
            }

            $status = $this->applicationStatus($data['status'], $index);

            $application = $candidate->applications()->updateOrCreate(
                ['job_id' => $jobId],
                [
                    'status' => $status,
                    'applied_at' => now()->subDays(20 - $index),
                    'status_changed_at' => now()->subDays(5 - $index),
                ],
            );

            if ($application->wasRecentlyCreated) {
                $application->histories()->create([
                    'from_status' => null,
                    'to_status' => $status,
                    'actor_id' => $admin->getKey(),
                    'notes' => 'Seeded application record.',
                    'created_at' => now()->subDays(20 - $index),
                ]);
            }
        }
    }

    private function applicationStatus(string $candidateStatus, int $index): string
    {
        if ($index > 0) {
            return $candidateStatus === User::STATUS_PENDING ? 'applied' : 'under_review';
        }

        return match ($candidateStatus) {
            User::STATUS_PLACED => 'placed',
            User::STATUS_SHORTLISTED => 'shortlisted',
            User::STATUS_UNDER_REVIEW => 'under_review',
            default => 'applied',
        };
    }

    private function devPassword(): string
    {
        $configured = config('gyrs.dev_password');

        if (is_string($configured) && $configured !== '') {
            return Hash::make($configured);
        }

        if (app()->environment('local', 'testing')) {
            return Hash::make('gyrs-dev-password');
        }

        return Hash::make(Str::password(32));
    }
}
