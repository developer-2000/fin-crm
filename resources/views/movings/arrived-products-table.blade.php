<div class="table-responsive">
    <table id="table-moving" class="table table-hover">
        <thead>
        <tr>
            <th> @lang('general.product') (id)</th>
            <th> @lang('general.product') (name)</th>
            <th> @lang('general.sent')</th>
            <th> @lang('movings.arrived')</th>
            <th> @lang('movings.shortfall')</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($arrivedProducts as $ap)
            <tr>
                <td>{{ $ap->id }}</td>
                <td>{{ $ap->title }}</td>
                <td>
                    <i class="fa fa-truck"></i>
                    {{ $ap->amount }}
                </td>
                <td>
                    <i class="fa fa-check-square-o"></i>
                    {{ $ap->arrived }}
                </td>
                <td>
                    <i class="fa fa-thumbs-down"></i>
                    {{ $ap->shortfall }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
