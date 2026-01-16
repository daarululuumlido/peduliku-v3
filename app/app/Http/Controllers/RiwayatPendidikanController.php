<?php

namespace App\Http\Controllers;

use App\Models\PeranPegawai;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatPendidikanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, PeranPegawai $pegawai)
    {
        $pendidikan = $pegawai->riwayatPendidikan()
            ->orderBy('tahun_masuk', 'desc')
            ->get();

        return response()->json($pendidikan);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PeranPegawai $pegawai)
    {
        $validated = $request->validate([
            'jenjang_pendidikan' => 'required|in:SD,SMP,SMA,D3,S1,S2,S3,Lainnya',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_masuk' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'ipk' => 'nullable|numeric|min:0|max:4.00',
            'gelar_akademik' => 'nullable|string|max:255',
            'no_ijazah' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $pegawai->riwayatPendidikan()->create($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Riwayat pendidikan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan riwayat pendidikan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PeranPegawai $pegawai, RiwayatPendidikan $riwayatPendidikan)
    {
        $validated = $request->validate([
            'jenjang_pendidikan' => 'required|in:SD,SMP,SMA,D3,S1,S2,S3,Lainnya',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_masuk' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tahun_lulus' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'ipk' => 'nullable|numeric|min:0|max:4.00',
            'gelar_akademik' => 'nullable|string|max:255',
            'no_ijazah' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $riwayatPendidikan->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Riwayat pendidikan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui riwayat pendidikan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeranPegawai $pegawai, RiwayatPendidikan $riwayatPendidikan)
    {
        DB::beginTransaction();

        try {
            $riwayatPendidikan->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Riwayat pendidikan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus riwayat pendidikan: ' . $e->getMessage());
        }
    }
}
