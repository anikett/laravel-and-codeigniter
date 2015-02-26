@extends('layouts.modal')

@section('modal-before') 
	<form id="modal-target" method="post" action="{{ $action_url }}" accept-charset="UTF-8">
@stop

@section('modal-footer')
	<button class="button primary" ic-post-to="{{ route($route_group.'.create.post', ['save-only' => $save_only, 'rel_id' => $rel_id]) }}" ic-target="#modal-target">Save</button>
	<button class="button inverse" ic-post-to="{{ route($route_group.'.create.post', ['save-and-edit' => 'true', 'rel_id' => $rel_id]) }}" ic-target="#modal-target" data-modal-trigger="false">Save & Edit</button>
	<a href="#" class="button link close" data-modal-trigger="false">Cancel</a>
@stop

@section('modal-after')
	</form>
@stop