@if (isset($errors) && (count($errors)>0))
    <div class="alert alert-danger col-lg-7 col-md-10" role="alert">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif

@if (isset($messages) && (count($messages)>0))
    <div class="alert alert-info col-lg-7 col-md-10" role="alert">
        @foreach ($messages as $message)
            {{ $message }}<br>
        @endforeach
    </div>
@endif

@if (session('messages') && count(session('messages')) > 0)
    <div class="alert alert-info col-lg-7 col-md-10" role="alert">
        @foreach (session('messages') as $message)
            {{ $message }}<br>
        @endforeach
    </div>
@endif
