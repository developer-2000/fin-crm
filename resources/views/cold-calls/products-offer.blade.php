<table class="table">
    <tbody>
    @if ($products)
        @foreach($products as $product)
            @if ($product[0]->type == 1)
                <tr>
                    <td colspan="4"><b>  @lang('general.up-sell')</b></td>
                </tr>
                @foreach($product as $pr)
                    <tr>
                        <td>{{$pr->product}}</td>
                        <td>
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->name}}
                            @endif
                        </td>
                        <td>
                            {{$pr->price}}
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->currency}}
                            @endif
                        </td>
                        <td style="width: 40px">
                            <a href="#" class="table-link danger delete_product">
                                                            <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                <i class="fa fa-square fa-stack-2x"></i>
                                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                            </span>
                            </a>
                        </td>
                    </tr>
                @endforeach

            @elseif($product[0]->type == 2)
                <tr>
                    <td colspan="4"><b>  @lang('general.up-sell') 2</b></td>
                </tr>
                @foreach($product as $pr)
                    <tr>
                        <td>{{$pr->product}}</td>
                        <td>
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->name}}
                            @endif
                        </td>
                        <td>
                            {{$pr->price}}

                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->currency}}
                            @endif
                        </td>
                        <td style="width: 40px">
                            <a href="#" class="table-link danger delete_product">
                                                                    <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @elseif($product[0]->type == 4)
                <tr>
                    <td colspan="4"><b>Cross sell</b></td>
                </tr>
                @foreach($product as $pr)
                    <tr>
                        <td>{{$pr->product}}</td>
                        <td>
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->name}}
                            @endif
                        </td>
                        <td>
                            {{$pr->price}}
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->currency}}
                            @endif
                        </td>
                        <td style="width: 40px">
                            <a href="#" class="table-link danger delete_product">
                                                                    <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                            </a>
                        </td>
                    </tr>
                @endforeach

            @elseif($product[0]->type == 0)
                <tr>
                    <td colspan="4"><b> @lang('general.product')</b></td>
                </tr>
                @foreach($product as $pr)
                    <tr>
                        <td>{{$pr->product}}</td>
                        <td>
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->name}}
                            @endif
                        </td>
                        <td>
                            {{$pr->price}}
                            @if (isset($countries[$pr->geo]) )
                                {{$countries[$pr->geo]->currency}}
                            @endif
                        </td>
                        <td style="width: 40px">
                            <a href="#" class="table-link danger delete_product">
                                                                    <span class="fa-stack " data-id="{{ $pr->id }}">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
                            </a>
                        </td>
                    </tr>
                @endforeach

            @endif
        @endforeach
    @endif
    </tbody>
</table>