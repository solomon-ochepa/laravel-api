    <form action="{{ route('login') }}" method="POST">
        <div class="mb-7 text-center">
            <h3 class="text-1000">Sign In</h3>
            <p class="text-700">Get access to your <strong>{{ $client->name }}</strong> account</p>
        </div>

        {{-- Username --}}
        <div class="mb-3 text-start">
            <label class="form-label" for="email">{{ __('Email address') }}</label>
            <div class="form-icon-container">
                <input :value="old('email')" autocomplete="username" autofocus class="form-control form-icon-input"
                    id="email" name="email" placeholder="name@example.com" required type="email" />
                <span class="fas fa-user text-900 fs--1 form-icon"></span>
                @error('email')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3 text-start">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <div class="form-icon-container">
                <input autocomplete="current-password" class="form-control form-icon-input" id="password"
                    name="password" placeholder="{{ __('Password') }}" required type="password" />
                <span class="fas fa-key text-900 fs--1 form-icon"></span>
            </div>
            @error('password')
                <div class="form-text text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="row flex-between-center mb-7">
            <div class="col-auto">
                <div class="form-check mb-0">
                    <input checked="checked" class="form-check-input" id="basic-checkbox" type="checkbox" />
                    <label class="form-check-label mb-0" for="basic-checkbox">{{ __('Remember me') }} </label>
                </div>
            </div>

            <div class="col-auto">
                @if (Route::has('password.request'))
                    <a class="fs--1 fw-semi-bold"
                        href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                @endif
            </div>
        </div>

        <button class="btn btn-primary w-100 mb-3">{{ __('Sign In') }}</button>
        <div class="text-center">
            <a class="fs--1 fw-bold" href="sign-up.html">{{ __('Create an account') }}</a>
        </div>

        @csrf

        <input name="client_id" type="hidden" value="{{ $client->id }}" />
        <input name="request" type="hidden" value="@json($request ?? [])" />
    </form>

    {{--
        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" class="mb-4" />

        @if ($errors = json_decode(session('errors'), true))
            <h3 class="h3 border-b pt-4 text-red-400">Errors</h3>

            @foreach ($errors as $key => $items)
                <h4 class="font-bold text-red-400">&raquo; {{ $key }}</h4>
                <ul class="ps-4 text-gray-900 dark:text-gray-400">
                    @foreach ($items as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endforeach
        @endif
    --}}
