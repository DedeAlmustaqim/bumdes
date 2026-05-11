<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class KecamatanController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kelola Data Kecamatan'
        ];
        return view('master.kecamatan', $data);
    }

    public function getKecamatan()
    {
       $data = DB::table('kecamatan')
          
            ->orderBy('kecamatan.id', 'ASC')
            //Gunakan kondisi sesuai role login
            //->when(auth()->user()->role === 'role', function ($query) {
                //return $query->where('table.role', session('role'));
           //})
            ->get();
        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getData($id): JsonResponse
    {
     $data = DB::table('kecamatan')->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Kecamatan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $id = $request->input('id_kec'); // ID user yang akan diupdate (kosong jika INSERT)

        // Aturan Validasi Dasar
        $rules = [
            'nm_kecamatan' => 'required|string|max:255',
            // ✅ Aturan UNIQUE: Mengabaikan ID user saat update
        ];

        $messages = [
            'nm_kecamatan.required' => 'Kolom Nama wajib diisi.',
        ];

       

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Mengembalikan error validasi 422
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'nm_kecamatan' => $request->nm_kecamatan,
                'updated_at' => now()
            ];

            

            if ($id) {
                // ============== MODE UPDATE ==============
                DB::table('kecamatan')
                    ->where('id', $id)
                    ->update($dataToSave);

                $message = '✅ Kecamatan "' . $request->nm_kecamatan . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                // ============== MODE INSERT ==============
                $dataToSave['created_at'] = now();


               
                

                $id = DB::table('kecamatan')->insertGetId($dataToSave);

                $message = '✅ Kecamatan "' . $request->nm_kecamatan . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            // Ambil data kecamatan yang baru disimpan/diperbarui
            $kecamatan = DB::table('kecamatan')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $kecamatan    
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
        $desa = DB::table('kecamatan')->where('id', $id)->first();

        if (!$desa) {
            return response()->json([
                'success' => false,
                'message' => 'Kecamatan tidak ditemukan.'
            ], 404);
        }

        try {
            // Cek relasi ke tabel lain


            DB::table('kecamatan')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kecamatan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }
}
