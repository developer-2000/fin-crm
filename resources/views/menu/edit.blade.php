<div class="col-md-6">
    <div class="main-box clearfix">
        <header class="main-box-header clearfix">
            <h2>
                {{$itemMenu->title}}
            </h2>
        </header>
        <div class="main-box-body clearfix">
            <form action="{{route('menu.update', $itemMenu)}}" id="edit_form">
                @include('menu.form')
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">@lang('general.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>