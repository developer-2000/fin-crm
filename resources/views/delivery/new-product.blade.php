<div class="row product_papa" style="padding-top:12px;" data-product="{{ $product->id }}" id="product_papa_{{$count_product}}" data-count="{{$count_product}}"> <div class="numbo_product">{{$count_product}}</div>

<div class="flex_product">

            {{-- товар --}}
            <div class="col-xs-4">
                <table> <tr> <td colspan="4"><label class="storage-label">Выбраный товар</label></td> </tr>
                    <tr>
                        <td><span class="input-group-addon for_id" id="select_product_{{$count_product}}" data-value="{{ $product->id }}"> id: {{ $product->id }} </span></td>
                        <td><span class="input-group-addon for_title"> {{ $product->title }} </span></td>
                        <td><input id="count_product_{{$count_product}}" class="form-control numbo_inut" value="0" type="number"></td>
                        <td>

                            <button class="btn btn-primary product_minus" data-id="{{$count_product}}">
                                <i class="fa fa-trash"></i>
                            </button>

                        </td>
                    </tr>
                </table>
            </div>
            {{-- / товар --}}

            {{-- опции  --}}
            <div class="col-xs-2 col-xs-offset-1">

                <div class="block_color">
                    <?php foreach ($color_array as $key => $value){ ?>
                    <div class="one_color">
                    <div class="color">{{$value}}</div>
                    <div class="add_color btn btn-primary" id="plus_{{$key}}" data-color="{{$key}}"><i class="fa fa-plus" data-product="{{$count_product}}" data-color="{{$key}}"></i></div>
                    </div>
                    <?php } ?>
                </div>

            </div>
            {{-- / опции  --}}


            {{-- textarea --}}
            <div class="col-xs-4 col-xs-offset-1 block_text">
                <textarea name="comment" class="form-control" id="comment_{{$count_product}}" placeholder="коментарий к товару"></textarea>
            </div>

</div>
</div>















