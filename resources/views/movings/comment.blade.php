<div class="conversation-item clearfix item-right">
    <div class="conversation-user">
        <img src="{{ URL::asset('img' . $comment->user->photo) }}" alt=""/>
    </div>
    <div class="conversation-body">
        <div class="name">
            {{ $comment->user->name }}
            {{ $comment->user->surname ?: '' }}
        </div>
        <div class="time hidden-xs">
            {{$comment->date}}
        </div>
        <div class="text">
            {{ $comment->text }}
        </div>
    </div>
</div>