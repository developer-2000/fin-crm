@extends('layouts.app') @section('title') @lang('documentation.title') @stop @section('css')
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
      <li class="active"><span>{{ $documentation->name }}</span></li>
    </ol>
    <div class="clearfix">
      <h1 class="pull-left">{{ $documentation->name }}</h1>
    </div>

    <div class="col-md-12">
      <div class="main-box">
        <header class="main-box-header clearfix"></header>
        <div class="main-box-body">
          {!! $documentation->text !!}
        </div>
      </div>
    </div>
    @if($documentation->files->count()>0)
    <div class="col-md-12">
      <div class="main-box">
        <header class="main-box-header clearfix"> @lang('documentation.download-files')</header>
        {{-- You can download this files --}}
        <div class="main-box-body clearfix">
          <div id="myDropzone" class="dropzone " action="/files/upload">

          </div>
        </div>
      </div>
    </div>
  @endif
  </div>
</div>
<!-- Modal -->
{!! view('modal_access.create') !!} @stop @section('jsBottom')
<script src="{{ URL::asset('js/vendor/dropzone.js') }}"></script>
<script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
<script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
<script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
<script type="text/javascript">
  Dropzone.options.myDropzone = {
    clickable: false,
    dictDefaultMessage: '',
    init: function() {

      @foreach($documentation->files as $file)
      mockFile = {!! $file->option !!};
      addFileToDz(this, mockFile, '{{ $file->path }}');
      @endforeach
      this._updateMaxFilesReachedClass();
      $('#myDropzone .dz-default').removeClass('dz-message');
    },
  };

  function addFileToDz(dz, mockFile , url) {
    dz.options.addedfile.call(dz, mockFile);
    dz.options.thumbnail.call(dz, mockFile, '/download'+url);
    mockFile.previewElement.classList.add('dz-success');
    mockFile.previewElement.classList.add('dz-complete');
    mockFile.previewElement.addEventListener("click", downloadFile);
    var a = document.createElement('a');
    a.setAttribute('href', "#");
    a.setAttribute('onclick', "return false");
    a.setAttribute('class', "dz-link-download");
    a.innerHTML = '<i class="fa fa-cloud-download"></i>';
    mockFile.previewElement.append(a);
  }

  function downloadFile() {
    url = $(this).context.childNodes[1].childNodes[5].src;
    filename = $(this).context.childNodes[1].childNodes[5].src;
    filename = $(this).context.childNodes[1].childNodes[1].innerText;
    var a = document.createElement("a");
    a.href = url;
    a.setAttribute("download", filename);
    var b = document.createEvent("MouseEvents");
    b.initEvent("click", false, true);
    a.dispatchEvent(b);
    return false;
  }
</script>
@stop
