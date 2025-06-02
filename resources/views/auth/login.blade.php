<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Deming :: Log in</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background-image: url('/images/deming.png');
            background-size: 800px 800px;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navview-content {
            background: url("/images/bg-1.jpg") bottom right no-repeat;
            background-size: cover;
        }

        .dark-side .navview-content {
            background: url("/images/bg-2.jpg") bottom right no-repeat;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.75);
            padding: 2rem;
            border: 1px solid black;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
@if (!app()->environment('production'))
<div class="app-bar pos-fixed bg-orange fg-white" data-role="appbar">
    <div class="app-bar-section">
        <span class="mif-warning"></span> {{ trans('menu.test') }}
    </div>
</div>
@endif
<form
    method="POST"
    action="/login"
    data-role="validator"
    data-clear-invalid="2000"
    data-on-error-form="invalidForm"
    data-on-validate-form="validateForm">
    @csrf

    <div class="login-box">
        <div class="h3 text-weight-7">{{ trans("cruds.login.connection") }}</div>

        <div class="form-group">
            <input type="text" data-role="input" name="login" id="login" required
                   class="form-control @error('login') is-invalid @enderror @error('email') is-invalid @enderror"
                   value="{{ old('login') }}"
                   data-prepend="<span class='mif-person'></span>">
            @error('login')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
            @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
        </div>

        <div class="form-group">
            <input type="password" data-role="input" name="password" required
                   class="form-control @error('password') is-invalid @enderror"
                   data-prepend="<span class='mif-lock'></span>">
            @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
        </div>

        <div class="mt-5 d-flex flex-justify-start flex-align-center">
            <button class="button primary">{{ trans("cruds.login.identification") }}</button>
        </div>

        @if(count(Config::get('services.socialite_controller.providers')) > 0)
        <hr />
        @foreach(Config::get('services.socialite_controller.providers') as $provider)
        <div class="d-flex flex-align-center my-2">
            <a href="{{ route('socialite.redirect', $provider) }}" class="button secondary w-100">
                <span class="mif-share fg-white mr-2"></span>
                {{ trans("cruds.login.connection_with") }} <strong>{{ Config::get('services.socialite_controller.'.$provider.'.display_name') }}</strong>
            </a>
        </div>
        @endforeach
        @if($errors->has('socialite'))
        <span class="invalid-feedback"><strong>{{ $errors->first('socialite') }}</strong></span>
        @endif
        @endif
    </div>
</form>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $("#login").focus();
        }, 1500);
    });
</script>
</body>
</html>
