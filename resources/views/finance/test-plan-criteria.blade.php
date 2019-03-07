<table class="table">
    @if ($planCriteria)
    <thead>
    <tr>
        <th> @lang('general.company')</th>
        <th> @lang('finance.group-operators')</th>
        <th> @lang('general.operator')</th>
        <th> @lang('general.country')</th>
        <th> @lang('general.offer')</th>
        <th> @lang('general.product')</th>
        <th> @lang('general.type-product')</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{$planCriteria['company']}}</td>
        {{--<td>{{$planCriteria['operator-group']}}</td>--}}
        {{--<td>{{$planCriteria['operator']}}  {{$planCriteria['operator']}}</td>--}}
        {{--<td>{{$planCriteria['country']}}</td>--}}
        {{--<td>{{$planCriteria['offer']}}</td>--}}
        {{--<td>{{$planCriteria['product']}}</td>--}}
        {{--@if ($planCriteria['product-type'] == 1)--}}
        {{--<td colspan="4"><b>Up sell</b></td>--}}
        {{--@elseif($planCriteria['product-type'] == 2)--}}
        {{--<td colspan="4"><b>Up sell 2</b></td>--}}
        {{--@else--}}
        {{--<td colspan="4"><b>Cross sell</b></td>--}}
        {{--@endif--}}
        <td>
            <a href="#" class="table-link danger delete_criteria">
                                    <span class="fa-stack " data-id="">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                    </span>
            </a>
        </td>
    </tr>
    @endif
    </tbody>
</table>
