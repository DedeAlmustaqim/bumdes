<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserKecamatanController extends Controller
{
    public function userKecamatan()
    {

        $data = [
            'title' => 'Kelola User Kecamatan',
            'kecamatan' => DB::table('kecamatan')->get()
        ];
        return view('admin.user_kecamatan', $data);
    }

    public function getDatatablesUserKecamatan()
    {
        $data = DB::table('users')
            ->select([
                'id',
                'name',
                'username',
                'role',
                'created_at',
                'updated_at',
                'kecamatan_id'
            ])
            //->join('table_2', 'table.id_kec', '=', 'table_2.id')
            ->where('users.role', 'admin_kecamatan')
            ->orderBy('users.id', 'ASC')
            //Gunakan kondisi sesuai role login
            //->when(auth()->user()->role === 'role', function ($query) {
            //return $query->where('table.role', session('role'));
            //})
            ->get();
        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }




    public function storeUserKecamatan(Request $request)
    {
        $id = $request->input('user_id_kecamatan'); // ID user yang akan diupdate (kosong jika INSERT)

        // Aturan Validasi Dasar
        $rules = [
            'name_user_kecamatan' => 'required|string|max:255',
            // ✅ Aturan UNIQUE: Mengabaikan ID user saat update
            'username_user_kecamatan' => 'required|string|max:255|unique:users,username,' . ($id ? $id : 'NULL') . ',id',
            'kecamatan_id_user_kecamatan' => 'required|integer|exists:kecamatan,id',
        ];

        $messages = [
            'name_user_kecamatan.required' => 'Kolom Nama wajib diisi.',
            'username_user_kecamatan.unique' => 'Username ini sudah digunakan.',
            'kecamatan_id_user_kecamatan.exists' => 'ID Kecamatan tidak valid atau tidak ditemukan.',
        ];

        // Aturan Tambahan untuk Password (Hanya berlaku saat INSERT ATAU saat password diisi pada UPDATE)
        if (!$id || $request->filled('password_user_kecamatan')) {
            $rules['password_user_kecamatan'] = 'required|string|min:8';
            // Validasi 'same' untuk konfirmasi password
            $rules['password_confirmation_user_kecamatan'] = 'required|same:password_user_kecamatan';

            // Tambahkan pesan error password spesifik
            $messages['password_user_kecamatan.required'] = 'Password wajib diisi.';
            $messages['password_confirmation_user_kecamatan.required'] = 'Konfirmasi password wajib diisi.';
            $messages['password_confirmation_user_kecamatan.same'] = 'Konfirmasi password tidak cocok.';
            $messages['password_user_kecamatan.min'] = 'Password minimal 8 karakter.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Mengembalikan error validasi 422
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'name' => $request->name_user_kecamatan,
                'username' => $request->username_user_kecamatan,
                'kecamatan_id' => $request->kecamatan_id_user_kecamatan,
                'updated_at' => now()
            ];

            // Hanya masukkan password jika diisi (mode insert ATAU update dengan ganti password)
            if ($request->filled('password_user_kecamatan')) {
                $dataToSave['password'] = Hash::make($request->password_user_kecamatan);
            }

            if ($id) {
                // ============== MODE UPDATE ==============
                DB::table('users')
                    ->where('id', $id)
                    ->update($dataToSave);

                $message = '✅ User "' . $request->name_user_kecamatan . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                // ============== MODE INSERT ==============
                $dataToSave['created_at'] = now();
                $dataToSave['role'] = 'admin_kecamatan';

                // Pastikan password ada saat insert (sudah dijamin oleh validasi)
                if (!isset($dataToSave['password'])) {
                    throw new \Exception('Kesalahan logika: Password hilang saat mode insert.');
                }

                $id = DB::table('users')->insertGetId($dataToSave);

                $message = '✅ User "' . $request->name_user_kecamatan . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            // Ambil data user yang baru disimpan/diperbarui
            $user = DB::table('users')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $user
            ], $statusCode);
        } catch (\Exception $e) {
            // Mengembalikan error server 500
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showUserKecamatan(int $id)
    {
        $desa = DB::table('users')->where('id', $id)->first();

        if (!$desa) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $desa
        ]);
    }

    public function destroyUserKecamatan(int $id)
    {
        $desa = DB::table('users')->where('id', $id)->first();

        if (!$desa) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        try {
            // Cek relasi ke tabel lain


            DB::table('users')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => ' berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }
}
