@extends('layouts.app')

@section('title')@lang('movings.title')@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/datepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/storages-common.css') }}">
@stop

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-datepicker.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ URL::asset('js/vendor/modalEffects.js') }}"></script>
    <script src="{{ URL::asset('js/storages/movings.js') }}"></script>
@stop

@section('content')

    @php $status_colors = ['danger', 'warning', 'success', 'default']; @endphp

    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}"> @lang('general.main')</a></li>
                <li class="active"><span> @lang('movings.title')</span></li>
            </ol>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle fa-fw fa-lg"></i>
            {{ session('message') }}
        </div>
    @endif

    <div class="clearfix">
        <h1 class="pull-left">
            @lang('warehouses.movings-found') : (<span class="badge">{{ $movings->total() }}</span>)
        </h1>
        @if(isset($permissions['moving_create']))
            <a href="{{ route('moving-create') }}" class="btn btn-primary pull-right">
                <i class="fa fa-plus"></i>
                @lang('warehouses.moving-create')
            </a>
        @endif
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix" style="padding-top:12px;">
            <div class="row">
                {!! Form::open(['route' => ['movings'], 'method' => 'get', 'id' => 'movings_form']) !!}
                {!! Form::token() !!}

                <div class="form-group col-sm-2 form-horizontal" style="width:10%">
                    {!! Form::number('id',
                        \Request::get('id', null),
                        ['placeholder' => '[id]', 'id' => 'id', 'min' => 0, 'class' => 'form-control']
                    ) !!}
                </div>

                <div class="form-group col-sm-3 form-horizontal" style="width:20%">
                    {!! Form::select(
                        'status',
                        $statuses,
                        \Request::get('status', null),
                        ['placeholder' => '['.trans('general.status').']', 'id' => 'status_select', 'style' => 'width:100%;']
                    ) !!}
                </div>

                <div class="form-group col-sm-3 form-horizontal" style="width:35%">
                    {!! Form::select(
                        'sender_id',
                        $senders,
                        \Request::get('sender_id', null),
                        ['placeholder' => '['.trans('general.sender').']', 'id' => 'sender_select', 'style' => 'width:100%;']
                    ) !!}
                </div>

                <div class="form-group col-sm-3 form-horizontal" style="width:35%">
                    {!! Form::select(
                        'receiver_id',
                        $receivers,
                        \Request::get('receiver_id', null),
                        ['placeholder' => '['.trans('general.receiver').']', 'id' => 'receiver_select', 'style' => 'width:100%;']
                    ) !!}
                </div>

                <div style="clear:both;"></div>

                <div class="form-group col-sm-3" style="width:15%">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input class="form-control" id="date_start" type="text"
                               data-toggle="tooltip" name="{{ $date_start_name }}"
                               data-placement="bottom"
                               placeholder=" @lang('general.from')"
                               value="{{ \Request($date_start_name, null) }}">
                    </div>
                </div>

                <div class="form-group col-sm-3" style="width:15%">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input class="form-control" id="date_end" type="text"
                               data-toggle="tooltip" name="{{ $date_end_name }}"
                               data-placement="bottom"
                               placeholder=" @lang('general.to')"
                               value="{{ \Request($date_end_name, null) }}">
                    </div>
                </div>

                <div class="col-sm-2" style="width:130px;">
                    <div class="btn-group date_type" data-toggle="buttons">
                        <label class="btn btn-primary
                               {!! (($date_start_name == 'created_at_start') || ($date_end_name == 'created_at_end')) ? 'active' : '' !!}"
                               id="created_at" data-toggle="tooltip"
                               data-placement="bottom" title=" @lang('general.date-created')">
                            <input type="radio" name="date-type" value="1"
                                    {!! (($date_start_name == 'created_at_start') || ($date_end_name == 'created_at_end')) ? 'checked' : '' !!} />
                            <i class="fa fa-road"></i>
                        </label>
                        <label class="btn btn-primary
                               {!! (($date_start_name == 'send_date_start') || ($date_end_name == 'send_date_end')) ? 'active' : '' !!}"
                               id="send_date" data-toggle="tooltip"
                               data-placement="bottom" title=" @lang('movings.date-send')">
                            <input type="radio" name="date-type" value="3"
                                    {!! (($date_start_name == 'send_date_start') || ($date_end_name == 'send_date_end')) ? 'checked' : '' !!} />
                            <i class="fa fa-truck"></i>
                        </label>
                        <label class="btn btn-primary
                               {!! (($date_start_name == 'received_date_start') || ($date_end_name == 'received_date_end')) ? 'active' : '' !!}"
                               id="received_date" data-toggle="tooltip"
                               data-placement="bottom" title=" @lang('movings.date-resived')">
                            <input type="radio" name="date-type" value="3"
                                    {!! (($date_start_name == 'received_date_start') || ($date_end_name == 'received_date_end')) ? 'checked' : '' !!} />
                            <i class="fa fa-check-square-o"></i>
                        </label>
                    </div>
                </div>

                <div class="col-sm-6" style="width:calc(70% - 130px);">
                    <div class="input-group" style="width:100%;">
                        <div class="btn-group" data-toggle="buttons" id="date_template" style="width:100%;">
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
                            <label class="btn btn-default pattern_date pattern_date_big">
                                <input type="radio" name="date_template" value="2"> @lang('general.last-month')
                            </label>
                        </div>
                    </div>
                </div>

                <div class="btns_filter">
                    <button class="btn btn-warning" type="reset" id="clear_form" data-url="{{ route('movings') }}"
                            style="width:calc(30% + 114px);">
                        <i class="fa fa-times"></i>
                        @lang('general.reset')
                    </button>
                    <button class="btn btn-success" type="submit" id="submit" style="width:calc(70% - 147px);">
                        <i class="fa fa-filter"></i>
                        @lang('general.search')
                    </button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="main-box clearfix">
        <div class="main-box-body clearfix">
            <div class="table-responsive">
                <table id="table_movings" class="table table-striped table-hover storage-table">
                    <thead>
                    <tr style="border-bottom:none;">
                        <th><span>ID</span></th>
                        <th><span> @lang('movings.responsibles')</span></th>
                        <th><span> @lang('general.count')</span></th>
                        <th><span> @lang('general.sender')</span></th>
                        <th><span> @lang('general.receiver')</span></th>
                        <th><span> @lang('general.products')</span></th>
                        <th><span> @lang('general.status')</span></th>
                        <th><span> @lang('general.date')</span></th>
                        <th></th>
                    </tr>
                    </thead>

                    @if($movings->isNotEmpty())
                        <tbody>
                        @foreach($movings as $moving)
                            <tr >
                                <td>
                                    <a href="{{ route('moving', $moving->id) }}" target="_blank">
                                        #{{ $moving->id }}
                                    </a>
                                </td>
                                <td>
                                    <span>{{ $moving->user->surname ?? ''}} {{ $moving->user->name ?? '' }}</span>
                                    @if ($moving->movingProducts->isNotEmpty())
                                        @foreach($moving->movingProducts as $movingProduct)
                                            @if ($movingProduct->parts->isNotEmpty())
                                                @foreach($movingProduct->parts as $part)
                                                    <div>  : {{$part->user->surname ?? ''}} {{$part->user->name ?? ''}}</div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td><span>{{ $moving->amount }}</span></td>
                                <td><span>{{ $moving->sender }}</span></td>{{--отправитель --}}
                                <td><span>{{ $moving->receiver }}</span></td>
                                <td>
                                    @if ($moving->movingProducts->isNotEmpty())
                                        @foreach($moving->movingProducts as $movingProduct)
                                            {{$movingProduct->amount}} - {{$movingProduct->product->title ?? $movingProduct->product_id}} <br>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <span class="label label-{!! $status_colors[$moving->status] !!}">
                                        {{--{{ \App\Models\Moving::langStatuses()[$moving->status] }}--}}
                                        @switch ($moving->status)
                                            @case('0')
                                            @lang('movings.new')
                                            @break
                                            @case('1')
                                            @lang('movings.send')
                                            @break
                                            @case('2')
                                            @lang('movings.received')
                                            @break
                                            @case('3')
                                            @lang('movings.closed')
                                            @break
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        @lang('general.date-created') :
                                        <span class="trans-time">
                                            {{ $moving->created_at ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $moving->created_at)->format('H:i:s') : '' }}
                                        </span>
                                        <span class="trans-date">
                                            {{ $moving->created_at ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $moving->created_at)->format('d.m.Y') : '' }}
                                        </span>
                                    </div>
                                    @if ($moving->send_date)
                                    <div>
                                        @lang('general.sent') :
                                        <span class="trans-time">
                                            {{ $moving->send_date ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $moving->send_date)->format('H:i:s') : '' }}
                                        </span>
                                        <span class="trans-date">
                                            {{ $moving->send_date ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $moving->send_date)->format('d.m.Y') : '' }}
                                        </span>
                                    </div>
                                    @endif
                                    @if ($moving->received_date)
                                        <div>
                                            @lang('movings.received') :
                                            <span class="trans-time">
                                                {{ $moving->received_date ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $moving->received_date)->format('H:i:s') : '' }}
                                            </span>
                                            <span class="trans-date">
                                                {{ $moving->received_date ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $moving->received_date)->format('d.m.Y') : '' }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('moving', $moving->id) }}" class="pull-right btn btn-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            </div>

            @if($movings->isEmpty())
                <p> @lang('general.no-results')</p>
            @endif
        </div>
    </div>

    {!! $movings->appends($appendPage)->links() !!}

@stop
