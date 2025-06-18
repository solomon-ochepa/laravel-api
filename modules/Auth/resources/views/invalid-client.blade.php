<x-guest-layout>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

    <h3 class="text-xl border-b text-gray-600 dark:text-gray-400 mb-4 pb-1 font-bold">Access blocked: Authorization Error</h3>

    <p class="text-gray-600 dark:text-gray-400 mb-4">
        {{-- &raquo; The Auth client was not found. --}}

        @if (session('errors'))
            <ul class="text-gray-600 dark:text-gray-400 gap-3">
                @foreach (session('errors') as $key => $error)
                    {{-- <p>{{ $key }}</p> --}}
                    @foreach ($error as $item)
                        <li class="mb-1">{{ $item }}</li>
                    @endforeach
                @endforeach
            </ul>
        @endif
    </p>

    {{-- <p class="text-gray-600 dark:text-gray-400 mb-4">If you are a developer of this app, see error details.</p> --}}

    <p class="text-gray-600 dark:text-gray-400 border-t mt-4 pt-1 border-gray-400 border-dotted">Error 401: invalid_client</p>
</x-guest-layout>
