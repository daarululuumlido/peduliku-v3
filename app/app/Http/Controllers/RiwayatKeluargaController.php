<?php

namespace App\Http\Controllers;

use App\Models\PeranPegawai;
use App\Models\RiwayatKeluargaPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatKeluargaController extends Controller
{
    public function index(Request $request, PeranPegawai $pegawai)
    {
        $keluarga = $pegawai->riwayatKeluarga()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($keluarga);
    }

    public function store(Request $request, PeranPegawai $pegawai)
    {
        $validated = $request->validate([
            'hubungan' => 'required|in:Suami,Istri,Ayah,Ibu,Anak,Saudara,Lainnya',
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:16|unique:riwayat_keluarga_pegawai,nik',
            'tgl_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'status_hidup' => 'nullable|in:Hidup,Meninggal',
        ]);

        DB::beginTransaction();

        try {
            $pegawai->riwayatKeluarga()->create($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Data keluarga berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan data keluarga: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PeranPegawai $pegawai, RiwayatKeluargaPegawai $keluarga)
    {
        $validated = $request->validate([
            'hubungan' => 'required|in:Suami,Istri,Ayah,Ibu,Anak,Saudara,Lainnya',
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:16|unique:riwayat_keluarga_pegawai,nik,' . $keluarga->id,
            'tgl_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'status_hidup' => 'nullable|in:Hidup,Meninggal',
        ]);

        DB::beginTransaction();

        try {
            $keluarga->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Data keluarga berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data keluarga: ' . $e->getMessage());
        }
    }

    public function destroy(PeranPegawai $pegawai, RiwayatKeluargaPegawai $keluarga)
    {
        DB::beginTransaction();

        try {
            $keluarga->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Data keluarga berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus data keluarga: ' . $e->getMessage());
        }
    }
}
