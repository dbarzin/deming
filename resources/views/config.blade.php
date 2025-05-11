@extends("layout")

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.config.notifications.title') }}" data-collapsible="true" data-title-icon="<span class='mif-alarm'></span>">

    @include('partials.errors')

    <form method="POST" action="/config/save" enctype="multipart/form-data">
        @csrf

		<div class="grid">
	    	<div class="row">
	    		<div class="cell-6">
		            <label for="name">{{ trans("cruds.config.notifications.help") }}</label>
		        </div>
	        </div>

	    	<div class="row">
	    		<div class="cell-6">
		        </div>
	        </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.config.notifications.sent_from") }}</strong>
		    	</div>
				<div class="cell-8">
		            <input type="text" name="mail_from" id="mail_from" value="{{ $mail_from }}" required size=64/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.config.notifications.message_subject") }}</strong>
		    	</div>
				<div class="cell-8">
		            <input type="text" name="mail_subject" id="mail_subject" value="{{ $mail_subject }}" required size=64/>
				</div>
			</div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.config.notifications.message_content") }}</strong>
		    	</div>
				<div class="cell-6">
		            <textarea name="mail_content" rows="10" data-role="textarea" data-clear-button="false">{{ strlen($mail_content)==0 ? trans('cruds.config.notifications.message_default_content') : $mail_content }}</textarea>
				</div>
			</div>

    	<div class="row">
    		<div class="cell-1">
	    		<strong>{{ trans("cruds.config.notifications.recurrence") }}</strong>
	    	</div>
			<div class="cell-4">
	            <select data-role="select" name="frequency" id="frequency">
	                <option value="0" {{ $frequency=="0" ? 'selected' : '' }}>{{ trans("common.never") }}</option>
	                <option value="1" {{ $frequency=="1" ? 'selected' : '' }}>{{ trans("common.day") }}</option>
	                <option value="7" {{ $frequency=="7" ? 'selected' : '' }}>{{ trans("common.week") }}</option>
	                <option value="30" {{ $frequency=="30" ? 'selected' : '' }}>{{ trans("common.month") }}</option>
	            </select>
	        </div>
	    </div>

	    	<div class="row">
	    		<div class="cell-1">
		    		<strong>{{ trans("cruds.config.notifications.delay") }}</strong>
		    	</div>
				<div class="cell-4">
		            <select data-role="select" name="expire_delay" id="expire_delay">
		                <option value="1" {{ $expire_delay=="1" ? 'selected' : '' }}>1 {{ trans("cruds.config.notifications.duration.day") }}</option>
		                <option value="7" {{ $expire_delay=="7" ? 'selected' : '' }}>7 {{ trans("cruds.config.notifications.duration.days") }}</option>
		                <option value="15" {{ $expire_delay=="15" ? 'selected' : '' }}>15 {{ trans("cruds.config.notifications.duration.days") }}</option>
		                <option value="30" {{ $expire_delay=="30" ? 'selected' : '' }}>1 {{ trans("cruds.config.notifications.duration.month") }}</option>
		            </select>
				</div>
			</div>

    	<div class="row">
    		<div class="cell-6">
	        </div>
        </div>

    	<div class="row">
            <div class="form-group">
    			<button type="submit" class="button success" name="action" value="save">
                    <span class="mif-floppy-disk2"></span>
    	            &nbsp;
    				{{ trans("common.save") }}
    			</button>
                <button type="submit" class="button primary" name="action" value="test">
    	            <span class="mif-lab"></span>
    	            &nbsp;
    				{{ trans("common.test") }}
    			</button>
        		<button type="submit" class="button cancel" name="action" value="cancel">
        			<span class="mif-cancel"></span>
        			&nbsp;
        			{{ trans("common.cancel") }}
        		</button>

            </div>
        </div>
    </form>


</div>
@endsection
