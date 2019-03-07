@if ($data)
    <table class="table">
        <thead>
        <tr>
            <th class="text-center"> @lang('general.id')</th>
            <th class="text-center"> @lang('finance.type-transaction')</th>
            <th class="text-center"> @lang('general.date')</th>
            <th class="text-center"> @lang('general.order') @lang('general.id')</th>
            <th class="text-center"> @lang('general.operator')</th>
            <th class="text-center"> @lang('general.country')</th>
            <th class="text-center"> @lang('finance.text-log')</th>
            <th class="text-center"> @lang('general.sum'), грн</th>
            <th class="text-center"> @lang('finance.plan-completed')/@lang('finance.plan-not-completed')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $planLog)
            <tr>
                <td class="text-center">{{!empty($planLog->transaction_id)? $planLog->transaction_id : 'N/A' }}</td>
                <td class="text-center">
                    @if($planLog->type == 'success' && intval($planLog->result) !==0)
                        <span class="label label-primary"> @lang('finance.bonus')</span>
                    @elseif($planLog->type == 'success' && intval($planLog->result) ==0
                    ||$planLog->type == 'failed' && intval($planLog->result) ==0)
                        <span class="label label-default">N/A</span>
                    @elseif($planLog->type == 'failed' && intval($planLog->result) !==0)
                        <span class="label label-warning"> @lang('finance.retention')</span>
                    @endif
                </td>
                <td>{{$planLog->created_at->format('Y-m-d')}}</td>
                <td>
                    @if(!empty($planLog->order_id))
                        <a href="{{route('order', $planLog->order_id)}}">{{ $planLog->order_id}}</a>
                    @else
                        {{'N/A'}}
                    @endif
                </td>
                <td class="text-center">{{ !empty($planLog['operator']) ? $planLog['operator']->surname.' '. $planLog['operator']->name : 'N/A'}}</td>
                <td class="text-center">{{$planLog['company']->name}}</td>
                <td class="text-center">{{$planLog->text}}</td>
                <td class="text-center">
                    @if($planLog->type == 'success' && $planLog->result !==0 ||
                    $planLog->type == 'failed' && $planLog->result !==0)
                        {{$planLog->result}}
                    @else
                        {{'N/A'}}
                    @endif
                </td>
                <td class="text-center">@if($planLog->type == 'success')
                        <span class="label label-success"> @lang('general.success')</span>
                    @else
                        <span class="label label-danger"> @lang('general.failed')</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
