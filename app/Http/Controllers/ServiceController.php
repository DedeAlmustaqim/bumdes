<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
      public function getDataDesa($id): JsonResponse
    {
        // Menggunakan get() untuk mendapatkan Collection dari semua nama usaha
        // yang terkait dengan id_bid_usaha yang diberikan.
        $data = DB::table('desa')->where('kecamatan_id', $id)->get();

        // Cek apakah koleksi $data tidak kosong (ada data)
        if ($data->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                // Data dikembalikan sebagai array of objects (koleksi)
                'data' => $data,
            ], Response::HTTP_OK);
        } else {
            // Mengembalikan status 'success' dengan data kosong (array kosong)
            // atau 'error' dengan HTTP_NOT_FOUND, saya sarankan data kosong
            // dengan status OK agar lebih mudah dihandle di JS.
            return response()->json([
                'status' => 'success',
                'data' => [], // Kirim array kosong
                'message' => 'Data tidak ditemukan.',
            ], Response::HTTP_OK);
        }
    }

}
