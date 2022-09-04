<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET USERS ORDER BY LATEST
        $users = User::latest()->when(request()->q, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->paginate(10);

        // RETURN VIEW
        return view('admin.user.index', compact('users'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        // RETURN VIEW
        return view('admin.user.create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // VALIDATION RULES
        $this->validate($request, [
            'name'       => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|confirmed'
        ]);

        // SAVE TO DB
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
        ]);

        // REDIRECT WITH MESSAGE
        if ($user) {
            return redirect()->route('admin.user.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            return redirect()->route('admin.user.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * edit
     *
     * @param  mixed $user
     * @return void
     */
    public function edit(User $user)
    {
        // RETURN VIEW
        return view('admin.user.edit', compact('user'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function update(Request $request, User $user)
    {
        // VALIDATION RULES
        $this->validate($request, [
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => 'required|confirmed'
        ]);

        // CHECK IF PASSWORD IS NULL(?)
        if ($request->password == '') {
            // UPDATE WITHOUT PASSWORD
            $user = User::findOrFail($user->id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);
        } else {
            // UPDATE WITH PASSWORD
            $user = User::findOrFail($user->id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
        }

        // REDIRECT WITH MESSAGE
        if ($user) {
            return redirect()->route('admin.user.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } else {
            return redirect()->route('admin.user.index')->with(['error' => 'Data Gagal Diperbarui!']);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        // GET USER BY ID
        $user = User::findOrFail($id);

        // DELETE USER
        $user->delete();

        // RETURN RESPONSE
        if ($user) {
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
