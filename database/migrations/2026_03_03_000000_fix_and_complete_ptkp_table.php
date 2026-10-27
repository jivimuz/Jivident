<?php

use App\Models\Ptkp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Original seed only had 5 rows (TK, K0, K1, K2, K3) with K0 wrong
     * (58.000.000, should be 58.500.000) and TK/1, TK/2, TK/3 missing entirely —
     * those are required to map an employee's PTKP status to a TER withholding
     * category (A/B/C) per PMK 168/2023. Fixed + completed here, plus a
     * `ter_kategori` column so PayrollHelper can look up the right TER table.
     */
    public function up(): void
    {
        Schema::table('m_ptkp', function (Blueprint $table) {
            $table->string('ter_kategori', 1)->nullable()->after('nominal');
        });

        Ptkp::where('name', 'TK')->update(['name' => 'TK0', 'nominal' => 54000000, 'ter_kategori' => 'A']);
        Ptkp::where('name', 'K0')->update(['nominal' => 58500000, 'ter_kategori' => 'A']);
        Ptkp::where('name', 'K1')->update(['nominal' => 63000000, 'ter_kategori' => 'B']);
        Ptkp::where('name', 'K2')->update(['nominal' => 67500000, 'ter_kategori' => 'B']);
        Ptkp::where('name', 'K3')->update(['nominal' => 72000000, 'ter_kategori' => 'C']);

        Ptkp::insert([
            ['name' => 'TK1', 'nominal' => 58500000, 'ter_kategori' => 'A'],
            ['name' => 'TK2', 'nominal' => 63000000, 'ter_kategori' => 'B'],
            ['name' => 'TK3', 'nominal' => 67500000, 'ter_kategori' => 'B'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Ptkp::whereIn('name', ['TK1', 'TK2', 'TK3'])->delete();
        Ptkp::where('name', 'TK0')->update(['name' => 'TK']);
        Schema::table('m_ptkp', function (Blueprint $table) {
            $table->dropColumn('ter_kategori');
        });
    }
};
