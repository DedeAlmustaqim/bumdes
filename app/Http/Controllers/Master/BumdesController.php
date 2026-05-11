<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BumdesController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kelola Data Bumdes',
            'kecamatan' => DB::table('kecamatan')->orderBy('nm_kecamatan', 'ASC')->get() // Ambil data kecamatan untuk dropdown
        ];
        return view('master.bumdes', $data);
    }

    public function getBumdes()
    {
       $data = DB::table('bumdes')
          
            ->orderBy('bumdes.id', 'ASC')
            //Gunakan kondisi sesuai role login
            //->when(auth()->user()->role === 'role', function ($query) {
                //return $query->where('table.role', session('role'));
           //})
           ->join('kecamatan', 'bumdes.kecamatan_id', '=', 'kecamatan.id') // Join dengan tabel kecamatan
           ->join('desa', 'bumdes.desa_id', '=', 'desa.id') // Join dengan tabel desa
           ->select('bumdes.*', 'kecamatan.nm_kecamatan','desa.nm_desa') // Pilih semua
            ->get();
        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getData($id): JsonResponse
    {
     $data = DB::table('bumdes')->where('id', $id)
     ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Bumdes tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $id = $request->input('id_bumdes'); // ID bumdes yang akan diupdate (kosong jika INSERT)

        // Aturan Validasi Dasar
        $rules = [
            'nm_bumdes' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'desa_id' => 'required|exists:desa,id',
            // ✅ Aturan UNIQUE: Mengabaikan ID bumdes saat update
        ];

        $messages = [
            'nm_bumdes.required' => 'Kolom Nama wajib diisi.',
            'kecamatan_id.required' => 'Kolom Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'desa_id.required' => 'Kolom Desa wajib dipilih.',
            'desa_id.exists' => 'Desa yang dipilih tidak valid.',
        ];

       

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Mengembalikan error validasi 422
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'nm_bumdes' => $request->nm_bumdes,
                'kecamatan_id' => $request->kecamatan_id,
                'desa_id' => $request->desa_id,
                'updated_at' => now()
            ];

            

            if ($id) {
                // ============== MODE UPDATE ==============
                DB::table('bumdes')
                    ->where('id', $id)
                    ->update($dataToSave);

                $message = '✅ Bumdes "' . $request->nm_bumdes . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                // ============== MODE INSERT ==============
                $dataToSave['created_at'] = now();


               
                

                $id = DB::table('bumdes')->insertGetId($dataToSave);

                $message = '✅ Bumdes "' . $request->nm_bumdes . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            // Ambil data bumdes yang baru disimpan/diperbarui
            $bumdes = DB::table('bumdes')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $bumdes    
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
        $bumdes = DB::table('bumdes')->where('id', $id)->first();

        if (!$bumdes) {
            return response()->json([
                'success' => false,
                'message' => 'Bumdes tidak ditemukan.'
            ], 404);
        }

        try {
            // Cek relasi ke tabel lain


            DB::table('bumdes')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bumdes berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }

    
}
