<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class IzinUsahaController extends Controller
{
    //
    private function generateNomorPengajuan(): string
    {
        // Format: IU-2024-0001
        $year = date('Y');
        $lastNumber = DB::table('izin_usaha')
            ->whereYear('created_at', $year)
            ->max(DB::raw("CAST(RIGHT(nomor_pengajuan, 4) AS INTEGER)"));

        $nextNumber = ($lastNumber ?? 0) + 1;
        return 'IU-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bumdes()
    {
        $data = [
            'title' => 'Pengajuan Izin Usaha',
            'izin' => DB::table('izin_usaha')
                ->where('bumdes_id', auth()->user()->bumdes_id)
                ->orderBy('id', 'DESC')
                ->get(),
        ];
        return view('izin_usaha.bumdes', $data);
    }

    public function storeIzinUsahaBumdes(Request $request)
    {
        $id     = $request->input('izin_usaha_id');
        $isEdit = !empty($id);

        $rules = [
            'nama_usaha'      => 'required|string|max:255',
            'deskripsi_usaha' => 'required|string|min:20',
            'dok_surat'       => ($isEdit ? 'nullable' : 'required') . '|file|mimes:pdf,doc,docx|max:10240',
            'foto_usaha'      => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
        ];

        $messages = [
            'nama_usaha.required'      => 'Nama usaha wajib diisi.',
            'deskripsi_usaha.required' => 'Deskripsi usaha wajib diisi.',
            'deskripsi_usaha.min'      => 'Deskripsi usaha minimal 20 karakter.',
            'dok_surat.required'       => 'Dokumen surat wajib diunggah.',
            'dok_surat.mimes'          => 'Format dokumen harus PDF, DOC, atau DOCX.',
            'dok_surat.max'            => 'Ukuran dokumen maksimal 10MB.',
            'foto_usaha.max'           => 'Ukuran foto maksimal 4MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors()
            ], 422);
        }

        try {
            $dataToSave = [
                'bumdes_id'       => auth()->user()->bumdes_id,
                'nama_usaha'      => $request->nama_usaha,
                'deskripsi_usaha' => $request->deskripsi_usaha,
                'latitude'        => $request->latitude,
                'longitude'       => $request->longitude,
                'opd_id'          => 0,
                'updated_at'      => now(),
            ];

            if ($isEdit) {
                // Mode EDIT (hanya DRAFT yang bisa diedit)
                $izin = DB::table('izin_usaha')->where('id', $id)->first();

                if (!$izin || $izin->bumdes_id != auth()->user()->bumdes_id) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Pengajuan tidak ditemukan atau bukan milik Anda.'
                    ], 404);
                }

                if ($izin->status !== 'DRAFT' || $izin->status !== 'DITOLAK') {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Hanya pengajuan dengan status DRAFT/DITOLAK yang dapat diedit.'
                    ], 403);
                }

                // Replace dok_surat jika diupload ulang
                if ($request->hasFile('dok_surat')) {
                    if ($izin->dok_surat && file_exists(public_path($izin->dok_surat))) {
                        unlink(public_path($izin->dok_surat));
                    }
                    $file     = $request->file('dok_surat');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('izin_usaha/dokumen'), $filename);
                    $dataToSave['dok_surat'] = 'izin_usaha/dokumen/' . $filename;
                }

                // Replace foto_usaha jika diupload ulang
                if ($request->hasFile('foto_usaha')) {
                    if ($izin->foto_usaha && file_exists(public_path($izin->foto_usaha))) {
                        unlink(public_path($izin->foto_usaha));
                    }
                    $file     = $request->file('foto_usaha');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('izin_usaha/foto'), $filename);
                    $dataToSave['foto_usaha'] = 'izin_usaha/foto/' . $filename;
                }

                DB::table('izin_usaha')->where('id', $id)->update($dataToSave);

                $message    = '✅ Pengajuan berhasil diperbarui.';
                $statusCode = 200;
                $izinId     = $id;
            } else {
                // Mode CREATE
                $dataToSave['nomor_pengajuan']  = $this->generateNomorPengajuan();
                $dataToSave['status']           = 'DRAFT';
                $dataToSave['tahapan_saat_ini'] = 'DRAFT';
                $dataToSave['created_at']       = now();

                $izinId = DB::table('izin_usaha')->insertGetId($dataToSave);

                // Upload dokumen (WAJIB di mode create)
                if ($request->hasFile('dok_surat')) {
                    $file     = $request->file('dok_surat');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('izin_usaha/dokumen'), $filename);
                    DB::table('izin_usaha')->where('id', $izinId)->update([
                        'dok_surat' => 'izin_usaha/dokumen/' . $filename
                    ]);
                }

                // Upload foto (OPSIONAL)
                if ($request->hasFile('foto_usaha')) {
                    $file     = $request->file('foto_usaha');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('izin_usaha/foto'), $filename);
                    DB::table('izin_usaha')->where('id', $izinId)->update([
                        'foto_usaha' => 'izin_usaha/foto/' . $filename
                    ]);
                }

                $message    = '✅ Pengajuan berhasil dibuat.';
                $statusCode = 201;
            }

            $izinUsaha = DB::table('izin_usaha')->find($izinId);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $izinUsaha,
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDatatablesIzinUsaha()
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
            ->where('izin_usaha.bumdes_id', auth()->user()->bumdes_id)
            ->orderBy('izin_usaha.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function showIzinBumdes(int $id)
    {
        $izin = DB::table('izin_usaha')
            ->where('izin_usaha.id', $id)
            ->leftJoin('bumdes', 'izin_usaha.bumdes_id', '=', 'bumdes.id')
            ->leftJoin('opd', 'izin_usaha.opd_id', '=', 'opd.id')
            ->leftJoin('users as verifikator', 'izin_usaha.diverifikasi_oleh', '=', 'verifikator.id')
            ->leftJoin('users as approver', 'izin_usaha.disetujui_oleh', '=', 'approver.id')
            ->select(
                'izin_usaha.*',
                'bumdes.nm_bumdes',
                'opd.nm_opd',
                'verifikator.name as nama_verifikator',
                'approver.name as nama_approver'
            )
            ->first();

        if (!$izin) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $izin
        ]);
    }

    public function destroyIzinUsahaBumdes(int $id)
    {
        $izin = DB::table('izin_usaha')->where('id', $id)->first();

        if (!$izin || $izin->bumdes_id != auth()->user()->bumdes_id) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ], 404);
        }

        try {
            // Cek relasi ke tabel lain
            if ($izin->status !== 'DRAFT') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengajuan dengan status DRAFT yang dapat dihapus.'
                ], 403);
            }

            DB::table('izin_usaha')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }

    public function submit(int $id)
    {
        // ✅ Operator BUMDes submit pengajuan untuk verifikasi
        $izin = DB::table('izin_usaha')->find($id);

        if (!$izin || $izin->bumdes_id != auth()->user()->bumdes_id) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ], 404);
        }

        if ($izin->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => '❌ Hanya pengajuan DRAFT yang dapat disubmit.'
            ], 403);
        }

        try {
            DB::table('izin_usaha')->where('id', $id)->update([
                'status' => 'SUBMIT',
                'tahapan_saat_ini' => 'MENUNGGU_VERIFIKASI',
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Pengajuan berhasil disubmit untuk verifikasi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan saat submit. ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifikator_draft()
    {
        $data = [
            'title' => 'Pengajuan Izin Usaha - DRAFT',
        ];
        return view('izin_usaha.verifikator_draft', $data);
    }

    public function getDatatablesIzinUsahaDraft()
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
            ->where('izin_usaha.status', 'DRAFT')
            ->orderBy('izin_usaha.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function unverifikasi()
    {
        $data = [
            'title' => 'Pengajuan Izin Usaha - SUBMIT (Belum Diverifikasi)',
            'opds' => DB::table('opd')->orderBy('nm_opd', 'ASC')->get(),
        ];
        return view('izin_usaha.unverifikasi', $data);
    }

    public function getDatatablesIzinUsahaSubmit()
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
            ->where('izin_usaha.status', 'SUBMIT')
            ->orderBy('izin_usaha.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function verify(Request $request, int $id)
    {
        // ✅ Verifikator approve/reject pengajuan & pilih OPD
        $izin = DB::table('izin_usaha')->find($id);

        if (!$izin) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ], 404);
        }

        if ($izin->status !== 'SUBMIT') {
            return response()->json([
                'success' => false,
                'message' => '❌ Hanya pengajuan SUBMIT yang dapat diverifikasi.'
            ], 403);
        }

        $rules = [
            'is_verified' => 'required|boolean',
            'catatan_verifikasi' => 'required|string|min:10',
            // 👈 OPD hanya wajib saat LOLOS verifikasi
            'opd_id' => 'nullable|required_if:is_verified,1|exists:opd,id',
        ];

        $messages = [
            'is_verified.required' => 'Status verifikasi wajib dipilih.',
            'catatan_verifikasi.required' => 'Catatan verifikasi wajib diisi (min 10 karakter).',
            'opd_id.required_if' => 'OPD Disposisi wajib dipilih jika pengajuan LOLOS verifikasi.',
            'opd_id.exists' => 'OPD yang dipilih tidak valid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 422);
        }

        try {
            $isVerified = $request->is_verified;
            $newStatus = $isVerified ? 'VERIFIED' : 'DITOLAK';
            $newTahapan = $isVerified ? 'MENUNGGU_PERSETUJUAN' : 'VERIFIKASI_DITOLAK';

            $updateData = [
                'status' => $newStatus,
                'tahapan_saat_ini' => $newTahapan,
                'diverifikasi_oleh' => auth()->id(),
                'catatan_verifikasi' => $request->catatan_verifikasi,
                'tanggal_verifikasi' => now(),
                'updated_at' => now(),
            ];

            // 👈 SET OPD hanya jika LOLOS verifikasi
            if ($isVerified) {
                $updateData['opd_id'] = $request->opd_id;
            } else {
                $updateData['opd_id'] = 0;
            }

            DB::table('izin_usaha')->where('id', $id)->update($updateData);

            $opdName = $isVerified
                ? DB::table('opd')->find($request->opd_id)?->nm_opd ?? 'OPD'
                : '';

            $message = $isVerified
                ? '✅ Pengajuan berhasil diverifikasi dan akan diteruskan ke ' . $opdName . '.'
                : '✅ Pengajuan ditolak di tahap verifikasi.';

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan saat verifikasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
