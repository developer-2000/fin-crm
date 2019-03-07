<html>
<table>
    <thead>
    <tr>
        <th>Продажи за период</th>
        <th>1</th>
    </tr>
    <tr>
        <th>Сформирован</th>
        <th>{{now()}}</th>
    </tr>
    <tr></tr>
    </thead>
    @if(!isset($filters['product']))
        <thead>
        <tr>
            <th bgcolor="f3f5f6">ID товара</th>
            <th bgcolor="f3f5f6">Наименование товара</th>
            <th bgcolor="f3f5f6">Количество, товаров, шт</th>
            <th bgcolor="f3f5f6">Всего{{$filters['status'] ? $statuses[$filters['status']]->name : 'Хороший клиент'}}
                ,шт
            </th>
            <th bgcolor="f3f5f6">Всего approve, шт</th>
            <th bgcolor="f3f5f6">% выкупа</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{$product->id}}</td>
                <td>{{$product->title}}</td>
                <td>{{!empty($product->paidUpProductsCount) ? $product->paidUpProductsCount : ''}}</td>
                <td>{{!empty($product->paidUpOrdersCount) ? $product->paidUpOrdersCount : 0 }}</td>
                <td>{{!empty($product->approveOrdersCount) ? $product->approveOrdersCount : 0 }}</td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    @endif
    @if(isset($filters['product']))
        <thead>
        <tr>
            <th bgcolor="f3f5f6">ID товара</th>
            <th bgcolor="f3f5f6">Наименование товара</th>
            <th bgcolor="f3f5f6">Количество, товаров, шт</th>
            <th bgcolor="f3f5f6">Всего{{$filters['status'] ? $statuses[$filters['status']]['name'] : 'Хороший клиент'}}
                ,шт
            </th>
            <th bgcolor="f3f5f6">Всего approve, шт</th>
            <th bgcolor="f3f5f6">% выкупа</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{$product->id}}</td>
                <td>{{$product->title}}</td>
                <td>{{!empty($product->paidUpProductsCount) ? $product->paidUpProductsCount : ''}}</td>
                <td>{{!empty($product->paidUpOrdersCount) ? $product->paidUpOrdersCount : 0 }}</td>
                <td>{{!empty($product->approveOrdersCount) ? $product->approveOrdersCount : 0 }}</td>
                <td></td>
            </tr>
            <!--With Orders-->
            @if(!empty($ordersForProducts))
                @foreach($ordersForProducts as $order)
                    <tr>
                        <td>{{ $order->id}}</td>
                        <td>
                            {{$order->name_last. ' ' . $order->name_first . '.'}}
                            Сумма
                            заказа: {{$order->price_total. ' ' .  $products[0]->currency}}
                        </td>
                        <td>{{$order->order_products_count ? $order->order_products_count : ''}}</td>
                        <td></td>
                    </tr>
                @endforeach
            @endif
        @endforeach
        </tbody>
    @endif
</table>
</html>
