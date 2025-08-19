<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1" name="viewport">

        <title>{{ config('app.name') }} - Authorization</title>

        <!-- Styles -->
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet">

        <style>
            .passport-authorize .container {
                margin-top: 30px;
            }

            .passport-authorize .scopes {
                margin-top: 20px;
            }

            .passport-authorize .buttons {
                margin-top: 25px;
                text-align: center;
            }

            .passport-authorize .btn {
                width: 125px;
            }

            .passport-authorize .btn-approve {
                margin-right: 15px;
            }

            .passport-authorize form {
                display: inline;
            }
        </style>
    </head>

    <body class="passport-authorize">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header">
                            Authorization Request
                        </div>
                        <div class="card-body">
                            <!-- Introduction -->
                            <p><strong>{{ $client->name }}</strong> is requesting permission to access your account.</p>

                            <!-- Scope List -->
                            @if (count($scopes) > 0)
                                <div class="scopes">
                                    <p><strong>This application will be able to:</strong></p>

                                    <ul>
                                        @foreach ($scopes as $scope)
                                            <li>{{ $scope->description }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="buttons">
                                <!-- Authorize Button -->
                                <form action="{{ route('passport.authorizations.approve') }}" method="post">
                                    @csrf

                                    <input name="state" type="hidden" value="{{ $request->state }}">
                                    <input name="client_id" type="hidden" value="{{ $client->getKey() }}">
                                    <input name="auth_token" type="hidden" value="{{ $authToken }}">
                                    <button class="btn btn-success btn-approve" type="submit">
                                        Authorize ({{ auth()->user()->username }})
                                    </button>
                                </form>

                                @route('logout')
                                    <!-- Logout -->
                                    <form action="{{ route('logout') }}" method="post">
                                        @csrf

                                        <input name="state" type="hidden" value="{{ $request->state }}">
                                        <input name="client_id" type="hidden" value="{{ $client->getKey() }}">
                                        <input name="auth_token" type="hidden" value="{{ $authToken }}">
                                        <button class="btn btn-success btn-approve" type="submit">Logout</button>
                                    </form>
                                @endroute

                                <!-- Cancel Button -->
                                <form action="{{ route('passport.authorizations.deny') }}" method="post">
                                    @csrf
                                    @method('DELETE')

                                    <input name="state" type="hidden" value="{{ $request->state }}">
                                    <input name="client_id" type="hidden" value="{{ $client->getKey() }}">
                                    <input name="auth_token" type="hidden" value="{{ $authToken }}">
                                    <button class="btn btn-danger">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
