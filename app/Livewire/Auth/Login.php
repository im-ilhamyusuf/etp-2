<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        request()->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/peserta');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
