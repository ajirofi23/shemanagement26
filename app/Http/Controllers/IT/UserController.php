<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | USER + SECTION (JOIN)
        |--------------------------------------------------------------------------
        */
        $query = DB::table('tb_user as u')
            ->leftJoin('tb_section as s', 'u.section_id', '=', 's.id')
            ->select(
                'u.*',
                's.section as section_name'
            );

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('u.nama', 'like', "%$search%")
                    ->orWhere('u.usr', 'like', "%$search%")
                    ->orWhere('u.kode_user', 'like', "%$search%")
                    ->orWhere('u.email', 'like', "%$search%");
            });
        }

        $users = $query->orderBy('u.nama')->get();

        /*
        |--------------------------------------------------------------------------
        | SEMUA SECTION (UNTUK DROPDOWN)
        |--------------------------------------------------------------------------
        */
        $sections = DB::table('tb_section')
            ->orderBy('section', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */
        $permission = DB::table('tb_user_permissions as up')
            ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
            ->where('up.user_id', Auth::id())
            ->where('m.url', '/it/management-user')
            ->select('up.can_edit', 'up.can_delete')
            ->first();

        return view('IT.managementuser', compact(
            'users',
            'sections',
            'permission'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'usr' => 'required|string|max:50|unique:tb_user,usr',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email|max:100',
            'kode_user' => 'nullable|string|max:50',
            'section_id' => 'nullable|integer',
            'level' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'is_user_computer' => 'required|boolean',
        ]);

        $data = [
            'nama' => $request->nama,
            'usr' => $request->usr,
            'pswd' => md5($request->password),
            'email' => $request->email,
            'kode_user' => $request->kode_user,
            'section_id' => $request->section_id,
            'level' => $request->level,
            'is_active' => $request->is_active,
            'is_user_computer' => $request->is_user_computer,
        ];

        if ($request->hasFile('image_sign')) {
            $file = $request->file('image_sign');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/sign'), $filename);
            $data['image_sign'] = $filename;
        }

        DB::table('tb_user')->insert($data);

        return redirect('/it/management-user')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'usr' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'kode_user' => 'nullable|string|max:50',
            'section_id' => 'nullable|integer',
            'level' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'is_user_computer' => 'required|boolean',
        ]);

        $data = [
            'nama' => $request->nama,
            'usr' => $request->usr,
            'email' => $request->email,
            'kode_user' => $request->kode_user,
            'section_id' => $request->section_id,
            'level' => $request->level,
            'is_active' => $request->is_active,
            'is_user_computer' => $request->is_user_computer,
        ];

        if ($request->filled('password')) {
            $data['pswd'] = md5($request->password);
        }

        if ($request->hasFile('image_sign')) {
            $file = $request->file('image_sign');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/sign'), $filename);
            $data['image_sign'] = $filename;
        }

        DB::table('tb_user')->where('id', $id)->update($data);

        return redirect('/it/management-user')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy($id)
    {
        DB::table('tb_user')->where('id', $id)->delete();

        return redirect('/it/management-user')
            ->with('success', 'User berhasil dihapus.');
    }
}
