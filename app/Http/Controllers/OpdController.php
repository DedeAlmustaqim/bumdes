<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class OpdController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Izin Usaha - OPD',
        ];
        return view('izin_usaha.opd', $data);
    }

    public function getDatatablesOpd()
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
            ->where('izin_usaha.status', 'DISETUJUI')
            ->where('izin_usaha.opd_id', auth()->user()->opd_id)
            ->orderBy('izin_usaha.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function MonitoringOpd()
    {
        $data = [
            'title' => 'Monitoring Izin Usaha - OPD',
        ];
        return view('izin_usaha.monitoring_opd', $data);
    }

    public function getDatatablesMonitoringOpd()
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
            ->where('izin_usaha.opd_id', auth()->user()->opd_id)
            ->orderBy('izin_usaha.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }
}
