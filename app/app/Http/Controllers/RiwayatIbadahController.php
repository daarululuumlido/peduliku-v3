<?php

namespace App\Http\Controllers;

use App\Models\PeranPegawai;
use App\Models\RiwayatIbadah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatIbadahController extends Controller
{
    public function index(Request $request, PeranPegawai $pegawai)
    {
        $ibadah = $pegawai->riwayatIbadah()
            ->orderBy('tahun', 'desc')
            ->get();

        return response()->json($ibadah);
    }

    public function store(Request $request, PeranPegawai $pegawai)
    {
        $validated = $request->validate([
            'jenis_ibadah' => 'required|in:Umrah,Haji',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'status' => 'required|in:RENCANA,SUDAH',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $pegawai->riwayatIbadah()->create($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Riwayat ibadah berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan riwayat ibadah: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PeranPegawai $pegawai, RiwayatIbadah $riwayatIbadah)
    {
        $validated = $request->validate([
            'jenis_ibadah' => 'required|in:Umrah,Haji',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'status' => 'required|in:RENCANA,SUDAH',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $riwayatIbadah->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Riwayat ibadah berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui riwayat ibadah: ' . $e->getMessage());
        }
    }

    public function destroy(PeranPegawai $pegawai, RiwayatIbadah $riwayatIbadah)
    {
        DB::beginTransaction();

        try {
            $riwayatIbadah->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Riwayat ibadah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus riwayat ibadah: ' . $e->getMessage());
        }
    }
}
