<tr>
            <td class="text-center">
                {{$feedback->id}}
            </td>
            <td class="text-center">
                {{$feedback->created_at}}
            </td>
            <td class="text-center">
                @if(!empty($feedback->order_id))
                    <a href="{{ route('order', $feedback->order_id) }}"
                       class="crm_id">{{$feedback->order_id}}</a>
                @else
                    {{'N\A'}}
                @endif
            </td>
            <td class="text-center">
                @if(!empty($feedback->user->id))
                    <div style=" border-bottom: 2px solid #ebebeb; padding-bottom: 5px">
                        <a href="{{ route('users-edit', $feedback->user->id) }}"
                        >{{$feedback->user->name.'  '.$feedback->user->surname}}</a>
                    </div>
                @else
                    {{'N/A'}}
                @endif
                @if(!empty($feedback->user->company->name))
                    <div style="padding-top: 7px; padding-bottom: 7px; font-weight: bold; color: rgba(41,41,41,0.84)">{{$feedback->user->company->name}}</div>
                @endif
            </td>
            <td class="text-center">
                @if(!empty($feedback->moderator))
                    {{$feedback->moderator->name. '  ' .$feedback->moderator->surname}}
                @endif
            </td>
            {{--<td class="text-center">--}}
            {{--{{'Mentor'}}--}}
            {{--</td>--}}
            <td>
                @if(!empty($feedback->mistakes))
                    @foreach($feedback->mistakes as $mistake)
                        {{$mistake->name}}<br>
                    @endforeach
                @elseif(!empty($feedback->title))
                    <span style="color: #383838; font-weight: bold">{{$feedback->title}}</span>
                @else
                    {{'N/A'}}
                @endif
            </td>
            <td class="text-center">
                <div style="border-bottom: 2px solid #ebebeb; padding-bottom: 15px">
                    @if($feedback->status == 'opened')
                        <span id="activity"
                              class="label label-success"> @lang('general.open')</span>
                    @elseif($feedback->status == 'closed')
                        <span id="activity" class="label label-danger"> @lang('general.closed')</span>
                    @endif
                </div>
                <div style="color: #868b98">
                    @if(!empty($feedback->user_id) &&!empty($feedback->user) && $feedback->moderator_id && $feedback->read == 2 )
                        @lang('feedbacks.awaiting-response'):
                        <br> {{$feedback->user->name.' '.$feedback->user->surname}}
                    @elseif(!empty($feedback->user_id) && !empty($feedback->moderator) && $feedback->read == 1)
                        @lang('feedbacks.awaiting-response'):
                        <br>  {{$feedback->moderator->name.' '.$feedback->moderator->surname}}
                    @elseif(!empty($feedback->user_id) && auth()->user()->id == $feedback->user_id && $feedback->read == 2)
                        @lang('feedbacks.awaiting-your-response'):
                    @elseif(auth()->user()->id == $feedback->moderator_id && $feedback->read == 1 )
                        @lang('feedbacks.awaiting-your-response'):
                    @elseif($feedback->status == 'closed')
                        @lang('feedbacks.discussion-completed'):
                    @else
                    @endif
                </div>
            </td>
            <td class="text-center">
                <a href="{{route('feedback', $feedback->id)}}"
                   class="table-link">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                    </span>
                </a>
            </td>
        </tr>
