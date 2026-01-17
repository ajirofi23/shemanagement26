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
    $query = DB::table('tb_user');

    // Search
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%$search%")
              ->orWhere('usr', 'like', "%$search%")
              ->orWhere('kode_user', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    $users = $query->get();

    // Ambil permission user login untuk menu /it/management-user
    $permission = DB::table('tb_user_permissions as up')
        ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
        ->where('up.user_id', Auth::id())
        ->where('m.url', '/it/management-user')
        ->select('up.can_edit', 'up.can_delete')
        ->first();

    return view('IT.managementuser', compact('users', 'permission'));
}

public function update(Request $request, $id)
{
    $permission = DB::table('tb_user_permissions as up')
        ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
        ->where('up.user_id', Auth::id())
        ->where('m.url', '/it/management-user')
        ->select('up.can_edit')
        ->first();

    if (!($permission && $permission->can_edit)) {
        return redirect('/it/management-user')->with('error', 'Anda tidak punya izin mengedit user.');
    }

    $request->validate([
        'nama' => 'required|string|max:100',
        'usr' => 'required|string|max:50',
        'email' => 'required|email|max:100',
        'kode_user' => 'nullable|string|max:50'
    ]);

    DB::table('tb_user')->where('id', $id)->update([
        'nama' => $request->nama,
        'usr' => $request->usr,
        'email' => $request->email,
        'kode_user' => $request->kode_user,
        
    ]);

    return redirect('/it/management-user')->with('success', 'User berhasil diupdate.');
}

public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'usr' => 'required|string|max:50|unique:tb_user,usr',
        'password' => 'required|string|min:6',
        'email' => 'nullable|email|max:100',
        'kode_user' => 'nullable|string|max:50',
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
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('images/sign'), $filename);
        $data['image_sign'] = $filename;
    }

    \App\Models\User::create($data);

    return redirect('/it/management-user')->with('success', 'User berhasil ditambahkan.');
}

public function destroy($id)
{
    $permission = DB::table('tb_user_permissions as up')
        ->join('tb_menus as m', 'up.menu_id', '=', 'm.id')
        ->where('up.user_id', Auth::id())
        ->where('m.url', '/it/management-user')
        ->select('up.can_delete')
        ->first();

    if ($permission && $permission->can_delete) {
        DB::table('tb_user')->where('id', $id)->delete();
        return redirect('/it/management-user')->with('success', 'User berhasil dihapus.');
    } else {
        return redirect('/it/management-user')->with('error', 'Anda tidak punya izin menghapus user.');
    }
}
}