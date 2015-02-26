@extends('layouts.2-column')

{{-- Off Canvas Content --}}
@section('off-canvas-left')
	@include('partials.off-canvas.left-menu')
@stop

@section('off-canvas-right')
	@include('partials.off-canvas.right-menu')
@stop

{{-- Page Navigation --}}
@section('navigation')
	@include('partials.navigation.main')
@overwrite

{{-- Search --}}
@section('search')
	@include('partials.search.default')
@stop

{{-- Content --}}
@section('content-header')
	@include('partials.header.content-default')
@stop

{{-- Sidebar --}}
@section('sidebar')

@stop