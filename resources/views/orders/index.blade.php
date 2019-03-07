@extends('layouts.app')

@section('title') @lang('orders.all-sendings') @stop

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <style>
        .delivery_note label {
            display: block;
            border: 0;
            background-color: #7FC8BA;
            padding: 5px;
            padding-top: 7%;
            border-radius: 50% !important;
            margin-right: 4px;
            height: 34px;
            width: 34px;
            color: #ffffff;
        }

        .delivery_note label:hover {
            margin-right: 3px;
        }

        .item_rows .filter label:hover,
        .item_rows .filter label:focus,
        .item_rows .filter label.active {
            color: #FFFFFF;
        }

        .item_rows .filter label.active {
            margin-top: 1px;
            box-shadow: inset 0 0 10px #333;
            border: none;
        }
    </style>
@stop

@section('jsBottom')

    <script src="{{ URL::asset('js/vendor/snap.svg-min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/orders/order.js?a=1') }}"></script>
    <script src="{{ URL::asset('js/orders/print-register.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/prints/index.js') }}"></script>
@stop

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('orders.all-sendings')</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('orders.all-sendings')(<span class="badge">{{$countOrder}}</span>)</h1>
                @if(isset($permissions['page_order_create']))
                    <div class="pull-right top-page-ui">
                        <a href="{{ route("order-create") }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i>
                            @lang('general.create')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="order_container">
        <div class="row">
            <div class="col-lg-12">
                <form class="form" action="{{Request::url() }}"
                      method="post">
                    <div class="main-box">
                        <div class="item_rows ">
                            {{--<div class="number_block">--}}
                            {{--<div>1</div>--}}
                            {{--</div>--}}
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_id_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="id" class="col-sm-4 control-label"> @lang('general.id')</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="id" name="id"
                                                       value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                            </div>

                                        </div>
                                    @endif
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="oid" class="col-sm-4 control-label"> @lang('general.oid')</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="oid" name="oid"
                                                       value="@if (isset($_GET['oid'])){{ $_GET['oid'] }}@endif">
                                            </div>
                                        </div>
                                    @if (isset($permissions['filter_surname_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="surname" class="col-sm-4 control-label"> @lang('general.surname')</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="surname" name="surname"
                                                       value="@if (isset($_GET['surname'])){{ $_GET['surname'] }}@endif">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_phone_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="phone" class="col-sm-4 control-label"> @lang('general.phone')</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                    <input type="text" class="form-control" id="phone" name="phone"
                                                           value="@if (isset($_GET['phone'])){{ $_GET['phone'] }}@endif">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_country_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="country" class="col-sm-4 control-label"> @lang('general.country')</label>
                                            <div class="col-sm-8">
                                                <select id="country" name="country[]" style="width: 100%" multiple>
                                                    @foreach ($country as $covalue)
                                                        <option
                                                                @if (isset($_GET['country']))
                                                                <? $countryGet = explode(',', $_GET['country']); ?>
                                                                @foreach ($countryGet as $cg)
                                                                @if ($covalue->code == $cg)
                                                                selected
                                                                @endif
                                                                @endforeach
                                                                @endif
                                                                value="{{$covalue->code }}">
                                                            @lang('countries.' . $covalue->code)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="project" class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <div class="col-sm-8">
                                                @if(!auth()->user()->project_id)
                                                    <input id="project"
                                                           data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                           class="project " name="project[]"
                                                           value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                           style="width: 100%">
                                                @else
                                                    <input type="hidden" id="project"
                                                           class="project " name="project[]"
                                                           value="{{auth()->user()->project_id}}">
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_project" class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                            <div class="col-sm-8">
                                                <input id="sub_project"
                                                       data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                                       class="sub_project " name="sub_project[]"
                                                       value="{{$dataSubProject ?? NULL}}"
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
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 form-horizontal">
                                            <label for="initiator" class="col-sm-4 control-label"> @lang('general.initiator')</label>
                                            <div class="col-sm-8">

                                                <input id="initiator"
                                                       data-initiators="{{!empty($dataInitiators) ? $dataInitiators : ''}}"
                                                       class="initiator " name="initiator[]"
                                                       value="{{$dataInitiatorsIds ?? NULL}}" style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_proc_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="status" class="col-sm-4 control-label"> @lang('general.processing-status')</label>
                                            <div class="col-sm-8">
                                                <select id="status" name="status[]" style="width: 100%" multiple>
                                                    <option value="3"
                                                            @if ($statuses)
                                                            @if (isset($_GET['status']))
                                                            <? $statusGet = explode(',', $_GET['status']); ?>
                                                            @foreach ($statusGet as $stg)
                                                            @if (3 == $stg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            @endif
                                                    > @lang('general.new')
                                                    </option>
                                                    @if ($statuses)
                                                        @foreach ($statuses as $key => $status)
                                                            @if (!$status->parent_id)
                                                                <option
                                                                        @if (isset($_GET['status']))
                                                                        <? $statusGet = explode(',', $_GET['status']); ?>
                                                                        @foreach ($statusGet as $stg)
                                                                        @if ($key == $stg)
                                                                        selected
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                        value="{{ $key }}">
                                                                    @if ($status->name)
                                                                        {{!empty($status->key) ? trans('statuses.' . $status->key) : $status->name}}
                                                                    @endif
                                                                    </option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_proc_status_2_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_status" class="col-sm-4 control-label"> @lang('general.substatus')</label>
                                            <div class="col-sm-8">
                                                <select id="sub_status" name="sub_status[]" style="width: 100%"
                                                        multiple>
                                                    @if ($statuses)
                                                        @foreach ($statuses as $key => $status)
                                                            @if ($status->parent_id)
                                                                <option
                                                                        @if (isset($_GET['sub_status']))
                                                                        <? $statusGet = explode(',', $_GET['sub_status']); ?>
                                                                        @foreach ($statusGet as $stg)
                                                                        @if ($key == $stg)
                                                                        selected
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                        value="{{ $key }}">
                                                                    @if ($status->name)
                                                                        {{ $status->name }}
                                                                    @endif
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_target_status_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="target" class="col-sm-4 control-label"> @lang('general.result')</label>
                                            <div class="col-sm-8">
                                                <select id="target" name="target[]" style="width: 100%" multiple>
                                                    <?
                                                    $dataTargets = [
                                                        1 => trans('orders.good-client'),
                                                        2 => trans('orders.bad-client'),
                                                        3 => trans('general.rejected'),
                                                        5 => trans('orders.without-result'),
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
                                                                    value="{{ $key }}">{{ $status }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_partners_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="partners" class="col-sm-4 control-label"> @lang('general.partners')</label>
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
                                </div>
                            </div>
                        </div>
                        <div class="item_rows">
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_offers_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="offers" class="col-sm-2 control-label"> @lang('general.offers')</label>
                                            <div class="col-sm-10">
                                                <input id="offers"
                                                       data-offers="{{!empty($dataOffers) ? $dataOffers : ''}}"
                                                       class="offers " name="offers[]"
                                                       value="{{!empty($dataOffersIds) ? $dataOffersIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                        @if (isset($permissions['filter_ip_orders']))
                                            <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                <label for="ip" class="col-sm-4 control-label"> @lang('general.ip-address')</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="ip" name="ip"
                                                           value="@if (isset($_GET['ip'])){{ $_GET['ip'] }}@endif">
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset($permissions['filter_by_hp']))
                                            <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                <label for="entity" class="col-sm-4 control-label">
                                                    @lang('general.type')
                                                </label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="entity" name="entity"
                                                            style="width: 100%">
                                                        <option value=""> @lang('general.all')</option>
                                                        <option @if (isset($_GET['entity']) && $_GET['entity'] == 'order')
                                                                selected @endif value="order"> @lang('general.order')
                                                        </option>
                                                        <option @if (isset($_GET['entity']) && $_GET['entity'] == 'cold_call')
                                                                selected @endif value="cold_call"> @lang('orders.cold-calls')
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                </div>
                                <div class="row">
                                    @if (isset($permissions['filter_products_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="product" class="col-sm-2 control-label"> @lang('general.products')</label>
                                            <div class="col-sm-10">
                                                <input id="product"
                                                       data-product="{{!empty($dataProducts) ? $dataProducts : ''}}"
                                                       class="product " name="product[]"
                                                       value="{{!empty($dataProductsIds) ? $dataProductsIds : ''}}"
                                                       style="width: 100%">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_products_page_orders']))
                                        <div class="form-group col-lg-3 form-horizontal">
                                            <label class="col-lg-4 control-label" for="products_count">
                                                @lang('orders.quantity-products')
                                            </label>
                                            <div class="col-lg-8"><input class="form-control" type="number"
                                                                         id="products_count"
                                                                         name="products_count" min="1"
                                                                         value="{{isset($_GET['products_count']) ? $_GET['products_count'] : ''}}">
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_add_products_info']))
                                        <div class="col-sm-2">
                                            <div class="checkbox-nice">
                                                <input type="checkbox" id="display_products" name="display_products"
                                                       @if (isset($_GET['display_products']) && $_GET['display_products'] == 'on') checked @endif>
                                                <label for="display_products">
                                                    @lang('orders.view-products')
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @if (isset($permissions['filter_deliveries_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="deliveries" class="col-sm-2 control-label">
                                                @lang('general.delivery')
                                            </label>
                                            <div class="col-sm-10">
                                                <select id="deliveries" name="deliveries[]" style="width: 100%"
                                                        multiple>
                                                    @if ($deliveries)
                                                        @foreach ($deliveries as $delivery)
                                                            <option
                                                                    @if (isset($_GET['deliveries']))
                                                                    <? $deliveriesGet = explode(',', $_GET['deliveries']); ?>
                                                                    @foreach ($deliveriesGet as $deliveryGet)
                                                                    @if ($delivery->id == $deliveryGet)
                                                                    selected
                                                                    @endif
                                                                    @endforeach
                                                                    @endif
                                                                    value="{{ $delivery->id }}">{{$delivery->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_track_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="track_filter" class="col-sm-2 control-label">
                                                @lang('general.track')
                                            </label>
                                            <div class="col-sm-10">
                                                @php
                                                    $formatted_tracks = [];

                                                        if (isset($_GET['track_filter'])){
                                                        $tracksGet = explode(',', $_GET['track_filter']);
                                                        foreach ($tracksGet as $trackGet){
                                                        $formatted_tracks[] = ['id' => $trackGet, 'text' => $trackGet];
                                                        }
                                                        }
                                                      $tracksData = json_encode($formatted_tracks);
                                                @endphp
                                                <input id="track_filter"
                                                       data-tracks="{{!empty($tracksData) ? $tracksData : ''}}"
                                                       class="track_filter "
                                                       name="track_filter"
                                                       value="" style="width: 100%">
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <div class="row">
                                    @php
                                        $sort = [
                                            'id' => trans('general.id'),
                                            'oid' => trans('general.oid'),
                                            'geo' => trans('general.country'),
                                            'time_created' => trans('general.date-created'),
                                            'time_modified' => trans('general.date-target'),
                                            'time_status_updated' => trans('orders.date-changed-status'),
                                            'time_comment_added' => trans('orders.date-last-comment'),
                                            'time_sms_send' => trans('orders.date-last-sms'),
                                            'project_id' => trans('general.project'),
                                            'subproject_id' => trans('general.subproject'),
                                            'price_total' => trans('general.price'),
                                        ];
                                    @endphp
                                    <div class="form-group col-md-3 form-horizontal">
                                        <label for="order_cell" class="col-sm-4 control-label"> @lang('general.sort-by')</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="order_cell" id="order_cell">
                                                <option value=""></option>
                                                @foreach($sort as $key => $value)
                                                    <option value="{{$key}}"
                                                            @if(isset($_GET['order_cell']) && $_GET['order_cell'] == $key) selected @endif>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 form-horizontal">
                                        <label for="order_sort" class="col-sm-4 control-label"> @lang('general.order-by')</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="order_sort" id="order_sort">
                                                <option value=""></option>
                                                <option value="asc"
                                                        @if(isset($_GET['order_sort']) && $_GET['order_sort'] == 'asc') selected @endif>
                                                    @lang('general.ascending')
                                                </option>
                                                <option value="desc"
                                                        @if(isset($_GET['order_sort']) && $_GET['order_sort'] == 'desc') selected @endif>
                                                    @lang('general.descending')
                                                </option>
                                            </select>
                                        </div>
                                    </div>
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

                        @if (isset($permissions['filter_date_orders']))
                            <div class="item_rows">
                                <div class="main-box-body clearfix">
                                    <div class='main-box-body clearfix section_filter'>
                                        <div class='main-box-body clearfix'>
                                        </div>
                                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;"> @lang('general.date')</div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="date_start"> @lang('general.date-from')</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
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
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                    <input class="form-control" id="date_end" type="text"
                                                           data-toggle="tooltip" name="date_end"
                                                           data-placement="bottom"
                                                           value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="btn-group date_type" data-toggle="buttons">
                                                <div> @lang('general.type')</div>
                                                <label style="padding-top: 6%;"
                                                       class="btn btn-primary @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) || !isset($_GET['date-type'])) active @endif"
                                                       id="time_created" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('general.date-created')">
                                                    <input type="radio" name="date-type" value="1"
                                                           @if ((isset($_GET['date-type']) && $_GET['date-type'] == 1) || !isset($_GET['date-type'])) checked @endif>
                                                    <i class="fa fa-calendar"></i>
                                                </label>
                                                <label style="padding-top: 6%;"
                                                       class="btn btn-primary @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) active @endif"
                                                       id="time_modified" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('general.date-target')">
                                                    <input type="radio" name="date-type" value="3"
                                                           @if (isset($_GET['date-type']) && $_GET['date-type'] == 3) checked @endif><i
                                                            class="fa fa-star-half-empty"></i>
                                                </label>
                                                <label style="padding-top: 6%;"
                                                       class="btn btn-primary @if (isset($_GET['date-type']) && $_GET['date-type'] == 4) active @endif"
                                                       id="time_comment" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('orders.date-last-comment')">
                                                    <input type="radio" name="date-type" value="4"
                                                           @if (isset($_GET['date-type']) && $_GET['date-type'] == 4) checked @endif><i
                                                            class="fa fa-comment-o"></i>
                                                </label>
                                                <label style="padding-top: 6%;"
                                                       class="btn btn-primary @if (isset($_GET['date-type']) && $_GET['date-type'] == 5) active @endif"
                                                       id="time_status" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('orders.date-changed-status')">
                                                    <input type="radio" name="date-type" value="5"
                                                           @if (isset($_GET['date-type']) && $_GET['date-type'] == 5) checked @endif><i
                                                            class="fa fa-edit"></i>
                                                </label>
                                                <label style="padding-top: 6%;"
                                                       class="btn btn-primary @if (isset($_GET['date-type']) && $_GET['date-type'] == 6) active @endif"
                                                       id="time_sms" data-toggle="tooltip"
                                                       data-placement="bottom" title=" @lang('orders.date-last-sms')">
                                                    <input type="radio" name="date-type" value="6"
                                                           @if (isset($_GET['date-type']) && $_GET['date-type'] == 6) checked @endif><i class="fa fa-envelope-o"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4" style="padding-top: 20px;padding-bottom: 10px;">
                                            <div class="input-group">
                                                <div class="btn-group" data-toggle="buttons" id="date_template">
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="1"> @lang('general.today')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="5"> @lang('general.yesterday')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="9"> @lang('general.week')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="10"> @lang('general.month')
                                                    </label>
                                                    <label class="btn btn-default pattern_date">
                                                        <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (isset($permissions['filter_grouping_by_status_orders']))
                            <div class="item_rows">
                                <div class="main-box-body clearfix">
                                    <div class="row">
                                        <div class="col-sm-12 text-center" id="countByStatus">
                                            @php
                                                $statusesGrouping = $statusesGrouping->isNotEmpty() ? $statusesGrouping : $statuses;
                                                $chunk = $statusesGrouping->chunk(6);
                                            @endphp
                                            @if ($chunk)
                                                @for($i = 0; $i < $chunk->count(); $i++)
                                                    <div class="btn-group filter" data-toggle="buttons"
                                                         style="padding-bottom: 8px;">
                                                        @if ($i == 0)
                                                            <label class="btn btn-success">
                                                                <input type="radio" name="grouping" value="3">
                                                                @lang('general.new')
                                                                <span class="label label-success status"
                                                                      data-status="3"></span>
                                                            </label>
                                                        @endif
                                                        @if ($chunk[$i])
                                                            @foreach($chunk[$i] as $status)
                                                                <label class="btn btn-default @if(isset($_GET['grouping']))@if($_GET['grouping'] == $status->id) active @endif @endif "
                                                                       style="background-color: {{$status->color}};border-bottom-color: {{$status->color}}">
                                                                    <input type="radio" name="grouping"
                                                                           value="{{$status->id}}"
                                                                           @if(isset($_GET['grouping']) && $_GET['grouping'] == $status->id) checked @endif>
                                                                    {{ !empty($status->key) ? trans('statuses.' . $status->key) : $status->name}}
                                                                    <span class="label status"
                                                                          style="background-color: {{colorForLabel($status->color, 20)}}"
                                                                          data-status="{{$status->id}}"></span>
                                                                </label>
                                                            @endforeach
                                                        @endif
                                                        @if (isset($_GET['grouping']) && $i == $chunk->count() - 1)
                                                            <label class="btn btn-default"><input type="radio"
                                                                                                  name="grouping"
                                                                                                  value=""> @lang('general.all')</label>
                                                        @endif
                                                    </div>
                                                    <br>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='@lang('general.search')'/>
                        <a href="{{ route('orders') }}" class="btn btn-warning" type="submit"> @lang('general.reset')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-box clearfix">
        {{--<div class="main-box-body clearfix">--}}
        <div class="tabs-wrapper">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#">
                        @lang('orders.all-sendings')
                    </a>
                </li>
                @if(isset($permissions['page_print_orders']))
                    <li class="">
                        <a href="{{route('orders-print')}}">
                            @lang('orders.queued-to-print')
                        </a>
                    </li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="orders">
                    @if ($orders)
                        <div class="table-responsive">
                            <table id="orders" class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">№</th>
                                    <th class="text-center">
                                        @lang('general.id')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.country')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.date-created')
                                    </th>
                                    <th class="text-center">
                                        @lang('orders.date-last-comment')
                                    </th>
                                    <th class="text-center">
                                        @lang('orders.date-changed-status')
                                    </th>
                                    <th class="text-center">
                                        @lang('orders.date-last-sms')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.project')/<br> @lang('general.subproject')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.phone')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.offer')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.products')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.price')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.delivery')<br> @lang('general.track')
                                    </th>
                                    <th class="text-center">
                                        @lang('general.processing-status')/<br> @lang('general.result')
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $n =0;
                                if(Request::get('page')){
                                $pageNumder =  Request::get('page');
                                 $n +=($pageNumder - 1) *50;
                                }
                                @endphp
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="text-center" style="color: #545454; font-weight: bold">
                                            {{$n +=1}}
                                        </td>
                                        <td class="text-center">
                                                <span class="crm_id">
                                                    {{$order->id}}
                                                </span>
                                            @if($order->partner_oid)
                                                <div class="project_oid">{{$order->partner_oid}}</div>
                                            @elseif(isset($usersTarget[$order->target_user]))
                                                <div style="font-size: 12px;color: #6e6e6e; font-weight: bold">{!! !empty($usersTarget[$order->target_user]->name) && !empty($usersTarget[$order->target_user]->surname) ?
                                                $usersTarget[$order->target_user]->name . ' ' .  $usersTarget[$order->target_user]->surname: '' !!}</div>
                                            @endif

                                        </td>
                                        <td class="text-center">
                                            <img class="country-flag"
                                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png')  }}"/>
                                        </td>
                                        <td class="text-center ">
                                            <div class="order_phone_block">
                                                    <span class="order_date">
                                                        {{ \Carbon\Carbon::parse($order->time_created)->format('d/m/Y')}}
                                                    </span>
                                                <div class="project_oid">{{ \Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}</div>
                                            </div>
                                        </td>
                                        <td class="text-center ">
                                            <div class="order_phone_block">
                                                @if(isset($comments[$order->id]->date))
                                                    <span class="order_comment_date">
                                                          {{ \Carbon\Carbon::parse($comments[$order->id]->date)->format('d/m/Y')}}
                                                              </span>
                                                    <div class="project_oid">  {{ \Carbon\Carbon::parse($comments[$order->id]->date)->format('H:i:s')}}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center ">
                                            <div class="">
                                                @if(isset($order->time_status_updated))
                                                    <span class="order_status_date">
                               {{ \Carbon\Carbon::parse($order->time_status_updated)->format('d/m/Y')}}
                                                              </span>
                                                    <div class="project_oid">{{ \Carbon\Carbon::parse($order->time_status_updated)->format(' H:i:s')}}</div>
                                            </div>
                                            @endif
                                        </td>
                                        <td class="text-center ">
                                            <div class="order_phone_block">
                                                @if(isset($smsMessages[$order->id]->date))
                                                    <span class="order_sms_date">
                                                         {{ \Carbon\Carbon::parse($smsMessages[$order->id]->date)->format('d/m/Y')}}
                                                              </span>
                                                    <div class="project_oid">  {{ \Carbon\Carbon::parse($smsMessages[$order->id]->date)->format(' H:i:s')}}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="project_oid">
                                            @if (isset($projects[$order->project_id]))
                                                <div>
                                                    <b>{{$projects[$order->project_id]->name}}</b>
                                                </div>
                                            @endif
                                            @if (isset($projects[$order->subproject_id]))
                                                {{$projects[$order->subproject_id]->name}}
                                            @endif
                                        </td>
                                        <td class="text-center"
                                            style="font-weight: bold; color: #5f5f5f; font-size: 13px">
                                            {{!empty($order->phone) ? $order->phone : ''}}
                                        </td>
                                        <td class="text-center">
                                            @if (isset($offers[$order->offer_id]))
                                                <div class="order_phone_block">
                                                    <a href="#" class="pop">
                                                        <div class="offer_name">{{$offers[$order->offer_id]->offer_id}}</div>
                                                    </a>
                                                    <div class="data_popup">
                                                        <div class="arrow"></div>
                                                        <h3 class="title">Оффер</h3>
                                                        <p class="content">{{$offers[$order->offer_id]->name}}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        @if (Request::get('display_products') && Request::get('display_products') == 'on')
                                            <td>
                                                @if (isset($orderProducts[$order->id]))
                                                    @foreach($orderProducts[$order->id] as $product)
                                                        {{$product['name'] .' - '. $product['price']}}
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                        @else
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
                                                        <h3 class="title">
                                                            @lang('general.products')
                                                        </h3>
                                                        <div class="content">

                                                            @if (isset($orderProducts[$order->id]))
                                                                @foreach($orderProducts[$order->id] as $product)
                                                                    {{$product['name']}}
                                                                    @if($product['type'] == 1)
                                                                        <span class="label label-success">Up Sell</span>
                                                                    @elseif($product['type'] ==  2)
                                                                        <span class="label label-primary ">Up Sell 2</span>
                                                                    @elseif($product['type'] == 4)
                                                                        <span class="label label-info ">Cross Sell</span>
                                                                    @endif
                                                                    <br>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                        <td class="text-center price_order">
                                            {{$order->price}}
                                            @if (isset($country[strtoupper($order->geo)]))
                                                {{ $country[strtoupper($order->geo)]->currency }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (isset($targets[$order->target_approve]))
                                                {{$targets[$order->target_approve]->name}}
                                            @endif
                                            <br>
                                                <span class="track_blue"> {{!empty($targetValues[$order->id]->track) ? $targetValues[$order->id]->track : ''}}</span>
                                        </td>
                                        @if ($order->final_target)
                                            <?
                                            $target = '';
                                            $classLabel = '';
                                            $classBtn = '';
                                            switch ($order->final_target) {
                                                case 1:
                                                    {
                                                        $target = trans('orders.good-client');
                                                        $classLabel = 'label-primary';
                                                        break;
                                                    }
                                                case 2:
                                                    {
                                                        $target = trans('orders.bad-client');
                                                        $classLabel = 'label-danger';
                                                        $classBtn = 'custom_danger';
                                                        break;
                                                    }
                                                case 3:
                                                    {
                                                        $target = trans('general.rejected');
                                                        $classLabel = 'label-warning';
                                                        $classBtn = 'custom_warning';
                                                        break;
                                                    }
                                            }
                                            ?>
                                            <td class="text-center">
                                                <span class="badge {{$classLabel}}">{{$target}}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('order-sending', $order->id) }}"
                                                   class="table-link {{$classBtn}}">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x "></i>
                                                        <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                </a>
                                            </td>
                                        @else
                                            <td class="text-center">
                                                @if (isset($statuses[$order->proc_status]))
                                                    <span class="label label-default" style="background: {{$statuses[$order->proc_status]->color}}">{{ !empty($statuses[$order->proc_status]->key) ? trans('statuses.' .$statuses[$order->proc_status]->key) : $statuses[$order->proc_status]->name}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{route('order-sending', $order->id)}}"
                                                   class="table-link custom_badge">
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
                        <div class="pull-right">
                            {{$orders->links()}}
                        </div>
                    @endif
                    <input type="hidden" id="authProjectId"
                           name="authProjectId" value="{{!empty(auth()->project_id) ? true : false}}">
                </div>
            </div>
        </div>
        {{--</div>--}}
    </div>
    <div class="md-overlay"></div>
    @include('orders.print_error_messages')
@stop
