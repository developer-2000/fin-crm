@forelse($menuItems as $item)
    <li class="dd-item" data-id="{{$item->id}}">
        <div class="dd-handle">
            <span class="dd-nodrag">
                <a href="#" class="edit_menu" data-id="{{$item->id}}">
                    @php
                        $title = trans($item->title);

                        if (is_array($title)) {
                            $title = $item->title;
                        }
                    @endphp
                    {{$title}}
                </a>
            </span>
            <div class="nested-links dd-nodrag">
                <a href="#" class="nested-link delete_item_menu" data-id="{{$item->id}}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
        @if ($item->subMenuRecursive->isNotEmpty())
            <ol class="dd-list">
                @include('menu.item', ['menuItems' => $item->subMenuRecursive])
            </ol>
        @endif
    </li>
@empty
@endforelse