@if ($offers)
    @foreach ($offers as $offer)
        <tr @if ($offer->disabled) class="warning"
            @endif data-id="{{ $offer->ooid }}">
            <td class="value">
                {{ $offer->title }}
                @if (!$offer->disabled && !$order->moderation_id)
                    <br>
                    <a href="#" data-pk="{{$offer->ooid}}"
                       data-title=" @lang('general.select-offer')"
                       data-type="select2"
                       data-product="{{$offer->id}}"
                       data-emptytext=" @lang('orders.select-option')"
                       data-placeholder=" @lang('orders.select-option')"
                       style="font-weight: 300;"
                       class="product_option">{{$offer->option}}</a>
                @endif
            </td>
            <td class="text-center">
                @if ($offer->storageAmount > 10)
                    <img src="{{ URL::asset('img/stock_1.png') }}"
                         alt=" @lang('products.in-stock')">
                @elseif($offer->storageAmount > 0)
                    <img src="{{ URL::asset('img/stock_2.png') }}"
                         alt=" @lang('general.end')">
                @else
                    <img src="{{ URL::asset('img/stock_3.png') }}"
                         alt=" @lang('orders.not-in-stock')">
                @endif
            </td>

            @if ($order->moderation_id)

                <td class="comments">
                    {{$offer->comment ?? '-'}}
                    @if ($offer->option)
                        {{$offer->option}}
                    @endif
                </td>

            @else

                @if ($offer->type == 1 || $offer->type == 2 || $offer->type == 3 || $offer->type == 4 || $offer->type == 5)
                    <td class="text-center">
                        @if($offer->productType == 'upsell_1' || $offer->productType == 0)
                            <div class="checkbox-nice">
                                <input type="checkbox" id="up_sell_{{ $offer->ooid }}"
                                       class="up_cross_sell" value="1"
                                       name="products[{{$offer->ooid}}][up1]"
                                       @if ($offer->type == 1)
                                       checked
                                        @endif
                                >
                                <label for="up_sell_{{ $offer->ooid }}"></label>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($offer->productType == 'upsell_2' || $offer->productType == 0)
                            <div class="checkbox-nice">
                                <input type="checkbox" id="up_sell_2{{ $offer->ooid }}"
                                       class="up_cross_sell" value="2"
                                       name="products[{{$offer->ooid}}][up2]"
                                       @if ($offer->type == 2)
                                       checked
                                        @endif
                                >
                                <label for="up_sell_2{{ $offer->ooid }}"></label>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($offer->productType == 'cross' || $offer->productType == 0)
                            <div class="checkbox-nice">
                                <input type="checkbox"
                                       name="products[{{$offer->ooid}}][cross]"
                                       id="cross_sell_{{ $offer->ooid }}"
                                       class="up_cross_sell" value="4"
                                       @if ($offer->type == 4)
                                       checked
                                        @endif
                                >
                                <label for="cross_sell_{{ $offer->ooid }}"></label>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($offer->productType == 'cross_2' || $offer->productType == 0)
                            <div class="checkbox-nice">
                                <input type="checkbox"
                                       name="products[{{$offer->ooid}}][cross2]"
                                       id="cross_sell_2_{{ $offer->ooid }}"
                                       class="up_cross_sell" value="5"
                                       @if ($offer->type == 5)
                                       checked
                                        @endif
                                >
                                <label for="cross_sell_2_{{ $offer->ooid }}"></label>
                            </div>
                        @endif
                    </td>
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
                    <td class="comments">
                        @if (!$offer->disabled)
                            <a href="#" data-pk="{{$offer->ooid}}"
                               data-title=" @lang('general.select')"
                               data-emptytext=" @lang('general.add')"
                               class="product_comments">{{$offer->comment}}</a>
                        @endif
                    </td>

            @endif
            <td class="text-center">
                <input type="hidden" name="products[{{$offer->ooid}}][id]" value="{{$offer->ooid}}">
                <input type="hidden" name="products[{{$offer->ooid}}][disabled]" value="{{$offer->disabled}}">
                @if ($offer->disabled)
                    {{ $offer->price }}
                @else
                    <input type="text"
                           style="width: 70px; display: inline-block;"
                           class="form-control price_offer"
                           data-value="{{ $offer->price }}"
                           value="{{ $offer->price }}"
                           placeholder=" @lang('general.price')"
                           name="products[{{$offer->ooid}}][price]"
                    >
                @endif
            </td>
            <td class="text-right">
                @if (!$offer->disabled)
                    <a href="#" class="table-link danger delete_product">
                                                        <span class="fa-stack " data-id="{{ $offer->ooid }}">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                    </a>
                @endif
            </td>
        </tr>
    @endforeach
    <tr>
        <td class="value text-center"> @lang('general.total')</td>

        @if (!$order->moderation_id)
            <td></td>
            <td></td>
            <td></td>
        @endif
        <td></td>
        <td></td>
        <td></td>
        <td class="text-center" id="total_price">{{$price}}</td>
        <td class="text-center">
            {{$currency}}
        </td>
    </tr>
@endif
