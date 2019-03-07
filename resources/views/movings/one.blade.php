@extends('layouts.app')

@section('title')@lang('movings.title') #{{ $moving->id }}@stop

@section('css')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/storages-common.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/moving-one.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/moving_create.css') }}">
@stop

@section('jsBottom')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js"></script>
    <script src="{{ URL::asset('js/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js') }}"></script>
    <script src="{{ URL::asset('js/storages/moving.js') }}"></script>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('index') }}"> @lang('general.main')</a></li>
                <li><a href="{{ route('movings') }}"> @lang('movings.title')</a></li>
                <li class="active"><span>#{{ $moving->id }}</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left"> @lang('movings.title') #{{ $moving->id }}</h1>

                @if (($moving->status == \App\Models\Moving::STATUS_RECEIVED) && auth()->user()->isReceiverHandling($moving))
                <form action="{{ route('moving-close', $moving->id) }}" method="post" class="pull-right" id="close_form">
                    <button class="btn btn-primary" id="close_button">
                        <i class="fa fa-flag-checkered"></i>
                        @lang('general.close')
                    </button>
                </form>
                @endif

            </div>
        </div>
    </div>

    <div class="alert alert-success" id="message" style="{!! session('message') ? '' : 'display:none' !!}">
        <i class="fa fa-check-circle fa-fw fa-lg"></i>
        <span>{{ session('message') ?: '' }}</span>
    </div>

    <div class="alert alert-danger" id="error" style="{!! session('error') ? '' : 'display:none' !!}">
        <i class="fa fa-exclamation fa-fw fa-lg"></i>
        <span>{{ session('error') ?: '' }}</span>
    </div>

    <div class="main-box clearfix">
        <header class="main-box-header clearfix moving-header">
            <div class="row">
                <div class="col-md-6">
                    <h4>
                        <span> @lang('general.status'):</span>
                        @php $status_colors = ['danger', 'warning', 'success', 'default']; @endphp
                        <b class="label label-{!! $status_colors[$moving->status] !!}">
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
                        </b>
                    </h4>
                    <h4>
                        <input type="hidden" id="moving_id" value="{{ $moving->id }}" />
                        <input type="hidden" id="sender_id" value="{{ $moving->sender_id }}" />
                        <input type="hidden" id="project_id" value="{{
                            $moving->sender_id ? $moving->sender->parent_id : $moving->receiver->parent_id }}" />
                        <span> @lang('general.sender'):</span>
                        @if ($moving->sender_id)
                            {{  $moving->sender->parent->name }} / {{ $moving->sender->name }}
                        @else
                            - @lang('movings.space') -
                        @endif
                    </h4>
                    <h4>
                        <span> @lang('general.receiver'):</span>
                        @if ($moving->receiver_id)
                            {{  $moving->receiver->parent->name }} / {{ $moving->receiver->name }}
                        @else
                            - @lang('movings.space') -
                        @endif
                    </h4>
                </div>
                <div class="col-md-6">
                    <h4>
                        <span> @lang('general.date-send'):</span>

                        @if (auth()->user()->isSenderHandling($moving))
                            <div class="move-date">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input class="form-control mb-2 mr-sm-2 datetimepicker" type="text" name="datetime" id="send_date"
                                               value="{{ $moving->send_date }}" />
                                        <span class="input-group-btn">
                                        <button class="btn btn-primary" id="send_date_btn"
                                                data-url="{{ route('moving-change-date', $moving->id) }}">
                                            @lang('general.change')
                                        </button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <i id="send_date">{{ $moving->send_date ?: '-' }}</i>
                        @endif
                    </h4>
                    <h4>
                        <span> @lang('movings.date-received'):</span>
                        @if (auth()->user()->isSenderHandling($moving))
                            <div class="move-date">
                                <div class="form-group">
                                    <div class="input-group">

                                        <input class="form-control mb-2 mr-sm-2 datetimepicker" type="text" name="datetime" id="received_date"
                                               value="{{ $moving->received_date }}" />
                                        <span class="input-group-btn">
                                        <button class="btn btn-primary" id="received_date_btn"
                                                data-url="{{ route('moving-change-date', $moving->id) }}">
                                            @lang('general.change')
                                        </button>
                                    </span>
                                    </div>
                                </div>

                            </div>
                        @else
                            <i id="received_date">{{ $moving->received_date ?: '-' }}</i>
                        @endif
                    </h4>
                </div>
            </div>
        </header>

        <div class="main-box-body clearfix">

            @switch ($moving->status)

                @case(\App\Models\Moving::STATUS_NEW)
                    @if (auth()->user()->isSenderHandling($moving))
                        <div id="for_products">
                            @if (!empty($movingProducts))
                                @foreach($movingProducts as $mp)
                                    @include('movings.new-product', ['product' => $mp])
                                @endforeach
                                @include('movings.products2'/*, ['products' => $mpInSelect]*/)
                                @include('movings.button', ['type' => 1])
                            @endif
                        </div>
                        @include('movings.for-errors-and-messages')
                    @else
                        <div class="table-responsive">
                            <table id="table-moving" class="table table-hover">
                                <thead>
                                <tr>
                                    <th> @lang('general.product') ( @lang('general.id')) </th>
                                    <th> @lang('general.product') ( @lang('general.name'))</th>
                                    <th> @lang('movings.scheduled-send')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($movingProducts as $mp)
                                    <tr>
                                        <td>{{ $mp['id'] }}</td>
                                        <td>{{ $mp['title'] }}</td>
                                        <td>{{ $mp['amount'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @break

                @case(\App\Models\Moving::STATUS_SENT)
                    @if (auth()->user()->isReceiverHandling($moving))
                        <div id="for_products">
                            @if ($arrivedProducts->isNotEmpty())
                                @foreach($arrivedProducts as $ap)
                                    @include('movings.arrived-product', ['product' => $ap])
                                @endforeach
                                @include('movings.button', ['type' => 2])
                            @endif
                        </div>
                        @include('movings.for-errors-and-messages')
                    @else
                        @include('movings.arrived-products-table', $arrivedProducts)
                    @endif
                @break

                @case(\App\Models\Moving::STATUS_RECEIVED)
                    @include('movings.arrived-products-table', $arrivedProducts)
                @break

                @case(\App\Models\Moving::STATUS_CLOSED)
                    @include('movings.arrived-products-table', $arrivedProducts)
                @break
            @endswitch

        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2> @lang('movings.history-move'):</h2>
                </header>
                <div class="main-box-body clearfix">
                    <h3> @lang('general.date-created'):</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr class="active">
                                <td><span>{{ $moving->user->name }} {{ $moving->user->surname }}</span></td>
                                <td>
                                    <span class="trans-time">{{ $moving->created_at->format('H:i:s') }}</span>
                                    <span class="trans-date">{{ $moving->created_at->format('d.m.Y') }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    @if ($user_sender && $send_date)
                    <h3> @lang('general.send'):</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr class="active">
                                <td><span>{{ $user_sender }}</span></td>
                                <td>
                                    <span class="trans-time">{{ $send_date->format('H:i:s') }}</span>
                                    <span class="trans-date">{{ $send_date->format('d.m.Y') }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    @endif

                    @if (!empty($history))
                    <h3> @lang('general.received'):</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                        @foreach ($history as $hi)
                            <tr class="active">
                                <td><span>{{ $hi[0]['user_name'] }}</span></td>
                                <td>
                                    <span class="trans-time">{{ $hi[0]['created_at']->format('H:i:s') }}</span>
                                    <span class="trans-date">{{ $hi[0]['created_at']->format('d.m.Y') }}</span>
                                </td>
                                <td></td>
                            </tr>
                            @foreach($hi as $h)
                            <tr>
                                <td></td>
                                <td><span>{{ $h['product_name'] }}</span></td>
                                <td>
                                    <span>
                                    @if ($h['status'] == \App\Models\MovingProductPart::STATUS_ARRIVED)
                                        <i class="fa fa-check-square-o" title=" @lang('movings.arrived')"></i>
                                    @else
                                        <i class="fa fa-thumbs-down" title=" @lang('movings.shortage')"></i>
                                    @endif
                                    {{ $h['amount'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                        </table>
                    </div>
                    @endif

                    @if ($moving->status == \App\Models\Moving::STATUS_CLOSED)
                    <h3> @lang('general.closed')</h3>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2> @lang('movings.correspondence'):</h2>
                </header>
                <div class="main-box-body clearfix">
                    <div class="conversation-wrapper">

                        <div class="conversation-new-message">
                            <form method="post" id="moving_comment" data-url="{{ route('moving-comment') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="moving_id" value="{{ $moving->id }}"/>

                                <div class="form-group">
                                    <textarea class="form-control" rows="2" name="text"
                                              placeholder=" @lang('movings.enter-your-message')...">{{ old('text') }}</textarea>
                                    <p class="help-block" style="display:none;">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <span></span>
                                    </p>
                                </div>
                                <div class="clearfix">
                                    <button type="submit" class="btn btn-success pull-right">
                                        @lang('general.send')</button>
                                </div>
                            </form>
                        </div>

                        @if ($moving->comments->isNotEmpty())
                            @php
                                $comments = $moving->comments->sortByDesc('date');
                            @endphp
                        @endif

                        <div class="conversation-content">
                            <div class="conversation-inner">

                                @if ($moving->comments->isNotEmpty())
                                    @foreach ($comments as $comment)
                                        <div class="conversation-item clearfix
                                            @if ($comment->user_id == auth()->user()->id) item-right
                                            @else item-left @endif">
                                            <div class="conversation-user">
                                                <img src="{{ URL::asset('img' . $comment->user->photo) }}" alt=""/>
                                            </div>
                                            <div class="conversation-body">
                                                <div class="name">
                                                    {{ $comment->user->name }}
                                                    {{ $comment->user->surname ?: '' }}
                                                </div>
                                                <div class="time hidden-xs">
                                                    {{ $comment->date }}
                                                </div>
                                                <div class="text">
                                                    {{ $comment->text }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop