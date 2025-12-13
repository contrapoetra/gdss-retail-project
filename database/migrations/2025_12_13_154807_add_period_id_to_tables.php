<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create a default period if none exists
        $defaultPeriodId = DB::table('periods')->insertGetId([
            'name' => 'Initial Period (2024)',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Add period_id to candidates
        Schema::table('candidates', function (Blueprint $table) use ($defaultPeriodId) {
            $table->foreignId('period_id')->nullable()->after('id');
        });

        // 3. Update existing candidates
        DB::table('candidates')->update(['period_id' => $defaultPeriodId]);

        // 4. Enforce FK on candidates (modify column to not be nullable if desired, but here we just add constraint)
        // Note: SQLite/MySQL might behave differently on changing columns. 
        // For safety, we keep it nullable or we need to ensure the column is updated before constraint.
        // Let's just add the foreign key constraint.
        Schema::table('candidates', function (Blueprint $table) {
             // We can't easily change to not null without doctrine/dbal usually, 
             // but we can add the foreign key.
             $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
        });

        // 5. Add period_id to consensus_logs
        Schema::table('consensus_logs', function (Blueprint $table) use ($defaultPeriodId) {
            $table->foreignId('period_id')->nullable()->after('id');
        });

        // 6. Update existing logs
        DB::table('consensus_logs')->update(['period_id' => $defaultPeriodId]);

        // 7. Enforce FK on consensus_logs
        Schema::table('consensus_logs', function (Blueprint $table) {
             $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });

        Schema::table('consensus_logs', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });
    }
};