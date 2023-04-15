<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Deming :: Log in</title>

    <link rel="stylesheet" href="/css/all.css" />
    <script src="/js/all.js"></script>
</head>

<body class="d-flex flex-justify-center flex-align-center bg-default">
    <form  
          method="POST" action="{{ route('login') }}"
          class="login-form bg-white p-6 mx-auto border fg-black win-shadow"
          data-role="validator"
          action="javascript:"
          data-clear-invalid="2000"
          data-on-error-form="invalidForm"
          data-on-validate-form="validateForm">
        @csrf
        <span class="mif-lock mif-4x place-right ani-shake fg-cyan" style="margin-top: -10px;"></span>
            <h2 class="text-medium m-0 pl-7" style="line-height: 52px">Deming</h2>
            <div class="mb-4">{{ trans("cruds.login.connection") }}</div>
            <div class="form-group">
                <input type="text" data-role="input" class="form-control @error('email') is-invalid @enderror" data-prepend="<span class='mif-user'></span>" name="email" value="{{ old('email') }}" required autofocus>
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

