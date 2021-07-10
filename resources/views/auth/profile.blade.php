@extends("layout")

@section("title")
Modifier le profil
@endsection

@section("content")

    @if (count($errors))
    <div class= “form-group”>
        <div class= “alert alert-danger”>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" role="form" enctype="multipart/form-data">
        @csrf                
        <ul class="form-style-1">
            <li>
                <label>Name</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}"  disabled>
            </li>
            <li>
                <label>Title</label>
                <input id="name" type="text" class="form-control" name="Title" value="{{ old('title', auth()->user()->title) }}"  disabled>
            </li>
            <li>
                <label>Email</label>                
                <input id="email" type="text" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}" disabled>
            </li>
            <li>
                <label>Profile Image</label><br>
                    @if (auth()->user()->profile_image)
                        <img src="{{asset('/storage/avatar/'.Auth::user()->id)}}" style="width: 100px; height: 100px; border-radius: 50%;">
                    @else
                        <img src="/images/user.jpeg" style="width: 100px; height: 100px; border-radius: 50%;">
                    @endif
                    <input type="file" data-role="file" data-prepend="Select your photo" name="profile_image">
            </li>
        </ul>
        <button type="submit" class="button success">Sauver</button>
        <button type="submit" class="button" onclick='this.form.method="GET";this.form.action="/";'><span class="mif-cancel"></span> Annuler</button>
    </form>

@endsection
