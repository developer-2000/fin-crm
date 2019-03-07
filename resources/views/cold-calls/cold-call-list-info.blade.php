@extends('layouts.app')
@section('title') @lang('cold-calls.edit') @stop
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-default.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-bar.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/ns-style-theme.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/plans.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/orders_all.css') }}"/>
    <style>
        .crm_id {
            color: #19bc9d;
            font-size: 14px;
            border-bottom: 1px dashed #19bc9d;
            line-height: 15px;
            font-weight: 600;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('cold-calls.list'){{$listFile->id}}</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('cold-calls.list'){{$listFile->id}}</h1>
                @if (isset($permissions['create_edit_cold_call_list']))
                    <div class="pull-right top-page-ui">
                        <a href="{{route('cold-calls-import')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> @lang('cold-calls.list-upload')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="tabs-wrapper profile-tabs">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="{{ route('cold-calls-lists') }}"> @lang('cold-calls.list') {{$listFile->id . '   /    ' . $listFile->file_name}}</a>
                        </li>
                    </ul>
                    <div class="main-box clearfix">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($listFile)
                            <div class="main-box clearfix">
                                <div class="col-sm-6">
                                    <div class="main-box-body clearfix">
                                        <div class="table-responsive clearfix">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th><span>#</span></th>
                                                    <th class="text-center"><span> @lang('general.status')</span></th>
                                                    <th class="text-right"><span> @lang('general.quantity')</span></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        1
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge label-primary"> @lang('general.success')</span>
                                                    </td>
                                                    <td class="text-right" id="success">
                                                        {{$listFile->call_statuses->successQuantity}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        2
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge label-danger"> @lang('general.failure')</span>
                                                    </td>
                                                    <td class="text-right" id="failure">
                                                        {{$listFile->call_statuses->failureQuantity}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        3
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge label-warning"> @lang('general.shortcall')</span>
                                                    </td>
                                                    <td class="text-right" id="shortcall">
                                                        {{$listFile->call_statuses->shortCallQuantity}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        4
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge label-not-data"> @lang('general.abandoned')</span>
                                                    </td>
                                                    <td class="text-right" id="abandoned">
                                                        {{$listFile->call_statuses->abandonedQuantity}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        5
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge label-default"> @lang('general.no-answer')</span>
                                                    </td>
                                                    <td class="text-right" id="noanswer">
                                                        {{$listFile->call_statuses->noAnswerQuantity}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                    </td>
                                                    <td class="text-center" style="font-weight: bold">
                                                        @lang('general.total')
                                                    </td>
                                                    <td class="text-right">
                                                        <div id="totalrows"
                                                             style="font-weight: bold"> {{$listFile->call_statuses->listRowQuantity}}</div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="main-box-body clearfix">
                                        <div id="graph-flot-donut"
                                             style="height: 400px; padding: 0px; position: relative;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="main-box-body clearfix">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form class="form"
                                              action="{{ $_SERVER['REQUEST_URI'] }}"
                                              method="post">
                                            <div class="main-box">
                                                <div class="item_rows ">
                                                    <div class="main-box-body clearfix">
                                                        <br>
                                                        <div class="row">
                                                            <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                                <label for="id"
                                                                       class="col-sm-4 control-label"> @lang('general.id')</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" class="form-control" id="id"
                                                                           name="id"
                                                                           value="@if (isset($_GET['id'])){{ $_GET['id'] }}@endif">
                                                                </div>
                                                            </div>
                                                            @if (isset($permissions['filter_companies_page_orders']))
                                                                <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                                    <label for="company"
                                                                           class="col-sm-4 control-label"> @lang('general.phone')</label>
                                                                    <div class="col-sm-8">
                                                                        <select id="phone_number" name="phone_number[]"
                                                                                style="width: 100%" multiple>
                                                                            @foreach ($phone_numbers as $number)
                                                                                <option
                                                                                        @if (isset($_GET['phone_number']))
                                                                                        <? $numbersGet = explode(',', $_GET['phone_number']); ?>
                                                                                        @foreach ($numbersGet as $numberGet)
                                                                                        @if ($number == $numberGet)
                                                                                        selected
                                                                                        @endif
                                                                                        @endforeach
                                                                                        @endif
                                                                                        value="{{ $number }}">{{ $number }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="form-group col-md-3 col-sm-6 form-horizontal">
                                                                <label for="status"
                                                                       class="col-sm-4 control-label"> @lang('general.status')</label>
                                                                <div class="col-sm-8">
                                                                    <select id="status" name="status[]"
                                                                            style="width: 100%" multiple>
                                                                        <?
                                                                        $dataStatus = [
                                                                            'Success'   => 'Success',
                                                                            'Failure'   => 'Failure',
                                                                            'ShortCall' => 'ShortCall',
                                                                            'Abandoned' => 'Abandoned',
                                                                            'NoAnswer'  => 'NoAnswer',
                                                                        ];
                                                                        ?>
                                                                        @if ($dataStatus)
                                                                            @foreach ($dataStatus as $key => $status)
                                                                                <option
                                                                                        @if (isset($_GET['status']))
                                                                                        <? $statusGet = explode(',', $_GET['status']); ?>
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btns_filter">
                                                <input class="btn btn-success" type="submit" name="button_filter"
                                                       value=' @lang('general.search')'/>
                                                <a href="{{ route('cold-calls-lists-info', $listFile->id ) }}"
                                                   class="btn btn-warning"
                                                   type="submit"> @lang('general.reset')</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th> @lang('general.id')</th>
                                            <th class="text-center">  @lang('general.phone')</th>
                                            <th class="text-center"> @lang('general.fio')</th>
                                            <th> @lang('general.status')</th>
                                            <th> @lang('general.order')</th>
                                            <th class="text-center"> @lang('general.call-detailing')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($lists as $item)
                                            <tr class="list-row" id="{{$item->id}}">
                                                <td class="text-center">{{$item->id}}</td>
                                                @if(!empty($item->phone_number && $item->phone_number[1] == 1))
                                                    <td class="text-center">{{$item->phone_number[0]}}</td>
                                                    <input type="hidden" value="{{$item->phone_number[1]}}"
                                                           id="correct_number" name="correct_number">
                                                @elseif(!empty($item->phone_number && $item->phone_number[1] == 0))
                                                    <td class="text-center">{{$item->phone_number[0]}}</td>
                                                    <input type="hidden" value="{{$item->phone_number[1]}}"
                                                           id="correct_number" name="correct_number">
                                                @else
                                                    {{'N/A'}}
                                                @endif
                                                <td class="text-center">
                                                    @if(!empty($item->add_info->фио))
                                                        {{$item->add_info->фио}}
                                                    @elseif(!empty($item->add_info->фамилия)
                                                    && !empty($item->add_info->имя))
                                                        {{$item->add_info->фамилия.
                                                        ' '.$item->add_info->имя}}
                                                    @else
                                                        {{'N/A'}}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($item->call_status->call_status))
                                                        {{$item->call_status->call_status}}
                                                    @else
                                                        {{'N/A'}}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(!empty($item->order_id))
                                                        <a class="crm_id"
                                                           href="{{route('order', $item->order_id)}}">{{$item->order_id}}</a>
                                                    @else
                                                        {{'N/A'}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($item->progress_log))
                                                        @foreach($item->progress_log as $log)
                                                            {{  date('Y/m/d H:i:s', $log->date )}}
                                                            @if($log->status == 'Success')
                                                                <span class="badge label-primary">{{ $log->status }}</span>
                                                            @elseif($log->status == 'ShortCall')
                                                                <span class="badge label-warning">{{ $log->status }}</span>
                                                            @elseif($log->status == 'NoAnswer')
                                                                <span class="badge label-default">{{ $log->status }}</span>
                                                            @elseif($log->status == 'Failure')
                                                                <span class="badge label-danger">{{ $log->status }}</span>
                                                            @elseif($log->status == 'Abandoned')
                                                                <span class="badge label-not-data">{{ $log->status }}</span>
                                                            @endif  <br>
                                                            @if ($log->status == 'Success' || $log->status == 'ShortCall')
                                                                <?
                                                                $url = route('get-call-by-name') . '?fileName=' . $log->file;
                                                                $agent = $_SERVER['HTTP_USER_AGENT'];
                                                                if (preg_match('/(OPR|Firefox)/i', $agent)) {
                                                                    $output = '<p><a href="' . $url . '"><span class="fa-stack">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-download fa-stack-1x fa-inverse"></i>
                                                            </span></a></p>';
                                                                } else {
                                                                    $output = '
                                            <audio controls>
                                                <source src="' . $url . '" type="audio/mpeg">
                                            </audio>
                                    ';
                                                                }

                                                                echo '<div class="row">' . $output . '</div>';?>
                                                            @endif
                                                            <br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $lists->links() }}
@endsection
@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modernizr.custom.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/classie.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/notificationFx.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/plans/plan-create.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.pie.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.stack.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.resize.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.time.min.js') }}"></script>
    <script src="{{ URL::asset('js/flot/jquery.flot.orderBars.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/pace.min.js') }}"></script>
    <script>
        $(function () {

            // bar chart
            if ($('#graph-bar').length) {
                var db1 = [];
                for (var i = 0; i <= 10; i += 1) {
                    db1.push([i, parseInt(Math.random() * 30)]);
                }

                var db2 = [];
                for (var i = 0; i <= 10; i += 1) {
                    db2.push([i, parseInt(Math.random() * 30)]);
                }

                var db3 = [];
                for (var i = 0; i <= 10; i += 1) {
                    db3.push([i, parseInt(Math.random() * 30)]);
                }

                var series = new Array();

                series.push({
                    data: db1,
                    bars: {
                        show: true,
                        barWidth: 0.2,
                        order: 1,
                        lineWidth: 1,
                        fill: 1
                    }
                });
                series.push({
                    data: db2,
                    bars: {
                        show: true,
                        barWidth: 0.2,
                        order: 2,
                        lineWidth: 1,
                        fill: 1
                    }
                });
                series.push({
                    data: db3,
                    bars: {
                        show: true,
                        barWidth: 0.2,
                        order: 3,
                        lineWidth: 1,
                        fill: 1
                    }
                });

                $.plot("#graph-bar", series, {
                    colors: ['#FF6C60', '#ffc107', '#1abc9c', '#777777', '#90a4ae', '#90a4ae'],
                    grid: {
                        tickColor: "#ddd",
                        borderWidth: 0
                    },
                    shadowSize: 0
                });
            }

            // bar chart - horizontal
            if ($('#graph-flot-bar-horizontal').length) {
                var db1 = [];
                for (var i = 0; i <= 4; i += 1) {
                    db1.push([parseInt(Math.random() * 30), i]);
                }

                var db2 = [];
                for (var i = 0; i <= 4; i += 1) {
                    db2.push([parseInt(Math.random() * 30), i]);
                }

                var db3 = [];
                for (var i = 0; i <= 4; i += 1) {
                    db3.push([parseInt(Math.random() * 30), i]);
                }

                var series = new Array();

                series.push({
                    data: db1,
                    bars: {
                        show: true,
                        barWidth: 0.2,
                        order: 1,
                        lineWidth: 1,
                        horizontal: true,
                        fill: 1
                    }
                });
                series.push({
                    data: db2,
                    bars: {
                        show: true,
                        barWidth: 0.2,
                        order: 2,
                        lineWidth: 1,
                        horizontal: true,
                        fill: 1
                    }
                });
                series.push({
                    data: db3,
                    bars: {
                        show: true,
                        barWidth: 0.2,
                        order: 3,
                        lineWidth: 1,
                        horizontal: true,
                        fill: 1
                    }
                });

                $.plot("#graph-flot-bar-horizontal", series, {
                    colors: ['#FF6C60', '#ffc107', '#1abc9c', '#777777', '#90a4ae', '#90a4ae'],
                    grid: {
                        tickColor: "#ddd",
                        borderWidth: 0
                    },
                    shadowSize: 0
                });
            }

            // graph with points - sin/cos example
            if ($('#graph-flot-sin').length) {
                var sin = [],
                    cos = [];

                for (var i = 0; i < 14; i += 0.5) {
                    sin.push([i, Math.sin(i)]);
                    cos.push([i, Math.cos(i)]);
                }

                var plot = $.plot("#graph-flot-sin", [
                    {data: sin, label: "sin(x)"},
                    {data: cos, label: "cos(x)"}
                ], {
                    series: {
                        lines: {
                            show: true,
                            lineWidth: 2
                        },
                        points: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true,
                        tickColor: "#ddd",
                        borderWidth: 0
                    },
                    yaxis: {
                        min: -1.2,
                        max: 1.2
                    },
                    colors: ['#FF6C60', '#1abc9c', '#ffc107', '#777777', '#90a4ae', '#90a4ae'],
                    shadowSize: 0
                });

                function showTooltip(x, y, contents) {
                    $("<div id='tooltip'>" + contents + "</div>").css({
                        position: "absolute",
                        display: "none",
                        top: y + 5,
                        left: x + 5,
                        border: "1px solid #fdd",
                        padding: "2px",
                        "background-color": "#fee",
                        opacity: 0.80
                    }).appendTo("body").fadeIn(200);
                }

                var previousPoint = null;
                $("#graph-flot-sin").bind("plothover", function (event, pos, item) {

                    if ($("#enablePosition:checked").length > 0) {
                        var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
                        $("#hoverdata").text(str);
                    }

                    if ($("#enableTooltip:checked").length > 0) {
                        if (item) {
                            if (previousPoint != item.dataIndex) {

                                previousPoint = item.dataIndex;

                                $("#tooltip").remove();
                                var x = item.datapoint[0].toFixed(2),
                                    y = item.datapoint[1].toFixed(2);

                                showTooltip(item.pageX, item.pageY,
                                    item.series.label + " of " + x + " = " + y);
                            }
                        } else {
                            $("#tooltip").remove();
                            previousPoint = null;
                        }
                    }
                });

                $("#graph-flot-sin").bind("plotclick", function (event, pos, item) {
                    if (item) {
                        $("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
                        plot.highlight(item.series, item.datapoint);
                    }
                });
            }

            // stack graph
            if ($('#graph-flot-stacking').length) {
                var d1 = [];
                for (var i = 0; i <= 10; i += 1) {
                    d1.push([i, parseInt(Math.random() * 30)]);
                }

                var d2 = [];
                for (var i = 0; i <= 10; i += 1) {
                    d2.push([i, parseInt(Math.random() * 30)]);
                }

                var d3 = [];
                for (var i = 0; i <= 10; i += 1) {
                    d3.push([i, parseInt(Math.random() * 30)]);
                }

                var stack = 0,
                    bars = true,
                    lines = false,
                    steps = false;

                function plotWithOptions() {
                    $.plot("#graph-flot-stacking", [d1, d2, d3], {
                        series: {
                            stack: stack,
                            lines: {
                                show: lines,
                                fill: true,
                                steps: steps,
                                lineWidth: 1,
                                fill: 1
                            },
                            bars: {
                                show: bars,
                                barWidth: 0.3,
                                lineWidth: 1,
                                fill: 1
                            }
                        },
                        colors: ['#FF6C60', '#ffc107', '#1abc9c', '#777777', '#90a4ae', '#90a4ae'],
                        grid: {
                            tickColor: "#ddd",
                            borderWidth: 0
                        },
                        shadowSize: 0
                    });
                }

                plotWithOptions();

                $(".stackControls button").click(function (e) {
                    e.preventDefault();
                    stack = $(this).text() == "With stacking" ? true : null;
                    plotWithOptions();
                });

                $(".graphControls button").click(function (e) {
                    e.preventDefault();
                    bars = $(this).text().indexOf("Bars") != -1;
                    lines = $(this).text().indexOf("Lines") != -1;
                    steps = $(this).text().indexOf("steps") != -1;
                    plotWithOptions();
                });
            }

            // donut chart
            if ($('#graph-flot-donut').length) {
                var success = $('#success')[0].textContent;
                var failure = $('#failure')[0].textContent;
                var abandoned = $('#abandoned')[0].textContent;
                var shortcall = $('#shortcall')[0].textContent;
                var noanswer = $('#noanswer')[0].textContent;

                var dataDonut = [
                    {label: "Failure", data: failure},
                    {label: "ShortCall", data: shortcall},
                    {label: "Success", data: success},
                    {label: "Abandoned", data: abandoned},
                    {label: "NoAnswer", data: noanswer},
                ];

                $.plot('#graph-flot-donut', dataDonut, {
                    series: {
                        pie: {
                            show: true,
                            innerRadius: 0.5,
                            label: {
                                show: true,
                            }
                        }
                    },
                    colors: ['#FF6C60', '#ffc107', '#1abc9c', '#777777', '#90a4ae', '#90a4ae'],
                    legend: {
                        show: false,
                    }
                });
            }

            // graph with points
            if ($('#graph-flot-points').length) {
                var likes = [[1, 5], [2, 10], [3, 15], [4, 20], [5, 25], [6, 30], [7, 35], [8, 40], [9, 45], [10, 50], [11, 55], [12, 60], [13, 65], [14, 70], [15, 75], [16, 80], [17, 85], [18, 90], [19, 85], [20, 80], [21, 75], [22, 80], [23, 75], [24, 70], [25, 65], [26, 75], [27, 80], [28, 85], [29, 90], [30, 95]];

                var plot = $.plot($("#graph-flot-points"),
                    [{data: likes, label: "Fans"}], {
                        series: {
                            lines: {
                                show: true,
                                lineWidth: 2,
                                fill: true,
                                fillColor: {colors: [{opacity: 0.3}, {opacity: 0.3}]}
                            },
                            points: {
                                show: true,
                                lineWidth: 2
                            },
                            shadowSize: 0
                        },
                        grid: {
                            hoverable: true,
                            clickable: true,
                            tickColor: "#f9f9f9",
                            borderWidth: 0
                        },
                        colors: ["#58DDD0"],
                        xaxis: {ticks: 6, tickDecimals: 0},
                        yaxis: {ticks: 3, tickDecimals: 0},
                    });

                function showTooltip(x, y, contents) {
                    $('<div id="tooltip">' + contents + '</div>').css({
                        position: 'absolute',
                        display: 'none',
                        top: y + 5,
                        left: x + 5,
                        border: '1px solid #fdd',
                        padding: '2px',
                        'background-color': '#dfeffc',
                        opacity: 0.80
                    }).appendTo("body").fadeIn(200);
                }

                var previousPoint = null;
                $("#graph-flot-points").bind("plothover", function (event, pos, item) {
                    $("#x").text(pos.x.toFixed(2));
                    $("#y").text(pos.y.toFixed(2));

                    if (item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2);

                            showTooltip(item.pageX, item.pageY,
                                item.series.label + " of " + x + " = " + y);
                        }
                    }
                    else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            }

            // graph real time
            if ($('#graph-flot-realtime').length) {

                var data = [],

                    totalPoints = $('#totalrows')[0].textContent;

                function getRandomData() {

                    if (data.length > 0)
                        data = data.slice(1);

                    // Do a random walk

                    while (data.length < totalPoints) {

                        var prev = data.length > 0 ? data[data.length - 1] : 50,
                            y = prev + Math.random() * 10 - 5;

                        if (y < 0) {
                            y = 0;
                        } else if (y > 100) {
                            y = 100;
                        }

                        data.push(y);
                    }

                    // Zip the generated y values with the x values

                    var res = [];
                    for (var i = 0; i < data.length; ++i) {
                        res.push([i, data[i]])
                    }

                    return res;
                }

                // Set up the control widget

                var updateInterval = 30;
                $("#updateInterval").val(updateInterval).change(function () {
                    var v = $(this).val();
                    if (v && !isNaN(+v)) {
                        updateInterval = +v;
                        if (updateInterval < 1) {
                            updateInterval = 1;
                        } else if (updateInterval > 2000) {
                            updateInterval = 2000;
                        }
                        $(this).val("" + updateInterval);
                    }
                });

                var plot = $.plot("#graph-flot-realtime", [getRandomData()], {
                    series: {
                        lines: {
                            show: true,
                            lineWidth: 2,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.3}, {opacity: 0.3}]}
                        },
                        shadowSize: 0	// Drawing is faster without shadows
                    },
                    colors: ["#FF6C60"],
                    yaxis: {
                        min: 0,
                        max: 100
                    },
                    xaxis: {
                        show: false
                    }
                });

                function update() {

                    plot.setData([getRandomData()]);

                    // Since the axes don't change, we don't need to call plot.setupGrid()

                    plot.draw();
                    setTimeout(update, updateInterval);
                }

                update();
            }
        });

        function labelFormatter(label, series) {
            return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
        }
    </script>

    <script src="{{ URL::asset('js/cold-calls/cold-call-info.js') }}"></script>
@stop
