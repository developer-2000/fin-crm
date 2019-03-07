<div class="conversation-content">
    <div class="conversation-inner"
         style="overflow: hidden; width: auto; height: auto;">
        <div class="conversation-item item-right clearfix">
            <div class="conversation-user">
                <img src="{{$feedback->moderator->photo}}"
                     alt=""
                     class="profile-img img-responsive center-block"/>
            </div>
            <div class="conversation-body moderator-feedback"
                 style="background: rgba(240,82,71,0.14);">
                <div class="text-center"> @lang('feedbacks.initiator-message'):</div>
                <div class="name">
                    @if(auth()->user()->id == $feedback->moderator->id)
                        {{'Вы:'}}
                    @elseif(!empty($feedback->moderator->name))
                        {{(!empty($feedback->moderator->name) ? $feedback->moderator->name. ' ' .$feedback->moderator->surname : '') .(!empty($feedback->moderator->company->name) ? ' > '. $feedback->moderator->company->name .' ' : '') }}
                    @endif
                </div>
                <div class="time hidden-xs">
                    {{$feedback->created_at->format('Y-m-d H:i:s')}}
                </div>
                <div class="text-center"
                     style="font-weight: bold; font-size: 14px">
                    @if(!empty($comments[0]->text))
                        {{ $comments[0]->text }}
                    @else
                        @lang('feedbacks.comment-not-added')
                    @endif
                </div>
            </div>
        </div>
        @if(!empty($comments))
            @foreach( $comments as $key=> $comment)
                @if($comment->user->role == 1 )
                    <div class="conversation-item item-left clearfix">
                        <div class="conversation-user">
                            <img src="{{$comment->user->photo}}"
                                 alt=""
                                 class="profile-img img-responsive center-block"/>
                        </div>
                        <div class="conversation-body">
                            <div class="name">
                                @if(!empty($comment->user->name))
                                    {{'Вы:'}}
                                @else
                                    {{$comment->user->name. ' ' .$comment->user->surname .(!empty($comment->user->company->name) ? ' > '. $comment->user->company->name .' ' : '') }}
                                @endif
                            </div>
                            <div class="time hidden-xs">
                                {{ date('Y/m/d H:i:s', (int)$comment->date) }}
                            </div>
                            <div class="text">
                                {{!empty($comment->text) ? $comment->text : '' }}
                            </div>
                        </div>
                    </div>
                @endif
                @if(($comment->user->role >1)&& $key != 0 && !empty($comment->text))
                    <div class="conversation-item item-right clearfix">
                        <div class="conversation-user">
                            <img src="{{$comment->user->photo}}"
                                 alt=""
                                 class="profile-img img-responsive center-block"/>
                        </div>
                        <div class="conversation-body">
                            <div class="name">
                                @if(!empty($comment->user->name) && auth()->user()->id == $comment->user_id)
                                    {{'Вы:'}}
                                @elseif(!empty($comment->user->name))
                                    {{$comment->user->name. ' ' .$comment->user->surname .(!empty($comment->user->company->name) ? ' > '. $comment->user->company->name .' ' : '') }}
                                @endif
                            </div>
                            <div class="time hidden-xs">
                                {{ date('Y/m/d H:i:s', (int)$comment->date) }}
                            </div>
                            <div class="text">
                                {{$comment->text}}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>
