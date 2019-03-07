@extends('layouts.app')
@section('title') @lang('cold-calls.list-import')@stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active" href="{{route('cold-calls-import')}}"><span> @lang('general.main')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('cold-calls.list-create')</h1>
            </div>
        </div>
    </div>

    <div class="main-box clearfix">
        <header class="main-box-header clearfix">
            <h2></h2>
        </header>
        <div class="main-box-body clearfix">
            <form class="form-horizontal" method="POST" action="{{ route('cold-calls-import') }}">
                <input type="hidden" name="file_name" value="{{ $fileName }}"/>
                <input type="hidden" name="header" value="{{ isset($header) }}"/>
                <input type="hidden" name="excel_csv_data" value="{{ json_encode($excelCsvRows) }}"/>
                <div class="col-xs-12 form-group form-group-select2">
                    <label for="country"> @lang('general.select-country')</label>
                    <select style="width:300px;display: inline-block" id="country" name="country" class="form-control"
                            required>
                        <option value=""></option>
                        @foreach($countries as $country)
                            <option value="{{strtolower($country->code)}}"> @lang('countries.' . $country->code)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-12 form-group form-group-select2">
                    <label for="company"> @lang('general.select-company') </label>
                    <select style="width:300px;display: inline-block" id="company" name="company" class="form-control"
                            required>
                        <option value=""></option>
                        @foreach($companies as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-12 form-group form-group-select2">
                    <div class="table-responsive">
                        <header class="main-box-header clearfix">
                            @lang('general.example')
                        </header>
                        <table class="table table-striped table-hover">
                            <thead>
                            @if (isset($csv_header_fields))
                                <tr>
                                    @foreach ($csv_header_fields as $csv_header_field)
                                        <th>{{ $csv_header_field }}</th>
                                    @endforeach
                                </tr>
                            @endif
                            </thead>
                            <tbody>
                            @foreach ($excelCsvDataSliced as $row)
                                <tr>
                                    @foreach ($row as $key => $value)
                                        <td><span>{{ $value }}</span></td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                @foreach ($excelCsvRows[0] as $key => $value)
                                    <td>
                                        <select class="form-control" name="fields[{{ $key }}]">
                                            @foreach (config('app.db_fields_cold_call_table') as $key2 =>$db_field)
                                                <option value="{{ $db_field}}">{{ $db_field }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                  <div class="col-xs-12  ">
                      <div class="form-group">
                          <label for="comment"> @lang('general.comment')</label>
                          {{ Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'comment', 'rows' => 4]) }}
                      </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    @lang('general.import')
                </button>
            </form>
        </div>
    </div>
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/import-fields.js') }}"></script>

@stop
