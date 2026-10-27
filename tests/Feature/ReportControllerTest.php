<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\TindakanPasien;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'RPA')->value('id');
        $user->save();

        return $user;
    }

    public function test_report_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/report');

        $response->assertRedirect(route('error'));
    }

    public function test_omset_bulanan_sums_in_and_out_transactions_per_month(): void
    {
        $user = $this->authorizedUser();

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-R1',
            'transaction_type' => 'in',
            'typecode' => 'PAY',
            'amount' => 100000,
            'created_at' => now()->startOfMonth()->addDays(2),
        ]);
        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-R2',
            'transaction_type' => 'out',
            'typecode' => 'MISC',
            'amount' => 30000,
            'created_at' => now()->startOfMonth()->addDays(3),
        ]);

        $response = $this->actingAs($user)->post('/report/getOmsetBulanan', [
            'year' => now()->year,
        ]);

        $response->assertOk();
        $currentMonthLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][now()->month - 1];
        $data = collect($response->json('data'));
        $thisMonth = $data->firstWhere('bulan', $currentMonthLabel);

        $this->assertEquals(70000, $thisMonth['total']);
    }

    public function test_tindakan_terlaris_groups_and_orders_by_count(): void
    {
        $user = $this->authorizedUser();

        foreach (range(1, 3) as $i) {
            TindakanPasien::create([
                'klienfk' => $user->klienfk,
                'tindakan' => 'Scaling',
                'dokter' => 'drg. A',
                'qty' => 1,
                'amount' => 50000,
            ]);
        }
        TindakanPasien::create([
            'klienfk' => $user->klienfk,
            'tindakan' => 'Cabut Gigi',
            'dokter' => 'drg. B',
            'qty' => 1,
            'amount' => 150000,
        ]);

        $response = $this->actingAs($user)->post('/report/getTindakanTerlaris', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame('Scaling', $data[0]['tindakan']);
        $this->assertSame(3, $data[0]['jumlah']);
        $this->assertSame(150000.0, (float) $data[0]['total_pendapatan']);
    }

    public function test_performa_dokter_groups_by_dokter_name(): void
    {
        $user = $this->authorizedUser();

        TindakanPasien::create([
            'klienfk' => $user->klienfk,
            'tindakan' => 'Scaling',
            'dokter' => 'drg. A',
            'qty' => 1,
            'amount' => 50000,
        ]);
        TindakanPasien::create([
            'klienfk' => $user->klienfk,
            'tindakan' => 'Cabut Gigi',
            'dokter' => 'drg. A',
            'qty' => 1,
            'amount' => 150000,
        ]);

        $response = $this->actingAs($user)->post('/report/getPerformaDokter', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame('drg. A', $data[0]['dokter']);
        $this->assertSame(2, $data[0]['jumlah_tindakan']);
        $this->assertSame(200000.0, (float) $data[0]['total_pendapatan']);
    }

    public function test_demografi_pasien_buckets_gender_and_age(): void
    {
        $user = $this->authorizedUser();

        Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM001',
            'nik' => '1111',
            'nama' => 'Pasien Muda',
            'tgllahir' => now()->subYears(20)->toDateString(),
        ]);
        Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM002',
            'nik' => '2222',
            'nama' => 'Pasien Tua',
            'tgllahir' => now()->subYears(70)->toDateString(),
        ]);

        $response = $this->actingAs($user)->post('/report/getDemografiPasien');

        $response->assertOk();
        $usia = $response->json('data.usia');
        $this->assertSame(1, $usia['18-30']);
        $this->assertSame(1, $usia['60+']);
    }
}
