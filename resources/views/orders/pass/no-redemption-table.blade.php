@if ($pass->ordersPass->isNotEmpty())
    @php
        $allTotal = 0;
        $returns = 0;
        $cost = 0;
        $actaul = 0;
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
                        <span class="label label-default" style="background-color: {{$order->procStatus->color}};">{{$order->procStatus->name}}</span>
                    @endif
                </td>
                <td class="text-center">
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
                    @if ($pass->active)
                    <input type="text"
                           class="form-control change_input"
                           name="cost_return[{{$order->id}}]"
                           value="{{(float)$orderPass->cost_return}}"
                           data-type="cost_return"
                           data-id="{{$order->id}}"
                           data-pass="{{$pass->id}}"
                    >
                    @else
                        {{$orderPass->cost_return > 0 ? (float)$orderPass->cost_return : ''}}
                    @endif
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
                $returns += $orderPass->cost_return;
                $cost += (float)($json['cost']['field_value'] ?? 0);
                $actaul += $order->getTargetValue->cost_actual ?? 0;
            @endphp
        @endif
    @endforeach
    <tr>
        <td class="text-center">{{$pass->ordersPass->count()}}</td>
        <td colspan="5"></td>
        <td class="text-center">{{$allTotal}}</td>
        <td class="text-center">{{$cost}}</td>
        <td class="text-center">{{$actaul}}</td>
        <td class="text-center" >
            @if ($pass->active)
            <input type="text"
                   class="form-control"
                   name="cost_return_all"
                   id="cost_return_all"
                   value="{{$returns}}"
            >
            @else
                {{$returns}}
            @endif
        </td>
        <td ></td>
    </tr>
@else
    <tr>
        <td class="text-center" colspan="11">
            @lang('general.order-not-found')
        </td>
    </tr>
@endif
