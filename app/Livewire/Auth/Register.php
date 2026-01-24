<?php

namespace App\Livewire\Auth;

use App\Models\Peserta;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if ($user) {
            Peserta::create([
                'user_id' => $user->id
            ]);
        }

        Auth::login($user);

        return redirect('/peserta');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
