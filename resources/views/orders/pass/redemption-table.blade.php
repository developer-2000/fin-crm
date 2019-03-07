@if ($pass->ordersPass->isNotEmpty())
    @php
        $allTotal = 0;
        $cost = 0;
        $actaul = 0;
        $incomeTotal = 0;
    @endphp
    @foreach($pass->ordersPass as $orderPass)
        @php
            $order = $orderPass->order;
        @endphp
        @if ($order)
            <tr>
                <td>
                    <a href="{{route('order-sending', $order->id)}}">{{$order->id}} ({{$order->name_last}} {{$order->name_first}} {{$order->name_middle}})</a>
                </td>
                <td>
                    <img class="country-flag"
                         src="{{ URL::asset('img/flags/' . mb_strtoupper($order->geo) . '.png') }}" />
                </td>
                <td class="text-center">
                    @if ($order->procStatus)
                        <span class="label label-default" style="background-color: {{$order->procStatus->color}};">{{$order->procStatus->name}}</span><br>
                    @endif
                        {{$order->subProject ? $order->subProject->name : ''}}
                </td>
                <td class="text-center">
                    {{$order->phone}}
                </td>
                <td class="text-center">{{$order->getTargetValue ? $order->getTargetValue->track : ''}}
                </td>
                <td class="text-center">{{$order->price_total}} {{$order->country ? $order->country->currency : ''}}</td>
                <td class="text-center">
                    @php
                        $json = json_decode($order->getTargetValue->values ?? '', true);
                    @endphp
                    {{($json['cost']['field_value'] ?? '-')}}
                </td>
                <td class="text-center">
                    {{$order->getTargetValue ? $order->getTargetValue->cost_actual : ''}}
                </td>
                <td class="text-center">
                    @php
                        $income = $order->price_total + (float)($json['cost']['field_value'] ?? 0);
                    @endphp
                    {{$income}}
                </td>
                <td>
                    @if ($pass->active)
                    <a href="#" class="table-link danger delete_rank pull-right"
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
                $allTotal += $order->price_total;
                $cost += (float)($json['cost']['field_value'] ?? 0);
                $actaul += $order->getTargetValue->cost_actual ?? 0;
                $incomeTotal += $income;
            @endphp
        @endif
    @endforeach
    <tr>
        <td class="text-center">{{$pass->ordersPass->count()}}</td>
        <td colspan="4"></td>
        <td class="text-center">{{$allTotal}}</td>
        <td class="text-center">{{$cost}}</td>
        <td class="text-center">{{$actaul}}</td>
        <td class="text-center">{{$incomeTotal}}</td>
        <td ></td>
    </tr>
@else
    <tr>
        <td class="text-center" colspan="10">
            @lang('general.order-not-found')
        </td>
    </tr>
@endif
