<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ApproverController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Persetujuan Pengajuan Izin Usaha',
        ];
        return view('izin_usaha.approve', $data);
    }

    public function getDatatablesApprove()
    {
        $data = DB::table('izin_usaha')
            ->select([
                'izin_usaha.id',
                'izin_usaha.nomor_pengajuan',
                'izin_usaha.bumdes_id',
                'izin_usaha.nama_usaha',
                'izin_usaha.deskripsi_usaha',
                'izin_usaha.status',
                'izin_usaha.tahapan_saat_ini',
                'izin_usaha.diverifikasi_oleh',
                'izin_usaha.catatan_verifikasi',
                'izin_usaha.tanggal_verifikasi',
                'izin_usaha.disetujui_oleh',
                'izin_usaha.catatan_approval',
                'izin_usaha.tanggal_approval',
                'izin_usaha.opd_id',
                'izin_usaha.dok_surat',
                'izin_usaha.foto_usaha',
                'izin_usaha.latitude',
                'izin_usaha.longitude',
                'izin_usaha.created_at',
                'izin_usaha.updated_at',
                'bumdes.nm_bumdes',
                'bumdes.desa_id as bumdes_desa_id',
                'bumdes.kecamatan_id as bumdes_kecamatan_id',
                'kecamatan.nm_kecamatan',
                'kecamatan.updated_at as kecamatan_updated_at',
                'desa.nm_desa',
                'desa.kecamatan_id as desa_kecamatan_id',
                'opd.nm_opd',
            ])
            ->leftJoin('bumdes', 'izin_usaha.bumdes_id', '=', 'bumdes.id')
            ->leftJoin('kecamatan', 'bumdes.kecamatan_id', '=', 'kecamatan.id')
            ->leftJoin('desa', 'bumdes.desa_id', '=', 'desa.id')
            ->leftJoin('opd', 'izin_usaha.opd_id', '=', 'opd.id')
            ->where('izin_usaha.status', 'VERIFIED')
            ->orderBy('izin_usaha.id', 'ASC')

            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function approve(Request $request, int $id)
    {
        // ✅ Approver (pejabat disposisi) approve/reject pengajuan
        $izin = DB::table('izin_usaha')->find($id);

        if (!$izin) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ], 404);
        }

        if ($izin->status !== 'VERIFIED') {
            return response()->json([
                'success' => false,
                'message' => '❌ Hanya pengajuan VERIFIED yang dapat diapprove.'
            ], 403);
        }

        $rules = [
            'is_approved' => 'required|boolean',
            'catatan_approval' => 'required|string|min:10',
        ];

        $messages = [
            'is_approved.required' => 'Status approval wajib dipilih.',
            'catatan_approval.required' => 'Catatan approval wajib diisi (min 10 karakter).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 422);
        }

        try {
            $isApproved = $request->is_approved;
            $newStatus = $isApproved ? 'DISETUJUI' : 'PERSETUJUAN_DITOLAK';
            $newTahapan = $isApproved ? 'SELESAI_DITERUSKAN_KE_OPD' : 'TIDAK_DITERUSKAN_KE_OPD';

            DB::table('izin_usaha')->where('id', $id)->update([
                'status' => $newStatus,
                'tahapan_saat_ini' => $newTahapan,
                'disetujui_oleh' => auth()->id(),
                'catatan_approval' => $request->catatan_approval,
                'tanggal_approval' => now(),
                'updated_at' => now(),
            ]);

            $message = $isApproved 
                ? '✅ Pengajuan berhasil disetujui dan akan diteruskan ke OPD.' 
                : '✅ Pengajuan ditolak di tahap approval.';

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan saat approval.'
            ], 500);
        }
    }

}
