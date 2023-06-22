@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="{{ trans('cruds.imports.index') }}" data-collapsible="true" data-title-icon="<span class='mif-chart-line'></span>">

			@if (count($errors))
			<div class="form-group">
				<div class= "remark alert alert-danger">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			</div>
			@endif

			<a href="/export/measures" target="_blank">Current security Measures</a>

			<div class="row">
		        <div class="cell-3">
					<b>{{ trans('cruds.imports.title') }}</b>
				</div>
			</div>

			<form action="/measure/import" method="post" enctype="multipart/form-data">
			@csrf
			<div class="row">
		        <div class="cell-5">
					<input name="file" type="file" data-role="file" data-prepend="Select import file:">
				</div>
		        <div class="cell-1">
				    <button type="submit" class="button success drop-shadow">Import</button>
		        </div>
		    </div>


			<div class="row">
		        <div class="cell-5">
		        	The import format is an XLSX document with these columns :
		        </div>
		    </div>
			<div class="row">
		        <div class="cell-6">

		    	<table 	class="table subcompact">
		    		<thead>
		        		<tr>
		        			<td>Column</td><td>Name</td><td>Type</td><td>Description</td>
		        		</tr>
		    		</thead>
		    		<tbody>
		        		<tr>
		        			<td>A</td><td>Domain</td><td>string(32)</td>
		        			<td>
		        				The domains name must be provided, it is created if it does not exists.
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>B</td><td>Clause</td><td>string(32)</td>
		        			<td>
		        					If the clause exists the security measure is updated,<br>
		        					if the clause does not exists, a new security measure is created,<br>
		        					if all other fields of the line are empty, the measure, related controls and documents are removed.
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>C</td><td>Name</td><td>string(255)</td><td>The name of the security measure</td>
		        		</tr>
		        		<tr>
		        			<td>D</td><td>Description</td><td>text</td><td>The description of the security measure</td>
		        		</tr>
		        		<tr>
		        			<td>E</td><td>Attributes</td><td>text</td><td>List of tags (#... #... #...)</td>
		        		</tr>
		        		<tr>
		        			<td>F</td><td>Input</td><td>text</td><td>The input elements</td>
		        		</tr>
		        		<tr>
		        			<td>G</td><td>Model</td><td>text</td><td>The computation model</td>
		        		</tr>
		        		<tr>
		        			<td>H</td><td>Indicator</td><td>text</td><td>The indicator (Green, Orange, Red)</td>
		        		</tr>
		        		<tr>
		        			<td>I</td><td>Action plan</td><td>text</td><td>The proposed action plan</td>
		        		</tr>
		        	</tbody>
		    	</table>

		        </div>
		    </div>

			<div class="row">
		        <div class="cell-6">
		        	This action could not be undone, take a backup before !
		        </div>
		    </div>


			</form>

		</div>
@endsection