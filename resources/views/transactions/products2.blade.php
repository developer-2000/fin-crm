{!! Form::label('product_id', trans('general.product'), ['class' => 'storage-label']) !!}

{!! Form::text('product_id', null, [
    'id' => 'product_id',
    'data-url' => route('transaction-get-products-list'),
    'data-url_2' => route('transaction-get-product'),
    'placeholder' => '--' . trans('general.product') . '--',
    'style' => 'width:100%;'
]) !!}
