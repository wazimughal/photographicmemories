@extends('layouts.app')

@section('head_resources')

	@parent
    
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.css') }}">

@endsection

@section('content')
<div class="container-fluid">
	@include('modals.modal_upload')
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="col-md-12 mb-4">
				<button type="button" class="btn btn-primary btn-ico" data-toggle="modal" data-target="#uploaderModal"><i class="fa fa-files-o"></i> {{ __('File Upload') }}</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('footer_resources')
	@parent
    <!-- jQuery -->
<script src="{{url('adminpanel/plugins/jquery/jquery.min.js')}}"></script>
		<!-- Scripts -->
		<script src="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.js') }}"></script>
		<script src="{{ asset('js/file_upload.js') }}" defer></script>

		<script>
			var home_url = "{{env('APP_URL') }}";
			var deleteAction = '{{ route("file-delete") }}';
			var generalTS =  document.getElementById('dataTS').value;
			var generalDATE = document.getElementById('dataDATE').value;
			var token = '{!! csrf_token() !!}';
		</script>

@endsection

