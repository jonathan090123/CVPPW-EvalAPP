<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\Criterion;
use App\Models\Employee;
use Illuminate\Console\Command;

class ExportToCsv extends Command
{
    protected $signature = 'export:csv';

    protected $description = 'Export semua data (karyawan, kriteria, penilaian) ke file CSV';

    public function handle(): int
    {
        $dir = public_path('exports');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->exportKaryawan($dir);
        $this->exportKriteria($dir);
        $this->exportKaryawanKriteria($dir);
        $this->exportPenilaian($dir);
        $this->exportRanking($dir);

        $this->info('Semua file CSV berhasil diekspor ke folder public/exports/');

        return self::SUCCESS;
    }

    private function exportKaryawanKriteria(string $dir): void
    {
        $fp = fopen("{$dir}/karyawan_kriteria.csv", 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        // Section: Karyawan
        fputcsv($fp, ['=== DATA KARYAWAN ===']);
        fputcsv($fp, ['NIP', 'Nama Karyawan', 'Departemen', 'Jabatan']);
        Employee::orderBy('nip')->each(function (Employee $e) use ($fp) {
            fputcsv($fp, [$e->nip, $e->name, $e->department, $e->position]);
        });

        // Blank separator row
        fputcsv($fp, []);

        // Section: Kriteria
        fputcsv($fp, ['=== DATA KRITERIA ===']);
        fputcsv($fp, ['Kode', 'Nama Kriteria', 'Jenis', 'Bobot']);
        $codes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7'];
        Criterion::orderBy('id')->each(function (Criterion $c, int $i) use ($fp, $codes) {
            fputcsv($fp, [$codes[$i] ?? "C{$c->id}", $c->name, $c->type, number_format($c->weight, 2)]);
        });

        fclose($fp);
        $this->line('  ✓ karyawan_kriteria.csv');
    }

    private function exportKaryawan(string $dir): void
    {
        $fp = fopen("{$dir}/karyawan.csv", 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        fputcsv($fp, ['NIP', 'Nama Karyawan', 'Departemen', 'Jabatan']);

        Employee::orderBy('nip')->each(function (Employee $e) use ($fp) {
            fputcsv($fp, [$e->nip, $e->name, $e->department, $e->position]);
        });

        fclose($fp);
        $this->line('  ✓ karyawan.csv');
    }

    private function exportKriteria(string $dir): void
    {
        $fp = fopen("{$dir}/kriteria.csv", 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($fp, ['Kode', 'Nama Kriteria', 'Jenis', 'Bobot']);

        $codes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7'];
        Criterion::orderBy('id')->each(function (Criterion $c, int $i) use ($fp, $codes) {
            fputcsv($fp, [$codes[$i] ?? "C{$c->id}", $c->name, $c->type, number_format($c->weight, 2)]);
        });

        fclose($fp);
        $this->line('  ✓ kriteria.csv');
    }

    private function exportPenilaian(string $dir): void
    {
        $assessments = Assessment::with('details.employee', 'details.criterion')->orderBy('id')->get();
        $criteria = Criterion::orderBy('id')->get();
        $codes = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7'];

        foreach ($assessments as $assessment) {
            $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $assessment->name ?? $assessment->period);
            $fp = fopen("{$dir}/penilaian_{$safe}.csv", 'w');
            fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            $header = ['NIP', 'Nama Karyawan', 'Departemen'];
            foreach ($criteria as $i => $c) {
                $header[] = ($codes[$i] ?? "C{$c->id}").' - '.$c->name.' ('.strtoupper($c->type).')';
            }
            fputcsv($fp, $header);

            // Group details by employee
            $byEmployee = $assessment->details->groupBy('employee_id');

            Employee::orderBy('nip')->each(function (Employee $emp) use ($fp, $criteria, $byEmployee) {
                $row = [$emp->nip, $emp->name, $emp->department];
                $empDetails = $byEmployee->get($emp->id, collect())->keyBy('criterion_id');

                foreach ($criteria as $c) {
                    $row[] = $empDetails->get($c->id)?->value ?? '-';
                }

                fputcsv($fp, $row);
            });

            fclose($fp);
            $this->line("  ✓ penilaian_{$safe}.csv");
        }
    }

    private function exportRanking(string $dir): void
    {
        $assessments = Assessment::with('details.employee', 'details.criterion')->orderBy('id')->get();
        $mcdm = app(\App\Services\McdmService::class);
        $proportions = \App\Services\McdmService::loadProportions();

        foreach ($assessments as $assessment) {
            $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $assessment->name ?? $assessment->period);
            $fp = fopen("{$dir}/ranking_{$safe}.csv", 'w');
            fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($fp, ['Peringkat', 'NIP', 'Nama Karyawan', 'Departemen', 'Skor']);

            $data = $mcdm->buildMatrix($assessment);
            $scores = $mcdm->calculateTopsis($data['matrix'], $data['criteria'], $data['positions'], $proportions);

            arsort($scores);
            $rank = 1;
            foreach ($scores as $empId => $ci) {
                $emp = $data['employees']->firstWhere('id', $empId);
                if (! $emp) {
                    continue;
                }
                fputcsv($fp, [
                    $rank++,
                    $emp->nip,
                    $emp->name,
                    $emp->department,
                    round($ci, 4),
                ]);
            }

            fclose($fp);
            $this->line("  ✓ ranking_{$safe}.csv");
        }
    }
    }
}
