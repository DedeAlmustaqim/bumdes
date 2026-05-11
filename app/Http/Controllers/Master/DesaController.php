<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class DesaController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kelola Data Desa',
            'kecamatan' => DB::table('kecamatan')->orderBy('nm_kecamatan', 'ASC')->get() // Ambil data kecamatan untuk dropdown
        ];
        return view('master.desa', $data);
    }

    public function getDesa()
    {
       $data = DB::table('desa')
          
            ->orderBy('desa.id', 'ASC')
            //Gunakan kondisi sesuai role login
            //->when(auth()->user()->role === 'role', function ($query) {
                //return $query->where('table.role', session('role'));
           //})
           ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id') // Join dengan tabel kecamatan
           ->select('desa.*', 'kecamatan.nm_kecamatan') // Pilih semua
            ->get();
        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getData($id): JsonResponse
    {
     $data = DB::table('desa')->where('id', $id)
     ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Desa tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $id = $request->input('id_desa'); // ID desa yang akan diupdate (kosong jika INSERT)

        // Aturan Validasi Dasar
        $rules = [
            'nm_desa' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            // ✅ Aturan UNIQUE: Mengabaikan ID desa saat update
        ];

        $messages = [
            'nm_desa.required' => 'Kolom Nama wajib diisi.',
            'kecamatan_id.required' => 'Kolom Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
        ];

       

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Mengembalikan error validasi 422
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'nm_desa' => $request->nm_desa,
                'kecamatan_id' => $request->kecamatan_id,
                'updated_at' => now()
            ];

            

            if ($id) {
                // ============== MODE UPDATE ==============
                DB::table('desa')
                    ->where('id', $id)
                    ->update($dataToSave);

                $message = '✅ Desa "' . $request->nm_desa . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                // ============== MODE INSERT ==============
                $dataToSave['created_at'] = now();


               
                

                $id = DB::table('desa')->insertGetId($dataToSave);

                $message = '✅ Desa "' . $request->nm_desa . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            // Ambil data desa yang baru disimpan/diperbarui
            $desa = DB::table('desa')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $desa    
            ], $statusCode);
        } catch (\Exception $e) {
            // Mengembalikan error server 500
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        $desa = DB::table('desa')->where('id', $id)->first();

        if (!$desa) {
            return response()->json([
                'success' => false,
                'message' => 'Desa tidak ditemukan.'
            ], 404);
        }

        try {
            // Cek relasi ke tabel lain


            DB::table('desa')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Desa berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }
}
