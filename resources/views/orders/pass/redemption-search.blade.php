    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th> @lang('general.id')</th>
                <th></th>
                <th> @lang('general.delivery')</th>
                <th class="text-center"> @lang('general.status')</th>
                <th class="text-center"> @lang('general.subproject')</th>
                <th class="text-center"> @lang('general.phone')</th>
                <th class="text-center"> @lang('general.track')</th>
                <th class="text-center"> @lang('general.total')</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @if (count($searchOrders))
                @foreach($searchOrders as $searchOrder)
                    <tr>
                        <td>
                            <a href="{{route('order-sending', $searchOrder->id)}}">{{$searchOrder->id}} ({{$searchOrder->name_last}} {{$searchOrder->name_first}} {{$searchOrder->name_middle}})</a>
                        </td>
                        <td>
                            <img class="country-flag"
                                 src="{{ URL::asset('img/flags/' . mb_strtoupper($searchOrder->geo) . '.png') }}" />
                        </td>
                        <td style="padding: 0;">
                            @if (isset($targets[$searchOrder->id]['name']))
                                @foreach($targets[$searchOrder->id] as $key => $field)
                                    @if ($key != 'name')
                                        <div class="clearfix">
                                            <div style="padding: 0; width: 40%" class="pull-left">{{$field['title']}}: </div>
                                            <div style="padding: 0; width: 60%" class="pull-left">
                                                @forelse($field['value'] as $k => $value)
                                                    {{$value}}
                                                    @if (count($field['value']) != $k + 1)
                                                        ,
                                                    @endif
                                                @empty
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($searchOrder->procStatus)
                                <span class="label label-default" style="background-color: {{$searchOrder->procStatus->color}};">{{$searchOrder->procStatus->name}}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{$searchOrder->subProject ? $searchOrder->subProject->name : ''}}
                        </td>
                        <td class="text-center">
                            {{$searchOrder->phone}}
                        </td>
                        <td class="text-center">
                            {{$searchOrder->getTargetValue ? $searchOrder->getTargetValue->track : ''}}
                        </td>
                        <td class="text-center">{{$searchOrder->price_total}} {{$searchOrder->country ? $searchOrder->country->currency : ''}}</td>
                        <td>
                            <a href="" class="table-link">
                                                                <span class="fa-stack add_order"
                                                                      data-id="{{$searchOrder->id}}">
                                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                                    <i class="fa fa-plus-square fa-stack-1x fa-inverse"></i>
                                                                </span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center" id="not_found">
                        @lang('general.order-not-found')
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>