<?php

namespace App\Http\Controllers;

use App\Models\PeranPegawai;
use App\Models\CatatanKepegawaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatatanKepegawaianController extends Controller
{
    public function index(Request $request, PeranPegawai $pegawai)
    {
        $catatan = $pegawai->catatanKepegawaian()
            ->orderBy('tanggal_catatan', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($catatan);
    }

    public function store(Request $request, PeranPegawai $pegawai)
    {
        $validated = $request->validate([
            'jenis_catatan' => 'required|in:Prestasi,Pelanggaran,Kesehatan,Lainnya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_catatan' => 'required|date',
            'lampiran' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $pegawai->catatanKepegawaian()->create($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Catatan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan catatan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PeranPegawai $pegawai, CatatanKepegawaian $catatan)
    {
        $validated = $request->validate([
            'jenis_catatan' => 'required|in:Prestasi,Pelanggaran,Kesehatan,Lainnya',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_catatan' => 'required|date',
            'lampiran' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $catatan->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Catatan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui catatan: ' . $e->getMessage());
        }
    }

    public function destroy(PeranPegawai $pegawai, CatatanKepegawaian $catatan)
    {
        DB::beginTransaction();

        try {
            $catatan->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Catatan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus catatan: ' . $e->getMessage());
        }
    }
}
