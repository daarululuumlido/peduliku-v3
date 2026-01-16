<?php

namespace App\Http\Controllers;

use App\Models\PeranPegawai;
use App\Models\ChecklistDokumenPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokumenPegawaiController extends Controller
{
    public function index(Request $request, PeranPegawai $pegawai)
    {
        $dokumen = ChecklistDokumenPegawai::with('masterDokumenWajib')
            ->where('peran_pegawai_id', $pegawai->id)
            ->orderBy('is_verifikasi', 'asc')
            ->orderBy('master_dokumen_wajib_id')
            ->get();

        return response()->json($dokumen);
    }

    public function store(Request $request, PeranPegawai $pegawai)
    {
        $validated = $request->validate([
            'master_dokumen_wajib_id' => 'required|exists:master_dokumen_wajib,id',
            'url_dokumen' => 'nullable|string|max:500',
            'tgl_upload' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $pegawai->checklistDokumen()->create($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Dokumen berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan dokumen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PeranPegawai $pegawai, ChecklistDokumenPegawai $dokumen)
    {
        $validated = $request->validate([
            'url_dokumen' => 'nullable|string|max:500',
            'tgl_upload' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $dokumen->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }

    public function verify(Request $request, PeranPegawai $pegawai, ChecklistDokumenPegawai $dokumen)
    {
        $validated = $request->validate([
            'is_verifikasi' => 'required|boolean',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $dokumen->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Status verifikasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui verifikasi: ' . $e->getMessage());
        }
    }

    public function destroy(PeranPegawai $pegawai, ChecklistDokumenPegawai $dokumen)
    {
        DB::beginTransaction();

        try {
            $dokumen->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Dokumen berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }
}
