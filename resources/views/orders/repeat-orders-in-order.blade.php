<table id="orders" class="table">
    <thead>
    <tr>
        <th class="text-center"> @lang('general.id')</th>
        <th class="text-center"> @lang('general.country')</th>
        <th class="text-center"> @lang('general.date')</th>
        <th class="text-center"> @lang('general.offer')</th>
        <th class="text-center"> @lang('general.product')</th>
        <th class="text-center"> @lang('general.phone')</th>
        <th class="text-center"> @lang('general.sum')</th>
        <th colspan="5"></th>
    </tr>
    </thead>
    <tbody>
    @if ($orders->isNotEmpty())
        @foreach ($orders as $order)
            <tr class="active">
                <td class="text-center">
                    <span class="crm_id">
                        {{$order->id}}
                    </span>
                    <div class="project_oid">{{$order->partner_oid}}</div>
                </td>
                <td class="text-center">
                    <img class="country-flag"
                         src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}"/>
                </td>
                <td class="text-center ">
                    <div class="order_phone_block">
                        <a href="#" class="pop">
                            <span class="order_date">
                                {{\Carbon\Carbon::parse($order->time_created)->format('H:i:s')}}
                            </span>
                            <div class="project_oid">
                                {{\Carbon\Carbon::parse($order->time_created)->format('d/m/Y')}}
                            </div>
                        </a>
                        <div class="data_popup">
                            <div class="arrow"></div>
                            <h3 class="title"> @lang('general.time')</h3>
                            <div class="content">
                                {{\Carbon\Carbon::parse($order->time_created)->format('H:i:s d/m/y')}}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <div class="order_phone_block">
                        <a href="#" class="pop">
                            @if ($order->offer)
                                <div class="offer_name">{{$order->project ? $order->project->alias : ''}}
                                    - {{$order->offer_id}}</div>
                            @endif
                        </a>
                        <div class="data_popup">
                            <div class="arrow"></div>
                            <h3 class="title"> @lang('general.offer')</h3>
                            <p class="content">
                                @if ($order->offer)
                                    {{$order->offer->name}}
                                @endif
                            </p>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <div class="order_phone_block">
                        <a href="#" class="pop">
                            <span class="badge badge-danger">
                                {{count($order->products)}}
                            </span>
                        </a>
                        <div class="data_popup">
                            <div class="arrow"></div>
                            <h3 class="title"> @lang('general.product')</h3>
                            <div class="content">
                                @if ($order->products)
                                    @foreach($order->products as $product)
                                        {{$product->name}}
                                        @if($product->type == 1)
                                            Up Sell
                                        @elseif($product->type ==  2)
                                            Up Sell 2
                                        @elseif($product->type == 4)
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
                        <a href="#" class="pop">
                            <span class="order_phone">
                                <i class="fa fa-mobile-phone @if ($order->proc_status == 6) phone_error @endif"></i>
                            </span>
                        </a>
                        <div class="data_popup">
                            <div class="arrow"></div>
                            <h3 class="title"> @lang('general.phone')</h3>
                            <div class="content">{{$order->phone}}</div>
                        </div>
                    </div>
                </td>
                <td class="text-center price_order">
                    {{$order->price_total}} {{$order->country ? $order->country->currency : ''}}
                    <div class=" border_proc @if ($order->target_status) border @endif"></div>
                </td>
                @if ($order->procStatus && $order->procStatus->type == \App\Models\ProcStatus::TYPE_SENDERS)
                    <td class="text-center" colspan="4">
                        <div class="proc_label">
                            @lang('general.status')
                        </div>
                        <span class="label label-default"
                              style="background-color: {{$order->procStatus->color}};"> {{$order->procStatus->name}}</span>
                    </td>
                    <td>
                        <a href="{{ route('order', $order->id) }}/" class="table-link custom_badge">
                            <span class="fa-stack">
                                <i class="fa fa-square fa-stack-2x "></i>
                                <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                    </td>
                @elseif (!$order->target_status)
                    <td class="text-center" style="font-size: 13px;">
                        {{$order->campaign ? $order->campaign->name : ''}}
                    </td>
                    <td class="text-center">
                        <div class="proc_label">
                            @lang('general.status')
                        </div>
                        <?
                        $status = '';
                        $class = '';
                        switch ($order->proc_status) {
                            case 1:
                                $status = trans('statuses.in-processing');
                                $class = 'label-default';
                                break;
                            case 2:
                                {
                                    $status = trans('statuses.dialing');
                                    $class = 'label-success';
                                    break;
                                }
                            case 3:
                                {
                                    $status = trans('statuses.contact');
                                    $class = 'label-primary';
                                    break;
                                }
                            case 4:
                                {
                                    $status = trans('statuses.repeat');
                                    $class = 'label-warning';
                                    break;
                                }
                            case 5:
                                {
                                    $status = trans('statuses.under-call');
                                    $class = 'label-danger';
                                    break;
                                }
                            case 6:
                                {
                                    $status = trans('statuses.invalid-phone');
                                    $class = 'label-danger';
                                    break;
                                }
                            case 7:
                                {
                                    $status = trans('statuses.other-language');
                                    $class = 'label-info';
                                    break;
                                }
                            case 8:
                                {
                                    $status = trans('statuses.error');
                                    $class = 'label-danger';
                                    break;
                                }
                            case 9:
                                {
                                    $status = trans('statuses.completed');
                                    $class = 'label-default';
                                    break;
                                }
                            case 13:
                                {
                                    $status = trans('statuses.fail');
                                    $class = 'label-danger';
                                    break;
                                }
                            default :
                                {
                                    $class = 'label-default';
                                    break;
                                }
                        }
                        ?>
                        <span class="label {{$class}}"
                              @if (!$status && $order->procStatus) style="background-color: {{$order->procStatus->color}};">
                            {{$order->procStatus->name}} @else >{{$status}} @endif
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
                                @lang('general.callback')
                            </div>
                            <a href="#" class="pop">
                                <span class="order_date @if (!$order->proc_callback_time) proc_time @endif">
                                    {{ \Carbon\Carbon::parse($order->proc_time)->format('d/m/Y')}}
                                </span>
                            </a>
                            <div class="data_popup">
                                <div class="arrow"></div>
                                <h3 class="title"> @lang('general.callback')</h3>
                                <div class="content">{{ \Carbon\Carbon::parse($order->proc_time)->format('H:i:s d/m/y')}}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('order', $order->id) }}/" class="table-link custom_badge">
                            <span class="fa-stack">
                                <i class="fa fa-square fa-stack-2x "></i>
                                <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
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
                        {{$order->operName}} {{$order->operSurname}}
                    </td>
                    <td class="text-center {{$classRow }}">
                        <div class="proc_label">
                            @lang('general.target')
                        </div>
                        <span class="badge {{$classLabel}}">{{$target}}</span>
                    </td>
                    <td class="text-center {{$classRow }}" colspan="2">
                        @if ($order->target_status != 1 && isset($targets[$order->id]))
                            @foreach($targets[$order->id] as $key => $cause)
                                @if ($key != 'name')
                                    <div class="proc_label">
                                        {{$cause['title']}} :
                                        <span class="order_comment">
                                            @if ($cause['value'])
                                                @foreach($cause['value'] as $k => $value)
                                                    {{$value}}
                                                    @if (count($cause['value']) != $k + 1)
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
                        <a href="{{ route('order', $order->id) }}/" class="table-link {{$classBtn}}">
                            <span class="fa-stack">
                                <i class="fa fa-square fa-stack-2x "></i>
                                <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                    </td>
                @endif
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
