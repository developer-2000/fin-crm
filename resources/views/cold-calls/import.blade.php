@extends('layouts.app')
@section('title') @lang('cold-calls.list-import') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
@stop
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"> @lang('cold-calls.file-import') </div>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cold-calls-import-parse') }}"
                              enctype="multipart/form-data">
                            <div class="form-group{{ $errors->has('excel_csv_file') ? ' has-error' : '' }}">
                                <label for="excel_csv_file" class="col-md-4 control-label"> @lang('cold-calls.file-to-import') </label>
                                <div class="col-md-6">
                                    {{--<input id="excel_csv_file" type="file"  class="btn btn-success" name="excel_csv_file"--}}
                                    {{--required>--}}

                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-btn">
                                    <span class="btn btn-success" style="line-height: 22px;">
                                        <span class="fa fa-photo"></span>
                                      @lang('general.file-upload')
                                        <input type="file" style="display: none;" id="excel_csv_file"
                                               name="excel_csv_file" required>
                                    </span>
                                            </label>
                                            <input type="text" class="form-control" id="excel_csv_file" readonly>
                                        </div>
                                    </div>
                                    @if ($errors->has('excel_csv_file'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('excel_csv_file') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="header" checked> @lang('cold-calls.file-header-contains')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('cold-calls.parse-csv')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/cold-calls/import.js') }}"></script>

@stop
