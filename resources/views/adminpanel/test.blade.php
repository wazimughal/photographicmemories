@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Lead | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    
<div class="container">
       
    <h3 class="jumbotron">Laravel Multiple Images Upload Using Dropzone</h3>
    <form method="post" action="{{route('bookings.adddocuments')}}" enctype="multipart/form-data" 
                  class="dropzone" id="dropzone">
    @csrf
</form>   
</body>
</html>
@endsection

@section('head-js-css')
      <!-- dropzonecss -->
      <link rel="stylesheet" href="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- dropzonejs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>

      @endsection