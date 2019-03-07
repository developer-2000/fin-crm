<div class="col-md-4">
    <header class="main-box-header clearfix text-center">
        <h5>Группа назначений</h5>
    </header>
    @if ($campaigns)
        <ul class="group_operator">
            @foreach($campaigns as $campaign)
                <li id="group_{{$campaign->id}}">
                    <div class="name_campaign_wrap">
                        <?php
                        $color = 'rgb(' . rand(0,255) . ',' . rand(0,255) . ',' . rand(0,255) . ')';
                        ?>
                        <div class="count" style="color: {{$color}}">
                            @if (isset($operators[$campaign->id]))
                                {{count($operators[$campaign->id])}}
                            @else
                                0
                            @endif
                        </div>
                        <div class="content" style="background-color: {{$color}};">{{$campaign->name}}</div>
                    </div>
                    <ul class="operators_in_group" style="display: @if ($all) none @else block @endif">
                        @if (isset($operators[$campaign->id]))
                            @foreach($operators[$campaign->id] as $operator)
                                <li id="operator_{{$operator->id}}" class="operator" style="border-color: {{$color}};" data-id="{{$operator->login_sip}}">
                                    <div>
                                        {{$operator->surname}}  {{$operator->name}}
                                    </div>
                                    <span class="pull-right">
                                                <b>ID</b> {{$operator->login_sip}}
                                                </span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
            @endforeach
        </ul>
    @endif
</div>
<div class="col-md-4">
    <header class="main-box-header clearfix text-center">
        <h5>Перенос по группам</h5>
    </header>
    @if ($campaigns)
        <ul class="groups">
            @foreach($campaigns as $campaign)
                <li class="group">
                    <ul class="target_groups " group-id="{{$campaign->id}}">{{$campaign->name}}
                    </ul>
                </li>
            @endforeach
            <li class="group">
                <ul class="target_groups " group-id="0">Не назначен
                </ul>
            </li>
        </ul>
    @endif
</div>
<div class="col-md-4">
    <header class="main-box-header clearfix text-center">
        <h5>Свобдные операторы</h5>
    </header>
    @if (isset($operators[0]))
        <ul class="operators">
            @foreach($operators[0] as $operator)
                <li id="operator_{{$operator->id}}" class="operator" data-id="{{$operator->login_sip}}">
                    <div>
                        {{$operator->surname}}  {{$operator->name}}
                    </div>
                    <span class="pull-right">
                                    <b>ID</b> {{$operator->login_sip}}
                                </span>
                </li>
            @endforeach
        </ul>
    @endif
</div>