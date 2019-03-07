<table class="table table-striped table-hover">
    <tbody>
    <tr>
        <td>
            @lang('finance.transactions')
        </td>
        <td>
            {{$transaction->count ? $transaction->count : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            @lang('finance.users')
        </td>
        <td>
            {{$transaction->users ? $transaction->users : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            @lang('general.approved')
        </td>
        <td>
            {{$transaction->approve ? $transaction->approve : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            @lang('general.up-sell')
        </td>
        <td>
            {{$transaction->count_up ? $transaction->count_up : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            Up sell 2
        </td>
        <td>
            {{$transaction->count_up2 ? $transaction->count_up2 : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            @lang('general.cross-sell')
        </td>
        <td>
            {{$transaction->count_cross ? $transaction->count_cross : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            @lang('general.time-in')
        </td>
        <td>
            {{$transaction->time_crm ? $transaction->time_crm : 0 }}
        </td>
    </tr>
    <tr>
        <td>
            @lang('general.time-in') PBX
        </td>
        <td>
            {{$transaction->time_pbx ? $transaction->time_pbx : 0 }}
        </td>
    </tr>
    <tr class="success">
        <td>
            @lang('general.sum')
        </td>
        <td id="allPrice">{{$transaction->balance ? $transaction->balance : 0 }}
        </td>
    </tr>
    </tbody>
</table>
