<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sessions are stored in the database and users use UUID primary keys, but
     * the framework's stock `sessions` table declares `user_id` as a bigint.
     * Writing an authenticated session therefore fails on PostgreSQL with
     * "invalid input syntax for type bigint". Convert the column so it can hold
     * the authenticated user's UUID.
     */
    public function up(): void
    {
        if (! $this->isBigintUserId()) {
            return;
        }

        // Stale rows cannot be cast to uuid, and sessions are transient.
        DB::table('sessions')->whereNotNull('user_id')->update(['user_id' => null]);

        // PostgreSQL has no bigint -> uuid cast, and every value was just cleared.
        DB::statement('alter table "sessions" alter column "user_id" type uuid using null::uuid');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || $this->isBigintUserId()) {
            return;
        }

        DB::table('sessions')->whereNotNull('user_id')->update(['user_id' => null]);

        DB::statement('alter table "sessions" alter column "user_id" type bigint using null::bigint');
    }

    private function isBigintUserId(): bool
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return false;
        }

        $column = DB::selectOne(
            "select data_type from information_schema.columns where table_schema = 'public' and table_name = 'sessions' and column_name = 'user_id'"
        );

        return $column !== null && in_array($column->data_type, ['bigint', 'integer', 'smallint'], true);
    }
};
