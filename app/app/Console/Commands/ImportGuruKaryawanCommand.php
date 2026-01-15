<?php

namespace App\Console\Commands;

use App\Models\Alamat;
use App\Models\Orang;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class ImportGuruKaryawanCommand extends Command
{
    protected $signature = 'import:guru-karyawan {file? : Path to CSV file} {--force : Force operation without confirmation}';

    protected $description = 'Import Guru dan Karyawan from CSV file to orang table';

    public function handle()
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will import/update guru dan karyawan data. Do you wish to continue?')) {
                $this->info('Operation cancelled.');

                return;
            }
        }

        $filePath = $this->argument('file') ?? base_path('BIODATA GURU DAN KARYAWAN - data.csv');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return;
        }

        $this->info('Reading CSV file: '.$filePath);

        $csvData = $this->parseCsv($filePath);

        if (empty($csvData)) {
            $this->error('No data found in CSV file.');

            return;
        }

        $this->info('Found '.count($csvData).' records in CSV file.');

        $activeRecords = array_filter($csvData, function ($row) {
            return isset($row['Status Saat Ini']) && strcasecmp(trim($row['Status Saat Ini']), 'Aktif') === 0;
        });

        $this->info('Found '.count($activeRecords).' active records (Status Saat Ini = Aktif).');

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'addresses_created' => 0,
            'addresses_linked' => 0,
        ];

        $bar = $this->output->createProgressBar(count($activeRecords));
        $bar->start();

        DB::beginTransaction();

        try {
            foreach ($activeRecords as $row) {
                try {
                    $nik = $this->cleanNik($row['NIK'] ?? '');

                    if (empty($nik) || strlen($nik) !== 16) {
                        $this->warn("Invalid NIK: {$row['NIK']} for {$row['NAMA']}");
                        $stats['skipped']++;
                        $bar->advance();

                        continue;
                    }

                    $orang = Orang::withTrashed()->where('nik', $nik)->first();

                    $alamatId = null;
                    $alamatRaw = $row['ALAMAT'] ?? '';

                    if (! empty($alamatRaw)) {
                        $locationData = $this->findDesaId($alamatRaw);
                        $desaId = $locationData['desa_id'] ?? null;
                        $alamatLengkap = $this->cleanAlamatLengkap($alamatRaw, $locationData);

                        $alamat = Alamat::where('alamat_lengkap', $alamatLengkap)
                            ->where('desa_id', $desaId)
                            ->first();

                        if (! $alamat) {
                            $alamat = Alamat::create([
                                'id' => Str::uuid(),
                                'desa_id' => $desaId,
                                'alamat_lengkap' => $alamatLengkap,
                            ]);
                            $stats['addresses_created']++;
                        }

                        $alamatId = $alamat->id;
                        $stats['addresses_linked']++;
                    }

                    $data = [
                        'nama' => $this->cleanName($row['NAMA'] ?? ''),
                        'gelar_depan' => $this->cleanTitle($row['GELAR DEPAN'] ?? ''),
                        'gelar_belakang' => $this->cleanTitle($row['GELAR BELAKANG'] ?? ''),
                        'gender' => $this->parseGender($row['Jenis_Kelamin'] ?? $row['Kelamin'] ?? ''),
                        'tanggal_lahir' => $this->parseDate($row['TANGGAL LAHIR'] ?? $row['tahun lahir'].'-'.$row['bulan lahir'].'-'.$row['tanggal lahir'] ?? ''),
                        'tempat_lahir' => trim($row['TEMPAT LAHIR'] ?? 'TIDAK DIKETAHUI'),
                        'nama_ibu_kandung' => $row['Nama Ibu Kandung'] ?? null,
                        'no_whatsapp' => $this->cleanPhone($row['Nomor WHATSAPP'] ?? ''),
                        'alamat_ktp_id' => $alamatId,
                    ];

                    if ($orang) {
                        $orang->update($data);
                        $this->line("Updated: {$data['nama']} (NIK: {$nik})");
                        $stats['updated']++;
                    } else {
                        $data['nik'] = $nik;
                        $data['id'] = Str::uuid();
                        Orang::create($data);
                        $this->line("Created: {$data['nama']} (NIK: {$nik})");
                        $stats['created']++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing {$row['NAMA']}: ".$e->getMessage());
                    $stats['errors']++;
                }

                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: '.$e->getMessage());
            $bar->finish();

            return;
        }

        $bar->finish();
        $this->newLine();

        $this->info('Import completed successfully!');
        $this->table(['Status', 'Count'], [
            ['Created', $stats['created']],
            ['Updated', $stats['updated']],
            ['Skipped', $stats['skipped']],
            ['Errors', $stats['errors']],
            ['Addresses Created', $stats['addresses_created']],
            ['Addresses Linked', $stats['addresses_linked']],
            ['Total Processed', count($activeRecords)],
        ]);

        return 0;
    }

    private function parseCsv($filePath)
    {
        $rows = [];
        $header = null;
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return $rows;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            if ($header === null) {
                $header = str_getcsv($line);

                continue;
            }

            $data = str_getcsv($line);

            if (count($data) !== count($header)) {
                continue;
            }

            $rows[] = array_combine($header, $data);
        }

        fclose($handle);

        return $rows;
    }

    private function cleanNik($nik)
    {
        $nik = preg_replace('/[^0-9]/', '', $nik);

        return strlen($nik) === 16 ? $nik : null;
    }

    private function cleanName($name)
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    private function cleanTitle($title)
    {
        $title = trim($title);
        if ($title === '' || $title === '-') {
            return null;
        }

        return $title;
    }

    private function parseGender($gender)
    {
        $gender = strtoupper(trim($gender));

        if ($gender === 'PRIA' || $gender === 'L' || $gender === '1') {
            return 'L';
        }

        if ($gender === 'WANITA' || $gender === 'P' || $gender === '2') {
            return 'P';
        }

        return 'L';
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return '1970-01-01';
        }

        try {
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateString, $matches)) {
                return $dateString;
            }

            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateString, $matches)) {
                return $matches[3].'-'.$matches[2].'-'.$matches[1];
            }

            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $dateString, $matches)) {
                return $matches[3].'-'.$matches[2].'-'.$matches[1];
            }

            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
                return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }

            $parsed = Carbon::parse($dateString);

            return $parsed->format('Y-m-d');
        } catch (\Exception $e) {
            return '1970-01-01';
        }
    }

    private function cleanPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return strlen($phone) >= 10 ? $phone : null;
    }

    private function cleanAlamatLengkap($alamat, $locationData = [])
    {
        $result = trim($alamat);

        if (empty($result)) {
            return '';
        }

        if (! empty($locationData)) {
            $keywordsToRemove = ['Kec.', 'Kecamatan', 'Kab.', 'Kabupaten', 'Prov.', 'Provinsi', 'Kota'];

            foreach ($keywordsToRemove as $keyword) {
                $result = str_ireplace($keyword, '', $result);
            }

            if (isset($locationData['provinsi']) && ! empty($locationData['provinsi'])) {
                $result = str_ireplace($locationData['provinsi'], '', $result);
            }

            if (isset($locationData['kabupaten']) && ! empty($locationData['kabupaten'])) {
                $result = str_ireplace($locationData['kabupaten'], '', $result);
            }

            if (isset($locationData['kecamatan']) && ! empty($locationData['kecamatan'])) {
                $result = str_ireplace($locationData['kecamatan'], '', $result);
            }

            if (isset($locationData['desa']) && ! empty($locationData['desa'])) {
                $result = str_ireplace($locationData['desa'], '', $result);
            }
        }

        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result, " ,.\t\n\r\0\x0B");

        return $result;
    }

    private function findDesaId($alamat)
    {
        $cleanAlamat = $alamat;
        $keywords = ['Kec.', 'Kecamatan', 'Kab.', 'Kabupaten', 'Prov.', 'Provinsi', 'Kota', 'Jawa Barat', 'Jabar', 'RT', 'RW', 'KP.'];

        foreach ($keywords as $keyword) {
            $cleanAlamat = str_ireplace($keyword, '', $cleanAlamat);
        }

        $words = preg_split('/[\s,]+/', trim($cleanAlamat));
        $words = array_filter($words, fn ($w) => strlen($w) >= 3);

        if (count($words) < 2) {
            return null;
        }

        $result = [
            'desa_id' => null,
            'desa' => null,
            'kecamatan' => null,
            'kabupaten' => null,
            'provinsi' => null,
        ];

        $kecamatanName = null;
        $desaName = null;

        $lastWord = end($words);
        $prevWord = prev($words);

        $district = District::whereRaw('LOWER(name) = ?', [strtolower($lastWord)])
            ->first();

        if ($district) {
            $kecamatanName = $lastWord;
        }

        if (! $kecamatanName) {
            $district = District::whereRaw('LOWER(name) = ?', [strtolower($prevWord)])
                ->first();

            if ($district) {
                $kecamatanName = $prevWord;
            }
        }

        if (! $kecamatanName) {
            return null;
        }

        $result['kecamatan'] = $kecamatanName;

        foreach ($words as $index => $word) {
            if (strtolower($word) === strtolower($kecamatanName)) {
                if (isset($words[$index - 1])) {
                    $desaName = $words[$index - 1];
                }
                break;
            }
        }

        $city = City::where('code', $district->city_code)->first();

        if ($city) {
            $result['kabupaten'] = $city->name;
        }

        $provinceName = $this->getProvinceName($alamat);

        if ($provinceName) {
            $result['provinsi'] = $provinceName;
        }

        if (! $desaName) {
            $village = Village::where('district_code', $district->code)
                ->first();

            if ($village) {
                $result['desa_id'] = $village->code;
                $result['desa'] = $village->name;

                return $result;
            }

            return null;
        }

        $village = Village::where('district_code', $district->code)
            ->whereRaw('LOWER(name) = ?', [strtolower($desaName)])
            ->first();

        if ($village) {
            $result['desa_id'] = $village->code;
            $result['desa'] = $village->name;

            return $result;
        }

        $village = Village::where('district_code', $district->code)
            ->whereRaw('LOWER(name) LIKE ?', [strtolower($desaName).'%'])
            ->first();

        if ($village) {
            $result['desa_id'] = $village->code;
            $result['desa'] = $village->name;

            return $result;
        }

        return null;
    }

    private function getProvinceName($alamat)
    {
        $provinceKeywords = [
            'Jawa Barat' => 'Jawa Barat',
            'Jabar' => 'Jawa Barat',
            'Jawa Tengah' => 'Jawa Tengah',
            'Jateng' => 'Jawa Tengah',
            'Jawa Timur' => 'Jawa Timur',
            'Jatim' => 'Jawa Timur',
            'DKI Jakarta' => 'DKI Jakarta',
            'Jakarta' => 'DKI Jakarta',
            'Banten' => 'Banten',
            'Jawa Barat.' => 'Jawa Barat',
        ];

        foreach ($provinceKeywords as $keyword => $name) {
            if (stripos($alamat, $keyword) !== false) {
                return $name;
            }
        }

        return null;
    }
}
