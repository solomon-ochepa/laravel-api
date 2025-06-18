<?php

namespace Modules\Auth\app\Livewire;

use Livewire\Component;

class Login extends Component
{
    public $client;

    public $user;

    public function render()
    {
        return view('auth::livewire.login');
    }
}
