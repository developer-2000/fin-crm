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

    <script src="{{ URL::asset('js/flot/jquery.flot.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.pie.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.stack.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.resize.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.time.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.orderBars.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('tablesorter_master/jquery.tablesorter.js') }}"></script>
    <script src="{{ URL::asset('js/reports/statuses.js') }}"></script>
    <script src="{{ URL::asset('js/users/account.js') }}"></script>
    <script>
        $(function () {
            let vn = $('.vn, .ua, .kz, .ru');
            let results = [];
            let dataDonut = [];
            if (vn.length) {
                vn.each(function (index, element) {
                    let post = $(element).data('post');
                    
                    try {
                        if (post == 'viettel') {
                            $.get('/ajax/viettel/province/find', {province: $(element).text()}, function (json) {
                                if (json.length) {
                                    $(element).text(json[0].text);
                                    results.push(index);
                                }
                            })
                        } else if (post == 'wefast') {
                            $.get('/ajax/integrations/wefast/find/province', {q: $(element).text()}, function (json) {
                                if (json.length) {
                                    $(element).text(json[0].text);
                                    results.push(index);
                                }
                            })
                        } else if (post == 'novaposhta') {
                            $.post('/ajax/novaposhta/settlements/find', {SettlementRef: $(element).text()}, function (json) {
                                if (json.length) {
                                    let label = json[0].text.split(',');
                                    $(element).text(label[0]);
                                    results.push(index);
                                }
                            });
                        } else if (post == 'measoft') {
                            $.get('/ajax/integrations/measoft/find/town', {query: 'town', code: $(element).text()}, function (json) {
                                if (json.length) {
                                    $(element).text(json[0].text);
                                    results.push(index);
                                }
                            })
                        } else {
                            results.push(index);
                        }
                    } catch (e) {
                        results.push(index);
                    }
                });
            }

            var idInterval = setInterval(function () {
                if (results.length == vn.length) {
                    vn.each(function (index, element) {
                        let parentTr = $(element).parents('tr');
                        let count = parentTr.find('.count');
                        let obj = {
                            label: $(element).text(),
                            data: count.text()
                        };
                        dataDonut.push(obj)
                    });
                    // donut chart
                    if ($('#graph-flot-donut').length) {
                        let sortable = dataDonut.sort(function (a,b) {
                            if(+a.data < +b.data){ return 1}
                            if(+a.data > +b.data){ return -1}
                            return 0;
                        });
                        $.plot('#graph-flot-donut', sortable.slice(0, 10), {
                            series: {
                                pie: {
                                    show: true,
                                    innerRadius: 0.5,
                                    label: {
                                        show: true,
                                    }
                                }
                            },
                            legend: {
                                show: false,
                            }
                        });
                    }
                    clearInterval(idInterval);
                }
            }, 500);
        });
    </script>
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
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="country"> @lang('general.country')</label>
                                        <select name='country' id="country" style="width: 100%">
                                            <option value=""> @lang('general.all')</option>
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
                                            <label for="product"
                                                   class="col-sm-2 control-label">  @lang('general.products')</label>
                                            <input id="product"
                                                   data-product="{{!empty($dataProducts) ? $dataProducts : ''}}"
                                                   class="product " name="product"
                                                   value="{{!empty($dataProductsIds) ? $dataProductsIds : ''}}"
                                                   style="width: 100%">
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
                                <label class="btn btn-primary @if (!isset($_GET['date_type'])  || (isset($_GET['date_type'])  && $_GET['date_type'] == 'time_created')) active @endif"
                                       id="time_created" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-created')">
                                    <input type="radio"
                                           @if (!isset($_GET['date_type']) || (isset($_GET['date_type'])  && $_GET['date_type'] == 'time_created'))
                                           checked
                                           @endif
                                           name="date_type" value="time_created"> <i class="fa fa-calendar"></i>
                                </label>
                                <label class="btn btn-primary @if (isset($_GET['date_type'])  && $_GET['date_type'] == 1 ) active @endif"
                                       id="time_modified" data-toggle="tooltip"
                                       data-placement="bottom" title=" @lang('general.date-set-target')">
                                    <input type="radio"
                                           @if (isset($_GET['date_type'])  && $_GET['date_type'] == 'time_modified' )
                                           checked
                                           @endif
                                           name="date_type" value="time_modified"><i class="fa fa-star-half-empty"></i>
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
                    <a href="{{ route('report-by-city') }}" class="btn btn-warning"
                       type="submit"> @lang('general.reset')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-box clearfix">
                <div class='main-box-body clearfix'>
                    <div class="col-md-4">
                        <h2 class="text-center">Top 10</h2>
                        <div id="graph-flot-donut"
                             style="height: 400px; padding: 0px; position: relative;">
                        </div>
                    </div>
                    @if (count($result))
                        @foreach($result as $code => $country)
                            <div class="col-md-4">
                                <h2> @lang('countries.' . $code)</h2>
                                <div class="table-responsive">
                                    <table class="table tablesorter table_country">
                                        <thead>
                                        <tr>
                                            <th class="header"> @lang('general.city')</th>
                                            <th class="text-center header"> @lang('general.count')</th>
                                            <th class="text-center header"> %</th>
                                        </tr>
                                        </thead>
                                        @if(!empty($country['cities']))
                                            <tbody>
                                            @foreach($country['cities'] as $city)
                                                <tr>
                                                    <td class="{{$code}}" data-content="{{$city['name']}}" data-post="{{$city['post_name']}}">{{$city['name']}}</td>
                                                    <td class="count">{{$city['count']}}</td>
                                                    <td>
                                                        @if(!empty($country['all']))
                                                            {{number_format($city['count'] / $country['all'] * 100, 2)}} %
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>
                                                        @lang('general.all')
                                                    </th>
                                                    <th>
                                                        {{$country['all'] ?? 0}}
                                                    </th>
                                                    <th>100%</th>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>

                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop