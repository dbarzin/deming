
@if (isset($errors) && (count($errors)>0))
    <div class="mb-4">
        <div class="row">
            <div class="cell-lg-7 cell-md-10">
                <div data-role="directive" data-directive="caution">
        			@foreach ($errors->all() as $error)
                        {{ $error }}<br>
        			@endforeach
                </div>
            </div>
        </div>
    </div>
@endif

@if (isset($messages) && (count($messages)>0))
    <div class="mb-4">
        <div class="row">
            <div class="cell-lg-7 cell-md-10">
                <div data-role="directive" data-directive="info">
                    @foreach ($messages as $message)
                        {{ $message }}<br>
        			@endforeach
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('messages') && count(session('messages')) > 0)
    <div class="mb-4">
        <div class="row">
            <div class="cell-lg-7 cell-md-10">
                <div data-role="directive" data-directive="info">
                    @foreach (session('messages') as $message)
                        {{ $message }}<br>
        			@endforeach
                </div>
            </div>
        </div>
    </div>
@endif
