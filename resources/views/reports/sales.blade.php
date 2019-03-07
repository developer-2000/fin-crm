@extends('layouts.app')

@section('title')Продажи за период@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('tablesorter_master/themes/blue/style.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/account_all.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/dataTables.fixedHeader.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/dataTables.tableTools.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/daterangepicker/daterangepicker.css') }}"/>
    <style>
        body {
            color: #929292;;
        }

        div.value span {
            color: #4e4e4e;
        }

        .total {
            background-color: #c9fcda !important;
            font-weight: bold !important;
            color: #4c4c4c !important;
        }
    </style>
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.nanoscroller.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/scripts.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/pace.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/dataTables.fixedHeader.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/dataTables.tableTools.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/jquery.dataTables.bootstrap.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>

    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/reports/sales.js') }}"></script>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div id="content-header" class="clearfix">
                        <div class="pull-left">
                            <ol class="breadcrumb">
                                <li><a href="{{route('index')}}">Главная</a></li>
                                <li class="active"><span>Продажи за период</span></li>
                            </ol>
                            <div class="clearfix">
                                <h1 class="pull-left">Продажи за период</h1>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    @if (isset($permissions['filter_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="project" class="col-sm-4 control-label">Проект</label>
                                            <input id="project"
                                                   data-project="{{!empty($dataFilters['dataProject']) ? $dataFilters['dataProject'] : ''}}"
                                                   class="project " name="project"
                                                   value="{{!empty($dataFilters['dataProjectIds']) ? $dataFilters['dataProjectIds'] : ''}}"
                                                   style="width: 100%">
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_sub_projects_page_orders']))
                                        <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                            <label for="sub_project" class="col-sm-4 control-label">Под пректы</label>
                                            <input id="sub_project"
                                                   data-sub_project="{{!empty($dataFilters['dataSubProject']) ? $dataFilters['dataSubProject'] : ''}}"
                                                   class="sub_project " name="sub_project"
                                                   value="{{$dataFilters['dataSubProject'] ?? NULL}}"
                                                   style="width: 100%">
                                        </div>
                                    @endif
                                    @if (isset($permissions['filter_products_page_orders']))
                                        <div class="form-group col-md-6 form-horizontal">
                                            <label for="product" class="col-sm-2 control-label">Товары</label>
                                            <input id="product"
                                                   data-product="{{!empty($dataFilters['dataProducts']) ? $dataFilters['dataProducts'] : ''}}"
                                                   class="product col-sm-10" name="product"
                                                   value="{{!empty($dataFilters['dataProductsIds']) ? $dataFilters['dataProductsIds'] : ''}}"
                                                   style="width: 100%">
                                        </div>
                                </div>
                                @endif
                                @if (isset($permissions['filter_date_orders']))
                                    <div class="item_rows">
                                        <div class="main-box-body clearfix">
                                            <div class="row">
                                                <div class='main-box-body clearfix section_filter'>
                                                    <div class="col-sm-3">
                                                        @if (isset($permissions['filter_proc_status_page_orders']))
                                                            <div class="form-group form-horizontal">
                                                                <label for="status" class="col-sm-4 control-label">Процесинг
                                                                    статус</label>
                                                                <div class="col-sm-8">
                                                                    <select id="status" name="status"
                                                                            style="width: 100%">
                                                                        @if ($statuses)
                                                                            @foreach ($statuses as $key => $status)
                                                                                @if (!$status['parent_id'])
                                                                                    <option
                                                                                            @if (isset($_GET['status']))
                                                                                            <? $statusGet = explode(',', $_GET['status']); ?>
                                                                                            @foreach ($statusGet as $stg)
                                                                                            @if ($status['action'] == $stg)
                                                                                            selected
                                                                                            @endif
                                                                                            @endforeach
                                                                                            @endif
                                                                                            value="{{ $status['action'] }}">{{ $status['name'] }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif
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
                                                    <div class="col-sm-5" style="padding-top: 20px;padding-bottom: 10px;">
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
                                                                <label class="btn btn-default pattern_date">
                                                                    <input type="radio" name="date_template" value="3"> 3 @lang('general.months')
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="btns_filter">
                        <input class="btn btn-success" type="submit" name="button_filter" value='Фильтровать'/>
                        <a href="{{ route('sales') }}" class="btn btn-warning" type="submit">Сбросить фильтр</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <hr>
                        <div class="col-lg-12">
                            <a download class="btn btn-success"
                               href="{{route('get-sales-report-export', ['filters' => Request::all()])}}">
                                Export to Excel</a>
                        </div>
                        <hr>
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table tablesorter table_sales table-hover">
                                    <thead>
                                    <tr>
                                        <th class="header" style="width: 20px;">ID товара</th>
                                        <th class="header" style="width: 200px;">Наименование товара</th>
                                        <th class="header" style="width: 50px;">Количество,<br> товаров, шт</th>
                                        @if(!empty(Request::get('sub_project')) && Request::get('sub_project') != 'all' )
                                            <th class="header text-center" style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    С/С (шт), <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>
                                            <th class="header text-center" style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    С/С (всего), <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>
                                        @endif
                                        @if(!empty(Request::get('sub_project')) && Request::get('sub_project') != 'all' && isset($products[0]))
                                            <th tabindex="0" aria-controls="table-example-fixed" rowspan="1"
                                                colspan="1" aria-label=""
                                                style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    Цена
                                                    продажи, <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>
                                            <th tabindex="0" aria-controls="table-example-fixed" rowspan="1"
                                                colspan="1" aria-label=""
                                                style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    Доставка (прих), <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>

                                            <th tabindex="0" aria-controls="table-example-fixed" rowspan="1"
                                                colspan="1" aria-label=""
                                                style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    Доход, <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>
                                            <th tabindex="0" aria-controls="table-example-fixed" rowspan="1"
                                                colspan="1" aria-label=""
                                                style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    Доставка (расх.), <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>
                                            <th tabindex="0" aria-controls="table-example-fixed" rowspan="1"
                                                colspan="1" aria-label=""
                                                style="width: 142px;">
                                                <div style=" border-bottom: 2px solid #ebebeb;" class="text-center">
                                                    Итого (прибыль), <span style="color: #3e9157">$</span>
                                                </div>
                                                <div style="color: #868b98;" class="text-center">
                                                    {{isset($country->currency) ? $country->currency : ''}}
                                                </div>
                                            </th>

                                        @endif
                                        <th class="header text-center" style="width: 68px;">Всего <br>
                                            @php
                                                $labelClass = '';
                                                    if(Request::get('status')){
                                                    switch (Request::get('status')){
                                                     case 'sent':
                                                     $labelClass = 'label-primary';
                                                    break;
                                                     case 'at_department':
                                                           $labelClass = 'label-warning';
                                                    break;
                                                    case 'received':
                                                        $labelClass = 'label-info';
                                                    }
                                                    }
                                            @endphp
                                            {!! !Request::get('status') || Request::get('status') == 'paid_up' ? '<span class="label label-success">Хороший клиент</span> '
                                             : '<span class="label '.$labelClass.'">'. \App\Models\ProcStatus::where('action', Request::get('status'))->first()->name. '</span> '!!}
                                            ,
                                            <br>шт
                                        </th>
                                        <th class="header text-center" style="width: 68px;">Всего <br> approve, <br> шт
                                        </th>
                                        <th class="header text-center" style="width: 50px;">% выкупа</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $totalPaidUpProductsCount = 0;
                                    @endphp
                                    @foreach ($products as $product)
                                        <tr class="tr_sales">
                                            <td>
                                                {{$product->id}}
                                            </td>
                                            <td style="color: #626262; font-weight: bold">
                                                {{$product->title}}
                                            </td>
                                            <td class="text-center">
                                                {{!empty($product->paidUpProductsCount) ? $product->paidUpProductsCount : ''}}
                                            </td>
                                            @if(!empty(Request::get('sub_project')) && Request::get('sub_project') != 'all' )
                                                <td class="text-center">
                                                    <div style="border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {!! !empty($product->price_cost) ? $product->price_cost : 'N/A' !!}
                                                    </div>
                                                    <div style="color: #868b98">
                                                        {!! $product->price_cost && $country->exchange_rate ? $product->price_cost * $country->exchange_rate  : 'N/A' !!}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div style="border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {!! !empty($product->price_cost) && !empty($product->paidUpProductsCount)
                                                         ? $product->price_cost  * $product->paidUpProductsCount : 'N/A' !!}
                                                    </div>
                                                    <div style="color: #868b98">
                                                        {!! !empty($product->price_cost) &&  isset($country->exchange_rate )&& isset($product->paidUpProductsCount)?
                                                        round($product->price_cost *  $country->exchange_rate * $product->paidUpProductsCount, 2) : 'N/A' !!}
                                                    </div>
                                                </td>
                                                <!--Цена продажи-->
                                                <td class="text-center">
                                                    <div style=" border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {{!empty($orderProductsData[$product->id]->total_price) ?? !empty($country->exchange_rate) ? round($orderProductsData[$product->id]->total_price/$country->exchange_rate, 2): ''}}
                                                    </div>
                                                    <div style="color: #868b98;">
                                                        {{$orderProductsData[$product->id]->total_price ?? ''}}
                                                    </div>
                                                </td>
                                                <!--Доставка-->
                                                <td class="text-center">
                                                    <div style=" border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {{!empty($orderProductsData[$product->id]->cost) ?? !empty($country->exchange_rate) ? round($orderProductsData[$product->id]->cost/$country->exchange_rate, 2): ''}}
                                                    </div>
                                                    <div style="color: #868b98;">
                                                        {{!empty($orderProductsData[$product->id]->cost) ? $orderProductsData[$product->id]->cost : ''}}
                                                    </div>
                                                </td>
                                                <!-- Total Income -->
                                                <td class="text-center">
                                                    <div style=" border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {{!empty($orderProductsData[$product->id]->total_price) ?? !empty($country->exchange_rate) ?
                                                        round(($orderProductsData[$product->id]->total_price +  $orderProductsData[$product->id]->cost)/$country->exchange_rate, 2) : ''}}
                                                    </div>
                                                    <div style="color: #868b98;">
                                                        {{!empty($orderProductsData[$product->id]->total_price) ? ($orderProductsData[$product->id]->total_price + $orderProductsData[$product->id]->cost) : ''}}
                                                    </div>
                                                </td>
                                                <!--Доставка-->
                                                <td class="text-center">
                                                    <div style=" border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {{!empty($orderProductsData[$product->id]->cost_actual) ?? !empty($country->exchange_rate) ? round($orderProductsData[$product->id]->cost_actual/ $country->exchange_rate, 2): ''}}
                                                    </div>
                                                    <div style="color: #868b98;">
                                                        {{$orderProductsData[$product->id]->cost_actual ?? ''}}
                                                    </div>
                                                </td>
                                                <!--Итого, прибыль-->
                                                <td class="text-center">
                                                    @php
                                                        $profit =
                                                            isset($orderProductsData[$product->id]->total_price) ? $orderProductsData[$product->id]->total_price : 0
                                                         +  isset($orderProductsData[$product->id]->cost) ? $orderProductsData[$product->id]->cost : 0
                                                         -  isset($orderProductsData[$product->id]->cost_actual) ? $orderProductsData[$product->id]->cost_actual : 0
                                                         -  round($product->price_cost * $country->exchange_rate * $product->paidUpProductsCount, 2);
                                                    @endphp
                                                    <div style=" border-bottom: 2px solid #ebebeb; color: #3e9157">
                                                        {{!empty($profit) ? round($profit /$country->exchange_rate, 2): ''}}
                                                    </div>
                                                    <div style="color: #868b98;">
                                                        {{!empty($profit) ? $profit : 'N/A'}}
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="text-center" style="background-color: rgba(249,249,249,0.79)">
                                                {{!empty($product->paidUpOrdersCount) ? $product->paidUpOrdersCount : 0 }}
                                            </td>
                                            <!--Approve-->
                                            <td class="text-center">
                                                {{!empty($product->approveOrdersCount) ? $product->approveOrdersCount : 0 }}
                                            </td>
                                            <td class="text-center" style="font-weight: bold">
                                                @php
                                                    $percentArray =[];
                                                        foreach ($redemptionPercents as $data){
                                                      if($data->product_id == $product->id && Request::get('sub_project') &&
                                                       $data->subproject_id ==  Request::get('sub_project')){

                                                      $percent = $data->percent;

                                                      }
                                                      if($data->product_id == $product->id && !Request::get('sub_project')){
                                                      $percentArray[] = $data->percent;
                                                      }
                                                        }
                                                if(!empty($percentArray)){
                                                $percentWithSubproject = array_sum($percentArray) / count($percentArray);
                                                }
                                                @endphp
                                                @if(Request::get('sub_project'))
                                                    {{!empty($percent) ? $percent : 'N//A'}}
                                                @elseif(!Request::get('sub_project'))
                                                    {{!empty($percentWithSubproject) ? $percentWithSubproject : 'N/A'}}
                                                @endif
                                            </td>
                                        </tr>
                                        <!--With Orders-->
                                        @if(!empty(Request::get('product')))
                                            <tr>
                                                <td colspan="7" style="text-align: center; font-weight: bold"> Всего
                                                    заказов, {{!empty($product->approveOrdersCount) ? $product->approveOrdersCount : 0}}</td>
                                            </tr>
                                            @if(!empty($ordersForProducts))
                                                @foreach($ordersForProducts as $order)
                                                    <tr>
                                                        <td class="text-center">
                                                            <a href="{{route('order-sending', $order->id)}}">
                                                                {{ $order->id}}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            {{$order->name_last. ' ' . $order->name_first . '.'}}
                                                            Сумма
                                                            заказа: {{$order->price_total. ' ' .  $country->currency}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{$order->order_products_count ? $order->order_products_count : ''}}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endif
                                        <!--Calculate total rows sum-->
                                        @php
                                            $totalPaidUpProductsCount += $product->paidUpProductsCount;
                                        @endphp
                                    @endforeach
                                    <tr id="tr-total">
                                        @if(empty(Request::get('product')) && empty(Request::get('sub_project_id')))
                                            <td class="total text-center"></td>
                                            <td class="total text-center">ИТОГО</td>
                                            <td class="total text-center">{{$totalPaidUpProductsCount ?? 0}}</td>
                                            <td class="total text-center"></td>
                                            <td class="total text-center"></td>
                                            <td class="total text-center"></td>
                                        @endif
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
{{--@php--}}
{{--//    use App\Models\Redemption;--}}
{{--//  Redemption::calculatePercent();--}}
{{--@endphp--}}