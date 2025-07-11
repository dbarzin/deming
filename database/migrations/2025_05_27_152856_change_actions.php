<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL requires manual check before conversion
            $invalidTypes = DB::table('actions')
                ->whereRaw("type !~ '^\d+$'")
                ->count();

            if ($invalidTypes > 0) {
                throw new \RuntimeException("The 'type' column contains non-numeric values. Please clean up the data before running this migration.");
            }

            // Explicit conversion using PostgreSQL's USING clause
            DB::statement('ALTER TABLE actions ALTER COLUMN type TYPE integer USING type::integer');
            DB::statement('ALTER TABLE actions ALTER COLUMN type SET NOT NULL');
        } else {
            // MySQL, MariaDB, SQLite: automatic or dynamic type conversion
            Schema::table('actions', function (Blueprint $table) {
                $table->integer('type')->nullable(false)->change();
            });
        }
        Schema::table('actions', function (Blueprint $table) {
            $table->integer('progress')->nullable()->after('type');
        });
    }

    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Revert PostgreSQL changes
            DB::statement('ALTER TABLE actions ALTER COLUMN type TYPE text USING type::text');
            DB::statement('ALTER TABLE actions ALTER COLUMN type DROP NOT NULL');
        } else {
            // Revert changes for MySQL, MariaDB, SQLite
            Schema::table('actions', function (Blueprint $table) {
                $table->string('type')->nullable()->change();
            });
        }
        if (Schema::hasColumn('actions', 'progress')) {
            Schema::table('actions', function (Blueprint $table) {
                $table->dropColumn('progress');
            });
        }
    }
};
