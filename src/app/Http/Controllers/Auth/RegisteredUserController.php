<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * 登録ユーザーコントローラ
 */
class RegisteredUserController extends Controller
{
    /**
     * 登録ビューを表示
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * 登録リクエストを処理
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        /** @var \App\Models\User $user */
        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // メール認証画面に遷移
        return redirect()->route('verification.notice');
    }
}
