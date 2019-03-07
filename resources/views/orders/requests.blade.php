@extends('layouts.app')

@section('title') @lang('orders.all')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order.js?a=1') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('orders.all')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('orders.all')(<span class="badge">{{$countOrder}}</span>)</h1>
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url()}}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            {{--<div class="number_block">--}}
                            {{--<div>1</div>--}}
                            {{--</div>--}}
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="id" name="id"
                                                   value="@if (isset($_GET['id'])){{ $_GET['id'] }} @endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="surname"
                                               class="col-sm-4 control-label"> @lang('general.surname')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="surname" name="surname"
                                                   value="@if (isset($_GET['surname'])){{ $_GET['surname'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="phone"
                                               class="col-sm-4 control-label"> @lang('general.phone')</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                <input type="text" class="form-control" id="phone" name="phone"
                                                       value="@if (isset($_GET['phone'])){{ $_GET['phone'] }}@endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="ip" class="col-sm-4 control-label"> @lang('general.ip-address')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="ip" name="ip"
                                                   value="@if (isset($_GET['ip'])){{ $_GET['ip'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="oid" class="col-sm-4 control-label"> @lang('general.oid')</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="oid" name="oid"
                                                   value="@if (isset($_GET['oid'])){{ $_GET['oid'] }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                        <label for="country"
                                               class="col-sm-4 control-label"> @lang('general.country')</label>
                                        <div class="col-sm-8">
                                            <select id="country" name="country[]" style="width: 100%" multiple>
                                                @foreach ($countries as $country)
                                                    <option
                                                            @if (isset($_GET['country']))
                                                            <? $countriesGet = explode(',', $_GET['country']); ?>
                                                            @foreach ($countriesGet as $countryRequest)
                                                            @if ($country->code == $countryRequest)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="{{ mb_strtolower($country->code) }}">
                                                        @lang('countries.' . $country->code)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if (isset($permissions['filter_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="project"
                                                   class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <div class="col-sm-8">
                                                <input id="project"
                                                       data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                       class="project " name="project[]"
                                                       value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_project"
                                                   class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                            <div class="col-sm-8">
                                                <input id="sub_project"
                                                       data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                                       class="sub_project " name="sub_project[]"
                                                       value="{{!empty($dataSubProject) ? $dataProjectIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="divisions"
                                                   class="col-sm-4 control-label"> @lang('general.divisions')</label>
                                            <div class="col-sm-8">
                                                <input id="divisions"
                                                       data-divisions="{{!empty($dataDivisions) ? $dataDivisions : ''}}"
                                                       class="division " name="division[]"
                                                       value="{{!empty($dataDivisions) ? $dataDivisions : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_companies_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="company"
                                                   class="col-sm-4 control-label"> @lang('general.company')</label>
                                            <div class="col-sm-8">
                                                <select id="company" name="company[]" style="width: 100%" multiple>
                                                    @foreach ($companies as $company)
                                                        <option
                                                                @if (isset($_GET['company']))
                                                                <? $companyGet = explode(',', $_GET['company']); ?>
                                                                @foreach ($companyGet as $cg)
                                                                @if ($company->id == $cg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $company->id }}">{{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_partners_page_orders_sending']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="partners"
                                                   class="col-sm-4 control-label"> @lang('general.partners')</label>
                                            <div class="col-sm-8">
                                                <select id="partners" name="partners[]" style="width: 100%" multiple>
                                                    @foreach ($partners as $partner)
                                                        <option
                                                                @if (isset($_GET['partners']))
                                                                <? $partnersGet = explode(',', $_GET['partners']); ?>
                                                                @foreach ($partnersGet as $pg)
                                                                @if ($partner->id == $pg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{ $partner->id }}">{{ $partner->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_cause_cancel_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="cause_cancel" class="col-sm-4 control-label">
                                                {{--@lang('orders.reason-cancellation')--}}
                                                @lang('general.cancellation-reason')
                                            </label>
                                            <div class="col-sm-8">
                                                <select id="cause_cancel" name="cause_cancel" style="width: 100%">
                                                    <option></option>
                                                    @foreach ($cause_cancel as $value => $causeText)
                                                        <option
                                                                @if (isset($_GET['cause_cancel'])
                                                                && $_GET['cause_cancel'] == $value)
                                                                selected
                                                                @endif
                                                                value="{{ $value }}">{{ $causeText }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_by_hp']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="entity" class="col-sm-4 control-label">
                                                @lang('general.order-type')
                                            </label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="entity" name="entity"
                                                        style="width: 100%">
                                                    <option value=""> @lang('general.all')</option>
                                                    <option @if (isset($_GET['entity']) && $_GET['entity'] == 'order')
                                                            selected @endif value="order"> @lang('general.order')
                                                    </option>
                                                    <option @if (isset($_GET['entity']) && $_GET['entity'] == 'cold_call')
                                                            selected
                                                            @endif value="cold_call"> @lang('general.cold-calls')
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_not_available_page_orders']))
                                        <div class="col-sm-2">
                                            <div class="checkbox-nice">
                                                <input type="checkbox" id="not_available" name="not_available"
                                                       @if (isset($_GET['not_available'])
                                                       && $_GET['not_available'] == 'on') checked
                                                        @endif>
                                                <label for="not_available">
                                                    @lang('general.out-of-stock')
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_campanies_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="group"
                                                   class="col-sm-4 control-label"> @lang('general.campaign')</label>
                                            <div class="col-sm-8">
                                                <select id="group" name="group[]" style="width: 100%" multiple>
                                                    @if ($company_elastix)
                                                        @foreach ($company_elastix as $ce)
                                                            <option
                                                                    @if (isset($_GET['group']))
                                                                    <? $companyElastixGet = explode(',', $_GET['group']); ?>
                                                                    @foreach ($companyElastixGet as $ceg)
                                                                    @if ($ce->id == $ceg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $ce->id }}">{{ $ce->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_proc_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="status"
                                                   class="col-sm-4 control-label"> @lang('general.processing-status')</label>
                                            <div class="col-sm-8">
                                                <select id="status" name="status[]" style="width: 100%" multiple>
                                                    @if ($dataStatus)
                                                        @foreach ($dataStatus as $status)
                                                            <option
                                                                    @if (isset($_GET['status']))
                                                                    <? $statusGet = explode(',', $_GET['status']); ?>
                                                                    @foreach ($statusGet as $stg)
                                                                    @if ($status->id == $stg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $status->id }}">{{ !empty($status->key) ? trans('statuses.' . $status->key) : $status->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_target_user_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="user"
                                                   class="col-sm-4 control-label"> @lang('general.target-user')</label>
                                            <div class="col-sm-8">
                                                <select id="user" name="user[]" style="width: 100%" multiple>
                                                    @if ($users)
                                                        @foreach ($users as $user)
                                                            <option
                                                                    @if (isset($_GET['user']))
                                                                    <? $usersGet = explode(',', $_GET['user']); ?>
                                                                    @foreach ($usersGet as $usg)
                                                                    @if ($user->id == $usg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $user->id }}">
                                                                {{ $user->name . ' ' . $user->surname }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_target_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="target"
                                                   class="col-sm-4 control-label"> @lang('general.target-status')</label>
                                            <div class="col-sm-8">
                                                <select id="target" name="target[]" style="width: 100%" multiple>
                                                    <?
                                                    $dataTargets = [
                                                        1 => trans('general.approved'),
                                                        2 => trans('general.refusal'),
                                                        3 => trans('general.annulled'),
                                                        5 => trans('general.without-target'),
                                                    ];
                                                    ?>
                                                    @if ($dataTargets)
                                                        @foreach ($dataTargets as $key => $status)
                                                            <option
                                                                    @if (isset($_GET['target']))
                                                                    <? $statusGet = explode(',', $_GET['target']); ?>
                                                                    @foreach ($statusGet as $stg)
                                                                    @if ($key == $stg)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $key }}">{{ $status }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_offers_page_orders']) || Auth::user()->campaign_id == 10 )
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="offers"
                                                   class="col-sm-2 control-label"> @lang('general.offers')</label>
                                            <div class="col-sm-10">
                                                <input id="offers"
                                                       data-offers="{{!empty($dataOffers) ? $dataOffers : ''}}"
                                                       class="offers " name="offers[]"
                                                       value="{{!empty($dataOffersIds) ? $dataOffersIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_products_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="product"
                                                   class="col-sm-2 control-label"> @lang('general.products')</label>
                                            <div class="col-sm-10">
                                                <input id="product"
                                                       data-product="{{!empty($dataProducts) ? $dataProducts : ''}}"
                                                       class="product " name="product[]"
                                                       value="{{!empty($dataProductsIds) ? $dataProductsIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (isset($permissions['filter_by_tags']))
                            <div class="item_rows">
                                <div class="main-box-body clearfix">
                                    <div class="row">
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="tag_source" class="col-sm-4 control-label"> @lang('general.tag_source')</label>
                                            <div class="col-sm-8">
                                                <input id="tag_source"
                                                       data-tag ="{{$tag_source ?? NULL}}"
                                                       class="tag" name="tag_source"
                                                       value="{{$ids_tag_source ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="tag_medium" class="col-sm-4 control-label"> @lang('general.tag_medium')</label>
                                            <div class="col-sm-8">
                                                <input id="tag_medium"
                                                       data-tag ="{{$tag_medium ?? NULL}}"
                                                       class="tag" name="tag_medium"
                                                       value="{{$ids_tag_medium ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="tag_content" class="col-sm-4 control-label"> @lang('general.tag_content')</label>
                                            <div class="col-sm-8">
                                                <input id="tag_content"
                                                       data-tag ="{{$tag_content ?? NULL}}"
                                                       class="tag" name="tag_content"
                                                       value="{{$ids_tag_content ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="tag_campaign" class="col-sm-4 control-label"> @lang('general.tag_campaign')</label>
                                            <div class="col-sm-8">
                                                <input id="tag_campaign"
                                                       data-tag="{{$tag_campaign ?? NULL}}"
                                                       class="tag" name="tag_campaign"
                                                       value="{{$ids_tag_campaign ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="tag_term" class="col-sm-4 control-label"> @lang('general.tag_term')</label>
                                            <div class="col-sm-8">
                                                <input id="tag_term"
                                                       data-tag ="{{$tag_term ?? NULL}}"
                                                       class="tag" name="tag_term"
                                                       value="{{$ids_tag_term ?? NULL}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class='main-box-body clearfix section_filter'>
                                    <div class='main-box-body clearfix'>
                                    </div>
                                    <div class="col-md-1 hidden-sm hidden-xs"
                                         style="padding-left: 0;"> @lang('general.date')</div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="date_start"> @lang('general.date-from')</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control" id="date_start" type="text"
                                                       data-toggle="tooltip" name="date_start"
                                                       data-placement="bottom"
                                                       value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="date_end"> @lang('general.date-to')</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control" id="date_end" type="text"
                                                       data-toggle="tooltip" name="date_end"
                                                       data-placement="bottom"
                                                       value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="btn-group date_type" data-toggle="buttons">
                                            <div> @lang('general.type')</div>
                                            <label class="btn btn-primary
                                            @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1)
                                            || !isset($_GET['date-type'])) active
                                            @endif"
                                                   id="time_created" data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.date-created')">
                                                <input type="radio" name="date-type" value="1"
                                                       @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) ||
                                                       !isset($_GET['date-type'])) checked
                                                        @endif>
                                                <i class="fa fa-calendar"></i>
                                            </label>
                                            <label class="btn btn-primary @if (isset($_GET['date-type'])
                                            && $_GET['date-type'] == 3) active @endif"
                                                   id="time_modified" data-toggle="tooltip"
                                                   data-placement="bottom" title=" @lang('general.date-target')">
                                                <input type="radio" name="date-type" value="3"
                                                       @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) checked
                                                        @endif>
                                                <i class="fa fa-star-half-empty"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                                        <div class="input-group">
                                            <div class="btn-group" data-toggle="buttons" id="date_template">
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template"
                                                           value="1"> @lang('general.today')
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template"
                                                           value="5"> @lang('general.yesterday')
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template"
                                                           value="9"> @lang('general.week')
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template"
                                                           value="10"> @lang('general.month')
                                                </label>
                                                <label class="btn btn-default pattern_date">
                                                    <input type="radio" name="date_template"
                                                           value="2"> @lang('general.last-month')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter"
                               value=' @lang('general.search')'/>
                        <a href="{{ route('requests') }}" class="btn btn-warning"
                           type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="main-box-body clearfix">
                        @if ($orders)
                            <div class="table-responsive">
                                <table id="orders" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        @if (isset($permissions['chenge_campaigns_order']))
                                            <th>
                                                @lang('general.all')
                                                <div class="checkbox-nice">
                                                    <input type="checkbox" id="select_all_company_elastix"
                                                           class="select_all_company_elastix">
                                                    <label for="select_all_company_elastix"></label>
                                                </div>
                                            </th>
                                        @endif
                                        <th class="text-center"> @lang('general.id')</th>
                                        <th class="text-center"> @lang('general.country')</th>
                                        <th class="text-center"> @lang('general.date')</th>
                                        <th class="text-center"> @lang('general.offer')</th>
                                        <th class="text-center"> @lang('general.products')</th>
                                        <th class="text-center"> @lang('general.phone')</th>
                                        <th class="text-center"> @lang('general.price')</th>
                                        <th colspan="5" class="text-center">
                                            @lang('general.processing')/
                                            @lang('general.target')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            @if (isset($permissions['chenge_campaigns_order']))
                                                <td class="check_order">
                                                    <div class="checkbox-nice">
                                                        <input type="checkbox" class="change_company_elastix"
                                                               id="cce_{{ $order->id }}" value="{{ $order->id }}">
                                                        <label for="cce_{{ $order->id }}"></label>
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="text-center">

                                                       @if (!empty($order->moderation_id) && !empty($order->moderation_time))
                                                        <div class="order_phone_block">
                                                        <a href="#" class="pop">
                                                            <div class="offer_name">
                                                                <span class="crm_id">
                                                        {{$order->id}}
                                                            </span>
                                                                <div class="project_oid">{{$order->partner_oid}}</div>
                                                            </div>
                                                            <span class="badge badge-primary" style="background-color: #18bd1d">
                                                                   M
                                                            </span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title"> Промодерирован</h3>
                                                            <p class="content">{{ \Carbon\Carbon::parse($order->moderation_time)->format('d/m/Y H:i:s')}}</p>
                                                        </div>
                                                    </div>
                                                           @else
                                                    <span class="crm_id">
                                                        {{$order->id}}
                                                            </span>
                                                    <div class="project_oid">{{$order->partner_oid}}</div>
                                                    @endif


                                            </td>
                                            <td class="text-center">
                                                <img class="country-flag"
                                                     src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                                            </td>
                                            <td class="text-center ">
                                                <div class="order_phone_block">
                                                    <span class="order_date">
                                                        {{ \Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}
                                                    </span>
                                                    <div class="project_oid">
                                                        {{ \Carbon\Carbon::parse($order->time_created)->format('d/m/Y')}}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if (isset($offers[$order->offer_id]))
                                                    <div class="order_phone_block">
                                                        <a href="#" class="pop">
                                                            <div class="offer_name">
                                                                {{$offers[$order->offer_id]->offer_id}}
                                                            </div>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title"> @lang('general.offer')</h3>
                                                            <p class="content">{{$offers[$order->offer_id]->name}}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="order_phone_block">
                                                    <a href="#" class="pop">
                                                        <span class="badge badge-danger">
                                                            @if (isset($orderProducts[$order->id]))
                                                                {{count($orderProducts[$order->id])}}
                                                            @else
                                                                0
                                                            @endif
                                                        </span>
                                                    </a>
                                                    <div class="data_popup">
                                                        <div class="arrow"></div>
                                                        <h3 class="title"> @lang('general.products')</h3>
                                                        <div class="content">
                                                            @if (isset($orderProducts[$order->id]))
                                                                @foreach($orderProducts[$order->id] as $product)
                                                                    {{$product['name']}}
                                                                    @if($product['type'] == 1)
                                                                        Up Sell
                                                                    @elseif($product['type'] ==  2)
                                                                        Up Sell 2
                                                                    @elseif($product['type'] == 4)
                                                                        Cross Sell
                                                                    @endif
                                                                    <br>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="order_phone_block">
                                                    {{$order->phone}} <br>
                                                    <span style="font-weight: bold; color: #2b81af; font-size: 11px">{{$order->name_last . ' '. $order->name_first}}</span>
                                                    {{--<a href="#" class="pop">--}}
                                                    {{--<span class="order_phone">--}}
                                                    {{--<i class="fa fa-mobile-phone @if ($order->proc_status == 6) phone_error @endif"></i>--}}
                                                    {{--</span>--}}
                                                    {{--</a>--}}
                                                    {{--<div class="data_popup">--}}
                                                    {{--<div class="arrow"></div>--}}
                                                    {{--<h3 class="title">Телефон</h3>--}}
                                                    {{--<div class="content">{{$order->phone}}</div>--}}
                                                    {{--</div>--}}
                                                </div>
                                            </td>
                                            <td class="text-center price_order">
                                                {{$order->price}}
                                                @if (isset($countries[strtoupper($order->geo)]))
                                                    {{$countries[strtoupper($order->geo)]->currency}}
                                                @endif
                                                <div class=" border_proc @if ($order->target_status) border @endif"></div>
                                            </td>
                                            @if (!$order->target_status)
                                                <td class="text-center" style="font-size: 13px;">
                                                    {{(isset($company_elastix[$order->proc_campaign])) ?
                                                     $company_elastix[$order->proc_campaign]->name : ''}}
                                                </td>
                                                <td class="text-center">
                                                    <div class="proc_label">
                                                        @lang('general.processing-status')
                                                    </div>
                                                    <?
                                                    $class = '';
                                                    switch ($order->proc_status) {
                                                        case 1:
                                                            $class = 'label-default';
                                                            break;
                                                        case 2:
                                                            {
                                                                $class = 'label-success';
                                                                break;
                                                            }
                                                        case 3:
                                                            {
                                                                $class = 'label-primary';
                                                                break;
                                                            }
                                                        case 4:
                                                            {
                                                                $class = 'label-warning';
                                                                break;
                                                            }
                                                        case 5:
                                                            {
                                                                $class = 'label-danger';
                                                                break;
                                                            }
                                                        case 6:
                                                            {
                                                                $class = 'label-danger';
                                                                break;
                                                            }
                                                        case 7:
                                                            {
                                                                $class = 'label-info';
                                                                break;
                                                            }
                                                        case 8:
                                                            {
                                                                $class = 'label-danger';
                                                                break;
                                                            }
                                                        case 9:
                                                            {
                                                                $class = 'label-default';
                                                                break;
                                                            }
                                                        case 10:
                                                            {
                                                                $class = 'label-default';
                                                                break;
                                                            }
                                                        case 11:
                                                            {
                                                                $class = 'label-default';
                                                                break;
                                                            }
                                                        case 13:
                                                            {
                                                                $class = 'label-danger';
                                                                break;
                                                            }
                                                    }
                                                    ?>
                                                    <span class="label {{$class}}">
                                                        @if (isset($dataStatus[$order->proc_status]->name))
                                                            {{isset($dataStatus[$order->proc_status]->key) ?
                                                             trans('statuses.' . $dataStatus[$order->proc_status]->key)
                                                             : $dataStatus[$order->proc_status]->name}}
                                                        @else
                                                            {{isset($dataStatus[$order->proc_status]->key) ?
                                                             trans('statuses.' . $dataStatus[$order->proc_status]->key)
                                                             : $dataStatus[$order->proc_status]->name}}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="proc_label">
                                                        @lang('general.quantity')
                                                    </div>
                                                    <span class="badge badge-primary">{{$order->proc_stage}}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="order_phone_block">
                                                        <div class="proc_label">
                                                            @if($order->proc_callback_time)
                                                                <span class="badge badge-info"
                                                                      style="background-color: #0aa5f3"> @lang('general.callback')</span>
                                                            @else
                                                                @lang('general.schedule')
                                                            @endif
                                                        </div>
                                                        <a href="#" class="pop">
                                                            <span class="order_date
                                                            @if (!$order->proc_callback_time) proc_time
                                                            @endif">
                                                                {{ \Carbon\Carbon::parse($order->proc_time)->format('H:i:s')}}
                                                            </span>
                                                        </a>
                                                        <div class="data_popup">
                                                            <div class="arrow"></div>
                                                            <h3 class="title"> @lang('general.callback')</h3>
                                                            <div class="content">
                                                                {{ \Carbon\Carbon::parse($order->proc_time)->format('H:i:s d/m/y')}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{--@if(isset($permissions['moderator_changes']))--}}
                                                    {{--<a href="{{ route('order-one-manage', $order->id) }}/"--}}
                                                    {{--class="table-link custom_badge">--}}
                                                    {{--<span class="fa-stack">--}}
                                                    {{--<i class="fa fa-square fa-stack-2x "></i>--}}
                                                    {{--<i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>--}}
                                                    {{--</span>--}}
                                                    {{--</a>--}}
                                                    {{--@else--}}
                                                    <a href="{{ route('order', $order->id) }}/"
                                                       class="table-link custom_badge">
                                                        <span class="fa-stack">
                                                            <i class="fa fa-square fa-stack-2x "></i>
                                                            <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                    </a>
                                                    {{--@endif--}}
                                                </td>
                                            @else
                                                <?
                                                $target = '';
                                                $classLabel = '';
                                                $classRow = '';
                                                $classBtn = '';
                                                switch ($order->target_status) {
                                                    case 1:
                                                        {
                                                            $target = trans('general.approved');
                                                            $classLabel = 'label-primary';
                                                            $classRow = 'success';
                                                            break;
                                                        }
                                                    case 2:
                                                        {
                                                            $target = trans('general.refusal');
                                                            $classLabel = 'label-danger';
                                                            $classRow = 'danger';
                                                            $classBtn = 'custom_danger';
                                                            break;
                                                        }
                                                    case 3:
                                                        {
                                                            $target = trans('general.annulled');
                                                            $classLabel = 'label-warning';
                                                            $classRow = 'warning';
                                                            $classBtn = 'custom_warning';
                                                            break;
                                                        }
                                                }
                                                ?>
                                                <td class="text-center {{$classRow}}">
                                                    @if (isset($users[$order->target_user]))
                                                        {{$users[$order->target_user]->name}}
                                                        {{$users[$order->target_user]->surname}}
                                                        @if (isset($companies[$users[$order->target_user]->company_id]))
                                                            <br>
                                                            {{$companies[$users[$order->target_user]->company_id]->name}}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="text-center {{$classRow }}">
                                                    <div class="proc_label">
                                                        @lang('general.target')
                                                    </div>
                                                    <span class="badge {{$classLabel}}">{{$target}}</span>
                                                </td>
                                                <td class="text-left {{$classRow }}" colspan="2">
                                                    @if (isset($cause[$order->id]) && $order->target_status != 1)
                                                        @foreach($cause[$order->id] as $key => $field)
                                                            @if ($key != 'name')
                                                                <div class="proc_label">
                                                                    {{$field['title']}} :
                                                                    <span class="order_comment">
                                                                    @if ($field['value'])
                                                                            @foreach($field['value'] as $k => $value)
                                                                                {{$value}}
                                                                                @if (count($field['value']) != $k + 1)
                                                                                    ,
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                </span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="{{$classRow }}">
                                                    <a href="{{ route('order', $order->id) }}/"
                                                       class="table-link {{$classBtn}}">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x "></i>
                                                        <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (isset($permissions['chenge_campaigns_order']))
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div>
                                            <select class="form-control" style="width: 300px; margin-top: 10px;"
                                                    id="elastix_company_select">
                                                @if ($company_elastix)
                                                    @foreach ($company_elastix as $compel)
                                                        <option value="{{ $compel->id }}">{{ $compel->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="btn btn-primary change_company_elastix_button"
                                             style="margin-top: 10px"> @lang('general.campaign-change')
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="pull-right">
                                {{$orders->links()}}
                            </div>
                        @endif
                        @if (isset($permissions['reset_order_block']))
                            <div class="col-lg-12">
                                <div class="main-box clearfix" style=" background-color: #90a8af22;padding-top: 20px;">
                                    <div class="main-box-body clearfix">
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <label for="proc_stage"> @lang('general.priority')</label>
                                                <input type="text" class="form-control" id="proc_stage"
                                                       name="proc_stage"
                                                       value="1">
                                            </div>
                                            <div class="checkbox checkbox-nice" style="display: inline-block;">
                                                <input type="checkbox"
                                                       class="add_all"
                                                       id="add_all"
                                                       name="add_all">
                                                <label for="add_all"> @lang('general.all') ({{$countOrder}})</label>
                                            </div>
                                            <button class="btn btn-primary" id="change_order_proc_stage">
                                                <i class="fa fa-plus-circle fa-lg"></i> @lang('general.add-in-processing')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
@stop
