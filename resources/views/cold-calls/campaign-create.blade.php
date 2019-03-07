@extends('layouts.app')

@section('title')@lang('general.colling-companies') @stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li class="active"><span> @lang('general.colling-companies')</span></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                </div>
                <div class='main-box-body clearfix'>
                    <form id="form" role="form" method="post" action="{{route('cold-calls-campaigns-create')}}"
                          onsubmit="return false">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label for="name" id="label-name"> @lang('general.name')</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="checkbox checkbox-nice">
                                <input type="checkbox" id="status" checked="checked" name="status"/>
                                <label for="status" style="display: none">
                                    @lang('general.on-off')
                                </label>
                            </div>
                            <div class="form-group">
                                <input type="text" style="display: none;" class="form-control time" id="call-time"
                                       value="1" placeholder="1">
                            </div>
                        </div>
                        <div class="col-xs-12 form-group form-group-select2">
                            <label for="company"> @lang('general.company)</label>
                            <select style="width:300px;display: inline-block" id="company" name="company_id"
                                    class="form-control">
                                <option value=""> @lang('general.select-company')</option>
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12 form-group form-group-select2">
                            <label id="label_country" class="label_"> @lang('general.country')</label>
                            <select style="width:300px;display: inline-block" id="sel-country" name="country"
                                    class="form-control">
                                <option value=""></option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}"> <?php  /* mb_strtolower($country->code) ?? */ ?>
                                        @lang('countries.' . $country->code)
                                    </option>
                                @endforeach
                            </select>
                            <div class="checkbox checkbox-nice" style="display: inline-block;margin-left: 5px;">
                                <input class="include" type="checkbox" id="incl_country" checked="checked"
                                       name="incl_country"/>
                                <label for="incl_country">
                                    @lang('general.included')
                                </label>
                            </div>
                            <div class="btn-group" data-toggle="buttons" style="margin:0 0 4px 10px;"
                                 id="add_call_time">
                                <label class="btn btn-primary add-something">
                                    <input type="checkbox"> @lang('general.add')
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-2 ">
                            <button type="submit" class="btn btn-success" id="submit"> @lang('general.add')</button>
                        </div>
                    </form>
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
    <script src="{{ URL::asset('js/campaigns/company_elastix_add.js') }}"></script>
@stop
