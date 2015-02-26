<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en" ><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
	
	<head>
	@section('head')
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Suite Tea</title>
		@styles
		@scripts('head')
	@show
	</head>
    <body class="front">
	@section('body-before')
		<div {{ !isset($canvas_id) ? "" : $canvas_id }} class="off-canvas-wrap">
			<div class="inner-wrap">
				<div id="wrapper">
					<div id="main-col" class="medium-12 columns">
						@include('layouts._includes.message')
	@show
                    
		{{ $body }}
		

	@section('body-after')
					</div><!--/#main-col -->
				</div><!--/#wrapper -->
			@section('exit-off-canvas')
				<a class="exit-off-canvas"></a>
			@show
			</div><!--/.inner-wrap -->
		</div><!-- /.off-canvas-wrap-->
		@scripts('footer')
	@show
    </body>
</html>