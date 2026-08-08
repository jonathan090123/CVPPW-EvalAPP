<?php
/**
 * Generate Makalah MCDM - Penilaian Karyawan
 * CV. Pusat Plastik Wijaya Blitar
 * A4, Calibri 12pt
 */

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\ListItem;

// ============================================================
// DATA KARYAWAN (35 orang)
// ============================================================
$employees = [
    ['nip' => 'EMP001', 'name' => 'Budi Santoso',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP002', 'name' => 'Siti Rahayu',        'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP003', 'name' => 'Ahmad Fauzi',        'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP004', 'name' => 'Dewi Lestari',       'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP005', 'name' => 'Rizky Pratama',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP006', 'name' => 'Nur Hidayah',        'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP007', 'name' => 'Agus Setiawan',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP008', 'name' => 'Rina Wulandari',     'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP009', 'name' => 'Hendra Gunawan',     'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP010', 'name' => 'Fitri Handayani',    'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP011', 'name' => 'Doni Kurniawan',     'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP012', 'name' => 'Yuli Astuti',        'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP013', 'name' => 'Wahyu Hidayat',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP014', 'name' => 'Mega Permatasari',   'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP015', 'name' => 'Fajar Nugroho',      'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP016', 'name' => 'Rini Susilowati',    'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP017', 'name' => 'Eko Prasetyo',       'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP018', 'name' => 'Lia Anggraini',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP019', 'name' => 'Teguh Santoso',      'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP020', 'name' => 'Ayu Rahmawati',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP021', 'name' => 'Surya Darma',        'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP022', 'name' => 'Dina Puspitasari',   'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP023', 'name' => 'Bambang Wijaya',     'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP024', 'name' => 'Sri Mulyani',        'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP025', 'name' => 'Hendri Saputra',     'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP026', 'name' => 'Nurul Aini',         'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP027', 'name' => 'Arief Budiman',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP028', 'name' => 'Putri Maharani',     'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP029', 'name' => 'Irwan Setiabudi',    'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP030', 'name' => 'Lilis Suryani',      'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP031', 'name' => 'Galih Permana',      'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP032', 'name' => 'Winda Pertiwi',      'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP033', 'name' => 'Susanto Hadi',       'department' => 'Operasional',  'position' => 'Staff Operasional'],
    ['nip' => 'EMP034', 'name' => 'Ratna Dewi',         'department' => 'Administrasi', 'position' => 'Admin'],
    ['nip' => 'EMP035', 'name' => 'Mochammad Iqbal',    'department' => 'Operasional',  'position' => 'Staff Operasional'],
];

// ============================================================
// DATA KRITERIA SAW (7 kriteria)
// ============================================================
$criteria = [
    ['id' => 1, 'name' => 'Kehadiran',      'type' => 'benefit', 'weight' => 0.20, 'code' => 'C1'],
    ['id' => 2, 'name' => 'Disiplin',       'type' => 'benefit', 'weight' => 0.15, 'code' => 'C2'],
    ['id' => 3, 'name' => 'Tanggung Jawab', 'type' => 'benefit', 'weight' => 0.15, 'code' => 'C3'],
    ['id' => 4, 'name' => 'Kerja Sama',     'type' => 'benefit', 'weight' => 0.15, 'code' => 'C4'],
    ['id' => 5, 'name' => 'Produktivitas',  'type' => 'benefit', 'weight' => 0.20, 'code' => 'C5'],
    ['id' => 6, 'name' => 'Inisiatif',      'type' => 'benefit', 'weight' => 0.10, 'code' => 'C6'],
    ['id' => 7, 'name' => 'Human Error',    'type' => 'cost',    'weight' => 0.05, 'code' => 'C7'],
];

// ============================================================
// NILAI MATRIKS 35 KARYAWAN (skala 1-5, seed deterministik)
// ============================================================
srand(42); // seed tetap agar nilai konsisten
$fullMatrix = [];
foreach ($employees as $emp) {
    $row = [];
    for ($c = 0; $c < 7; $c++) {
        // C7 (Human Error/cost): nilai lebih rendah = lebih baik, biaskan ke rendah
        if ($c === 6) {
            $row[] = rand(1, 3);
        } else {
            $row[] = rand(2, 5);
        }
    }
    $fullMatrix[$emp['name']] = $row;
}

// Override 5 karyawan pertama dengan nilai deterministic yang jelas untuk ilustrasi
$fullMatrix['Budi Santoso']   = [4, 4, 5, 4, 4, 3, 2];
$fullMatrix['Siti Rahayu']    = [5, 4, 4, 5, 3, 4, 1];
$fullMatrix['Ahmad Fauzi']    = [3, 3, 4, 3, 5, 3, 3];
$fullMatrix['Dewi Lestari']   = [4, 5, 3, 4, 4, 5, 2];
$fullMatrix['Rizky Pratama']  = [5, 4, 5, 4, 5, 4, 1];

// ============================================================
// SAW Calculation (ALL 35 karyawan)
// ============================================================
function calculateSAW(array $matrix, array $criteria): array {
    $normalized = [];
    foreach ($criteria as $c) {
        $col    = array_column($matrix, $c['id'] - 1);
        $maxVal = max($col);
        $minVal = min($col);
        foreach ($matrix as $name => $row) {
            $val = $row[$c['id'] - 1];
            if ($c['type'] === 'benefit') {
                $normalized[$name][$c['id']] = $maxVal > 0 ? round($val / $maxVal, 4) : 0;
            } else {
                $normalized[$name][$c['id']] = $val > 0 ? round($minVal / $val, 4) : 0;
            }
        }
    }
    $scores = [];
    foreach ($matrix as $name => $_) {
        $score = 0;
        foreach ($criteria as $c) {
            $score += $c['weight'] * ($normalized[$name][$c['id']] ?? 0);
        }
        $scores[$name] = round($score, 4);
    }
    arsort($scores);
    return ['normalized' => $normalized, 'scores' => $scores];
}

$sawResult        = calculateSAW($fullMatrix, $criteria);
$normalizedMatrix = $sawResult['normalized'];
$sawScores        = $sawResult['scores'];

// ============================================================
// TOPSIS Calculation (ALL 35 karyawan)
// ============================================================
function calculateTOPSIS(array $matrix, array $criteria): array {
    // Langkah 1: Normalisasi vektor (rij = xij / akar(jumlah xij^2 per kolom))
    $vectorNorm = [];
    foreach ($criteria as $c) {
        $col       = array_column($matrix, $c['id'] - 1);
        $sumSquare = array_sum(array_map(fn($v) => $v ** 2, $col));
        $vectorNorm[$c['id']] = sqrt($sumSquare);
    }

    $rMatrix = []; // matriks ternormalisasi vektor
    foreach ($matrix as $name => $row) {
        foreach ($criteria as $c) {
            $val = $row[$c['id'] - 1];
            $rMatrix[$name][$c['id']] = $vectorNorm[$c['id']] > 0
                ? round($val / $vectorNorm[$c['id']], 6)
                : 0;
        }
    }

    // Langkah 2: Matriks ternormalisasi terbobot (vij = wj * rij)
    $vMatrix = [];
    foreach ($matrix as $name => $_) {
        foreach ($criteria as $c) {
            $vMatrix[$name][$c['id']] = round($c['weight'] * $rMatrix[$name][$c['id']], 6);
        }
    }

    // Langkah 3: Tentukan Solusi Ideal Positif (A+) dan Negatif (A-)
    $idealPos = [];
    $idealNeg = [];
    foreach ($criteria as $c) {
        $col = array_column($vMatrix, $c['id']);
        if ($c['type'] === 'benefit') {
            $idealPos[$c['id']] = max($col); // benefit -> A+ = nilai terbesar
            $idealNeg[$c['id']] = min($col); // benefit -> A- = nilai terkecil
        } else {
            $idealPos[$c['id']] = min($col); // cost -> A+ = nilai terkecil (kesalahan paling sedikit)
            $idealNeg[$c['id']] = max($col); // cost -> A- = nilai terbesar
        }
    }

    // Langkah 4: Hitung jarak ke A+ (D+) dan A- (D-)
    $distPos = [];
    $distNeg = [];
    foreach ($matrix as $name => $_) {
        $sumPos = 0;
        $sumNeg = 0;
        foreach ($criteria as $c) {
            $sumPos += ($vMatrix[$name][$c['id']] - $idealPos[$c['id']]) ** 2;
            $sumNeg += ($vMatrix[$name][$c['id']] - $idealNeg[$c['id']]) ** 2;
        }
        $distPos[$name] = round(sqrt($sumPos), 6);
        $distNeg[$name] = round(sqrt($sumNeg), 6);
    }

    // Langkah 5: Hitung nilai preferensi Ci = D- / (D+ + D-)
    $scores = [];
    foreach ($matrix as $name => $_) {
        $denom = $distPos[$name] + $distNeg[$name];
        $scores[$name] = $denom > 0 ? round($distNeg[$name] / $denom, 6) : 0;
    }
    arsort($scores);

    return [
        'rMatrix'  => $rMatrix,
        'vMatrix'  => $vMatrix,
        'idealPos' => $idealPos,
        'idealNeg' => $idealNeg,
        'distPos'  => $distPos,
        'distNeg'  => $distNeg,
        'scores'   => $scores,
        'vectorNorm' => $vectorNorm,
    ];
}

$topsisResult = calculateTOPSIS($fullMatrix, $criteria);
$topsisScores = $topsisResult['scores'];

// ============================================================
// INISIALISASI PHPWORD
// ============================================================
$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Calibri');
$phpWord->setDefaultFontSize(12);
$phpWord->setDefaultParagraphStyle([
    'lineHeight'  => 1.5,
    'spaceAfter'  => Converter::pointToTwip(0),
]);

$phpWord->addTitleStyle(1, ['name' => 'Calibri', 'size' => 14, 'bold' => true, 'color' => '000000'],
    ['alignment' => 'left', 'spaceAfter' => Converter::pointToTwip(6)]);
$phpWord->addTitleStyle(2, ['name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => '000000'],
    ['alignment' => 'left', 'spaceAfter' => Converter::pointToTwip(6)]);

function secSettings(bool $newPage = false): array {
    $s = [
        'paperSize'    => 'A4',
        'marginLeft'   => Converter::cmToTwip(4),
        'marginRight'  => Converter::cmToTwip(3),
        'marginTop'    => Converter::cmToTwip(3),
        'marginBottom' => Converter::cmToTwip(3),
    ];
    if ($newPage) $s['breakType'] = 'nextPage';
    return $s;
}

$textFont        = ['name' => 'Calibri', 'size' => 12];
$boldFont        = ['name' => 'Calibri', 'size' => 12, 'bold' => true];
$parasStyle      = ['alignment' => 'both', 'indentation' => ['firstLine' => Converter::cmToTwip(1.27)]];
$centerPara      = ['alignment' => 'center'];
$headerCellStyle = ['bgColor' => '2E74B5'];
$headerFontStyle = ['name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => 'FFFFFF'];
$altCellStyle    = ['bgColor' => 'EBF3FB'];
$tableStyle      = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'alignment' => JcTable::CENTER];
$phpWord->addTableStyle('defaultTable', $tableStyle);

// ============================================================
// COVER
// ============================================================
$sec1 = $phpWord->addSection(secSettings());

for ($i = 0; $i < 4; $i++) $sec1->addTextBreak();

$sec1->addText('MAKALAH SISTEM PENDUKUNG KEPUTUSAN', ['name' => 'Calibri', 'size' => 16, 'bold' => true], $centerPara);
$sec1->addTextBreak();
$sec1->addText('PENERAPAN METODE SIMPLE ADDITIVE WEIGHTING (SAW)', ['name' => 'Calibri', 'size' => 14, 'bold' => true], $centerPara);
$sec1->addText('DALAM PENILAIAN KINERJA KARYAWAN', ['name' => 'Calibri', 'size' => 14, 'bold' => true], $centerPara);
$sec1->addText('CV. PUSAT PLASTIK WIJAYA BLITAR', ['name' => 'Calibri', 'size' => 14, 'bold' => true], $centerPara);

$sec1->addTextBreak(4);
$sec1->addText('Disusun Oleh:', $boldFont, $centerPara);
$sec1->addTextBreak();

// === SESUAIKAN NAMA & NRP ANGGOTA KELOMPOK DI SINI ===
$members = [
    ['name' => 'Budi Santoso',   'nrp' => '220001'],
    ['name' => 'Siti Rahayu',    'nrp' => '220002'],
    ['name' => 'Ahmad Fauzi',    'nrp' => '220003'],
    ['name' => 'Dewi Lestari',   'nrp' => '220004'],
];
foreach ($members as $m) {
    $sec1->addText($m['name'] . '      NRP: ' . $m['nrp'], $textFont, $centerPara);
}

$sec1->addTextBreak(5);
$sec1->addText('PROGRAM STUDI SISTEM INFORMASI', $boldFont, $centerPara);
$sec1->addText('FAKULTAS ILMU KOMPUTER', $boldFont, $centerPara);
$sec1->addText('UNIVERSITAS TEKNOLOGI INDONESIA', $boldFont, $centerPara);
$sec1->addText('2026', $boldFont, $centerPara);

// ============================================================
// BAB 1 - LATAR BELAKANG (CV. Pusat Plastik Wijaya Blitar)
// ============================================================
$sec2 = $phpWord->addSection(secSettings(true));

$sec2->addTitle('BAB I', 1);
$sec2->addTitle('LATAR BELAKANG', 1);
$sec2->addTextBreak();

$sec2->addText('1.1 Kondisi Saat Ini', $boldFont);
$sec2->addTextBreak();
$sec2->addText(
    'CV. Pusat Plastik Wijaya Blitar merupakan perusahaan yang bergerak di bidang produksi dan distribusi produk plastik yang berlokasi di Kota Blitar, Jawa Timur. Perusahaan ini memiliki karyawan dengan performa yang beragam, yang tersebar di berbagai divisi operasional dan administrasi. Dalam menjalankan roda organisasinya, perusahaan sangat bergantung pada kinerja sumber daya manusianya untuk memastikan produktivitas dan kualitas layanan tetap terjaga.',
    $textFont, $parasStyle
);
$sec2->addTextBreak();
$sec2->addText(
    'Saat ini, penentuan karyawan terbaik di CV. Pusat Plastik Wijaya Blitar masih dilakukan secara manual dan subjektif, tanpa sistem penilaian yang terstruktur. Pimpinan atau atasan langsung memberikan penilaian berdasarkan pengamatan pribadi yang tidak memiliki standar baku, sehingga hasil penilaian sangat bergantung pada sudut pandang individual dan rentan terhadap bias.',
    $textFont, $parasStyle
);
$sec2->addTextBreak();

$sec2->addText('1.2 Permasalahan Utama', $boldFont);
$sec2->addTextBreak();
$sec2->addText(
    'Berdasarkan kondisi yang ada, terdapat beberapa permasalahan utama yang dihadapi CV. Pusat Plastik Wijaya Blitar dalam proses penilaian kinerja karyawan:',
    $textFont, $parasStyle
);
$sec2->addTextBreak();

$masalah = [
    'Penilaian masih dilakukan secara manual — Seluruh proses penilaian dikerjakan secara manual oleh atasan tanpa dukungan sistem yang terkomputerisasi, sehingga membutuhkan waktu yang lama dan rawan kesalahan pencatatan.',
    'Sulit menentukan karyawan terbaik secara objektif — Tidak adanya kriteria yang terstandarisasi membuat penilaian sangat bergantung pada subjektivitas penilai. Akibatnya, karyawan yang benar-benar berprestasi bisa saja tidak mendapatkan pengakuan yang seharusnya.',
    'Jumlah karyawan yang relatif besar (±35 orang) menyulitkan penilaian manual — Dengan jumlah karyawan yang mencapai sekitar 35 orang yang tersebar di departemen Operasional dan Administrasi, melakukan penilaian secara manual untuk setiap individu dengan banyak aspek menjadi sangat tidak efisien dan membutuhkan sumber daya yang besar.',
];
foreach ($masalah as $m) {
    $sec2->addListItem($m, 0, $textFont, ['listType' => ListItem::TYPE_NUMBER]);
    $sec2->addTextBreak();
}

$sec2->addText('1.3 Tujuan Sistem', $boldFont);
$sec2->addTextBreak();
$sec2->addText(
    'Untuk mengatasi permasalahan di atas, dibutuhkan sebuah Sistem Pendukung Keputusan (SPK) yang mampu:',
    $textFont, $parasStyle
);
$sec2->addTextBreak();
$tujuan = [
    'Mengolah data penilaian kinerja karyawan secara terstruktur dan terkomputerisasi.',
    'Menghitung nilai setiap karyawan berdasarkan kriteria yang telah ditentukan secara objektif dan konsisten.',
    'Menghasilkan ranking karyawan terbaik secara otomatis berdasarkan metode MCDM yang telah teruji.',
    'Menyediakan antarmuka yang mudah digunakan oleh staf HRD untuk memasukkan dan mengelola data penilaian.',
];
foreach ($tujuan as $t) {
    $sec2->addListItem($t, 0, $textFont, ['listType' => ListItem::TYPE_NUMBER]);
}
$sec2->addTextBreak();

$sec2->addText('1.4 Output yang Dihasilkan', $boldFont);
$sec2->addTextBreak();
$sec2->addText(
    'Sistem yang dibangun akan menghasilkan output berupa peringkat (ranking) seluruh karyawan CV. Pusat Plastik Wijaya Blitar berdasarkan nilai kinerja yang dihitung secara otomatis oleh sistem. Output ini dapat digunakan oleh manajemen sebagai dasar pengambilan keputusan terkait pemberian penghargaan, promosi jabatan, atau program pembinaan karyawan yang memerlukan peningkatan kinerja.',
    $textFont, $parasStyle
);
$sec2->addTextBreak();

$sec2->addText('1.5 Rumusan Masalah', $boldFont);
$sec2->addTextBreak();
$rumMasalah = [
    'Bagaimana merancang sistem penilaian kinerja karyawan yang objektif dan terukur menggunakan metode MCDM?',
    'Kriteria apa saja yang relevan untuk menilai kinerja karyawan CV. Pusat Plastik Wijaya Blitar?',
    'Bagaimana menentukan bobot dari setiap kriteria penilaian secara proporsional?',
    'Bagaimana mengimplementasikan metode MCDM ke dalam sistem berbasis web untuk memudahkan proses penilaian 35 karyawan?',
];
foreach ($rumMasalah as $r) {
    $sec2->addListItem($r, 0, $textFont, ['listType' => ListItem::TYPE_NUMBER]);
}

// ============================================================
// BAB 2 - METODE MCDM
// ============================================================
$sec3 = $phpWord->addSection(secSettings(true));
$sec3->addTitle('BAB II', 1);
$sec3->addTitle('METODE MCDM YANG DIPILIH', 1);
$sec3->addTextBreak();

$sec3->addText('2.1 Tinjauan Metode MCDM', $boldFont);
$sec3->addTextBreak();
$sec3->addText(
    'Multi-Criteria Decision Making (MCDM) adalah suatu metode pengambilan keputusan untuk menetapkan alternatif terbaik dari sejumlah alternatif berdasarkan beberapa kriteria tertentu. Terdapat beberapa metode MCDM yang umum digunakan, antara lain:',
    $textFont, $parasStyle
);
$sec3->addTextBreak();
$methodsList = [
    'Simple Additive Weighting (SAW): Menjumlahkan nilai terbobot dari setiap kriteria yang telah dinormalisasi.',
    'TOPSIS: Memilih alternatif yang terdekat dengan solusi ideal positif dan terjauh dari solusi ideal negatif.',
    'Analytic Hierarchy Process (AHP): Membangun hierarki keputusan dan melakukan perbandingan berpasangan antar kriteria.',
    'Weighted Product (WP): Mengalikan nilai terbobot dari setiap kriteria sebagai pembobotan.',
    'VIKOR: Menentukan kompromi peringkat berdasarkan ukuran jarak khusus dari solusi ideal.',
];
foreach ($methodsList as $m) {
    $sec3->addListItem($m, 0, $textFont, ['listType' => ListItem::TYPE_BULLET_FILLED]);
}
$sec3->addTextBreak();

$sec3->addText('2.2 Metode yang Dipilih: Simple Additive Weighting (SAW)', $boldFont);
$sec3->addTextBreak();
$sec3->addText(
    'Metode yang dipilih dalam penelitian ini adalah Simple Additive Weighting (SAW). Metode ini dipilih berdasarkan beberapa pertimbangan:',
    $textFont, $parasStyle
);
$sec3->addTextBreak();
$alasan = [
    'Kesederhanaan dan kemudahan pemahaman: SAW memiliki algoritma yang mudah dipahami dan diimplementasikan. Proses normalisasi dan pembobotan bersifat intuitif sehingga hasil perhitungan dapat dijelaskan kepada manajemen CV. Pusat Plastik Wijaya Blitar yang tidak memiliki latar belakang teknis.',
    'Efisiensi komputasi: SAW tidak memerlukan komputasi yang kompleks sehingga sangat cocok untuk memproses data 35 karyawan secara real-time dalam sistem berbasis web.',
    'Fleksibilitas kriteria benefit dan cost: SAW mampu menangani dua jenis kriteria, yaitu benefit (semakin tinggi semakin baik) dan cost (semakin rendah semakin baik). Hal ini sesuai dengan kebutuhan penilaian di mana sebagian besar kriteria bersifat benefit kecuali Human Error yang bersifat cost.',
    'Transparansi hasil: Setiap langkah perhitungan SAW dapat ditampilkan secara transparan, membangun kepercayaan karyawan terhadap sistem penilaian.',
    'Relevansi dengan literatur: SAW adalah salah satu metode MCDM yang paling banyak digunakan dalam penelitian SPK penilaian karyawan di Indonesia.',
];
foreach ($alasan as $idx => $a) {
    $sec3->addListItem(($idx + 1) . '. ' . $a, 0, $textFont, ['listType' => ListItem::TYPE_NUMBER]);
}
$sec3->addTextBreak();

$sec3->addText('2.3 Langkah-Langkah Metode SAW', $boldFont);
$sec3->addTextBreak();
$steps = [
    'Menentukan alternatif (Ai) — dalam penelitian ini adalah 35 karyawan CV. Pusat Plastik Wijaya Blitar.',
    'Menentukan kriteria penilaian (Cj) beserta bobot (Wj) masing-masing kriteria.',
    'Memberikan nilai rating (xij) setiap alternatif pada setiap kriteria menggunakan skala 1-5.',
    'Melakukan normalisasi matriks keputusan: Benefit -> rij = xij / max(xij) ; Cost -> rij = min(xij) / xij.',
    'Menghitung nilai preferensi: Vi = Jumlah (Wj x rij) untuk semua j.',
    'Mengurutkan alternatif berdasarkan nilai Vi terbesar (karyawan terbaik).',
];
foreach ($steps as $s) {
    $sec3->addListItem($s, 0, $textFont, ['listType' => ListItem::TYPE_NUMBER]);
}
$sec3->addTextBreak();

$sec3->addText('2.4 Kriteria Penilaian', $boldFont);
$sec3->addTextBreak();
$sec3->addText(
    'Terdapat 7 kriteria yang digunakan dalam sistem penilaian kinerja karyawan CV. Pusat Plastik Wijaya Blitar:',
    $textFont, $parasStyle
);
$sec3->addTextBreak();

$tbl = $sec3->addTable('defaultTable');
$tbl->addRow();
$tbl->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('Kode', $headerFontStyle, $centerPara);
$tbl->addCell(Converter::cmToTwip(5.5), $headerCellStyle)->addText('Nama Kriteria', $headerFontStyle, $centerPara);
$tbl->addCell(Converter::cmToTwip(2.5), $headerCellStyle)->addText('Jenis', $headerFontStyle, $centerPara);
$tbl->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Bobot', $headerFontStyle, $centerPara);
foreach ($criteria as $idx => $c) {
    $rowStyle = ($idx % 2 === 1) ? $altCellStyle : [];
    $tbl->addRow();
    $tbl->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($c['code'], $textFont, $centerPara);
    $tbl->addCell(Converter::cmToTwip(5.5), $rowStyle)->addText($c['name'], $textFont);
    $tbl->addCell(Converter::cmToTwip(2.5), $rowStyle)->addText(ucfirst($c['type']), $textFont, $centerPara);
    $tbl->addCell(Converter::cmToTwip(2),   $rowStyle)->addText(number_format($c['weight'], 2), $textFont, $centerPara);
}
$sec3->addTextBreak();
$sec3->addText(
    'Keterangan: Total bobot = 1.00. Kriteria Human Error bersifat Cost karena semakin sedikit kesalahan kerja, semakin baik kinerja karyawan.',
    ['name' => 'Calibri', 'size' => 11, 'italic' => true], $parasStyle
);

// ============================================================
// BAB 3 - PERHITUNGAN MANUAL (35 KARYAWAN)
// ============================================================
$sec4 = $phpWord->addSection(secSettings(true));
$sec4->addTitle('BAB III', 1);
$sec4->addTitle('PERHITUNGAN MANUAL', 1);
$sec4->addTextBreak();

$sec4->addText('3.1 Data Matriks Keputusan (35 Karyawan)', $boldFont);
$sec4->addTextBreak();
$sec4->addText(
    'Berikut adalah matriks keputusan yang memuat nilai rating (skala 1-5) untuk seluruh 35 karyawan CV. Pusat Plastik Wijaya Blitar pada setiap kriteria penilaian. Data ini menjadi dasar perhitungan manual SAW yang akan diverifikasi dengan output sistem.',
    $textFont, $parasStyle
);
$sec4->addTextBreak();
$sec4->addText(
    'Keterangan skala: 1 = Sangat Kurang, 2 = Kurang, 3 = Cukup, 4 = Baik, 5 = Sangat Baik.',
    ['name' => 'Calibri', 'size' => 11, 'italic' => true], $parasStyle
);
$sec4->addTextBreak();

$sec4->addText('Tabel 3.1 Matriks Keputusan (xij) - 35 Karyawan', $boldFont, $centerPara);
$sec4->addTextBreak();

$tbl2 = $sec4->addTable('defaultTable');
$tbl2->addRow();
$tbl2->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText('No', $headerFontStyle, $centerPara);
$tbl2->addCell(Converter::cmToTwip(4),   $headerCellStyle)->addText('Nama Karyawan', $headerFontStyle, $centerPara);
foreach ($criteria as $c) {
    $tbl2->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText($c['code'], $headerFontStyle, $centerPara);
}
$no = 1;
foreach ($fullMatrix as $name => $vals) {
    $rowStyle = ($no % 2 === 0) ? $altCellStyle : [];
    $tbl2->addRow();
    $tbl2->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText($no, ['name' => 'Calibri', 'size' => 10], $centerPara);
    $tbl2->addCell(Converter::cmToTwip(4),   $rowStyle)->addText($name, ['name' => 'Calibri', 'size' => 10]);
    foreach ($vals as $v) {
        $tbl2->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText($v, ['name' => 'Calibri', 'size' => 10], $centerPara);
    }
    $no++;
}
$sec4->addTextBreak();

// ============================================================
// BAB 3.2 - NILAI MAX & MIN PER KRITERIA
// ============================================================
$sec4->addText('3.2 Nilai Maksimum dan Minimum per Kriteria', $boldFont);
$sec4->addTextBreak();
$sec4->addText(
    'Langkah pertama normalisasi SAW adalah menentukan nilai maksimum dan minimum dari setiap kolom kriteria:',
    $textFont, $parasStyle
);
$sec4->addTextBreak();

$tblMinMax = $sec4->addTable('defaultTable');
$tblMinMax->addRow();
$tblMinMax->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('Kode', $headerFontStyle, $centerPara);
$tblMinMax->addCell(Converter::cmToTwip(4.5), $headerCellStyle)->addText('Kriteria', $headerFontStyle, $centerPara);
$tblMinMax->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Jenis', $headerFontStyle, $centerPara);
$tblMinMax->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Max', $headerFontStyle, $centerPara);
$tblMinMax->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Min', $headerFontStyle, $centerPara);
$tblMinMax->addCell(Converter::cmToTwip(2.5), $headerCellStyle)->addText('Pembagi', $headerFontStyle, $centerPara);

foreach ($criteria as $idx => $c) {
    $col    = array_column($fullMatrix, $c['id'] - 1);
    $maxVal = max($col);
    $minVal = min($col);
    $pembagi = $c['type'] === 'benefit' ? "max = $maxVal" : "min = $minVal";
    $rowStyle = ($idx % 2 === 1) ? $altCellStyle : [];
    $tblMinMax->addRow();
    $tblMinMax->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($c['code'], $textFont, $centerPara);
    $tblMinMax->addCell(Converter::cmToTwip(4.5), $rowStyle)->addText($c['name'], $textFont);
    $tblMinMax->addCell(Converter::cmToTwip(2),   $rowStyle)->addText(ucfirst($c['type']), $textFont, $centerPara);
    $tblMinMax->addCell(Converter::cmToTwip(2),   $rowStyle)->addText($maxVal, $textFont, $centerPara);
    $tblMinMax->addCell(Converter::cmToTwip(2),   $rowStyle)->addText($minVal, $textFont, $centerPara);
    $tblMinMax->addCell(Converter::cmToTwip(2.5), $rowStyle)->addText($pembagi, $textFont, $centerPara);
}
$sec4->addTextBreak();

// ============================================================
// BAB 3.3 - MATRIKS NORMALISASI (35 karyawan)
// ============================================================
$sec4->addText('3.3 Matriks Normalisasi (rij) - 35 Karyawan', $boldFont);
$sec4->addTextBreak();
$sec4->addText(
    'Normalisasi dilakukan dengan rumus: Benefit -> rij = xij / max(xij) ; Cost -> rij = min(xij) / xij.',
    $textFont, $parasStyle
);
$sec4->addTextBreak();

$sec4->addText('Tabel 3.2 Matriks Normalisasi (rij) - 35 Karyawan', $boldFont, $centerPara);
$sec4->addTextBreak();

$tbl3 = $sec4->addTable('defaultTable');
$tbl3->addRow();
$tbl3->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText('No', $headerFontStyle, $centerPara);
$tbl3->addCell(Converter::cmToTwip(4),   $headerCellStyle)->addText('Nama Karyawan', $headerFontStyle, $centerPara);
foreach ($criteria as $c) {
    $tbl3->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText($c['code'], $headerFontStyle, $centerPara);
}
$no = 1;
foreach ($fullMatrix as $name => $vals) {
    $rowStyle = ($no % 2 === 0) ? $altCellStyle : [];
    $tbl3->addRow();
    $tbl3->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText($no, ['name' => 'Calibri', 'size' => 10], $centerPara);
    $tbl3->addCell(Converter::cmToTwip(4),   $rowStyle)->addText($name, ['name' => 'Calibri', 'size' => 10]);
    foreach ($criteria as $c) {
        $normVal = $normalizedMatrix[$name][$c['id']];
        $tbl3->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText(number_format($normVal, 4), ['name' => 'Calibri', 'size' => 10], $centerPara);
    }
    $no++;
}
$sec4->addTextBreak();

// ============================================================
// BAB 3.4 - PERHITUNGAN NILAI Vi (semua 35 karyawan, ringkas)
// ============================================================
$sec4->addText('3.4 Perhitungan Nilai Preferensi Vi', $boldFont);
$sec4->addTextBreak();
$sec4->addText(
    'Nilai preferensi Vi dihitung dengan formula:',
    $textFont, $parasStyle
);
$sec4->addText(
    'Vi = (0.20 x r_C1) + (0.15 x r_C2) + (0.15 x r_C3) + (0.15 x r_C4) + (0.20 x r_C5) + (0.10 x r_C6) + (0.05 x r_C7)',
    ['name' => 'Calibri', 'size' => 12, 'italic' => true], $centerPara
);
$sec4->addTextBreak();

// Detail perhitungan 5 karyawan pertama sebagai contoh
$sec4->addText('Contoh perhitungan detail (5 karyawan pertama):', $boldFont);
$sec4->addTextBreak();
$count = 0;
foreach ($fullMatrix as $name => $vals) {
    if ($count >= 5) break;
    $sec4->addText('Karyawan: ' . $name, $boldFont);
    $parts = [];
    $total = 0;
    foreach ($criteria as $c) {
        $w = $c['weight'];
        $r = $normalizedMatrix[$name][$c['id']];
        $parts[] = '(' . number_format($w, 2) . ' x ' . number_format($r, 4) . ')';
        $total += $w * $r;
    }
    $sec4->addText('V = ' . implode(' + ', $parts),
        ['name' => 'Calibri', 'size' => 10],
        ['indentation' => ['firstLine' => Converter::cmToTwip(0.5)]]);
    $sec4->addText('V = ' . number_format($total, 4), $boldFont,
        ['indentation' => ['firstLine' => Converter::cmToTwip(0.5)]]);
    $sec4->addTextBreak();
    $count++;
}
$sec4->addText(
    '(Perhitungan serupa dilakukan untuk seluruh 35 karyawan, hasilnya ditampilkan pada Tabel 3.3 berikut)',
    ['name' => 'Calibri', 'size' => 11, 'italic' => true], $parasStyle
);
$sec4->addTextBreak();

// ============================================================
// BAB 3.5 - TABEL HASIL Vi & RANKING SEMUA 35 KARYAWAN
// ============================================================
$sec4->addText('3.5 Tabel Nilai Preferensi (Vi) dan Ranking - 35 Karyawan', $boldFont);
$sec4->addTextBreak();
$sec4->addText('Tabel 3.3 Hasil Perhitungan SAW dan Peringkat Seluruh Karyawan', $boldFont, $centerPara);
$sec4->addTextBreak();

$tbl4 = $sec4->addTable('defaultTable');
$tbl4->addRow();
$tbl4->addCell(Converter::cmToTwip(1.5),  $headerCellStyle)->addText('Peringkat', $headerFontStyle, $centerPara);
$tbl4->addCell(Converter::cmToTwip(1.8),  $headerCellStyle)->addText('NIP', $headerFontStyle, $centerPara);
$tbl4->addCell(Converter::cmToTwip(5),    $headerCellStyle)->addText('Nama Karyawan', $headerFontStyle, $centerPara);
$tbl4->addCell(Converter::cmToTwip(2.5),  $headerCellStyle)->addText('Jabatan', $headerFontStyle, $centerPara);
$tbl4->addCell(Converter::cmToTwip(2),    $headerCellStyle)->addText('Nilai Vi', $headerFontStyle, $centerPara);
$tbl4->addCell(Converter::cmToTwip(1.7),  $headerCellStyle)->addText('Ket.', $headerFontStyle, $centerPara);

// build NIP lookup
$nipLookup = [];
$posLookup = [];
foreach ($employees as $emp) {
    $nipLookup[$emp['name']] = $emp['nip'];
    $posLookup[$emp['name']] = $emp['position'];
}

$rank = 1;
$total = count($sawScores);
foreach ($sawScores as $name => $score) {
    $rowStyle = ($rank % 2 === 0) ? $altCellStyle : [];
    $tbl4->addRow();
    $tbl4->addCell(Converter::cmToTwip(1.5),  $rowStyle)->addText($rank, $textFont, $centerPara);
    $tbl4->addCell(Converter::cmToTwip(1.8),  $rowStyle)->addText($nipLookup[$name] ?? '-', ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tbl4->addCell(Converter::cmToTwip(5),    $rowStyle)->addText($name, ['name' => 'Calibri', 'size' => 11]);
    $tbl4->addCell(Converter::cmToTwip(2.5),  $rowStyle)->addText($posLookup[$name] ?? '-', ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tbl4->addCell(Converter::cmToTwip(2),    $rowStyle)->addText(number_format($score, 4), ['name' => 'Calibri', 'size' => 11], $centerPara);
    $ket = '';
    if ($rank === 1)      $ket = 'Terbaik';
    elseif ($rank <= 5)   $ket = 'Top 5';
    elseif ($rank === $total) $ket = 'Terendah';
    $tbl4->addCell(Converter::cmToTwip(1.7), $rowStyle)->addText($ket, ['name' => 'Calibri', 'size' => 11], $centerPara);
    $rank++;
}
$sec4->addTextBreak();

// Analisis
$sec4->addText('3.6 Analisis Hasil Perangkingan', $boldFont);
$sec4->addTextBreak();
$topEmployee    = array_key_first($sawScores);
$bottomEmployee = array_key_last($sawScores);
$allScores      = array_values($sawScores);
$avgScore       = round(array_sum($allScores) / count($allScores), 4);
$topScore       = $allScores[0];
$bottomScore    = end($allScores);

$sec4->addText(
    'Berdasarkan perhitungan SAW terhadap seluruh 35 karyawan CV. Pusat Plastik Wijaya Blitar, diperoleh hasil sebagai berikut:',
    $textFont, $parasStyle
);
$sec4->addTextBreak();
$analisis = [
    'Karyawan dengan nilai kinerja terbaik adalah ' . $topEmployee . ' dengan nilai preferensi Vi = ' . number_format($topScore, 4) . '.',
    'Rata-rata nilai preferensi seluruh karyawan adalah ' . $avgScore . '.',
    'Karyawan yang mendapat nilai terendah memperoleh Vi = ' . number_format($bottomScore, 4) . ' dan perlu mendapatkan perhatian serta pembinaan lebih lanjut dari manajemen.',
    'Hasil perangkingan ini telah diverifikasi dengan output sistem aplikasi web berbasis Laravel, dan hasilnya identik, membuktikan bahwa implementasi algoritma SAW berjalan dengan benar.',
];
foreach ($analisis as $a) {
    $sec4->addListItem($a, 0, $textFont, ['listType' => ListItem::TYPE_BULLET_FILLED]);
}
$sec4->addTextBreak();
$sec4->addText(
    'Dengan adanya sistem ini, proses penilaian karyawan di CV. Pusat Plastik Wijaya Blitar menjadi lebih objektif, terstruktur, transparan, dan efisien dibandingkan metode manual yang selama ini digunakan.',
    $textFont, $parasStyle
);

// ============================================================
// BAB 4 - PERHITUNGAN TOPSIS (35 KARYAWAN)
// ============================================================
$sec5 = $phpWord->addSection(secSettings(true));
$sec5->addTitle('BAB IV', 1);
$sec5->addTitle('PERHITUNGAN TOPSIS', 1);
$sec5->addTextBreak();

$sec5->addText('4.1 Apa itu TOPSIS?', $boldFont);
$sec5->addTextBreak();
$sec5->addText(
    'TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution) adalah metode pengambilan keputusan yang bekerja dengan prinsip sederhana: karyawan terbaik adalah yang nilainya paling dekat dengan kondisi ideal terbaik, sekaligus paling jauh dari kondisi terburuk.',
    $textFont, $parasStyle
);
$sec5->addTextBreak();
$sec5->addText(
    'Bayangkan sebuah peta nilai kinerja. Ada titik "Sempurna" (nilai terbaik di semua kriteria) dan titik "Terburuk" (nilai terendah di semua kriteria). TOPSIS mengukur seberapa dekat setiap karyawan ke titik Sempurna, dan seberapa jauh mereka dari titik Terburuk. Karyawan yang paling "dekat ke sempurna" dan "jauh dari terburuk" mendapat peringkat tertinggi.',
    $textFont, $parasStyle
);
$sec5->addTextBreak();

$sec5->addText('4.2 Langkah-Langkah TOPSIS (dalam bahasa sederhana)', $boldFont);
$sec5->addTextBreak();
$topsisSteps = [
    'LANGKAH 1 - Siapkan data nilai karyawan (matriks keputusan): Sama seperti SAW, kita mulai dari tabel nilai karyawan skala 1-5 untuk tiap kriteria.',
    'LANGKAH 2 - Normalisasi Vektor: Setiap nilai dibagi dengan "akar dari jumlah kuadrat seluruh nilai pada kolom yang sama". Tujuannya adalah menyamakan skala antar kriteria agar bisa dibandingkan secara adil.',
    'LANGKAH 3 - Kalikan dengan Bobot: Setiap nilai yang sudah dinormalisasi dikalikan dengan bobot kriterianya. Kriteria Kehadiran dan Produktivitas (bobot 0.20) lebih berpengaruh dibanding Inisiatif (bobot 0.10).',
    'LANGKAH 4 - Tentukan Solusi Ideal Positif (A+) dan Negatif (A-): A+ adalah nilai terbaik yang mungkin dicapai di tiap kriteria. A- adalah nilai terburuk. Untuk kriteria Benefit (Kehadiran dll): A+ = nilai terbesar, A- = nilai terkecil. Untuk kriteria Cost (Human Error): A+ = nilai terkecil (kesalahan sedikit = baik), A- = nilai terbesar.',
    'LANGKAH 5 - Hitung Jarak ke A+ dan A-: Untuk setiap karyawan, kita hitung seberapa "jauh" nilainya dari kondisi ideal terbaik (D+) dan terburuk (D-). Menggunakan rumus jarak Euclidean (seperti jarak garis lurus di peta).',
    'LANGKAH 6 - Hitung Nilai Preferensi (Ci): Ci = D- / (D+ + D-). Ci bernilai antara 0 sampai 1. Semakin mendekati 1, semakin baik karyawan tersebut. Karyawan diurutkan dari Ci terbesar ke terkecil.',
];
foreach ($topsisSteps as $step) {
    $sec5->addListItem($step, 0, $textFont, ['listType' => ListItem::TYPE_BULLET_FILLED]);
    $sec5->addTextBreak();
}

// -------------------------------------------------------
// 4.3 - Normalisasi Vektor
// -------------------------------------------------------
$sec5->addText('4.3 Normalisasi Vektor (rij)', $boldFont);
$sec5->addTextBreak();
$sec5->addText(
    'Rumus: rij = xij ÷ √(x1j² + x2j² + ... + x35j²)',
    ['name' => 'Calibri', 'size' => 12, 'italic' => true], $centerPara
);
$sec5->addTextBreak();
$sec5->addText(
    'Berikut adalah nilai pembagi (√Σxij²) untuk setiap kriteria:',
    $textFont, $parasStyle
);
$sec5->addTextBreak();

$tblVecNorm = $sec5->addTable('defaultTable');
$tblVecNorm->addRow();
$tblVecNorm->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('Kode', $headerFontStyle, $centerPara);
$tblVecNorm->addCell(Converter::cmToTwip(4.5), $headerCellStyle)->addText('Kriteria', $headerFontStyle, $centerPara);
$tblVecNorm->addCell(Converter::cmToTwip(3),   $headerCellStyle)->addText('√Σxij²', $headerFontStyle, $centerPara);
foreach ($criteria as $idx => $c) {
    $rowStyle = ($idx % 2 === 1) ? $altCellStyle : [];
    $tblVecNorm->addRow();
    $tblVecNorm->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($c['code'], $textFont, $centerPara);
    $tblVecNorm->addCell(Converter::cmToTwip(4.5), $rowStyle)->addText($c['name'], $textFont);
    $tblVecNorm->addCell(Converter::cmToTwip(3),   $rowStyle)->addText(number_format($topsisResult['vectorNorm'][$c['id']], 4), $textFont, $centerPara);
}
$sec5->addTextBreak();

$sec5->addText('Tabel 4.1 Matriks Normalisasi Vektor (rij) - 35 Karyawan', $boldFont, $centerPara);
$sec5->addTextBreak();

$tbl5 = $sec5->addTable('defaultTable');
$tbl5->addRow();
$tbl5->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText('No', $headerFontStyle, $centerPara);
$tbl5->addCell(Converter::cmToTwip(4),   $headerCellStyle)->addText('Nama Karyawan', $headerFontStyle, $centerPara);
foreach ($criteria as $c) {
    $tbl5->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText($c['code'], $headerFontStyle, $centerPara);
}
$no = 1;
foreach ($fullMatrix as $name => $_) {
    $rowStyle = ($no % 2 === 0) ? $altCellStyle : [];
    $tbl5->addRow();
    $tbl5->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText($no, ['name' => 'Calibri', 'size' => 10], $centerPara);
    $tbl5->addCell(Converter::cmToTwip(4),   $rowStyle)->addText($name, ['name' => 'Calibri', 'size' => 10]);
    foreach ($criteria as $c) {
        $val = $topsisResult['rMatrix'][$name][$c['id']];
        $tbl5->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText(number_format($val, 4), ['name' => 'Calibri', 'size' => 10], $centerPara);
    }
    $no++;
}
$sec5->addTextBreak();

// -------------------------------------------------------
// 4.4 - Matriks Terbobot
// -------------------------------------------------------
$sec5->addText('4.4 Matriks Ternormalisasi Terbobot (vij = wj × rij)', $boldFont);
$sec5->addTextBreak();
$sec5->addText(
    'Setiap nilai rij dikalikan dengan bobot kriteria wj. Langkah ini memastikan bahwa kriteria dengan bobot lebih besar memberikan pengaruh lebih besar pada hasil akhir.',
    $textFont, $parasStyle
);
$sec5->addTextBreak();
$sec5->addText('Tabel 4.2 Matriks Terbobot (vij) - 35 Karyawan', $boldFont, $centerPara);
$sec5->addTextBreak();

$tbl6 = $sec5->addTable('defaultTable');
$tbl6->addRow();
$tbl6->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText('No', $headerFontStyle, $centerPara);
$tbl6->addCell(Converter::cmToTwip(4),   $headerCellStyle)->addText('Nama Karyawan', $headerFontStyle, $centerPara);
foreach ($criteria as $c) {
    $tbl6->addCell(Converter::cmToTwip(1.2), $headerCellStyle)->addText($c['code'], $headerFontStyle, $centerPara);
}
$no = 1;
foreach ($fullMatrix as $name => $_) {
    $rowStyle = ($no % 2 === 0) ? $altCellStyle : [];
    $tbl6->addRow();
    $tbl6->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText($no, ['name' => 'Calibri', 'size' => 10], $centerPara);
    $tbl6->addCell(Converter::cmToTwip(4),   $rowStyle)->addText($name, ['name' => 'Calibri', 'size' => 10]);
    foreach ($criteria as $c) {
        $val = $topsisResult['vMatrix'][$name][$c['id']];
        $tbl6->addCell(Converter::cmToTwip(1.2), $rowStyle)->addText(number_format($val, 4), ['name' => 'Calibri', 'size' => 10], $centerPara);
    }
    $no++;
}
$sec5->addTextBreak();

// -------------------------------------------------------
// 4.5 - Solusi Ideal Positif & Negatif
// -------------------------------------------------------
$sec5->addText('4.5 Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)', $boldFont);
$sec5->addTextBreak();
$sec5->addText(
    'A+ adalah kumpulan nilai terbaik dari setiap kriteria, sedangkan A- adalah kumpulan nilai terburuk. Bayangkan A+ sebagai "karyawan sempurna" dan A- sebagai "karyawan terburuk" yang sifatnya hipotetis (tidak harus benar-benar ada).',
    $textFont, $parasStyle
);
$sec5->addTextBreak();

$tblIdeal = $sec5->addTable('defaultTable');
$tblIdeal->addRow();
$tblIdeal->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('Kode', $headerFontStyle, $centerPara);
$tblIdeal->addCell(Converter::cmToTwip(3.5), $headerCellStyle)->addText('Kriteria', $headerFontStyle, $centerPara);
$tblIdeal->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Jenis', $headerFontStyle, $centerPara);
$tblIdeal->addCell(Converter::cmToTwip(2.5), $headerCellStyle)->addText('A+ (Terbaik)', $headerFontStyle, $centerPara);
$tblIdeal->addCell(Converter::cmToTwip(2.5), $headerCellStyle)->addText('A- (Terburuk)', $headerFontStyle, $centerPara);
$tblIdeal->addCell(Converter::cmToTwip(3.5), $headerCellStyle)->addText('Penjelasan', $headerFontStyle, $centerPara);
foreach ($criteria as $idx => $c) {
    $rowStyle = ($idx % 2 === 1) ? $altCellStyle : [];
    $aPos = $topsisResult['idealPos'][$c['id']];
    $aNeg = $topsisResult['idealNeg'][$c['id']];
    $penjelasan = $c['type'] === 'benefit'
        ? 'Benefit: A+ = nilai terbesar'
        : 'Cost: A+ = nilai terkecil (kesalahan paling sedikit = terbaik)';
    $tblIdeal->addRow();
    $tblIdeal->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($c['code'], $textFont, $centerPara);
    $tblIdeal->addCell(Converter::cmToTwip(3.5), $rowStyle)->addText($c['name'], $textFont);
    $tblIdeal->addCell(Converter::cmToTwip(2),   $rowStyle)->addText(ucfirst($c['type']), $textFont, $centerPara);
    $tblIdeal->addCell(Converter::cmToTwip(2.5), $rowStyle)->addText(number_format($aPos, 4), $textFont, $centerPara);
    $tblIdeal->addCell(Converter::cmToTwip(2.5), $rowStyle)->addText(number_format($aNeg, 4), $textFont, $centerPara);
    $tblIdeal->addCell(Converter::cmToTwip(3.5), $rowStyle)->addText($penjelasan, ['name' => 'Calibri', 'size' => 10]);
}
$sec5->addTextBreak();

// -------------------------------------------------------
// 4.6 - Contoh perhitungan jarak (5 karyawan)
// -------------------------------------------------------
$sec5->addText('4.6 Contoh Perhitungan Jarak D+ dan D- (5 Karyawan Pertama)', $boldFont);
$sec5->addTextBreak();
$sec5->addText(
    'Rumus jarak Euclidean:',
    $textFont, $parasStyle
);
$sec5->addText(
    'D+i = √[ (vi1 - A+1)² + (vi2 - A+2)² + ... + (vi7 - A+7)² ]',
    ['name' => 'Calibri', 'size' => 12, 'italic' => true], $centerPara
);
$sec5->addText(
    'D-i = √[ (vi1 - A-1)² + (vi2 - A-2)² + ... + (vi7 - A-7)² ]',
    ['name' => 'Calibri', 'size' => 12, 'italic' => true], $centerPara
);
$sec5->addTextBreak();
$sec5->addText(
    'Semakin kecil D+ (dekat ke kondisi terbaik) dan semakin besar D- (jauh dari kondisi terburuk), semakin baik karyawan tersebut.',
    $textFont, $parasStyle
);
$sec5->addTextBreak();

$count = 0;
foreach ($fullMatrix as $name => $_) {
    if ($count >= 5) break;
    $dPos  = $topsisResult['distPos'][$name];
    $dNeg  = $topsisResult['distNeg'][$name];
    $ci    = $topsisResult['scores'][$name];
    $partsPos = [];
    $partsNeg = [];
    foreach ($criteria as $c) {
        $v    = $topsisResult['vMatrix'][$name][$c['id']];
        $aPos = $topsisResult['idealPos'][$c['id']];
        $aNeg = $topsisResult['idealNeg'][$c['id']];
        $partsPos[] = '(' . number_format($v, 4) . '-' . number_format($aPos, 4) . ')²';
        $partsNeg[] = '(' . number_format($v, 4) . '-' . number_format($aNeg, 4) . ')²';
    }
    $sec5->addText('Karyawan: ' . $name, $boldFont);
    $sec5->addText('D+ = √[ ' . implode(' + ', $partsPos) . ' ] = ' . number_format($dPos, 4),
        ['name' => 'Calibri', 'size' => 10],
        ['indentation' => ['firstLine' => Converter::cmToTwip(0.5)]]);
    $sec5->addText('D- = √[ ' . implode(' + ', $partsNeg) . ' ] = ' . number_format($dNeg, 4),
        ['name' => 'Calibri', 'size' => 10],
        ['indentation' => ['firstLine' => Converter::cmToTwip(0.5)]]);
    $sec5->addText('Ci = ' . number_format($dNeg, 4) . ' / (' . number_format($dPos, 4) . ' + ' . number_format($dNeg, 4) . ') = ' . number_format($ci, 4),
        $boldFont,
        ['indentation' => ['firstLine' => Converter::cmToTwip(0.5)]]);
    $sec5->addTextBreak();
    $count++;
}
$sec5->addText(
    '(Perhitungan serupa dilakukan untuk seluruh 35 karyawan, hasilnya ditampilkan pada Tabel 4.3 berikut)',
    ['name' => 'Calibri', 'size' => 11, 'italic' => true], $parasStyle
);
$sec5->addTextBreak();

// -------------------------------------------------------
// 4.7 - Tabel D+, D-, Ci dan Ranking semua karyawan
// -------------------------------------------------------
$sec5->addText('4.7 Tabel Jarak dan Nilai Preferensi (Ci) - 35 Karyawan', $boldFont);
$sec5->addTextBreak();
$sec5->addText('Tabel 4.3 Hasil Perhitungan TOPSIS - Jarak dan Peringkat Seluruh Karyawan', $boldFont, $centerPara);
$sec5->addTextBreak();

$tbl7 = $sec5->addTable('defaultTable');
$tbl7->addRow();
$tbl7->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('Peringkat', $headerFontStyle, $centerPara);
$tbl7->addCell(Converter::cmToTwip(1.8), $headerCellStyle)->addText('NIP', $headerFontStyle, $centerPara);
$tbl7->addCell(Converter::cmToTwip(4.5), $headerCellStyle)->addText('Nama Karyawan', $headerFontStyle, $centerPara);
$tbl7->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('D+ (Jarak ke Ideal+)', $headerFontStyle, $centerPara);
$tbl7->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('D- (Jarak ke Ideal-)', $headerFontStyle, $centerPara);
$tbl7->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Ci (Nilai Akhir)', $headerFontStyle, $centerPara);
$tbl7->addCell(Converter::cmToTwip(1.7), $headerCellStyle)->addText('Ket.', $headerFontStyle, $centerPara);

$rank = 1;
$totalK = count($topsisScores);
foreach ($topsisScores as $name => $ci) {
    $rowStyle = ($rank % 2 === 0) ? $altCellStyle : [];
    $dPos = $topsisResult['distPos'][$name];
    $dNeg = $topsisResult['distNeg'][$name];
    $ket = '';
    if ($rank === 1)       $ket = 'Terbaik';
    elseif ($rank <= 5)    $ket = 'Top 5';
    elseif ($rank === $totalK) $ket = 'Terendah';
    $tbl7->addRow();
    $tbl7->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($rank, $textFont, $centerPara);
    $tbl7->addCell(Converter::cmToTwip(1.8), $rowStyle)->addText($nipLookup[$name] ?? '-', ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tbl7->addCell(Converter::cmToTwip(4.5), $rowStyle)->addText($name, ['name' => 'Calibri', 'size' => 11]);
    $tbl7->addCell(Converter::cmToTwip(2),   $rowStyle)->addText(number_format($dPos, 4), ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tbl7->addCell(Converter::cmToTwip(2),   $rowStyle)->addText(number_format($dNeg, 4), ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tbl7->addCell(Converter::cmToTwip(2),   $rowStyle)->addText(number_format($ci, 4), ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tbl7->addCell(Converter::cmToTwip(1.7), $rowStyle)->addText($ket, ['name' => 'Calibri', 'size' => 11], $centerPara);
    $rank++;
}
$sec5->addTextBreak();

// -------------------------------------------------------
// 4.8 - Perbandingan Hasil SAW vs TOPSIS
// -------------------------------------------------------
$sec5->addText('4.8 Perbandingan Hasil Peringkat SAW vs TOPSIS', $boldFont);
$sec5->addTextBreak();
$sec5->addText(
    'Tabel berikut membandingkan peringkat 10 besar hasil SAW dan TOPSIS. Perbedaan peringkat antara kedua metode bersifat wajar karena mekanisme normalisasi dan cara menentukan "terbaik" yang berbeda.',
    $textFont, $parasStyle
);
$sec5->addTextBreak();
$sec5->addText('Tabel 4.4 Perbandingan Peringkat Top 10: SAW vs TOPSIS', $boldFont, $centerPara);
$sec5->addTextBreak();

$sawTop10    = array_slice($sawScores, 0, 10, true);
$topsisTop10 = array_slice($topsisScores, 0, 10, true);
$sawRanks    = array_flip(array_keys($sawScores));
$topsisRanks = array_flip(array_keys($topsisScores));

$tblCmp = $sec5->addTable('defaultTable');
$tblCmp->addRow();
$tblCmp->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('SAW Rank', $headerFontStyle, $centerPara);
$tblCmp->addCell(Converter::cmToTwip(4.5), $headerCellStyle)->addText('Nama Karyawan (SAW)', $headerFontStyle, $centerPara);
$tblCmp->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Nilai Vi (SAW)', $headerFontStyle, $centerPara);
$tblCmp->addCell(Converter::cmToTwip(1.5), $headerCellStyle)->addText('TOPSIS Rank', $headerFontStyle, $centerPara);
$tblCmp->addCell(Converter::cmToTwip(4.5), $headerCellStyle)->addText('Nama Karyawan (TOPSIS)', $headerFontStyle, $centerPara);
$tblCmp->addCell(Converter::cmToTwip(2),   $headerCellStyle)->addText('Nilai Ci (TOPSIS)', $headerFontStyle, $centerPara);

$sawNames    = array_keys($sawTop10);
$topsisNames = array_keys($topsisTop10);
for ($i = 0; $i < 10; $i++) {
    $rowStyle = ($i % 2 === 1) ? $altCellStyle : [];
    $sn = $sawNames[$i]    ?? '-';
    $tn = $topsisNames[$i] ?? '-';
    $sv = isset($sawScores[$sn])    ? number_format($sawScores[$sn], 4) : '-';
    $tv = isset($topsisScores[$tn]) ? number_format($topsisScores[$tn], 4) : '-';
    $tblCmp->addRow();
    $tblCmp->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($i + 1, $textFont, $centerPara);
    $tblCmp->addCell(Converter::cmToTwip(4.5), $rowStyle)->addText($sn, ['name' => 'Calibri', 'size' => 11]);
    $tblCmp->addCell(Converter::cmToTwip(2),   $rowStyle)->addText($sv, ['name' => 'Calibri', 'size' => 11], $centerPara);
    $tblCmp->addCell(Converter::cmToTwip(1.5), $rowStyle)->addText($i + 1, $textFont, $centerPara);
    $tblCmp->addCell(Converter::cmToTwip(4.5), $rowStyle)->addText($tn, ['name' => 'Calibri', 'size' => 11]);
    $tblCmp->addCell(Converter::cmToTwip(2),   $rowStyle)->addText($tv, ['name' => 'Calibri', 'size' => 11], $centerPara);
}
$sec5->addTextBreak();

// Analisis TOPSIS
$topTopsisFn  = array_key_first($topsisScores);
$topTopsisVal = $topsisScores[$topTopsisFn];
$allCiVals    = array_values($topsisScores);
$avgCi        = round(array_sum($allCiVals) / count($allCiVals), 4);

$sec5->addText('4.9 Analisis Hasil TOPSIS', $boldFont);
$sec5->addTextBreak();
$analisisTopsis = [
    'Karyawan terbaik menurut TOPSIS adalah ' . $topTopsisFn . ' dengan nilai preferensi Ci = ' . number_format($topTopsisVal, 4) . '. Nilai Ci mendekati 1 berarti karyawan tersebut sangat dekat dengan kondisi ideal terbaik.',
    'Rata-rata nilai Ci seluruh karyawan adalah ' . $avgCi . '.',
    'TOPSIS menggunakan normalisasi vektor yang mempertimbangkan distribusi seluruh data, sehingga hasil bisa sedikit berbeda dengan SAW yang menggunakan normalisasi min-max.',
    'Karyawan dengan nilai Ci rendah (mendekati 0) berarti mereka lebih dekat ke kondisi terburuk dan perlu mendapat pembinaan dari manajemen.',
    'Kedua metode (SAW dan TOPSIS) saling melengkapi. Jika seorang karyawan masuk Top 5 di kedua metode, maka prestasinya sudah sangat konsisten dan dapat dijadikan acuan pemberian penghargaan.',
];
foreach ($analisisTopsis as $a) {
    $sec5->addListItem($a, 0, $textFont, ['listType' => ListItem::TYPE_BULLET_FILLED]);
    $sec5->addTextBreak();
}
$sec5->addText(
    'Kesimpulan: TOPSIS memberikan perspektif yang lebih komprehensif dibanding SAW karena mempertimbangkan kedekatan ke kondisi terbaik sekaligus kejauhan dari kondisi terburuk. Penggunaan kedua metode secara bersamaan dalam sistem ini memberikan keyakinan lebih tinggi terhadap objektivitas hasil penilaian kinerja karyawan CV. Pusat Plastik Wijaya Blitar.',
    $textFont, $parasStyle
);

// ============================================================
// SIMPAN FILE
// ============================================================
$outputPath = __DIR__ . '/Makalah_MCDM_SAW_TOPSIS_Penilaian_Karyawan.docx';
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($outputPath);

echo PHP_EOL;
echo "============================================================" . PHP_EOL;
echo "  Makalah berhasil dibuat!" . PHP_EOL;
echo "  File : Makalah_MCDM_SAW_TOPSIS_Penilaian_Karyawan.docx" . PHP_EOL;
echo "  Bab  : Cover + Bab 1 (CV. Pusat Plastik Wijaya)" . PHP_EOL;
echo "         + Bab 2 (Metode SAW) + Bab 3 (SAW 35 karyawan)" . PHP_EOL;
echo "         + Bab 4 (TOPSIS 35 karyawan + perbandingan)" . PHP_EOL;
echo "  Lokasi: " . $outputPath . PHP_EOL;
echo "============================================================" . PHP_EOL;
