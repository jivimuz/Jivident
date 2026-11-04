<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_accounting_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('code', 20);
            $table->string('name');
            $table->string('type', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['klienfk', 'code']);
            $table->index(['klienfk', 'type']);
        });

        Schema::create('t_accounting_journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('journal_no', 40);
            $table->date('journal_date');
            $table->string('description');
            $table->string('source_type', 40)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamps();
            $table->unique(['klienfk', 'journal_no']);
            $table->index(['klienfk', 'journal_date']);
        });

        Schema::create('t_accounting_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->foreign('journal_id')->references('id')->on('t_accounting_journals')->cascadeOnDelete();
            $table->foreign('account_id')->references('id')->on('m_accounting_accounts');
            $table->index('account_id');
        });

        $groupId = DB::table('m_modules')->where('code', 'BL')->value('group_id') ?? 5;
        $groupName = DB::table('m_modules')->where('group_id', $groupId)->value('group_name') ?? 'KU';
        $listNo = (int) DB::table('m_modules')
            ->where('group_id', $groupId)
            ->where('isheader', 0)
            ->max('list_no') + 1;

        DB::table('m_modules')->updateOrInsert(
            ['code' => 'AKT'],
            [
                'name' => 'Akuntansi',
                'icon' => 'iconoir-stats-report',
                'route' => 'accounting',
                'group_id' => $groupId,
                'group_name' => $groupName,
                'list_no' => $listNo,
                'isheader' => 0,
                'isactive' => 1,
            ]
        );
    }

    public function down(): void
    {
        DB::table('m_modules')->where('code', 'AKT')->delete();
        Schema::dropIfExists('t_accounting_journal_lines');
        Schema::dropIfExists('t_accounting_journals');
        Schema::dropIfExists('m_accounting_accounts');
    }
};
