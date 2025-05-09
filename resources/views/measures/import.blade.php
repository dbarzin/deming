@extends("layout")

@section("content")
    <div data-role="panel" data-title-caption="{{ trans('cruds.imports.index') }}" data-collapsible="true" data-title-icon="<span class='mif-download'></span>">

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
                    <a href="/export/alices" target="_blank">{{ trans('cruds.imports.current') }}</a>
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
                        {{ trans('cruds.imports.or') }}
    					<input name="file" type="file" id="file" data-role="file" data-prepend="Select import file:">
    				</div>
    		    </div>
    			<div class="row">
    		        <div class="cell-4">
                        <input type="checkbox" data-role="checkbox" name="clean" id="clean" data-append="{{ trans('cruds.imports.remove_all') }}"/>
                    </div>
                </div>
    			<div class="row">
    		        <div class="cell-4">
                        <input type="checkbox" data-role="checkbox" name="test" data-append="{{ trans('cruds.imports.fake') }}"/>
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
                    {{ trans('cruds.imports.format') }}
		        </div>
		    </div>

            <div class="row">
                <div class="cell-md-6">
                    <table class="table striped border subcompact">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>A</td><td>{{ trans('cruds.imports.framework') }}</td><td>string(32)</td>
                                <td>{{ trans('cruds.imports.framework_helper') }}</td>
                            </tr>
                            <tr>
                                <td>B</td><td>{{ trans('cruds.imports.domain') }}</td><td>string(32)</td>
                                <td>{{ trans('cruds.imports.domain_helper') }}</td>
                            </tr>
                            <tr>
                                <td>C</td><td>{{ trans('cruds.imports.domain_description') }}</td><td>string(255)</td>
                                <td>{{ trans('cruds.imports.domain_description_helper') }}</td>
                            </tr>
                            <tr>
                                <td>D</td><td>{{ trans('cruds.imports.clause') }}</td><td>string(32)</td>
                                <td>
                                    {!! trans('cruds.imports.clause_helper') !!}
                                </td>
                            </tr>
                            <tr>
                                <td>E</td><td>{{ trans('cruds.imports.name') }}</td><td>string(255)</td>
                                <td>{{ trans('cruds.imports.name_helper') }}</td>
                            </tr>
                            <tr>
                                <td>F</td><td>{{ trans('cruds.imports.description') }}</td><td>text</td>
                                <td>{{ trans('cruds.imports.description_helper') }}</td>
                            </tr>
                            <tr>
                                <td>G</td><td>{{ trans('cruds.imports.attributes') }}</td><td>text</td>
                                <td>{{ trans('cruds.imports.attributes_helper') }}</td>
                            </tr>
                            <tr>
                                <td>H</td><td>{{ trans('cruds.imports.input') }}</td><td>text</td>
                                <td>{{ trans('cruds.imports.input_helper') }}</td>
                            </tr>
                            <tr>
                                <td>I</td><td>Model</td><td>text</td>
                                <td>{{ trans('cruds.imports.model_helper') }}</td>
                            </tr>
                            <tr>
                                <td>J</td><td>{{ trans('cruds.imports.indicator') }}</td><td>text</td>
                                <td>{{ trans('cruds.imports.indicator_helper') }}</td>
                            </tr>
                            <tr>
                                <td>K</td><td>{{ trans('cruds.imports.action') }}</td><td>text</td>
                                <td>{{ trans('cruds.imports.action_helper') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

			<div class="row">
		        <div class="cell-6 fg-red">
                    {{ trans('cruds.imports.warning') }}
		        </div>
		    </div>

@endsection
