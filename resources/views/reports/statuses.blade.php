@extends('layouts.app')

@section('title') @lang('reports.by-statuses')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/daterangepicker/daterangepicker.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <style>
        .click td {
            background-color: #ebebeb !important;
            font-weight: bold;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>
    <script src="{{ URL::asset('js/reports/statuses.js') }}"></script>
    <script src="{{ URL::asset('js/users/account.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                                <li class="active"><span> @lang('reports.by-statuses')</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left"> @lang('reports.by-statuses')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <form method="POST" action="{{Request::url()}}">
                <div class="main-box clearfix">
                    <div class='main-box-body clearfix'>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class="col-md-1 hidden-sm hidden-xs"
                             style="padding-left: 0;"> @lang('general.search')</div>
                        <div class="col-md-11">
                            <div class="row">
                                @if (isset($permissions['filter_offer_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="offers"
                                                   class="col-sm-2 control-label"> @lang('general.offers')</label>
                                            <input id="offers"
                                                   data-offers="{{!empty($dataOffers) ? $dataOffers : ''}}"
                                                   class="offers " name="offers"
                                                   value="{{!empty($dataOffersIds) ? $dataOffersIds : ''}}"
                                                   style="width: 100%">
                                        </div>
                                    </div>
                                @endif
                                @if (isset($permissions['filter_projects_page_account']))
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="project"
                                                   class="col-sm-4 control-label"> @lang('general.project')</label>
                                            <input id="project"
                                                   data-project="{{!empty($dataProject) ? $dataProject : ''}}"
                                                   class="project " name="project"
                                                   value="{{!empty($dataProjectIds) ? $dataProjectIds : ''}}"
                                                   style="width: 100%">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sub_project"
                                               class="col-sm-4 control-label"> @lang('general.subproject')</label>
                                        <input id="sub_project"
                                               data-sub_project="{{!empty($dataSubProject) ? $dataSubProject : ''}}"
                                               class="sub_project " name="sub_project"
                                               value="{{$dataSubProject ?? NULL}}"
                                               style="width: 100%">
                                    </div>
                                </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="divisions"
                                                   class="control-label"> @lang('general.divisions')</label>
                                            <input id="divisions"
                                                       data-divisions="{{!empty($dataDivisions) ? $dataDivisions : ''}}"
                                                       class="division " name="division"
                                                       value="{{!empty($dataDivisions) ? $dataDivisions : ''}}"
                                                       style="width: 100%">

                                        </div>
                                    </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="country"> @lang('general.country')</label>
                                        <select name='country' id="country" style="width: 100%">
                                            <option value="">  @lang('general.all')</option>
                                            @if ($countries)
                                                @foreach ($countries as $c)
                                                    <option @if (isset($_GET['country']) && mb_strtolower($_GET['country']) == $c->code) selected
                                                            @endif value="{{ mb_strtolower($c->code) }}">
                                                        @lang('countries.' . $c->code)
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="proc_status">  @lang('general.status')</label>
                                        <select name='proc_status[]' id="proc_status" style="width: 100%" multiple>
                                            <option value=""> @lang('general.all')</option>
                                            @if ($proc_statuses)
                                                @foreach ($proc_statuses as $c)
                                                    <option @if (isset($_GET['proc_status']))
                                                            <? $statusGet = explode(',', $_GET['proc_status']); ?>
                                                            @foreach ($statusGet as $stg)
                                                            @if ($c->id == $stg)
                                                            selected
                                                            @endif
                                                            @endforeach
                                                            @endif value="{{ $c->id }}">
                                                        {{$c->project ? $c->project->name . '::' : ''}}{{ !empty($c->key) ? trans('statuses.' . $c->key) : $c->name}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="product"
                                               class="col-sm-2 control-label">  @lang('general.products')</label>
                                        <input id="product"
                                               data-product="{{!empty($dataProducts) ? $dataProducts : ''}}"
                                               class="product " name="product"
                                               value="{{!empty($dataProductsIds) ? $dataProductsIds : ''}}"
                                               style="width: 100%">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="result">  @lang('general.result')</label>
                                        <select name='result' id="result" style="width: 100%" class="form-control">
                                            <option value=""> @lang('general.all')</option>
                                            <?
                                            $dataTargets = [
                                                1 => trans('general.good-client'),
                                                2 => trans('general.bad-client'),
                                                3 => trans('general.rejected'),
                                                5 => trans('general.without-target'),
                                            ];
                                            ?>
                                            @if ($dataTargets)
                                                @foreach ($dataTargets as $key => $status)
                                                    <option
                                                            @if (isset($_GET['result']) && $_GET['result'] == $key)
                                                            selected
                                                            @endif
                                                            value="{{ $key }}">{{ $status }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='main-box-body clearfix section_filter'>
                        <div class='main-box-body clearfix'>
                        </div>
                        <div class="col-md-1 hidden-sm hidden-xs" style="padding-left: 0;">  @lang('general.date')</div>
                        {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                        {{--<label for="date_start">С</label>--}}
                        {{--<div class="input-group">--}}
                        {{--<span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
                        <input class="form-control" id="date_start" type="hidden" data-toggle="tooltip"
                               name="date_start"
                               data-placement="bottom"
                               value="{{ isset($_GET['date_start']) ? $_GET['date_start'] : date('d-m-Y', time()) }}">
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-2">--}}
                        {{--<div class="form-group">--}}
                        {{--<label for="date_end">До</label>--}}
                        {{--<div class="input-group">--}}
                        {{--<span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
                        <input class="form-control" id="date_end" type="hidden" data-toggle="tooltip"
                               name="date_end"
                               data-placement="bottom"
                               value="{{ isset($_GET['date_end']) ? $_GET['date_end'] : date('d-m-Y', time()) }}">
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        <div class="col-sm-3">
                            <div id="form-group">
                                @php
                                    $startDate = isset($_GET['date_start']) ? $_GET['date_start'] : date('d-m-Y', time());
                                    $endDate = isset($_GET['date_end']) ? $_GET['date_end'] : date('d-m-Y', time());
                                @endphp
                                <input type="text" class="form-control" id="daterange" name="daterange"
                                       value="{{$startDate .' - '. $endDate}}"/>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="btn-group date_type" data-toggle="buttons">
                                <div>  @lang('general.type')</div>
                                <label class="btn btn-primary @if (!isset($_GET['date_type'])) active @endif"
                                       id="time_created" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-created')">
                                    <input type="radio"
                                           @if (!isset($_GET['date_type']))
                                           checked
                                           @endif
                                           name="date_type" value=""> <i class="fa fa-calendar"></i>
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['date_type'])  && $_GET['date_type'] == 1 ) active @endif"
                                       id="time_modified" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-set-target')">
                                    <input type="radio"
                                           @if (isset($_GET['date_type'])  && $_GET['date_type'] == 1 )
                                           checked
                                           @endif
                                           name="date_type" value="1"><i class="fa fa-star-half-empty"></i>
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['date_type']) && $_GET['date_type'] == 2 ) active @endif"
                                       id="time_result" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-set-result')">
                                    <input type="radio"
                                           @if (isset($_GET['date_type'])  && $_GET['date_type'] == 2 )
                                           checked
                                           @endif
                                           name="date_type" value="2"><i class="fa fa-empire"></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
                            <div class="input-group">
                                <div class="btn-group" data-toggle="buttons" id="date_template_statuses">
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
                                    <label class="btn btn-default pattern_date">
                                        <input type="radio" name="date_template" value="3"> 3 @lang('general.months')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center" style="padding-bottom:20px;">
                    <input class="btn btn-success" type="submit" name="button_filter" value=' @lang('general.search')'/>
                    <a href="{{ route('report-statuses') }}" class="btn btn-warning"
                       type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    @if(Request::get('date_type') == 2 && !Request::get('proc_status') )
                        <div class="alert alert-warning">
                            <i class="fa fa-warning fa-fw fa-lg"></i>
                            <strong> @lang('general.attention')
                                !</strong>@lang('general.search-parameters-not-available')
                            ! @lang('general.select-status')
                        </div>
                    @else
                        @if(Request::get('date_type') == 2 &&  Request::get('proc_status'))
                            @php
                                $getProcStatuses = explode(',',  Request::get('proc_status'));
                            @endphp
                            @foreach($getProcStatuses as $procS)
                                @if(!in_array($procStatusesByKey[$procS]['action'], ['sent', 'at_department', 'received',
                                  'returned','paid_up', 'refused']))
                                    <div class="alert alert-warning">
                                        <i class="fa fa-warning fa-fw fa-lg"></i>
                                        <strong> @lang('general.attention')
                                            ! </strong> @lang('general.search-status-not-available')
                                        : {{ !empty($procStatusesByKey[$procS]['key']) ? trans('statuses.' .$procStatusesByKey[$procS]['key']) : $procStatusesByKey[$procS]['name']}}
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        @if ($byStatus->isNotEmpty())

                            @foreach($byStatus as $statuses)
                                <h2> @lang('countries.' . $statuses->first()->country->code ?? '')</h2>
                                <div class="table-responsive">
                                    <table class="table tablesorter table_country">
                                        <thead>
                                        <tr>
                                            <th class="header"> @lang('general.status')</th>
                                            <th class="text-center header"> @lang('general.orders')</th>
                                            <th class="text-center header"> @lang('general.products-quantity')</th>
                                            <th class="text-center header"> @lang('general.products-price')</th>
                                            <th class="text-center header"> @lang('general.total')</th>
                                            <th class="text-center header"> @lang('general.cost')</th>
                                            <th class="text-center header"> @lang('general.cost-actual')</th>
                                        </tr>
                                        </thead>
                                        @if ($statuses->isNotEmpty())
                                            <tbody>
                                            <?
                                            $orders = 0;
                                            $items = 0;
                                            $subTotal = 0;
                                            $total = 0;
                                            $cost = 0;
                                            $cost_actual = 0;
                                            ?>

                                            @foreach ($statuses as $status)
                                                <tr>
                                                    <?
                                                    $orders += $status->orders;
                                                    $items += $status->products_count;
                                                    $subTotal += $status->price_products;
                                                    $total += $status->price_total;
                                                    $cost += $status->cost;
                                                    $cost_actual += $status->cost_actual;
                                                    ?>
                                                    <td class="text-center"
                                                        style="width: 20%">{{$status->procStatus->project ? $status->procStatus->project->name . '::' : '' }}{{ !empty($status->procStatus->key) ? trans('statuses.' . $status->procStatus->key) : $status->procStatus->name}}</td>
                                                    <td class="text-center">{{$status->orders}}</td>
                                                    <td class="text-center">{{$status->products_count}}</td>
                                                    <td class="text-center">{{$status->price_products}}</td>
                                                    <td class="text-center">{{$status->price_total}}</td>
                                                    <td class="text-center">{{$status->cost}}</td>
                                                    <td class="text-center">{{$status->cost_actual}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th>Всего</th>
                                                <th class="text-center">{{$orders}}</th>
                                                <th class="text-center">{{$items}}</th>
                                                <th class="text-center">{{$subTotal}}</th>
                                                <th class="text-center">{{$total}}</th>
                                                <th class="text-center">{{$cost}}</th>
                                                <th class="text-center">{{$cost_actual}}</th>
                                            </tr>
                                            </tfoot>
                                        @endif

                                    </table>
                                </div>
                            @endforeach
                        @endif

                        @if ($productsStat->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table tablesorter table_country table-hover" id="product_stat">
                                    <thead>
                                    <tr>
                                        <th class="header"> @lang('general.product')</th>
                                        <th class="text-center header"> @lang('general.total-orders')</th>
                                        <th class="text-center header"> @lang('general.hold')</th>
                                        <th class="text-center header"> @lang('statuses.received')</th>
                                        <th class="text-center header"> @lang('statuses.paid-up')</th>
                                        <th class="text-center header"> @lang('reports.check-average')</th>
                                        <th class="text-center header"> @lang('statuses.paid-up'), %</th>
                                        <th class="text-center header"> @lang('general.expected'), %</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($productsStat as $product)
                                        <tr>
                                            <td>{{$product->title}}</td>
                                            <td class="text-center">{{$product->count_orders}}</td>
                                            <td class="text-center">{{$product->hold}}</td>
                                            <td class="text-center">{{$product->received}}</td>
                                            <td class="text-center">{{$product->paid_up}}</td>
                                            <td class="text-center">@if ($product->count_orders > 0) {{number_format(($product->price_total / $product->count_orders), 2, '.', '')}} @else
                                                    0 @endif</td>
                                            <td class="text-center">
                                                @if ($product->count_orders)
                                                    {{number_format(($product->paid_up + $product->received) / $product->count_orders * 100, 2, '.', '')}}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($product->count_orders)
                                                    {{number_format(($product->paid_up + $product->hold + $product->received) / $product->count_orders * 100, 2, '.', '')}}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop