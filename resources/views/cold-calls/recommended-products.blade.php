@if ($recommended_products)
    @foreach($recommended_products as $type)
        <table class="table product_offer">
            <thead>
            <tr>
                <th class="text-left">
                    @if ($type[0]->type == 1)
                        @lang('general.up-sell') 1
                    @elseif($type[0]->type == 2)
                        @lang('general.up-sell') 2
                    @elseif($type[0]->type == 4)
                        @lang('general.cross-sell')
                    @elseif($type[0]->type == 0)
                        @lang('general.product')
                    @endif
                </th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($type as $product)
                <tr>
                    <td class="value">{{$product->name}}</td>
                    <td class="text-right ">
                        <input type="text" style="width: 60%; display: inline-block;"
                               class="form-control price_offer_add" data-value="{{$product->price}}"
                               value="{{$product->price}}" placeholder="Цена">
                        <span class="offer_currency">
                                                    {{$orderOne->currency}}
                                                </span>
                    </td>
                    <td class="text-right">
                        <a href="#" class="table-link">
                                                <span class="fa-stack add_product" data-id="{{$product->product_id}}">
                                                    <i class="fa  fa-plus-square fa-stack-2x"></i>
                                                </span>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
@endif