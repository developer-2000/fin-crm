@extends('collectings.layout')
@section('contentdata')
    @if(isset($permissions['assign_orders_by_collectors']))
        <div class="md-modal md-effect-6" id="add_orders">
            <div class="md-content">
                <div class="modal-header">
                    <button class="md-close close">×</button>
                    <h4 class="modal-title"> @lang('collectings.assign-orders'):</h4>
                    {{-- Назначить заказы коллекторам --}}
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-7 form-horizontal">
                            <label for="collectors" class="col-sm-4 control-label"> @lang('collectings.collectors')</label>
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
    @if ($logs)
        <div class="table-responsive">
            <table id="orders" class="table table-striped table-hover">
                <thead>
                <tr>
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
                    <th class="text-center"> @lang('general.date')</th>
                    <th class="text-center"> @lang('general.FIO')</th>
                    <th class="text-center"> @lang('general.phone')</th>
                    <th class="text-center"> @lang('general.collector')</th>
                    <th class="text-center"> @lang('general.type')</th>
                    <th class="text-center"> @lang('general.status')</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($logs as $log)
                    <tr>
                        @if(isset($permissions['assign_orders_by_collectors']))
                            <td class="text-center">
                                <div class="checkbox-nice checkbox">
                                    {{ Form::checkbox('choose', $log->order_id, null, ['id' => 'choose_'.$log->id, 'class' => 'choose']) }}
                                    {{ Form::label('choose_'.$log->id, ' ') }}
                                </div>
                            </td>
                        @endif
                        <td class="text-center">
                                                <span class="crm_id">
                                                    {{$log->order_id}}
                                                </span>
                            @if($log->order)
                                <div class="project_oid">{{$log->order->partner_oid}}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($log->order)
                            <img class="country-flag"
                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($log->order->geo) . '.png')  }}"/>
                            @endif
                        </td>
                        <td class="text-center ">
                            @if($log->order)
                            <div class="order_phone_block">
                                                    <span class="order_date">
                                                        {{\Carbon\Carbon::parse($log->order->time_created)->format('H:i:s')}}
                                                    </span>
                                <div class="project_oid">{{\Carbon\Carbon::parse($log->order->time_created)->format('d/m/Y')}}</div>
                            </div>
                            @endif
                        </td>
                        <td >
                            @if ($log->order)
                            {{$log->order->name_last}} {{$log->order->name_first}} {{$log->order->name_middle}}
                            @endif
                        </td>
                        <td class="text-center"
                            style="font-weight: bold; color: #5f5f5f; font-size: 13px">
                            {{!empty($log->order->phone) ? $log->order->phone : ''}}
                        </td>
                        <td>
                            @if(isset($log->user))
                                {{$log->user->surname}} {{$log->user->name}}
                            @elseif ($log->type == \App\Models\CollectorLog::TYPE_AUTO)
                                <span class="label label-danger"> @lang('general.not-phoned')</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @lang('collector.' . $log->type)
                        </td>
                            <td class="text-center">
                            <span class="label label-default" @if (!empty($log->order->procStatus)) style="background-color: {{$log->order->procStatus->color}};" @endif>{{$log->order->procStatus->name ?? ''}}  </span>
                        </td>
                        <td>
                            <a href="{{route('order-sending', $log->order_id)}}"
                               class="table-link custom_badge">
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
                {{$logs->links()}}
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
                                <button class=" btn btn-primary share" data-type="{{\App\Models\CollectorLog::TYPE_AUTO}}">
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
