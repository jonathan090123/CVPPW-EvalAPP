<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\Criterion;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AssessmentDetail::truncate();
        Assessment::truncate();
        Employee::truncate();
        Criterion::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call(UserSeeder::class);

        // ── 7 Kriteria ──────────────────────────────────────────────────────
        Criterion::insert([
            ['name' => 'Kehadiran',      'type' => 'benefit', 'weight' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Disiplin',       'type' => 'benefit', 'weight' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tanggung Jawab', 'type' => 'benefit', 'weight' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kerja Sama',     'type' => 'benefit', 'weight' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Produktivitas',  'type' => 'benefit', 'weight' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inisiatif',      'type' => 'benefit', 'weight' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Human Error',    'type' => 'cost',    'weight' => 5,  'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 40 Karyawan dari file Excel "DATA KARYAWAN 2026" ────────────────
        // Tiap departemen: karyawan pertama = KEPALA BAGIAN,
        // ~30% berikutnya = KOORDINATOR, sisanya = STAFF.
        $raw = [
            // PEMBELIAN (10)
            ['PEMBELIAN', 'NINA DAMARYANTI'],
            ['PEMBELIAN', 'INTAN LUTFI FATIKASARI'],
            ['PEMBELIAN', 'INTAN NUR AINI'],
            ['PEMBELIAN', 'AHIFA YUFITASARI'],
            ['PEMBELIAN', 'TIA JUFIA'],
            ['PEMBELIAN', 'MOH. LUKMAN KAHFI'],
            ['PEMBELIAN', 'AINUL SOEHEB'],
            ['PEMBELIAN', 'YUSAFA ABIL MAULANA'],
            ['PEMBELIAN', 'ARYO BIMO SETYO NUGROHO'],
            ['PEMBELIAN', 'FERI WAHYUDI'],
            // MARKETING (24)
            ['MARKETING', 'YESSY NURUL LAILI'],
            ['MARKETING', 'KIKI YUDA OCTAVIANA'],
            ['MARKETING', 'FRENTY DYAH AGGRAINI'],
            ['MARKETING', 'ENDAH DESI WULANDARI'],
            ['MARKETING', 'CANDRA APRILIA PUSPITA SARI'],
            ['MARKETING', 'RISKY ALAM SAPUTRA'],
            ['MARKETING', 'DIKA YOGI PRASETYO'],
            ['MARKETING', 'RICKY FITRI HERWANTO'],
            ['MARKETING', 'YOGI PRASETIYO'],
            ['MARKETING', 'RINDU WIDYA KRISTIANI'],
            ['MARKETING', 'NEISKA FRANSISCA RISANTI'],
            ['MARKETING', 'DIANDRA TRI WULANDARI'],
            ['MARKETING', 'REVALINA AYU R.'],
            ['MARKETING', 'TEGAR FITRIONO'],
            ['MARKETING', 'INDRI NURSAHEPIN'],
            ['MARKETING', 'FIORELLA JESSICA RISANTI'],
            ['MARKETING', 'NOVANKA HABILLA PUTRI'],
            ['MARKETING', 'WAHYU PUTRI EKO NINGTIYAS'],
            ['MARKETING', 'FILMA AULIA'],
            ['MARKETING', 'NAYLA PRATIWI'],
            ['MARKETING', 'IMAM MUSTOFA'],
            ['MARKETING', 'FERRY SEPTIA LAZUARDI'],
            ['MARKETING', 'SUHADI'],
            ['MARKETING', 'WAHYU PRASETIA'],
            // FINANCE (6)
            ['FINANCE', 'INDARTI'],
            ['FINANCE', 'SITI NUR CHASANAH'],
            ['FINANCE', 'FATMA NURHIDAYAH'],
            ['FINANCE', 'CANDRA DWI WAHYU NINGSIH'],
            ['FINANCE', 'LISA MARDIATI'],
            ['FINANCE', 'MEISA WULANDARI'],
        ];

        // Kelompokkan per departemen untuk tentukan posisi (jabatan).
        $byDept = [];
        foreach ($raw as $i => [$dept, $name]) {
            $byDept[$dept][] = [$i, $name];
        }

        $positionMap = []; // index => position
        foreach ($byDept as $dept => $members) {
            $count = count($members);
            $koordCount = max(1, (int) floor(($count - 1) * 0.3)); // ~30% sisanya jadi koordinator
            foreach ($members as $idx => [$i, $name]) {
                if ($idx === 0) {
                    $positionMap[$i] = 'KEPALA BAGIAN';
                } elseif ($idx <= $koordCount) {
                    $positionMap[$i] = 'KOORDINATOR';
                } else {
                    $positionMap[$i] = 'STAFF';
                }
            }
        }

        $rows = [];
        $now = now();
        foreach ($raw as $i => [$dept, $name]) {
            $nip = sprintf('EMP%03d', $i + 1);
            $rows[] = [
                'nip'        => $nip,
                'name'       => $name,
                'department' => $dept,
                'position'   => $positionMap[$i],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        Employee::insert($rows);

        // ── Matriks nilai acak beragam (seeded) ──────────────────────────────
        // Membuat 3 assessment (PEMBELIAN / MARKETING / FINANCE) — masing-masing
        // hanya menilai karyawan dari departemen tsb, dengan nilai random 1-5
        // (C7/Human Error cost dialihkan ke 1-3).
        $criteria = Criterion::orderBy('id')->get();
        $criterionIds = $criteria->pluck('id')->all();

        $sessions = [
            ['name' => 'Penilaian PEMBELIAN', 'period' => 'Q1 2026', 'dept' => 'PEMBELIAN',
             'description' => 'Penilaian kinerja departemen Pembelian — Q1 2026'],
            ['name' => 'Penilaian MARKETING', 'period' => 'Q1 2026', 'dept' => 'MARKETING',
             'description' => 'Penilaian kinerja departemen Marketing — Q1 2026'],
            ['name' => 'Penilaian FINANCE', 'period' => 'Q1 2026', 'dept' => 'FINANCE',
             'description' => 'Penilaian kinerja departemen Finance — Q1 2026'],
        ];

        foreach ($sessions as $s) {
            $assessment = Assessment::create([
                'name'        => $s['name'],
                'period'      => $s['period'],
                'description' => $s['description'],
            ]);

            $empIds = Employee::where('department', $s['dept'])->orderBy('id')->pluck('id')->all();

            // Seed stabil per assessment supaya nilai konsisten tiap reseed
            mt_srand(crc32($s['name']));
            $details = [];
            foreach ($empIds as $empId) {
                foreach ($criterionIds as $cId) {
                    $criterion = $criteria->firstWhere('id', $cId);
                    $value = $criterion->type === 'cost'
                        ? random_int(1, 3)
                        : random_int(1, 5);
                    $details[] = [
                        'assessment_id' => $assessment->id,
                        'employee_id'   => $empId,
                        'criterion_id'  => $cId,
                        'value'         => $value,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }
            }
            AssessmentDetail::insert($details);
            mt_srand(); // reset seed
        }
    }
}