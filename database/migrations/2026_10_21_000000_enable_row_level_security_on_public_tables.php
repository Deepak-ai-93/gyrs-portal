<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Every table in the `public` schema is exposed through Supabase's REST
     * API. Laravel is the only intended data access layer, and it connects as
     * the `postgres` table owner, which bypasses RLS. So we enable RLS without
     * adding policies: the anon/authenticated Supabase roles are denied direct
     * access while the Laravel application keeps working.
     *
     * FORCE ROW LEVEL SECURITY is deliberately not used because it would also
     * apply to the owning role and break the Laravel connection.
     *
     * @var list<string>
     */
    private array $tables = [
        // Application tables.
        'application_status_history',
        'applications',
        'audit_logs',
        'districts',
        'job_application_steps',
        'job_eligibility',
        'job_responsibilities',
        'job_selection_steps',
        'job_skills',
        'jobs',
        'notifications',
        'profile_skills',
        'rozgar_melas',
        'saved_jobs',
        'skills',
        'users',
        // Laravel framework tables (sessions and reset tokens must never be
        // readable through the public API either).
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'migrations',
        'password_reset_tokens',
        'queue_jobs',
        'sessions',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $existing = collect(
            DB::select("select tablename from pg_tables where schemaname = 'public'")
        )->pluck('tablename')->all();

        foreach ($this->tables as $table) {
            if (! in_array($table, $existing, true)) {
                continue;
            }

            DB::statement(sprintf(
                'alter table %s enable row level security',
                $this->quote($table),
            ));
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $existing = collect(
            DB::select("select tablename from pg_tables where schemaname = 'public'")
        )->pluck('tablename')->all();

        foreach ($this->tables as $table) {
            if (! in_array($table, $existing, true)) {
                continue;
            }

            DB::statement(sprintf(
                'alter table %s disable row level security',
                $this->quote($table),
            ));
        }
    }

    private function quote(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
};
