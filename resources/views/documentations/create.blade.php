@extends('layouts.app') @section('title') Документация @stop @section('css')
<link rel="stylesheet" type="text/css" href="/css/dropzone.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" /> @stop @section('content')
<div class="row">
  <div class="col-lg-12">
    <ol class="breadcrumb">
      <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
      <li><a href="/documentations"> @lang('documentation.title')</a></li>
      <li class="active"><span> @lang('documentation.title')</span></li>
    </ol>
    <div class="clearfix">
      <h1 class="pull-left"> @lang('documentation.create-document')</h1>
    </div>

      <div class="col-md-12">
        <div class="main-box">
          <header class="main-box-header clearfix">
            <div class="col-md-3 form-group">
              <input class="form-control" id="name" placeholder=" @lang('general.name')" type="text">
            </div>

            <div class="col-md-3 form-group">
              <select id="category" class="form-control selectpicker">
                <option value=""> @lang('general.select-category')</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-3 form-group">
              <button type="button" disabled class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
                @lang('general.set-access')
              </button>
            </div>
          </header>
          <div class="main-box-body">
              <textarea class=" ckeditor" id="ckeditor" name="ckeditor" rows="3"></textarea>
          </div>
          <footer class="main-box-header clearfix">
            <div class="col-md-3 pull-right">
              <button onclick="save()" class="form-control btn btn-success"> @lang('general.save')</button>
            </div>
          </footer>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="main-box clearfix">
          <header class="main-box-header clearfix">
            <h2> @lang('documentation.dtag-and-drop-file')</h2>
            {{-- Drag &amp; Drop file upload --}}
          </header>
          <div class="main-box-body clearfix">
            <div id="myDropzone" class="dropzone dz-clickable" action="/files/upload">
              <div class="dz-default dz-message">
                <span>Drop files here to upload</span>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>
<!-- Modal -->
{!! view('modal_access.create') !!}
@stop @section('jsBottom')
<script src="{{ URL::asset('js/vendor/dropzone.js') }}"></script>
<script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
<script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
<script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
<script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ URL::asset('js/documentations/create.js') }}"></script>

<script type="text/javascript">
  Dropzone.options.myDropzone = {
    parallelUploads: 20,
    maxFilesize: 50,
    maxFiles: 20,
    autoProcessQueue: false,
    acceptedFiles: ".doc,.docx,pdf,.xls,.xlsx,.mp3,.wav,.jpg,.jpeg,.png",
    addRemoveLinks: true,
    success: function(file, response, action) {
      if (response.success) {
        this.defaultOptions.success(file);
      } else {
        this.defaultOptions.error(file, response.message);
      }
    }
  };

  var editor = CKEDITOR.replace( 'ckeditor',{} );
</script>
@stop
