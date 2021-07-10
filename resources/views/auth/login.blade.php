<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Metro 4 -->
    <link rel="stylesheet" href="vendors/metro4/css/metro-all.min.css">
    <link rel="stylesheet" href="/css/index.css">

    <title>Deming :: Log in</title>
</head>


<body class="d-flex flex-justify-center flex-align-center bg-default">
    <form  
          method="POST" action="{{ route('login') }}"
          class="login-form bg-white p-6 mx-auto border bg-chem fg-white win-shadow"
          data-role="validator"
          action="javascript:"
          data-clear-invalid="2000"
          data-on-error-form="invalidForm"
          data-on-validate-form="validateForm">
        @csrf
        <span class="mif-vpn-lock mif-4x place-right" style="margin-top: -10px;"></span>
            <h2 class="text-medium m-0 pl-7" style="line-height: 52px">Deming</h2>
            <div class="text-muted mb-4">Sign in to start your session</div>
            <div class="form-group">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <span class="invalid_feedback">Please enter a password</span>
            </div>
            <div class="form-group d-flex flex-align-center flex-justify-between">
                <input type="checkbox" data-role="checkbox" data-caption="Remember Me">
                <button class="button">Sign In</button>
            </div>
        </form>

    <script src="vendors/jquery/jquery-3.4.1.min.js"></script>
    <script src="vendors/metro4/js/metro.min.js"></script>
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

