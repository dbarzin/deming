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

			<div class="row">
		        <div class="cell-5">
					<b>{{ trans('cruds.imports.title') }}</b>
                    /
                    <a href="/export/alices" target="_blank">Current security Measures</a>
				</div>
			</div>

			<form action="/alice/import" method="post" enctype="multipart/form-data">
			@csrf
    			<div class="row">
    		        <div class="cell-4">
        				<select data-role="select" name="model" id="model" data-prepend="Select model">
                            <option id="emptyOption"></option>
    						@foreach($models as $model)
                                <option>{{ basename($model,'.xlsx') }}</option>
                            @endforeach
                        </select>
                    </div>
    		        <div class="cell-1" align="right">
    				    <button type="submit" name="action" value="download" class="button info drop-shadow">
                                {{ trans("common.download") }}
                        </button>
    		        </div>
                </div>
    			<div class="row">
    		        <div class="cell-5">
                        or
    					<input name="file" type="file" id="file" data-role="file" data-prepend="Select import file:">
    				</div>
    		    </div>
    			<div class="row">
    		        <div class="cell-4">
                        <input type="checkbox" name="clean" id="clean">
                        Remove all other measures and controls
                    </div>
                </div>
    			<div class="row">
    		        <div class="cell-4">
                        <input type="checkbox" name="test">
                        Generate fake measurements
                    </div>
    		        <div class="cell-1" align="right">
    				    <button type="submit" class="button success drop-shadow"
                            onclick="if ($('#clean').is(':checked')) return confirm('Do you want to remove all other measures and controls  ?')">
                                {{ trans("common.import") }}
                        </button>
    		        </div>
                </div>

            </form>


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
		        			<td>A</td><td>Framework</td><td>string(32)</td>
		        			<td>
                                The security framework used.
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>B</td><td>Domain name</td><td>string(32)</td>
		        			<td>
		        				The domains name, it is created if it does not exists.
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>C</td><td>Domain description</td><td>string(255)</td>
		        			<td>
                                The description of the domain.
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>D</td><td>Clause</td><td>string(32)</td>
		        			<td>
		        					If the clause exists the security measure is updated,<br>
		        					if the clause does not exists, a new security measure is created,<br>
		        					if all other fields of the line are empty, the measure, related controls and documents are removed.
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>E</td><td>Name</td><td>string(255)</td><td>The name of the security measure</td>
		        		</tr>
		        		<tr>
		        			<td>F</td><td>Description</td><td>text</td><td>The description of the security measure</td>
		        		</tr>
		        		<tr>
		        			<td>G</td><td>Attributes</td><td>text</td><td>List of tags (#... #... #...)</td>
		        		</tr>
		        		<tr>
		        			<td>H</td><td>Input</td><td>text</td><td>The input elements</td>
		        		</tr>
		        		<tr>
		        			<td>I</td><td>Model</td><td>text</td><td>The computation model</td>
		        		</tr>
		        		<tr>
		        			<td>J</td><td>Indicator</td><td>text</td><td>The indicator (Green, Orange, Red)</td>
		        		</tr>
		        		<tr>
		        			<td>K</td><td>Action plan</td><td>text</td><td>The proposed action plan</td>
		        		</tr>
		        	</tbody>
		    	</table>

		        </div>
		    </div>

			<div class="row">
		        <div class="cell-6 fg-red">
		        	This action could not be undone, take a backup before !
		        </div>
		    </div>

		</div>
@endsection
