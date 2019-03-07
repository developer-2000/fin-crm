<div class="row">
    <div class="col-lg-12">
        <div class="main-box clearfix">
            <div class="main-box-body clearfix">
                @if (isset($data['orders']) && $data['orders'])
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th class="text-center">Владелец</th>
                                    <th class="text-center">Страна</th>
                                    <th class="text-center">Телефон</th>
                                    <th class="text-center">Дата</th>
                                    <th class="text-center">Звонки</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['orders'] as $o)
                                    <tr>
                                        <td>{{ $o->id }}</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">{{ $o->country }}</td>
                                        <td class="text-center">{{ $o->phone }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($o->time_created)->format('Y-m-d H:i:s')}}</td>
                                        <td class="text-center">
                                            <div style="display: inline-block; text-align: left">
                                                @if (isset($data['calls'][$o->id]) && $data['calls'][$o->id])
                                                    @foreach ($data['calls'][$o->id] as $position => $call)
                                                        {{ $position + 1 }} - {{ $call }}
                                                        <br />
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="" class="table-link">
                                                <span class="fa-stack set_not_calls_callback" data-id="{{ $o->id }}">
                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                    <i class="fa fa-phone fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </a>
                                            {{--<a href="" class="table-link">--}}
                                                {{--<span class="fa-stack set_not_calls" data-id="{{ $o->id }}">--}}
                                                    {{--<i class="fa fa-square fa-stack-2x"></i>--}}
                                                    {{--<i class="fa fa-check-square-o fa-stack-1x fa-inverse"></i>--}}
                                                {{--</span>--}}
                                            {{--</a>--}}
                                            <a href="{{ route('order', $o->id) }}" class="table-link">
                                                <span class="fa-stack">
                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                    <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($pagination && count($pagination[0]) > 1)
                        <ul class="pagination pull-right">
                            <li><a href="{{ route('orders', 'do-not-calls') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}"><i class="fa fa-chevron-left"></i></a></li>
                                @foreach ($pagination[0] as $number)
                                    <? $activaPage = '' ?>
                                    @if ($pagination[1] == $number)
                                        <li class=active><span>{{ $number }}</span></li>
                                    @else
                                        @if ($number == 1)
                                            <li><a href="{{ route('orders', 'do-not-calls') }}/{{ ($pagination[3]) ? $pagination[3] : '' }}">{{ $number }}</a></li>
                                        @else
                                            <li><a href="{{ route('orders', 'do-not-calls') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $number : '?page=' . $number }}">{{ $number }}</a></li>
                                        @endif
                                    @endif
                                @endforeach
                            <li><a href="{{ route('orders', 'do-not-calls') }}/{{ ($pagination[3]) ? $pagination[3] . '&page=' . $pagination[2] : '?page=' . $pagination[2] }}"><i class="fa fa-chevron-right"></i></a></li>
                        </ul>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>