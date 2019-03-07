@if ($pass->ordersPass->isNotEmpty())
    @php
        $allTotal = 0;
        $allProduct = 0;
        $sumActual = 0;
        $cost = 0;
        $totalIncome = 0;
        $n =0;
    @endphp
    @foreach($pass->ordersPass as $orderPass)
        @php
            $order = $orderPass->order;
        @endphp
        @if ($order)
            <tr>
                <td class="text-center" style="color: #676767;
            font-weight: bold">
                    {{$n +=1}}
                </td>
                <td>
                    <a href="{{route('order-sending', $order->id)}}">{{$order->id}} ({{$order->name_last}} {{$order->name_first}} {{$order->name_middle}})</a>
                </td>
                <td>
                    <img class="country-flag"
                         src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}" />
                </td>
                <td class="text-center">
                    @if ($order->procStatus)
                        <span class="label label-default" style="background-color: {{$order->procStatus->color}};">{{$order->procStatus->name}}</span>
                    @endif
                </td>
                <td class="text-center">
                    {{$order->subProject ? $order->subProject->name : ''}}
                </td>
                <td class="text-center">
                    {{$order->phone}}
                </td>
                <td class="text-center">{{$order->price_total}} {{$order->country ? $order->country->currency : ''}}</td>
                <td class="text-center">{{$order->price_products}} {{$order->country ? $order->country->currency : ''}}</td>
                <td class="text-center">
                    @php
                        $json = json_decode($order->getTargetValue->values ?? '', true);
                        $min = $json['cost_actual']['field_settings']['range_min'] ?? null;
                        $max = $json['cost_actual']['field_settings']['range_max'] ?? null;
                    @endphp
                    {{($json['cost']['field_value'] ?? '-')}}
                </td>
                <td class="text-center">
                    @php
                        $income = $order->price_total + (float)($json['cost']['field_value'] ?? 0);
                    @endphp
                    {{$income}}
                </td>
                <td class="text-center">
                    @php
                        $costActual = $orderPass->cost_actual > 0 ? (float)$orderPass->cost_actual : '';
                        if (!$costActual && $order->getTargetValue && $order->getTargetValue->cost_actual) {
                            $costActual = (float)$order->getTargetValue->cost_actual;
                        }
                    @endphp
                    @if ($pass->active)
                        <input value="{{$costActual}}"
                               class="form-control change_input"
                               name="orders[{{$order->id}}][cost_actual]"
                               data-type="cost_actual"
                               data-id="{{$order->id}}"
                               data-pass="{{$pass->id}}"
                               type="number"
                               @if ($min) min="{{$min}}" @endif @if ($max) max="{{$max}}" @endif>
                    @else
                        {{$costActual}}
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $track = $orderPass->track ? $orderPass->track : '';
                        if (!$track && $order->getTargetValue && $order->getTargetValue->track) {
                            $track = $order->getTargetValue->track;
                        }
                    @endphp
                    @if ($pass->active)
                        <input value="{{$track}}"
                               class="form-control change_input"
                               name="orders[{{$order->id}}][track]"
                               data-type="track"
                               data-id="{{$order->id}}"
                               data-pass="{{$pass->id}}">
                    @else
                        {{$track}}
                    @endif
                </td>
                <td>
                    @if ($pass->active)
                    <a href="#" class="table-link danger delete_order_send pull-right"
                       data-title=" @lang('general.delete')"
                       data-pk="{{$order->id}}"
                       data-name="delete"
                       style="border-bottom: 0;">
                                                                        <span class="fa-stack ">
                                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                        </span>
                    </a>
                    @endif
                </td>
            </tr>
            @php
                $totalIncome += $income;
                $cost += (float)($json['cost']['field_value'] ?? 0);
                $allTotal += $order->price_total;
                $allProduct += $order->price_products;
                $sumActual += $costActual;
            @endphp
        @endif
    @endforeach
    <tr>
        <td class="text-center" style="color: #676767;
            font-weight: bold">
            @lang('general.total')
        </td>
        <td colspan="5"></td>
        <td class="text-center number-total-row">{{$allTotal}}</td>
        <td class="text-center number-total-row">{{$allProduct}}</td>
        <td class="text-center number-total-row">{{$cost}}</td>
        <td class="text-center number-total-row">{{$totalIncome}}</td>
        <td id="sum_actual" class="text-center number-total-row">{{$sumActual}}</td>
        <td ></td>
        <td ></td>
    </tr>
@else
    <tr>
        <td class="text-center" colspan="13">
            @lang('general.order-not-found')
        </td>
    </tr>
@endif
