<?php

namespace App\Support;

/**
 * Canonical catalogue of the granular access rights an administrator account
 * can be granted. Super administrators implicitly hold every permission.
 */
class AdminPermissions
{
    public const JOBS_VIEW = 'jobs.view';

    public const JOBS_MANAGE = 'jobs.manage';

    public const CANDIDATES_VIEW = 'candidates.view';

    public const CANDIDATES_VERIFY = 'candidates.verify';

    public const APPLICATIONS_VIEW = 'applications.view';

    public const APPLICATIONS_MANAGE = 'applications.manage';

    public const EXPORT_CANDIDATES = 'exports.candidates';

    /**
     * Permission keys grouped under a human readable heading.
     *
     * @return array<string, array<string, string>>
     */
    public static function groups(): array
    {
        return [
            'Job postings' => [
                self::JOBS_VIEW => 'View job postings',
                self::JOBS_MANAGE => 'Create, edit and remove job postings',
            ],
            'Candidates' => [
                self::CANDIDATES_VIEW => 'View the candidate roster',
                self::CANDIDATES_VERIFY => 'Verify candidates and update their status',
            ],
            'Applications' => [
                self::APPLICATIONS_VIEW => 'View submitted applications',
                self::APPLICATIONS_MANAGE => 'Update application status',
            ],
            'Data' => [
                self::EXPORT_CANDIDATES => 'Export candidate data (CSV)',
            ],
        ];
    }

    /**
     * Flat map of permission key => label.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];

        foreach (self::groups() as $permissions) {
            $labels += $permissions;
        }

        return $labels;
    }

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_keys(self::labels());
    }

    public static function label(string $key): string
    {
        return self::labels()[$key] ?? $key;
    }

    public static function exists(string $key): bool
    {
        return in_array($key, self::all(), true);
    }
}
