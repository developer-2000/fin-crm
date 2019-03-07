@extends('layouts.app') @section('title') Документация @stop @section('css')
<link rel="stylesheet" type="text/css" href="/css/dropzone.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" /> @stop @section('content')
<style type="text/css">
  .dz-link-download {
    position: absolute;
    right: 6px;
    bottom: 2px;
    font-size: 18px;
  }
  .dz-filename{
    display: none;
  }
</style>
<div class="row">
  <div class="col-lg-12">
    <ol class="breadcrumb">
      <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
      <li><a href="/documentations"> @lang('documentation.title')</a></li>
      <li class="active"><span> @lang('documentation.edit')</span></li>
    </ol>
    <div class="clearfix">
      <h1 class="pull-left"> @lang('documentation.edit')</h1>
    </div>

    <div class="col-md-12">
      <div class="main-box">
        <header class="main-box-header clearfix">
          <div class="col-sm-3">
            <input class="form-control" id="name" value="{{ $documentation->name }}" placeholder=" @lang('general.name')"></input>
            <input type="hidden" id="entity_id" value="{{ $documentation->id }}" />
          </div>

          <div class="col-sm-3">
            <div class="form-group">
              <select id="category" class="form-control selectpicker">
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </header>
        <div class="main-box-body">

<textarea class="form-control ckeditor" id="ckeditor" name="ckeditor" rows="3">
{!! $documentation->text !!}
</textarea>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="main-box">
        <header class="main-box-header clearfix"> @lang('documentation.download-files') </header>
        {{-- You can download this files --}}
        <div class="main-box-body clearfix">
          <div id="myDropzone" class="dropzone " action="/files/upload">

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<!-- Modal -->
{!! view('modal_access.create') !!} @stop @section('jsBottom')
<script src="{{ URL::asset('js/vendor/dropzone.js') }}"></script>
<script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
<script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
<script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
<script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ URL::asset('js/documentations/edit.js') }}"></script>

<script type="text/javascript">
  Dropzone.options.myDropzone = {
    parallelUploads: 20,
    maxFilesize: 50,
    maxFiles: 20,
    autoProcessQueue: true,
    acceptedFiles: ".doc,.docx.,pdf,.xls,.xlsx,.mp3,.wav,.jpg,.jpeg,.png",
    addRemoveLinks: true,
    init: function() {
      this.on('sending', function(data, xhr, formData) {
        formData.append('entity', 'documentation');
        formData.append('entity_id', $('#entity_id').val());
      });

      @foreach($documentation->files as $file)
      mockFile = {!! $file->option !!};
      addFileToDz(this, mockFile, '{{ $file->path }}', '{{ $file->id }}');
      @endforeach
      this._updateMaxFilesReachedClass();
    },
    success: function(file, response) {
      if(response.success){
        file.id = response.id;
        file.previewElement.classList.add('dz-success');
      }else{
        file.previewElement.classList.add('dz-error');
      }
    },
    removedfile: function(file) {
            if(deleteFile(file.id)){
              showMessage('success', "File successfully deleted");
            file.previewElement.remove();
            }else{
              showMessage('error', "Error deleted file");
            }
          }
  };

  function addFileToDz(dz, mockFile , url, id) {
    dz.options.addedfile.call(dz, mockFile);
    dz.options.thumbnail.call(dz, mockFile, '/download'+url);
    mockFile.id = id;
    mockFile.previewElement.classList.add('dz-success');
    mockFile.previewElement.classList.add('dz-complete');
  }

  var delay = (function(){
    var timer = 0;
    return function(callback, ms){
      clearTimeout (timer);
      timer = setTimeout(callback, ms);
    };
  })();

  var editor = CKEDITOR.replace( 'ckeditor',{

    on: {
        change: function( evt ) {
            delay(function(){
              save();
            }, 2000 );
        }
    }
  } );
</script>
@stop
