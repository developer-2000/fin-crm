@if ($last_page > 1)
@php
$page = $current_page;
$page_minus_3 = $page - 3;
$page_minus_2 = $page - 2;
$page_minus_1 = $page - 1;
$page_plus_1 = $page + 1;
$page_plus_2 = $page + 2;
$page_plus_3 = $page + 3;
@endphp
    <div class="panel-body text-center">

        <ul class="pagination">
    @if($page > 1)
                <li   onclick="{{ $action . "({$page_minus_1})" }} "><a href="#" class="demo-pli-arrow-left">< Back</a></li>

                @if ($page_minus_3 > 1)<li  onclick="{{ $action . "(1);" }}"><a href="#">1</a></li>@endif
     @endif

            @if($page_minus_3 > 2) <li><span>...</span></li>@endif

            @if ($page_minus_3 >  0) <li  onclick="{{ $action . "({$page_minus_3});" }}"><a href="#">{{ $page_minus_3 }}</a></li>@endif

            @if ($page_minus_2 > 0) <li onclick="{{ $action . "({$page_minus_2});" }}" ><a href="#">{{ $page_minus_2 }}</a></li>@endif

            @if ($page_minus_1 > 0) <li onclick="{{ $action . "({$page_minus_1});" }}"><a href="#">{{ $page_minus_1 }}</a> @endif

            <li class="active"><a href="#">{{ $page }}</a></li>

    @if ($page_plus_1 <= $last_page) <li onclick="{{ $action . "({$page_plus_1});" }}"><a href="#">{{ $page_plus_1 }}</a></li>@endif

            @if($page_plus_2 <= $last_page)  <li onclick="{{ $action . "({$page_plus_2});" }}"><a href="#">{{ $page_plus_2 }}</a></li>@endif

            @if ($page_plus_3 <= $last_page) <li onclick="{{ $action . "({$page_plus_3});" }}"><a href="#">{{ $page_plus_3 }}</a></li>@endif

            @if ($page_plus_3 + 1 < $last_page) <li><span>...</span></li> @endif

            @if ($last_page > $page)
                @if($page_plus_3 < $last_page) <li onclick="{{ $action . "({$last_page});" }}"><a href="#">{{ $last_page }}</a></li>@endif

                <li  onclick="{{ $action . "({$page_plus_1});" }}"><a href="#" class="demo-pli-arrow-right">Next ></a></li>
            @endif
        </ul>
    </div>
@endif
