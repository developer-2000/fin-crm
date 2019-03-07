@if ($product)
    <tr style="background-color: rgba(139,251,60,0.25)" data-id="{{ $product->id }}">
        <td class="value">
            {{ $product->title }}
        </td>
        <td class="text-center">
            @if ($product->storageAmount > 10)
                <img src="{{ URL::asset('img/stock_1.png') }}" alt="Есть на складе">
            @elseif($product->storageAmount > 0)
                <img src="{{ URL::asset('img/stock_2.png') }}" alt="Заканчивается">
            @else
                <img src="{{ URL::asset('img/stock_3.png') }}" alt="Нет на складе">
            @endif
        </td>
        <td class="comments">
            <a href="#" data-pk="{{$product->id}}" data-title="Введите примечание"
               class="product_comments"></a>
        </td>
        <td class="text-center">
            <input type="text"
                   style="width: 90px; display: inline-block;"
                   class="form-control price_product_locked"
                   data-value="{{ $product->price }}"
                   value="{{ $product->price }}"
                   placeholder="Цена"
                   name="products_new[{{$product->id}}][price]"
            >
        </td>
        <td class="text-right">
            <a href="#" data-id="{{ $product->id }}"
               class="table-link danger delete_product_locked">
                                                        <span class="fa-stack " data-id="{{ $product->id }}">
                                                            <i class="fa fa-square fa-stack-2x"></i>
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
            </a>
        </td>
    </tr>
@endif