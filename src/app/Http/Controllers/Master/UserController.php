<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $users = User::with('manager')->orderBy('used', 'desc')->orderBy('id')->paginate(15);

        $managers = User::where('used', 1)->orderBy('department')->orderBy('id')->get();

        return view('master.user.index', compact('users', 'managers'));
    }


    /**
     * 更新
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'email_verified_at' => ['nullable', 'date'],
            'department'        => ['nullable', 'string', 'max:255'],
            'section'           => ['nullable', 'string', 'max:255'],
            'position'          => ['nullable', 'string', 'max:255'],
            'manager_id'        => ['nullable', 'integer', 'exists:users,id'],
            'used'              => ['nullable', 'integer'],
            'auth_type'         => ['nullable', 'integer'],
        ]);

        $user->update($request->all());

        return redirect()->route('master.users.index')->with('success', 'ユーザー情報を更新しました。');
    }

    public function create()
    {

        $managers = User::where('used', 1)->orderBy('department')->orderBy('id')->get();

        return view('master.user.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'department' => ['nullable', 'max:255'],
            'section'    => ['nullable', 'max:255'],
            'position'   => ['nullable', 'max:255'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'auth_type'  => ['required', 'in:0,1,2'],
        ]);

        $password = $this->generatePassword();

        User::create([
            ...$validated,
            'password' => Hash::make($password),
            'used' => 1,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'ユーザーを登録しました')
            ->with('generated_password', $password);
    }

    private function generatePassword(int $length = 8): string
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnpqrstuvwxyz';
        $numbers = '23456789';

        $password = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
        ];

        $all = $upper . $lower . $numbers;

        while (count($password) < $length) {
            $password[] = $all[random_int(0, strlen($all) - 1)];
        }

        shuffle($password);

        return implode('', $password);
    }
}
