<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `notes` and `alamat` were originally created as `date` columns (copy-paste
     * mistake) — fixed to nullable text so they can actually hold free text.
     * Also adds the columns needed for a single-approver flow: the original
     * schema only had three-tier approval booleans (spv/hod/hr) with no actual
     * approver/timestamp, and no rejection reason.
     */
    public function up(): void
    {
        Schema::table('t_cuti', function (Blueprint $table) {
            $table->text('notes')->nullable()->change();
            $table->string('alamat')->nullable()->change();
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejected_reason')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_cuti', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'rejected_reason']);
            $table->date('notes')->change();
            $table->date('alamat')->change();
        });
    }
};
