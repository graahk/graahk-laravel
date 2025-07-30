<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;

class Login extends Component
{
    public array $loginFields = [];
    public array $registerFields = [];

    public function mount()
    {
        app('site')->title('Login');
    }

    public function login()
    {
        $this->validate([
            'loginFields.username' => 'required',
            'loginFields.password' => 'required',
        ], [
            'loginFields.username.required' => 'Username is required',
            'loginFields.password.required' => 'Password is required',
        ]);

        if (auth()->attempt([
            'username' => $this->loginFields['username'],
            'password' => $this->loginFields['password'],
        ])) {
            return redirect()->to('/');
        }

        if (auth()->attempt([
            'email' => $this->loginFields['username'],
            'password' => $this->loginFields['password'],
        ])) {
            return redirect()->to('/');
        }

        $this->addError('loginFields.password', 'Invalid credentials');
    }

    public function register()
    {
        $this->validate([
            'registerFields.username' => 'required',
            'registerFields.email' => 'required|email|unique:users,email',
            'registerFields.password' => 'required|min:8',
            'registerFields.password_confirmation' => 'required|same:registerFields.password',
        ], [
            'registerFields.username.required' => 'Name is required',
            'registerFields.email.required' => 'E-mail is required',
            'registerFields.email.email' => 'E-mail is invalid',
            'registerFields.email.unique' => 'E-mail is already taken',
            'registerFields.password.required' => 'Password is required',
            'registerFields.password.min' => 'Password must be at least 8 characters',
            'registerFields.password_confirmation.required' => 'Password confirmation is required',
            'registerFields.password_confirmation.same' => 'Password confirmation must be same as password',
        ]);

        User::create([
            'username' => $this->registerFields['username'],
            'email' => $this->registerFields['email'],
            'password' => bcrypt($this->registerFields['password']),
        ]);

        auth()->attempt([
            'email' => $this->registerFields['email'],
            'password' => $this->registerFields['password'],
        ]);

        return redirect()->to('/');
    }
}
