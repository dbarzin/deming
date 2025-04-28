<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Deming :: Log in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-image: url('/images/deming.png');
            background-size:  800px 800px;
            background-position: center;
            background-repeat: no-repeat;
        }

        .login-form {
            background-color: rgba(255, 255, 255, 0.5) !important;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.7) !important;
            border: 1px solid rgba(0, 0, 0, 0.2);
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.9) !important;
        }
    </style>
</head>

<body class="d-flex flex-justify-center flex-align-center bg-default">
    <div class="login-form bg-white p-6 mx-auto border fg-black win-shadow">
        <form
            method="POST" action="/login"
            data-role="validator"
            action="javascript:"
            data-clear-invalid="2000"
            data-on-error-form="invalidForm">
            @csrf
                <div class="mb-4">{{ trans("cruds.login.connection") }}</div>
                <div class="form-group">
                    <input type="text" data-role="input" class="form-control @error('login') is-invalid @enderror @error('email') is-invalid @enderror" data-prepend="<span class='mif-user'></span>" name="login" value="{{ old('login') }}" id="login" required>
                    @error('login')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="password" data-role="input" class="form-control @error('password') is-invalid @enderror" data-prepend="<span class='mif-lock'></span>" name="password" value="{{ old('password') }}" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <span class="invalid_feedback">{{ trans("cruds.login.title") }}</span>
                </div>
                <div class="form-group d-flex flex-align-center flex-justify-between">
                    <button class="button primary">{{ trans("cruds.login.identification") }}</button>
                </div>
            </form>

        @if(count(Config::get('services.socialite_controller.providers')) > 0)
            <hr />
            @foreach(Config::get('services.socialite_controller.providers') as $provider)
            <div class="d-flex flex-align-center my-2">
                <a href="{{ route('socialite.redirect', $provider) }}"
                   class="button secondary w-100"
                   role="button"><span class="mif-share fg-white mr-2"></span>
                   {{ trans("cruds.login.connection_with") }}<strong>{{Config::get('services.socialite_controller.'.$provider.'.display_name')}}</strong></a>
            </div>
            @endforeach
                @if($errors->has('socialite'))
                <div class="d-flex flex-align-center my-2">
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('socialite') }}</strong>
                </div>
                @endif
        @endif
    </div>

    <script>
        function invalidForm(){
            var form  = $(this);
            form.addClass("ani-ring");
            setTimeout(function(){
                form.removeClass("ani-ring");
            }, 1000);
        }
    </script>
</body>
</html>
