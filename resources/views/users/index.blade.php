@extends("layout")

@section("title")
Utilisateurs
@endsection

@section("content")

	<div class="grid">
		<div class="row">
			<div class="cell-5">

			</div>
			<div class="cell-7" align="right">
				@if (Auth::User()->role==1)
				<a href="/users/create"><span class="mif-add"></span>Nouveau</a>
				@endif
			</div>
		</div>

		<div class="row">
			<div class="cell-12">


		<table class="table striped row-hover cell-border"
	       data-role="table"
	       data-rows="10"
		   data-show-search="false"
	       data-show-activity="true"
	       data-rownum="false"
	       data-check="false"
	       data-check-style="1">
		    <thead>
		    <tr>
				<th 
					data-sortable="true"
					data-sort-dir="asc"
					data-format="string"
					width="50"
					>Login</th>
				<th 
					data-sortable="true"
					data-sort-dir="asc"
					data-format="string"
					width="50"
					>Nom</th>
				<th 
					data-sortable="true"
					data-sort-dir="asc"
					data-format="string"
					width="50"
					>Titre</th>
				<th 
					data-sortable="true"
					data-format="string"
					data-format="string"
					width="200">eMail</th>
		    </tr>
		    </thead>
		    <tbody>
		@foreach($users as $user)
			<tr>
				<td><a href="/users/{{ $user->id}}">{{ $user->login==null ? "N/A" : $user->login }}</a></td>
				<td>{{ $user->name }}</td>
				<td>{{ $user->title }}</td>
				<td>{{ $user->email }}</td>
			</tr>
		@endforeach
			</tbody>
		</table>
		<br>
	</div>
</div>


	
@endsection

