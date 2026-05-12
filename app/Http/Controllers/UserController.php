<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function userPetugas()
    {
        $data = [
            'title' => 'Kelola Petugas',
        ];
        return view('user.petugas', $data);
    }

    public function getDatatablesPetugas()
    {
        $data = DB::table('users')
            ->select([
                'id',
                'name',
                'username',
                'role',
                'created_at',
                'updated_at',
            ])
            ->whereIn('users.role', ['verifikator', 'approver'])
            ->orderBy('users.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storePetugas(Request $request)
    {
        $id = $request->input('petugas_id');

        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . ($id ? $id : 'NULL') . ',id',
            'role' => 'required|in:verifikator,approver',
        ];

        $messages = [
            'name.required' => 'Kolom Nama wajib diisi.',
            'username.required' => 'Kolom Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'role.required' => 'Kolom Role wajib diisi.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ];

        if (!$id || $request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
            $rules['password_confirmation'] = 'required|same:password';

            $messages['password.required'] = 'Password wajib diisi.';
            $messages['password_confirmation.required'] = 'Konfirmasi password wajib diisi.';
            $messages['password_confirmation.same'] = 'Konfirmasi password tidak cocok.';
            $messages['password.min'] = 'Password minimal 8 karakter.';
        } else {
            // Mode edit tanpa mengubah password
            $rules['password'] = 'nullable|string|min:8';
            $rules['password_confirmation'] = 'nullable|same:password';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'name' => $request->name,
                'username' => $request->username,
                'role' => $request->role,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $dataToSave['password'] = Hash::make($request->password);
            }

            if ($id) {
                DB::table('users')
                    ->where('id', $id)
                    ->update($dataToSave);

                $message = '✅ User "' . $request->name . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                $dataToSave['created_at'] = now();

                if (!isset($dataToSave['password'])) {
                    throw new \Exception('Kesalahan logika: Password hilang saat mode insert.');
                }

                $id = DB::table('users')->insertGetId($dataToSave);

                $message = '✅ User "' . $request->name . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            $user = DB::table('users')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $user,
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showPetugas(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function destroyPetugas(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        try {
            DB::table('users')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User "' . $user->name . '" berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }

    //user operator Bumdes
    public function userOpBumdes()
    {
        $data = [
            'title' => 'Kelola Operator BUMDes',
            'bumdes' => DB::table('bumdes')->get(),
        ];
        return view('user.operator_bumdes', $data);
    }

    public function getDatatablesOpBumdes()
    {
        $data = DB::table('users')
            ->select([
                'id',
                'name',
                'username',
                'role',
                'created_at',
                'updated_at',
            ])
            ->where('users.role', 'operator-bumdes')
            ->orderBy('users.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeOpBumdes(Request $request)
    {
        // Cast ke integer agar falsy check bekerja benar (string "0" atau "" jadi falsy)
        $id = $request->input('operator_bumdes_id');
        $id = $id ? (int) $id : null; // ← FIX: pastikan null jika kosong, int jika ada

        $rules = [
            'name'     => 'required|string|max:255',
            'id_bumdes' => 'required|exists:bumdes,id',
            'username' => [
                'required',
                'string',
                'max:255',
                // FIX: gunakan Rule::unique agar lebih eksplisit dan aman
                \Illuminate\Validation\Rule::unique('users', 'username')->ignore($id),
            ],
            'role' => 'required|in:operator-bumdes',
        ];

        $messages = [
            'name.required'     => 'Kolom Nama wajib diisi.',
            'id_bumdes.required' => 'Kolom BUMDes wajib diisi.',
            'id_bumdes.exists'  => 'BUMDes yang dipilih tidak valid.',
            'username.required' => 'Kolom Username wajib diisi.',
            'username.unique'   => 'Username ini sudah digunakan.',
            'role.required'     => 'Kolom Role wajib diisi.',
            'role.in'           => 'Role yang dipilih tidak valid.',
        ];

        // FIX: password wajib hanya saat INSERT, opsional saat EDIT
        if (!$id) {
            // Mode tambah: password wajib
            $rules['password']              = 'required|string|min:8';
            $rules['password_confirmation'] = 'required|same:password';

            $messages['password.required']              = 'Password wajib diisi.';
            $messages['password_confirmation.required'] = 'Konfirmasi password wajib diisi.';
            $messages['password_confirmation.same']     = 'Konfirmasi password tidak cocok.';
            $messages['password.min']                   = 'Password minimal 8 karakter.';
        } else {
            // Mode edit: password opsional, hanya divalidasi jika diisi
            $rules['password']              = 'nullable|string|min:8';
            $rules['password_confirmation'] = 'nullable|string|same:password';

            $messages['password.min']               = 'Password minimal 8 karakter.';
            $messages['password_confirmation.same'] = 'Konfirmasi password tidak cocok.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'name'      => $request->name,
                'username'  => $request->username,
                'role'      => $request->role,
                'bumdes_id' => $request->id_bumdes,
                'updated_at' => now(),
            ];

            // Hanya hash password jika diisi
            if ($request->filled('password')) {
                $dataToSave['password'] = Hash::make($request->password);
            }

            if ($id) {
                DB::table('users')->where('id', $id)->update($dataToSave);

                $message    = '✅ User "' . $request->name . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                $dataToSave['created_at'] = now();

                if (!isset($dataToSave['password'])) {
                    throw new \Exception('Kesalahan logika: Password hilang saat mode insert.');
                }

                $id = DB::table('users')->insertGetId($dataToSave);

                $message    = '✅ User "' . $request->name . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            $user = DB::table('users')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $user,
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showOpBumdes(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function destroyOpBumdes(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        try {
            DB::table('users')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User "' . $user->name . '" berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }

    //user operator OPD
    public function userOpOpd()
    {
        $data = [
            'title' => 'Kelola Operator OPD',
            'opd' => DB::table('opd')->get(),
        ];
        return view('user.operator_opd', $data);
    }

    public function getDatatablesOpOpd()
    {
        $data = DB::table('users')
            ->select([
                'id',
                'name',
                'username',
                'role',
                'created_at',
                'updated_at',
            ])
            ->where('users.role', 'operator-opd')
            ->orderBy('users.id', 'ASC')
            ->get();

        return DataTables::of($data)
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeOpOpd(Request $request)
    {
        // Cast ke integer agar falsy check bekerja benar (string "0" atau "" jadi falsy)
        $id = $request->input('operator_opd_id');
        $id = $id ? (int) $id : null; // ← FIX: pastikan null jika kosong, int jika ada

        $rules = [
            'name'     => 'required|string|max:255',
            'id_opd' => 'required|exists:opd,id',
            'username' => [
                'required',
                'string',
                'max:255',
                // FIX: gunakan Rule::unique agar lebih eksplisit dan aman
                \Illuminate\Validation\Rule::unique('users', 'username')->ignore($id),
            ],
            'role' => 'required|in:operator-opd',
        ];

        $messages = [
            'name.required'     => 'Kolom Nama wajib diisi.',
            'id_opd.required' => 'Kolom OPD wajib diisi.',
            'id_opd.exists'   => 'OPD yang dipilih tidak valid.',
            'username.required' => 'Kolom Username wajib diisi.',
            'username.unique'   => 'Username ini sudah digunakan.',
            'role.required'     => 'Kolom Role wajib diisi.',
            'role.in'           => 'Role yang dipilih tidak valid.',
        ];

        // FIX: password wajib hanya saat INSERT, opsional saat EDIT
        if (!$id) {
            // Mode tambah: password wajib
            $rules['password']              = 'required|string|min:8';
            $rules['password_confirmation'] = 'required|same:password';

            $messages['password.required']              = 'Password wajib diisi.';
            $messages['password_confirmation.required'] = 'Konfirmasi password wajib diisi.';
            $messages['password_confirmation.same']     = 'Konfirmasi password tidak cocok.';
            $messages['password.min']                   = 'Password minimal 8 karakter.';
        } else {
            // Mode edit: password opsional, hanya divalidasi jika diisi
            $rules['password']              = 'nullable|string|min:8';
            $rules['password_confirmation'] = 'nullable|string|same:password';

            $messages['password.min']               = 'Password minimal 8 karakter.';
            $messages['password_confirmation.same'] = 'Konfirmasi password tidak cocok.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        try {
            $dataToSave = [
                'name'      => $request->name,
                'username'  => $request->username,
                'role'      => $request->role,
                'opd_id' => $request->id_opd,
                'updated_at' => now(),
            ];

            // Hanya hash password jika diisi
            if ($request->filled('password')) {
                $dataToSave['password'] = Hash::make($request->password);
            }

            if ($id) {
                DB::table('users')->where('id', $id)->update($dataToSave);

                $message    = '✅ User "' . $request->name . '" berhasil diperbarui.';
                $statusCode = 200;
            } else {
                $dataToSave['created_at'] = now();

                if (!isset($dataToSave['password'])) {
                    throw new \Exception('Kesalahan logika: Password hilang saat mode insert.');
                }

                $id = DB::table('users')->insertGetId($dataToSave);

                $message    = '✅ User "' . $request->name . '" berhasil ditambahkan.';
                $statusCode = 201;
            }

            $user = DB::table('users')->find($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $user,
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showOpOpd(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function destroyOpOpd(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        try {
            DB::table('users')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User "' . $user->name . '" berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan server saat menghapus data.'
            ], 500);
        }
    }
}
