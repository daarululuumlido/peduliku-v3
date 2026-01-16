<?php

namespace App\Console\Commands;

use App\Models\StrukturOrganisasi;
use App\Models\UnitOrganisasi;
use App\Models\MasterJabatan;
use App\Models\PeranPegawai;
use App\Models\Orang;
use App\Models\HistoriJabatanPegawai;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportStrukturOrganisasiCommand extends Command
{
    protected $signature = 'import:struktur-organisasi {--force : Force operation without confirmation}';

    protected $description = 'Import organizational structure from struk.md and populate HRIS tables';

    private array $jabatanCache = [];
    private array $nipToOrang = [];
    private array $unitCache = [];

    public function handle()
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will import organizational structure and create HRIS records. Continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $strukFile = dirname(base_path()).'/docs/fase/struk.md';

        if (! file_exists($strukFile)) {
            $this->error("File not found: {$strukFile}");
            return;
        }

        $this->info('Parsing struk.md...');
        $sections = $this->parseStrukturMarkdown($strukFile);

        $this->info('Found '.count($sections).' organizational sections.');

        DB::beginTransaction();

        try {
            // Create struktur_organisasi for 2025/2026
            $this->info('Creating Struktur Organisasi 2025/2026...');
            $struktur = StrukturOrganisasi::create([
                'id' => Str::uuid(),
                'nama_periode' => 'Tahun Pelajaran 2025 - 2026',
                'tgl_mulai' => '2025-08-01',
                'tgl_selesai' => '2026-07-31',
                'is_active' => true,
            ]);

            // Build NIP to Orang mapping
            $this->info('Building NIP to Orang mapping...');
            $this->buildNipMapping();

            $unitsCreated = 0;
            $jabatanCreated = 0;
            $pegawaiCreated = 0;
            $historiCreated = 0;

            // Process each section
            foreach ($sections as $section) {
                $this->info("Processing section: {$section['title']}");

                // Create unit hierarchy
                $rootUnit = $this->createUnitHierarchy($struktur->id, $section);

                // Create jabatan and link to pegawai
                foreach ($section['positions'] as $position) {
                    $masterJabatan = $this->createJabatan($rootUnit['id'], $position);
                    $jabatanCreated++;

                    if (isset($this->nipToOrang[$position['nip']])) {
                        $result = $this->createPegawai($this->nipToOrang[$position['nip']], $position['nip']);
                        if ($result) {
                            $pegawai = $result['pegawai'];
                            if ($result['created']) {
                                $pegawaiCreated++;
                            }

                            // Create histori_jabatan_pegawai
                            $this->createHistoriJabatan($pegawai->id, $masterJabatan->id);
                            $historiCreated++;
                        }
                    } else {
                        $this->warn("  ⚠ NIP {$position['nip']} not found for: {$position['pejabat']}");
                    }
                }

                $unitsCreated++;
            }

            DB::commit();

            $this->newLine();
            $this->info('Import completed successfully!');
            $this->table(['Metric', 'Count'], [
                ['Sections Processed', count($sections)],
                ['Jabatan Created', $jabatanCreated],
                ['Pegawai Created', $pegawaiCreated],
                ['Histori Jabatan Created', $historiCreated],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: '.$e->getMessage());
            $this->error($e->getTraceAsString());
            return;
        }

        return 0;
    }

    private function parseStrukturMarkdown(string $file): array
    {
        $content = file_get_contents($file);
        $lines = explode("\n", $content);

        $sections = [];
        $currentSectionIndex = -1;
        $currentSubsection = null;

        for ($i = 0; $i < count($lines); $i++) {
            $line = trim($lines[$i]);

            // Skip empty lines and metadata
            if (empty($line) || str_starts_with($line, '---') || str_starts_with($line, 'id:') || str_starts_with($line, 'title:')) {
                continue;
            }

            // Detect main section (## A, ## B, etc.)
            if (preg_match('/^##\s+([A-Z])\s+(.+)$/', $line, $matches)) {
                $sections[] = [
                    'level' => trim($matches[1]),
                    'title' => trim($matches[2]),
                    'subsections' => [],
                    'positions' => [],
                ];
                $currentSectionIndex = count($sections) - 1;
                $currentSubsection = null;
                continue;
            }

            // Detect subsection (### 1, ### 2, etc.)
            if (preg_match('/^###\s+([0-9]+)\.\s+(.+)$/', $line, $matches)) {
                $currentSubsection = trim($matches[2]);
                continue;
            }

            // Detect table header
            if (str_starts_with($line, '| Jabatan ')) {
                continue;
            }

            // Parse table row with NIP
            if (preg_match('/^\|\s*(.+?)\s*\|\s*(.+?)\s*\|\s*`(.+?)`\s*\|$/', $line, $matches)) {
                $jabatan = trim($matches[1]);
                $pejabat = trim($matches[2]);
                $nip = trim($matches[3]);

                // Skip header rows
                if ($jabatan === 'Jabatan' || $jabatan === '---') {
                    continue;
                }

                if ($currentSectionIndex >= 0) {
                    $sections[$currentSectionIndex]['positions'][] = [
                        'jabatan' => $jabatan,
                        'pejabat' => $pejabat,
                        'nip' => $nip,
                        'subsection' => $currentSubsection,
                    ];
                }
            }
        }

        return $sections;
    }

    private function buildNipMapping(): void
    {
        // Get all orang with their NIP from peran_pegawai table
        $peranPegawaiList = PeranPegawai::with('orang')
            ->whereNotNull('nip')
            ->where('nip', '!=', '')
            ->get();

        foreach ($peranPegawaiList as $peranPegawai) {
            $fullNip = $peranPegawai->nip;
            
            // Store full NIP mapping
            $this->nipToOrang[$fullNip] = $peranPegawai->orang_id;
            
            // Also store last 4 digits mapping (short NIP format used in struk.md)
            $shortNip = substr($fullNip, -4);
            if (!isset($this->nipToOrang[$shortNip])) {
                $this->nipToOrang[$shortNip] = $peranPegawai->orang_id;
            }
        }

        // Also check custom_attribute for backward compatibility
        $orangList = Orang::whereNotNull('custom_attribute')
            ->where('custom_attribute', '!=', 'null')
            ->get();

        foreach ($orangList as $orang) {
            $customAttr = $orang->custom_attribute;
            if (is_string($customAttr)) {
                $customAttr = json_decode($customAttr, true);
            }
            if (isset($customAttr['nip']) && !isset($this->nipToOrang[$customAttr['nip']])) {
                $this->nipToOrang[$customAttr['nip']] = $orang->id;
            }
        }

        $this->info('Found '.count($this->nipToOrang).' NIP mappings (including short formats).');
    }

    private function createUnitHierarchy(string $strukturId, array $section): array
    {
        $unitName = $section['title'];
        $cacheKey = md5($unitName);

        if (isset($this->unitCache[$cacheKey])) {
            return $this->unitCache[$cacheKey];
        }

        // Check if unit already exists
        $unit = UnitOrganisasi::where('struktur_id', $strukturId)
            ->where('nama_unit', $unitName)
            ->first();

        if (! $unit) {
            $unit = UnitOrganisasi::create([
                'id' => Str::uuid(),
                'struktur_id' => $strukturId,
                'parent_id' => null,
                'nama_unit' => $unitName,
                'kode_unit' => $this->generateKodeUnit($unitName),
                'level_hierarki' => 0,
                'urutan' => 0,
            ]);

            $this->line("  Created unit: {$unitName}");
        }

        $this->unitCache[$cacheKey] = $unit->toArray();
        return $this->unitCache[$cacheKey];
    }

    private function createJabatan(string $unitId, array $position): MasterJabatan
    {
        $jabatanName = $position['jabatan'];
        $cacheKey = md5($jabatanName);

        if (isset($this->jabatanCache[$cacheKey])) {
            return $this->jabatanCache[$cacheKey];
        }

        // Check if jabatan already exists
        $jabatan = MasterJabatan::where('unit_organisasi_id', $unitId)
            ->where('nama_jabatan', $jabatanName)
            ->first();

        if (! $jabatan) {
            // Determine if this is a pimpinan/kepala position
            $isPimpinan = str_contains(strtolower($jabatanName), 'pimpinan') ||
                          str_contains(strtolower($jabatanName), 'kepala') ||
                          str_contains(strtolower($jabatanName), 'direktur') ||
                          str_contains(strtolower($jabatanName), 'ketua');

            $jabatan = MasterJabatan::create([
                'id' => Str::uuid(),
                'unit_organisasi_id' => $unitId,
                'nama_jabatan' => $jabatanName,
                'is_pimpinan' => $isPimpinan,
                'kuota_sdm' => 1,
            ]);

            $this->line("  Created jabatan: {$jabatanName}");
        }

        $this->jabatanCache[$cacheKey] = $jabatan;
        return $jabatan;
    }

    private function createPegawai(string $orangId, string $nip): ?array
    {
        // Check if pegawai already exists
        $pegawai = PeranPegawai::where('orang_id', $orangId)
            ->where('is_active', true)
            ->first();

        if ($pegawai) {
            return ['pegawai' => $pegawai, 'created' => false];
        }

        // Create new pegawai
        $pegawai = PeranPegawai::create([
            'id' => Str::uuid(),
            'orang_id' => $orangId,
            'nip' => $nip,
            'tgl_bergabung' => '2025-08-01',
            'tmt' => '2025-08-01',
            'status_kepegawaian' => 'Guru Tetap',
            'status_mukim' => 'TIDAK',
            'is_pengajar' => true,
            'is_active' => true,
        ]);

        $this->line("  ✓ Activated pegawai: NIP {$nip}");

        return ['pegawai' => $pegawai, 'created' => true];
    }

    private function createHistoriJabatan(string $peranPegawaiId, string $masterJabatanId): void
    {
        // Check if histori already exists to avoid duplicates
        $exists = HistoriJabatanPegawai::where('peran_pegawai_id', $peranPegawaiId)
            ->where('master_jabatan_id', $masterJabatanId)
            ->whereNull('tgl_selesai')
            ->exists();

        if ($exists) {
            return;
        }

        HistoriJabatanPegawai::create([
            'id' => Str::uuid(),
            'peran_pegawai_id' => $peranPegawaiId,
            'master_jabatan_id' => $masterJabatanId,
            'tgl_mulai' => '2025-08-01',
        ]);
    }

    private function generateKodeUnit(string $unitName): string
    {
        // Extract meaningful code from unit name
        $words = explode(' ', $unitName);
        $code = '';

        foreach ($words as $word) {
            if (strtoupper($word) === $word && strlen($word) <= 5) {
                $code .= substr($word, 0, 3);
            }
        }

        return $code ?: strtoupper(substr($unitName, 0, 3));
    }
}
