<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class OpdController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kelola Data OPD',
        ];
        return view('master.opd', $data);
    }

    public function getOpd()
    {
       $data = DB::table('opd')
          
            ->orderBy('opd.id', 'ASC')
            //Gunakan kondisi sesuai role login
            //->when(auth()->user()->role === 'role', function ($query) {
                //return $query->where('table.role', session('role'));
           //})
           
           ->select('opd.*') // Pilih semua
            ->get();
        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getData($id): JsonResponse
    {
     $data = DB::table('opd')->where('id', $id)
     ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'OPD tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $id = $request->input('id_opd'); // ID opd yang akan diupdate (kosong jika INSERT)

        // Aturan Validasi Dasar
        $rules = [
            'nm_opd' => 'required|string|max:255',
        
            // ✅ Aturan UNIQUE: Mengabaikan ID opd saat update
        ];

        $messages = [
            'nm_opd.required' => 'Kolom Nama wajib diisi.',
            
        ];

       

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Mengembalikan error validasi 422
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'nm_opd' => $request->nm_opd,
                'updated_at' => now()
            ];

            

            if ($id) {
                // ============== MODE UPDATE ==============
                DB::table('opd')
                    ->where('id', $id)
                    ->update($dataToSave);

                $message = '✅ OPD "' . $request->nm_opd . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                // ============== MODE INSERT ==============
                $dataToSave['created_at'] = now();
                             
                $id = DB::table('opd')->insertGetId($dataToSave);

                $message = '✅ OPD "' . $request->nm_opd . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            // Ambil data opd yang baru disimpan/diperbarui
            $opd = DB::table('opd')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $opd      
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
        $opd = DB::table('opd')->where('id', $id)->first();

        if (!$opd) {
            return response()->json([
                'success' => false,
                'message' => 'OPD tidak ditemukan.'
            ], 404);
        }

        try {
            // Cek relasi ke tabel lain


            DB::table('opd')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'OPD berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }

    
}
