@extends('collectings.layout')
@section('contentdata')
    @if(isset($permissions['assign_orders_by_collectors']))
        <div class="md-modal md-effect-6" id="add_orders">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title"> @lang('collectings.assign-orders'):</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-7 form-horizontal">
                            <label for="collectors"
                                   class="col-sm-4 control-label"> @lang('collectings.collectors')</label>
                            <div class="col-sm-8">
                                <select id="collectors" name="collectors[]" style="width: 100%"
                                        multiple>
                                    @foreach ($collectors as $collector)
                                        <option value="{{ $collector->id }}">{{ $collector->surname .' '. $collector->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class=" btn btn-primary share" data-type="{{\App\Models\CollectorLog::TYPE_HAND}}">
                        <i class="fa fa-plus-circle fa-lg"></i> @lang('collectings.distribute-orders')
                    </button>
                </div>
            </div>
        </div>
    @endif
    @if ($orders)
        <div class="table-responsive">
            <table id="orders" class="table table-striped table-hover">
                <thead>
                <tr style="    color: #929292;">
                    @if(isset($permissions['assign_orders_by_collectors']))
                        <th class="text-center">
                            <div class="checkbox checkbox-nice col-lg-6"
                                 style="margin-top: 20px">
                                <input type="checkbox"
                                       class='choose_all'
                                       id="choose_all"
                                       name="choose_all">
                                <label for="choose_all" style="padding-left: 0px"></label>
                            </div>
                        </th>
                    @endif
                    <th class="text-center"> @lang('general.id')</th>
                    <th class="text-center"> @lang('general.country')</th>
                    <th class="text-center"> @lang('general.project')/<br> @lang('general.subproject')</th>
                    <th class="text-center"> @lang('general.date')</th>
                    <th class="text-center"> @lang('general.fio')</th>
                    <th class="text-center"> @lang('general.status')/@lang('general.result')</th>
                    <th class="text-center"> @lang('collectings.processing-quantity')</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $order)
                    <tr>
                        @if(isset($permissions['assign_orders_by_collectors']))
                            <td class="text-center">
                                <div class="checkbox-nice checkbox">
                                    {{ Form::checkbox('choose', $order->id, null, ['id' => 'choose_'.$order->id, 'class' => 'choose']) }}
                                    {{ Form::label('choose_'.$order->id, ' ') }}
                                </div>
                            </td>
                        @endif
                        <td class="text-center"><span class="crm_id">{{$order->id}}</span>
                            @if($order->partner_oid)
                                <div class="project_oid">{{$order->partner_oid}}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <img class="country-flag"
                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png')  }}"/>
                        </td>
                        <td style="color: #929292;">
                            <div><b>{{$order->projectName}}</b></div>
                            {{ $order->subProjectName}}
                        </td>
                        <td class="text-center ">
                            <div class="order_phone_block">
                                                    <span class="order_date">
                                                        {{ \Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}
                                                    </span>
                                <div class="project_oid">{{ \Carbon\Carbon::parse($order->time_created)->format('d/m/Y')}}</div>
                            </div>
                        </td>
                        <td style="font-weight: bold; color: #5f5f5f; font-size: 13px">
                            {{ $order->name_last .' '. $order->name_first.' '. $order->name_middle}}<br>
                            {{!empty($order->phone) ? $order->phone : ''}}
                        </td>
                        <td class="text-center">
                            <span class="label label-default">
                                {{ !empty($order->procStatus->key) ? trans('statuses.' . $order->procStatus->key) : $order->procStatus->name}}
                            </span>
                        </td>
                        <td>
                            @php
                                $logs = $order->collectorLogs->merge($order->comments)->sortBy('updated_at');
                            @endphp
                            @if ($logs->isNotEmpty())
                                @foreach($logs as $log)
                                    @if ($log instanceof \App\Models\CollectorLog)
                                        <div>
                                            @if ($log->type == \App\Models\CollectorLog::TYPE_AUTO && !$log->user_id && $log->processed)
                                                [{{$log->updated_at}}]
                                                <span class="label label-danger"> @lang('collector.' . $log->type)</span>
                                                - @lang('general.not-phoned')
                                            @else
                                                [{{$log->updated_at}}]
                                                <span class="label @if($log->type == \App\Models\CollectorLog::TYPE_AUTO) label-success @else label-primary @endif"> @lang('collector.' . $log->type)</span>
                                                - {{$log->user ? $log->user->surname . ' ' . $log->user->name : ''}}
                                            @endif
                                        </div>
                                    @elseif($log instanceof \App\Models\Comment)
                                        <div>
                                            [{{$log->updated_at}}] <span class="label label-default">sms</span>
                                            - {{$log->user ? $log->user->surname . ' ' . $log->user->name : ''}}
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <a href="{{route('order-sending', $order->id)}}" class="table-link custom_badge">
                                <span class="fa-stack">
                                    <i class="fa fa-square fa-stack-2x "></i>
                                    <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="clearfix">
            <div class="pull-right">
                {{$orders->links()}}
            </div>
        </div>
        @if(isset($permissions['assign_orders_by_collectors']))
            <div class="col-lg-12">
                <div class="main-box clearfix" style=" background-color: #90a8af22;">
                    <div class="main-box-body clearfix">
                        <div class="row">
                            <div class="text-center">
                                <div class="checkbox checkbox-nice" style="display: inline-block;">
                                    <input type="checkbox"
                                           class="add_all"
                                           id="add_all"
                                           name="add_all">
                                    <label for="add_all"> @lang('general.all') ({{$countOrder}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-center">
                                <button data-modal="add_orders" class=" md-trigger btn btn-primary">
                                    <i class="fa fa-plus-circle fa-lg"></i> @lang('collectings.add-to-processing')
                                </button>
                                <button class=" btn btn-primary share"
                                        data-type="{{\App\Models\CollectorLog::TYPE_AUTO}}">
                                    <i class="fa fa-plus-circle fa-lg"></i> @lang('collectings.add-to-autocall')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="md-overlay"></div>
    @endif
@endsection
