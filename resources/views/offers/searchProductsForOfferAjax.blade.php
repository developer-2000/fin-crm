@if ($products)
    @foreach($products as $product)
        <option value="{{$product->id}}">{{$product->title}}</option>
    @endforeach
@endif