@if ($comments)
    @foreach ($comments as $co)
        <div class="conversation-item item-left clearfix">
            <div class="conversation-user">
                <img src="{{$co->photo}}" alt=""/>
            </div>
            <div class="conversation-body">
                <div class="company_user">{{$co->company}}</div>
                <div class="name" style="max-width: 50%;">
                    {{ $co->name }} ({{ $co->login }})
                </div>
                <div class="time hidden-xs" style="max-width: 50%;">
                    {{ $co->date }}
                </div>
                <div class="text">
                    {{ $co->text }}
                </div>
            </div>
        </div>
    @endforeach
@endif