<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Deming :: Log in</title>

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url('/images/deming.png');
            background-size:  800px 800px;
            background-position: center;
            background-repeat: no-repeat;
        }

        .navview-content {
            background: url("/images/bg-1.jpg") bottom right no-repeat;
            background-size: cover;
        }

        .dark-side {
            .navview-content {
                background: url("/images/bg-2.jpg") bottom right no-repeat;
            }
        }
    </style>
</head>
<body>
<form
    method="POST" action="/login"
    data-role="validator"
    action="javascript:"
    data-clear-invalid="2000"
    data-on-error-form="invalidForm"
    data-on-validate-form="validateForm">
    @csrf
    <div class="h-100 d-flex flex-center" style="margin-top: 280px;">
        <div class="row w-100 flex-justify-content-center">
            <div class="cell-md-6 d-flex flex-center">
                <div class="box shadow-large-extra fg-black bd-black" style='background-color: rgba(255, 255, 255, 0.5)'>
                    <div class="h3 text-weight-7 bd-black">{{ trans("cruds.login.connection") }}</div>
                        <div class="form-group">
                            <input type="text" data-role="input" class="form-control @error('login') is-invalid @enderror @error('email') is-invalid @enderror" data-prepend="<span class='mif-person'></span>" name="login" value="{{ old('login') }}" id="login" required>
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
                        </div>
                        <div class="mt-5 d-flex flex-justify-start flex-align-center">
                            <button class="button primary">{{ trans("cruds.login.identification") }}</button>
                        </div>
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
            </div>
        </div>
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


<!--
    <div class="login-form bg-white p-6 mx-auto border fg-black win-shadow">
<!-- Splash screen

        <form
            method="POST" action="/login"
            data-role="validator"
            action="javascript:"
            data-clear-invalid="2000"
            data-on-error-form="invalidForm"
            data-on-validate-form="validateForm">
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
-->
