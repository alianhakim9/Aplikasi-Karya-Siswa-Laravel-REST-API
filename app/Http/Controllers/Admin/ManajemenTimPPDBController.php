<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TimPPDBImport;
use App\Models\TimPPDB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ManajemenTimPPDBController extends Controller
{
    private $user;
    private $timPPDB;

    public function __construct(User $user, TimPPDB $timPPDB)
    {
        $this->user = $user;
        $this->timPPDB = $timPPDB;
    }

    public function index()
    {
        $data = $this->timPPDB::with('user')->paginate(5);
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $this->authorize('is_admin');
        $this->validate($request, [
            'email' => 'required|email:dns|unique:users',
            'password' => 'required',
            'password_confirm' => 'required',
            'nama_lengkap' => 'required',
            'jabatan' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $user = $this->user;
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->role_id = 4;
            $user->save();
            $timPPDB = $this->timPPDB;
            $timPPDB->nama_lengkap = ucfirst($request->nama_lengkap);
            $timPPDB->jabatan = ucfirst($request->jabatan);
            $timPPDB->user_id = $user->id;
            $timPPDB->save();
            DB::commit();
            return response()->json(201);
        } catch (\Throwable $th) {
            DB::rollback();
            return response($th->getMessage(), 500);
        }
    }

    public function edit($id)
    {
        $data = $this->timPPDB::with('user')->where('id', $id)->first();
        return response()->json($data, 200);
    }

    public function update(Request $request)
    {
        $this->authorize('is_admin');
        // get data timPPDB by id
        $datatimPPDB = $this->timPPDB::with('user')->where('id', $request->id)->first();
        $dataUser = $this->user::where('id', $datatimPPDB->user_id)->first();
        if ($datatimPPDB && $dataUser) {
            DB::beginTransaction();
            // try catch
            try {
                $dataUser->email = strtolower($request->email);
                $dataUser->password = ($request->password === null ? $dataUser->password : Hash::make($request->password));
                $dataUser->save();
                $datatimPPDB->nama_lengkap = ucfirst($request->nama_lengkap);
                $datatimPPDB->jabatan = ucfirst($request->jabatan);
                $datatimPPDB->save();
                DB::commit();
                return response()->json(201);
                return redirect()->route('edit-tim-ppdb', $datatimPPDB->id)->with('success', 'Data tim ppdb berhasil disimpan');
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function destroy($id)
    {
        $this->authorize('is_admin');
        $datatimPPDB = $this->timPPDB::with('user')->where('id', $id)->first();
        $dataUser = $this->user->where('id', $datatimPPDB->user_id)->first();
        if ($datatimPPDB && $dataUser) {
            DB::beginTransaction();
            // try catch
            try {
                $datatimPPDB->delete();
                $dataUser->delete();
                DB::commit();
                return response()->json(201);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json($th->getMessage(), 500);
            }
        }
    }

    public function import(Request $request)
    {
        $this->authorize('is_admin');
        $import = Excel::import(new TimPPDBImport, request()->file('file'));
        if ($import) {
            return response()->json(['message' => 'Import successful', 200]);
        } else {
            return response()->json(['message' => 'Import failed', 400]);
        }
    }
}
