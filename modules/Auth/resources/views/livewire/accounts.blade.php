<x-guest-layout>
    <div class="mb-4 text-center">
        <h3 class="text-1000">Choose an account</h3>
        <p class="text-700 small">to continue to <strong>{{ $client->name }}</strong></p>

        <div class="text-end">
            <button class="btn btn-sm bg-soft-danger -px-1 rounded border dark:border-gray-700 dark:hover:bg-gray-700"
                wire:click="cancel()">
                <i aria-hidden="true" class="fa fa-user-times me-1"></i>
                Cancel
            </button>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" class="mb-4" />

    <form action="{{ route('login') }}" method="POST">
        <div class="text-muted mt-4">
            <div class="position-relative d-flex align-items-center group gap-3 rounded p-3 text-sm">
                <div
                    class="d-flex justify-content-center fa-xl align-items-center bg-light group-hover-bg-light rounded">
                    <i aria-hidden="true" class="fa-solid fa-xl fa-user-circle"></i>
                </div>

                <div class="flex-grow-1">
                    <a class="font-weight-bold text-dark" href="javascript://" wire:click="accept(@js($user->email))">
                        {{ $user->name }}
                        <span class="stretched-link"></span>
                    </a>
                    <p class="text-muted m-0">{{ $user->email }}</p>
                </div>

                <div class=""> <i class="fa fa-caret-right" aria-hidden="true"></i> </div>
            </div>
        </div>

        <p class="text-700 border-top py-3">
            To continue, {{ config('app.name') }} will share your name, email address, language preference, and
            profile picture with {{ $client->name }}.<br />
            Before using this app, you can review {{ $client->name }}’s <em>privacy policy</em> and <em>terms of
                service</em>.
        </p>

        @csrf
        <input name="client_id" type="hidden" value="{{ $client->id }}" />
    </form>
</x-guest-layout>
