<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimpleSantriImportCommand extends Command
{
    protected $signature = 'import:simple-santri {--force : Force operation to run without confirmation}';

    protected $description = 'Simple santri import from pesantren_cyber database';

    public function handle()
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will import santri data. Do you wish to continue?')) {
                $this->info('Operation cancelled.');

                return;
            }
        }

        $this->info('Starting simple santri import...');

        // Configure source database
        $sourceConfig = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'pesantren_cyber',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];

        Config::set('database.connections.source', $sourceConfig);

        try {
            DB::connection('source')->getPdo();
            $this->info('Connected to source database successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to connect to source database: '.$e->getMessage());

            return;
        }

        // Get santri data with school/class info
        $this->info('Fetching santri data...');
        $query = "SELECT master_santri.* 
                   FROM master_rombel_siswa 
                   RIGHT JOIN master_santri ON master_rombel_siswa.id_santri=master_santri.id 
                   LEFT JOIN master_rombel ON master_rombel.id=master_rombel_siswa.id_rombel 
                   LEFT JOIN master_kelas ON master_kelas.id=master_rombel.id_kelas 
                   LEFT JOIN master_sekolah ON master_sekolah.id=master_kelas.id_sekolah 
                   LEFT JOIN master_ajaran ON master_ajaran.id=master_rombel.tahun_ajaran 
                   WHERE (master_sekolah.sekolah='TMI') AND master_ajaran.`status`='Y'";

        $santriData = DB::connection('source')->select($query);

        $this->info('Found '.count($santriData).' santri records from TMI.');

        // Clear existing data
        $this->info('Clearing existing data...');
        DB::statement('DELETE FROM orang;');
        DB::statement('DELETE FROM alamat;');
        DB::statement('DELETE FROM kartu_keluarga;');
        DB::statement('DELETE FROM kartu_keluarga_anggota;');
        $this->info('Tables cleared.');


        // Prepare data arrays
        $peopleToInsert = [];
        $addressesToInsert = [];
        $familyCards = [];
        $familyMembersToInsert = [];
        $usedNiks = [];

        $totalRecords = count($santriData);
        $validRecords = 0;
        $skippedRecords = 0;
        $reasons = [];
        $usedNiks = [];

        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        foreach ($santriData as $santri) {
            $santri = (array) $santri;

            // Skip if missing required fields
            if (empty($santri['nomorkk']) || empty($santri['nama_ayah']) || empty($santri['nama_ibu'])) {
                $skippedRecords++;
                $bar->advance();

                continue;
            }

            $validRecords++;

            // Create address if available
            $alamatId = null;
            if (! empty($santri['alamat_orangtua'])) {
                $alamatId = Str::uuid();
                $addressesToInsert[] = [
                    'id' => $alamatId,
                    'desa_id' => null,
                    'alamat_lengkap' => $santri['alamat_orangtua'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Create family card if available
            $kartuKeluargaId = null;
            if (! empty($santri['nomorkk'])) {
                if (! isset($familyCards[$santri['nomorkk']])) {
                    $kartuKeluargaId = Str::uuid();
                    $familyCards[$santri['nomorkk']] = [
                        'id' => $kartuKeluargaId,
                        'no_kk' => $santri['nomorkk'],
                        'alamat_id' => $alamatId,
                        'kepala_keluarga_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $kartuKeluargaId = $familyCards[$santri['nomorkk']]['id'];
                    $alamatId = $familyCards[$santri['nomorkk']]['alamat_id'];
                }
            }

            // Prepare santri person data
            $santriId = Str::uuid();
            $santriNik = ! empty($santri['nik']) && strlen($santri['nik']) === 16
                ? $santri['nik']
                : $this->generateValidNik();

            if (in_array($santriNik, $usedNiks)) {
                $santriNik = $this->generateValidNik();
            }
            $usedNiks[] = $santriNik;

            $santriData = [
                'id' => $santriId,
                'nama' => ! empty($santri['nama']) ? $santri['nama'] : 'Unknown',
                'nik' => $santriNik,
                'gelar_depan' => null,
                'gelar_belakang' => null,
                'gender' => ! empty($santri['jk']) ? ($santri['jk'] === 'L' ? 'L' : 'P') : 'L',
                'tanggal_lahir' => (! empty($santri['tanggal_lahir']) && $santri['tanggal_lahir'] !== '0000-00-00' && $santri['tanggal_lahir'] !== '') ? Carbon::parse($santri['tanggal_lahir'])->format('Y-m-d') : '1970-01-01',
                'tempat_lahir' => ! empty($santri['tempat_lahir']) ? $santri['tempat_lahir'] : 'TIDAK DIKETAHUI',
                'nama_ibu_kandung' => ! empty($santri['nama_ibu']) ? $santri['nama_ibu'] : null,
                'no_whatsapp' => ! empty($santri['handphone_ayah']) ? $santri['handphone_ayah'] : null,
                'alamat_ktp_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $peopleToInsert[] = $santriData;

            // Prepare ayah person data
            $ayahId = Str::uuid();
            $ayahNik = ! empty($santri['nik_ayah']) && strlen($santri['nik_ayah']) === 16
                ? $santri['nik_ayah']
                : $this->generateValidNik();

            if (in_array($ayahNik, $usedNiks)) {
                $ayahNik = $this->generateValidNik();
            }
            $usedNiks[] = $ayahNik;

            $ayahTtl = $this->parseTtl($santri['ttl_ayah'] ?? null);

            $ayahData = [
                'id' => $ayahId,
                'nama' => $santri['nama_ayah'],
                'nik' => $ayahNik,
                'gelar_depan' => null,
                'gelar_belakang' => null,
                'gender' => 'L',
                'tanggal_lahir' => $this->parseDateFromTtl($ayahTtl['tanggal']),
                'tempat_lahir' => ! empty($ayahTtl['tempat']) ? $ayahTtl['tempat'] : 'TIDAK DIKETAHUI',
                'nama_ibu_kandung' => null,
                'no_whatsapp' => ! empty($santri['nomor_ayah']) ? $santri['nomor_ayah'] : null,
                'alamat_ktp_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $peopleToInsert[] = $ayahData;

            // Prepare ibu person data
            $ibuId = Str::uuid();
            $ibuNik = ! empty($santri['nik_ibu']) && strlen($santri['nik_ibu']) === 16
                ? $santri['nik_ibu']
                : $this->generateValidNik();

            if (in_array($ibuNik, $usedNiks)) {
                $ibuNik = $this->generateValidNik();
            }
            $usedNiks[] = $ibuNik;

            $ibuTtl = $this->parseTtl($santri['ttl_ibu'] ?? null);

            $ibuData = [
                'id' => $ibuId,
                'nama' => $santri['nama_ibu'],
                'nik' => $ibuNik,
                'gelar_depan' => null,
                'gelar_belakang' => null,
                'gender' => 'P',
                'tanggal_lahir' => $this->parseDateFromTtl($ibuTtl['tanggal']),
                'tempat_lahir' => ! empty($ibuTtl['tempat']) ? $ibuTtl['tempat'] : 'TIDAK DIKETAHUI',
                'nama_ibu_kandung' => null,
                'no_whatsapp' => ! empty($santri['nomor_ibu']) ? $santri['nomor_ibu'] : null,
                'alamat_ktp_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $peopleToInsert[] = $ibuData;

            // Add family members if family card exists
            if ($kartuKeluargaId) {
                // Ayah as kepala_keluarga
                $familyMembersToInsert[] = [
                    'id' => Str::uuid(),
                    'kartu_keluarga_id' => $kartuKeluargaId,
                    'orang_id' => $ayahId,
                    'status_hubungan' => 'kepala_keluarga',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Ibu as istri
                $familyMembersToInsert[] = [
                    'id' => Str::uuid(),
                    'kartu_keluarga_id' => $kartuKeluargaId,
                    'orang_id' => $ibuId,
                    'status_hubungan' => 'istri',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Santri as anak
                $familyMembersToInsert[] = [
                    'id' => Str::uuid(),
                    'kartu_keluarga_id' => $kartuKeluargaId,
                    'orang_id' => $santriId,
                    'status_hubungan' => 'anak',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($skippedRecords > 0) {
            $this->warn("Skipped {$skippedRecords} records due to missing required data.");
        }

        DB::beginTransaction();

        try {
            // Insert people in batches
            $this->info('Inserting people...');
            collect($peopleToInsert)->chunk(100)->each(function ($chunk) {
                try {
                    DB::table('orang')->insert($chunk->toArray());
                    $this->line('Inserted '.$chunk->count().' records');
                } catch (\Exception $e) {
                    $this->error('Failed to insert people: '.$e->getMessage());
                }
            });

            // Insert addresses
            if (! empty($addressesToInsert)) {
                $this->info('Inserting addresses...');
                collect($addressesToInsert)->chunk(100)->each(function ($chunk) {
                    try {
                        DB::table('alamat')->insert($chunk->toArray());
                    } catch (\Exception $e) {
                        $this->error('Failed to insert addresses: '.$e->getMessage());
                    }
                });
            }

            // Insert family cards
            if (! empty($familyCards)) {
                $this->info('Inserting family cards...');
                collect($familyCards)->chunk(100)->each(function ($chunk) {
                    try {
                        DB::table('kartu_keluarga')->insert($chunk->toArray());
                    } catch (\Exception $e) {
                        $this->error('Failed to insert family cards: '.$e->getMessage());
                    }
                });
            }

            // Insert family members
            if (! empty($familyMembersToInsert)) {
                $this->info('Creating family member relationships...');
                
                // Filter out family members with invalid kartu_keluarga_id
                $validFamilyCards = collect($familyCards)->pluck('id')->toArray();
                $filteredMembers = collect($familyMembersToInsert)->filter(function ($member) use ($validFamilyCards) {
                    return in_array($member['kartu_keluarga_id'], $validFamilyCards);
                })->toArray();
                
                $skippedMembers = count($familyMembersToInsert) - count($filteredMembers);
                if ($skippedMembers > 0) {
                    $this->warn("Skipped {$skippedMembers} family members with invalid kartu_keluarga_id");
                }
                
                collect($filteredMembers)->chunk(100)->each(function ($chunk) {
                    try {
                        DB::table('kartu_keluarga_anggota')->insert($chunk->toArray());
                    } catch (\Exception $e) {
                        $this->error('Failed to insert family members: '.$e->getMessage());
                    }
                });
            }

            // Basic relationship assignment
            $this->info('Assigning basic relationships...');
            $headsUpdated = 0;
            foreach ($familyCards as $noKk => $kk) {
                // Find first person in this family
                $firstPerson = DB::table('kartu_keluarga_anggota as kka')
                    ->join('orang as o', 'kka.orang_id', '=', 'o.id')
                    ->where('kka.kartu_keluarga_id', $kk['id'])
                    ->first();

                if ($firstPerson) {
                    DB::table('kartu_keluarga')
                        ->where('id', $kk['id'])
                        ->update(['kepala_keluarga_id' => $firstPerson->orang_id]);
                    DB::table('kartu_keluarga_anggota')
                        ->where('kartu_keluarga_id', $kk['id'])
                        ->where('orang_id', $firstPerson->orang_id)
                        ->update(['status_hubungan' => 'kepala_keluarga']);
                    $headsUpdated++;
                } else {
                    // If no members assigned, assign first person from the kartu_keluarga array
                    if (isset($peopleToInsert[0])) {
                        // This is a fallback, though we should have members from the above logic
                    }
                }
            }

            $this->info("Updated {$headsUpdated} head of family relationships.");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: '.$e->getMessage());
            throw $e;
        }

        return 0;
    }

    private function parseTtl($ttl)
    {
        $result = ['tempat' => 'TIDAK DIKETAHUI', 'tanggal' => null];

        if (empty($ttl)) {
            return $result;
        }

        $parts = explode(',', $ttl);

        if (count($parts) >= 2) {
            $tempat = trim($parts[0]);
            $result['tempat'] = ! empty($tempat) ? $tempat : 'TIDAK DIKETAHUI';

            $dateStr = trim($parts[1]);

            if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $dateStr, $matches)) {
                $result['tanggal'] = $matches[1].'-'.$matches[2].'-'.$matches[3];
            } elseif (preg_match('/(\d{2})-(\d{2})-(\d{4})/', $dateStr, $matches)) {
                $result['tanggal'] = $matches[3].'-'.$matches[2].'-'.$matches[1];
            } elseif (preg_match('/(\d{4})(\d{2})(\d{2})/', $dateStr, $matches)) {
                $result['tanggal'] = $matches[1].'-'.$matches[2].'-'.$matches[3];
            } elseif (preg_match('/(\d{2})-(\d{2})-(\d{2})/', $dateStr, $matches)) {
                $year = $matches[3];
                $year = ($year > 30) ? '19'.$year : '20'.$year;
                $result['tanggal'] = $year.'-'.$matches[1].'-'.$matches[2];
            } elseif (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $dateStr, $matches)) {
                $result['tanggal'] = $matches[3].'-'.$matches[2].'-'.$matches[1];
            } elseif (preg_match('/(\d{4})\/(\d{2})\/(\d{2})/', $dateStr, $matches)) {
                $result['tanggal'] = $matches[1].'-'.$matches[2].'-'.$matches[3];
            }
        }

        return $result;
    }

    private function parseDateFromTtl($dateString)
    {
        if (empty($dateString)) {
            return '1970-01-01';
        }

        try {
            // Try ISO date format YYYY-MM-DD
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateString, $matches)) {
                return $dateString;
            }
            // Try Indonesian format DD-MM-YYYY
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $dateString, $matches)) {
                return $matches[3].'-'.$matches[2].'-'.$matches[1];
            }
            // Try YYYYMMDD
            if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $dateString, $matches)) {
                return $matches[1].'-'.$matches[2].'-'.$matches[3];
            }
            // Try DD-MM-YY (short year)
            if (preg_match('/^(\d{2})-(\d{2})-(\d{2})$/', $dateString, $matches)) {
                $year = $matches[3];
                $fullYear = ($year > 30) ? '19'.$year : '20'.$year;

                return $fullYear.'-'.$matches[1].'-'.$matches[2];
            }
            // Try DD/MM/YYYY
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateString, $matches)) {
                return $matches[3].'-'.$matches[2].'-'.$matches[1];
            }
            // Try YYYY/MM/DD
            if (preg_match('/^(\d{4})\/(\d{2})\/(\d{2})$/', $dateString, $matches)) {
                return $matches[1].'-'.$matches[2].'-'.$matches[3];
            }
        } catch (\Exception $e) {
            // If parsing fails, return default date
        }

        return '1970-01-01';
    }

    private function generateValidNik(): string
    {
        $province = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
        $regency = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
        $district = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);

        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $year = str_pad(rand(70, 8), 2, '0', STR_PAD_LEFT);

        $serial = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $province.$regency.$district.$day.$month.$year.$serial;
    }
}
