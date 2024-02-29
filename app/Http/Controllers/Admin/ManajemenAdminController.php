<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AdminImport;
use App\Models\Guru;
use App\Models\KaryaCitra;
use App\Models\KaryaTulis;
use App\Models\Siswa;
use App\Models\TimPPDB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class ManajemenAdminController extends Controller
{
    public function index()
    {
        $this->authorize('is_admin');
        $data = User::where('role_id', 1)->paginate(12);
        return response()->json($data);
    }

    public function edit($id)
    {
        $this->authorize('is_admin');
        $data = User::where('id', $id)->first();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->authorize('is_admin');
        $this->validate($request, [
            'email' => 'required|email:dns|unique:users',
            'password' => 'required',
            'password_confirm' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user = new User();
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->role_id = 1;
            $user->save();
            DB::commit();
            return response()->json(200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(400);
        }
    }

    public function update(Request $request)
    {
        $this->authorize('is_admin');
        $dataUser = User::where('id', $request->id)->first();
        DB::beginTransaction();
        if ($dataUser) {
            try {
                $dataUser->email = strtolower($request->email);
                $dataUser->password = ($request->password === null ? $dataUser->password : Hash::make($request->password));
                $dataUser->save();
                DB::commit();
                return response()->json(200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function destroy($id)
    {
        $this->authorize('is_admin');
        $dataUser = User::where('id', $id)->first();
        DB::beginTransaction();
        if ($dataUser) {
            try {
                $dataUser->delete();
                DB::commit();
                return response()->json(200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json(500);
            }
        }
    }

    public function count()
    {
        $this->authorize('is_admin');
        return response()->json([
            'count_karya_citra' => KaryaCitra::count(),
            'count_karya_tulis' => KaryaTulis::count(),
            'akun_siswa' => Siswa::count(),
            'akun_guru' => Guru::count(),
            'akun_admin' => User::where('role_id', 1)->count(),
            'akun_tim' => TimPPDB::count()
        ], 200);
    }

    public function import(Request $request)
    {
        $this->authorize('is_admin');
        $import = Excel::import(new AdminImport, request()->file('file'), ExcelExcel::XLSX);
        if ($import) {
            return response()->json(['message' => 'Import successful', 200]);
        } else {
            return response()->json(['message' => 'Import failed', 400]);
        }
    }
}
