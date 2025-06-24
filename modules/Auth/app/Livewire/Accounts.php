<?php

namespace Modules\Auth\app\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Livewire\Component;

class Accounts extends Component
{
    /**
     * Requesting App client_id
     */
    public $client;

    /**
     * Auth user
     */
    public $user;

    /**
     * Original request
     */
    public $request;

    public function mount(Request $request)
    {
        $this->request = $request->all();
    }

    public function render()
    {
        return view('auth::livewire.accounts');
    }

    public function accept(Request $request)
    {
        $token = $this->user->createToken($this->client->name)->accessToken;
        $queries = Arr::query([
            'status' => 'success',
            'data' => [
                'user' => $this->user->toJson(),
                'access_token' => $token,
            ],
        ]);

        return redirect()->to("{$this->client->redirect}?{$queries}");
    }

    public function cancel(Request $request)
    {
        return redirect()->to($this->client->redirect);
    }
}
