@extends("layout")

@section("title")
Liste des contrôles
@endsection

@section("content")

<!--
 <div class="grid">
    <div class="row">
        <div class="cell"><div><h1 class="title">Controls</h1></div></div>
        <div class="cell"><div>
        	<form method="GET" action="/controls/create">
				<div class="multi-action">
				    <button class="action-button rotate-minus bg-green fg-white"
				            onclick="$(this).toggleClass('active')">
				        <span class="icon"><span class="mif-plus"></span></span>
				    </button>
				</div>
			</form>
	</div></div>
	</div></div>
-->	

	<div class="grid">
		<div class="row">
			<div class="cell-1">
	    		<strong>Domain</strong>
	    	</div>
			<div class="cell-4">
				<select id='domain_id' name="domain_id" size="1" width='10'>
				    <option value="0">-- Choisir un domaine --</option>
					@foreach ($domains as $domain)
				    	<option value="{{ $domain->id }}"
							@if (((int)Session::get("domain"))==$domain->id)		
								selected 
							@endif >
				    		{{ $domain->title }} - {{ $domain->description }}
				    	</option>
				    @endforeach
				</select>
			</div>
			<div class="cell-7" align="right">
				<a href="/controls/create"><span class="mif-add"></span>Nouveau</a>
			</div>
		</div>

	<script>
		window.addEventListener('load', function(){
	    var select = document.getElementById('domain_id');

	    select.addEventListener('change', function(){
	        window.location = '/controls?domain=' + this.value;
	    }, false);
	}, false);
	</script>

		<div class="row">
			<div class="cell">

	<table class="table striped row-hover cell-border"
       data-role="table"
       data-rows="10"
       data-show-activity="true"
       data-rownum="false"
       data-check="false"
       data-check-style="1">
	    <thead>
	    <tr>
			<th class="sortable-column sort-asc" width="10%">Domaine</th>
			<th class="sortable-column sort-asc" width="10%">Clause</th>
			<th class="sortable-column sort-asc" width="70%">Nom</th>
			<th width="10%">Actif</th>
	    </tr>
	    </thead>
	    <tbody>
	@foreach($controls as $control)
		<tr>
			<td>
				<a href="/domains/{{$control->domain_id}}">
					{{ $control->domain($control->domain_id)->title }}
				</a>
			</td>
			<td><a href="/controls/{{ $control->id}}">
				@if (strlen($control->clause)==0)
					None
				@else
					{{ $control->clause }}
				@endif
				</a>
			</td>
			<td>{{ $control->name }}</td>
			<td>
				<input type="checkbox" data-role="switch" data-material="true"
					@if ($control->isActive($control->id))
						checked 
					@endif
					onclick="handleClick(this,{{ $control->id }});">
			</td>
		</tr>
	@endforeach
	</tbody>
	</table>
</div>
</div>
<script type="text/javascript">
function handleClick(cb, id) {
  console.log(id + " clicked, new value = " + cb.checked);
  if (cb.checked)
	  $.ajax({
	    type: 'GET',
	    url: '{{ url( "/controls/activate" ) }}'+"?id="+id,
	    success: function (data){
	        console.log("Control "+id+" activated");
	    },
	    error: function(e) {
	        console.log("Error control "+id+" not activated");
	        console.log(e);
	    }});
	else
	  $.ajax({
	    type: 'GET',
	    url: '{{ url( "/controls/disable" ) }}'+"?id="+id,
	    success: function (data){
	        console.log("Control "+id+" disabled");
	    },
	    error: function(e) {
	        console.log("Error control "+id+" not disabled");
	        console.log(e);
	    }});
}
</script>
@endsection
