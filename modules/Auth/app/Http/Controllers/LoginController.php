<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Laravel\Passport\Client;
use Modules\Auth\App\Http\Requests\LoginRequest;
use Modules\User\App\Models\User;

class LoginController extends Controller
{
    /**
     * The client app requesting for authentication
     */
    private $client;

    /**
     * Authenticated user
     */
    private $user;

    /**
     * Extra data
     */
    public $data = [];

    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        return response(view('auth.login'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function client(Request $request)
    {
        /*
         * Validate: Client ID
         */
        $validation = Validator::make($request->all(), [
            'client_id' => ['required', Rule::exists(Client::class, 'id')],
        ]);

        if (in_array('client_id', array_keys($validation->errors()->toArray()))) {
            session()->flash('errors', $validation->errors()->messages());

            return response(view('auth::invalid-client', ['errors' => $validation->errors()]));
        }

        // Client: Get record
        $this->client = Client::find($request->client_id);

        // User: Get auth user
        $this->user = User::find(auth()->id());

        return response(view('auth::login', [
            'client' => $this->client,
            'user' => $this->user,
            'request' => $request->all(),
        ]));
    }

    public function authorize(LoginRequest $request)
    {
        if (! $this->client = Client::find($request->client_id)) {
            return abort(503, 'Client not found or missing.');
        }

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->to($this->client->redirect);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
