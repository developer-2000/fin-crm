@extends('layouts.app') @section('title') @lang('documentation.title') @stop
  @section('css')
    <link rel="stylesheet" type="text/css" href="/css/dropzone.css">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-editable.css') }}"/>
  @stop @section('content')
<div class="row">
  <div class="col-lg-12">
    <ol class="breadcrumb">
      <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
      <li class="active"><span> @lang('documentation.title')</span></li>
    </ol>
    <div class="clearfix">
      <h1 class="pull-left"> @lang('documentation.title')</h1>
      @if(isset($permissions['documentations_create']))
      <div class="pull-right">
        <a class="md-trigger btn btn-success mrg-b-lg" href="{{ route('documentations.create') }}"
        data-modal="product-create">
          @lang('documentation.create-document')
        </a>
      </div>
      @endif
    </div>
  </div>
</div>
<div class="order_container">
  <div class="row">
    <div class="col-lg-12">
      <div class="main-box">
        <div class="item_rows ">
          <div class="main-box-body clearfix">
            <br>
            <div class="row">

              <div class="form-group col-md-3 col-sm-6">
                <label for="category" class="col-sm-4 control-label"> @lang('general.name')</label>
                <div class="col-sm-8">
                  <input class="form-control" id="name" value="">
                </div>
              </div>

              <div class="form-group col-md-4 col-sm-6">
                <label for="category" class="col-sm-4 control-label"> @lang('general.categories')</label>
                <div class="col-sm-8">
                  <select id="category" class="form-control selectpicker">
                    <option selected value=""> @lang('general.select-category')</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div id="div_table">
  {!! view('documentations.table', ['documentations' => $documentations]) !!}
</div>
</div>
@stop
@section('jsBottom')
  <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
  <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
  <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
  <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
  <script src="{{ URL::asset('js/documentations/search.js') }}"></script>
@stop
