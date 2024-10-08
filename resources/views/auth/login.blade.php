<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Deming :: Log in</title>

    <link rel="stylesheet" href="/css/all.css" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <script src="/js/all.js"></script>

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
    <form
          method="POST" action="/login"
          class="login-form bg-white p-6 mx-auto border fg-black win-shadow"
          data-role="validator"
          action="javascript:"
          data-clear-invalid="2000"
          data-on-error-form="invalidForm"
          data-on-validate-form="validateForm">
        @csrf
            <div class="mb-4">Login</div>
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

    <script>
        function invalidForm(){
            var form  = $(this);
            form.addClass("ani-ring");
            setTimeout(function(){
                form.removeClass("ani-ring");
            }, 1000);
        }

   $(document).ready(function() {
      setTimeout(function() {
          $("#login").focus();
      }, 1500);
   });

    </script>
</body>
</html>
