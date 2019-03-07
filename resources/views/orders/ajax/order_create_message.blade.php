{{trans('alerts.order-created-successfully')}}
<a href="{{ route('order-sending', $order->id)}}">
    @lang('orders.order-page')
</a>
{{strtolower(trans('general.or'))}}
<a href="{{route('order-create')}}">
    @lang('orders.order-create')
</a>
