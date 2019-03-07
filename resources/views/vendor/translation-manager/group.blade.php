@extends('layouts.app')

@section('title')Translation Manager @endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/nifty-component.css') }}" />
    <style>
        a.status-1{
            font-weight: bold;
        }
        .form-remove-locale .form-group {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('jsBottom')
    <script src="{{ URL::asset('js/vendor/bootstrap-editable.min.js') }}"></script>
    <script>//https://github.com/rails/jquery-ujs/blob/master/src/rails.js
        (function(e,t){if(e.rails!==t){e.error("jquery-ujs has already been loaded!")}var n;var r=e(document);e.rails=n={linkClickSelector:"a[data-confirm], a[data-method], a[data-remote], a[data-disable-with]",buttonClickSelector:"button[data-remote], button[data-confirm]",inputChangeSelector:"select[data-remote], input[data-remote], textarea[data-remote]",formSubmitSelector:"form",formInputClickSelector:"form input[type=submit], form input[type=image], form button[type=submit], form button:not([type])",disableSelector:"input[data-disable-with], button[data-disable-with], textarea[data-disable-with]",enableSelector:"input[data-disable-with]:disabled, button[data-disable-with]:disabled, textarea[data-disable-with]:disabled",requiredInputSelector:"input[name][required]:not([disabled]),textarea[name][required]:not([disabled])",fileInputSelector:"input[type=file]",linkDisableSelector:"a[data-disable-with]",buttonDisableSelector:"button[data-remote][data-disable-with]",CSRFProtection:function(t){var n=e('meta[name="csrf-token"]').attr("content");if(n)t.setRequestHeader("X-CSRF-Token",n)},refreshCSRFTokens:function(){var t=e("meta[name=csrf-token]").attr("content");var n=e("meta[name=csrf-param]").attr("content");e('form input[name="'+n+'"]').val(t)},fire:function(t,n,r){var i=e.Event(n);t.trigger(i,r);return i.result!==false},confirm:function(e){return confirm(e)},ajax:function(t){return e.ajax(t)},href:function(e){return e.attr("href")},handleRemote:function(r){var i,s,o,u,a,f,l,c;if(n.fire(r,"ajax:before")){u=r.data("cross-domain");a=u===t?null:u;f=r.data("with-credentials")||null;l=r.data("type")||e.ajaxSettings&&e.ajaxSettings.dataType;if(r.is("form")){i=r.attr("method");s=r.attr("action");o=r.serializeArray();var h=r.data("ujs:submit-button");if(h){o.push(h);r.data("ujs:submit-button",null)}}else if(r.is(n.inputChangeSelector)){i=r.data("method");s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else if(r.is(n.buttonClickSelector)){i=r.data("method")||"get";s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else{i=r.data("method");s=n.href(r);o=r.data("params")||null}c={type:i||"GET",data:o,dataType:l,beforeSend:function(e,i){if(i.dataType===t){e.setRequestHeader("accept","*/*;q=0.5, "+i.accepts.script)}if(n.fire(r,"ajax:beforeSend",[e,i])){r.trigger("ajax:send",e)}else{return false}},success:function(e,t,n){r.trigger("ajax:success",[e,t,n])},complete:function(e,t){r.trigger("ajax:complete",[e,t])},error:function(e,t,n){r.trigger("ajax:error",[e,t,n])},crossDomain:a};if(f){c.xhrFields={withCredentials:f}}if(s){c.url=s}return n.ajax(c)}else{return false}},handleMethod:function(r){var i=n.href(r),s=r.data("method"),o=r.attr("target"),u=e("meta[name=csrf-token]").attr("content"),a=e("meta[name=csrf-param]").attr("content"),f=e('<form method="post" action="'+i+'"></form>'),l='<input name="_method" value="'+s+'" type="hidden" />';if(a!==t&&u!==t){l+='<input name="'+a+'" value="'+u+'" type="hidden" />'}if(o){f.attr("target",o)}f.hide().append(l).appendTo("body");f.submit()},formElements:function(t,n){return t.is("form")?e(t[0].elements).filter(n):t.find(n)},disableFormElements:function(t){n.formElements(t,n.disableSelector).each(function(){n.disableFormElement(e(this))})},disableFormElement:function(e){var t=e.is("button")?"html":"val";e.data("ujs:enable-with",e[t]());e[t](e.data("disable-with"));e.prop("disabled",true)},enableFormElements:function(t){n.formElements(t,n.enableSelector).each(function(){n.enableFormElement(e(this))})},enableFormElement:function(e){var t=e.is("button")?"html":"val";if(e.data("ujs:enable-with"))e[t](e.data("ujs:enable-with"));e.prop("disabled",false)},allowAction:function(e){var t=e.data("confirm"),r=false,i;if(!t){return true}if(n.fire(e,"confirm")){r=n.confirm(t);i=n.fire(e,"confirm:complete",[r])}return r&&i},blankInputs:function(t,n,r){var i=e(),s,o,u=n||"input,textarea",a=t.find(u);a.each(function(){s=e(this);o=s.is("input[type=checkbox],input[type=radio]")?s.is(":checked"):s.val();if(!o===!r){if(s.is("input[type=radio]")&&a.filter('input[type=radio]:checked[name="'+s.attr("name")+'"]').length){return true}i=i.add(s)}});return i.length?i:false},nonBlankInputs:function(e,t){return n.blankInputs(e,t,true)},stopEverything:function(t){e(t.target).trigger("ujs:everythingStopped");t.stopImmediatePropagation();return false},disableElement:function(e){e.data("ujs:enable-with",e.html());e.html(e.data("disable-with"));e.bind("click.railsDisable",function(e){return n.stopEverything(e)})},enableElement:function(e){if(e.data("ujs:enable-with")!==t){e.html(e.data("ujs:enable-with"));e.removeData("ujs:enable-with")}e.unbind("click.railsDisable")}};if(n.fire(r,"rails:attachBindings")){e.ajaxPrefilter(function(e,t,r){if(!e.crossDomain){n.CSRFProtection(r)}});r.delegate(n.linkDisableSelector,"ajax:complete",function(){n.enableElement(e(this))});r.delegate(n.buttonDisableSelector,"ajax:complete",function(){n.enableFormElement(e(this))});r.delegate(n.linkClickSelector,"click.rails",function(r){var i=e(this),s=i.data("method"),o=i.data("params"),u=r.metaKey||r.ctrlKey;if(!n.allowAction(i))return n.stopEverything(r);if(!u&&i.is(n.linkDisableSelector))n.disableElement(i);if(i.data("remote")!==t){if(u&&(!s||s==="GET")&&!o){return true}var a=n.handleRemote(i);if(a===false){n.enableElement(i)}else{a.error(function(){n.enableElement(i)})}return false}else if(i.data("method")){n.handleMethod(i);return false}});r.delegate(n.buttonClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);if(r.is(n.buttonDisableSelector))n.disableFormElement(r);var i=n.handleRemote(r);if(i===false){n.enableFormElement(r)}else{i.error(function(){n.enableFormElement(r)})}return false});r.delegate(n.inputChangeSelector,"change.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);n.handleRemote(r);return false});r.delegate(n.formSubmitSelector,"submit.rails",function(r){var i=e(this),s=i.data("remote")!==t,o,u;if(!n.allowAction(i))return n.stopEverything(r);if(i.attr("novalidate")==t){o=n.blankInputs(i,n.requiredInputSelector);if(o&&n.fire(i,"ajax:aborted:required",[o])){return n.stopEverything(r)}}if(s){u=n.nonBlankInputs(i,n.fileInputSelector);if(u){setTimeout(function(){n.disableFormElements(i)},13);var a=n.fire(i,"ajax:aborted:file",[u]);if(!a){setTimeout(function(){n.enableFormElements(i)},13)}return a}n.handleRemote(i);return false}else{setTimeout(function(){n.disableFormElements(i)},13)}});r.delegate(n.formInputClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);var i=r.attr("name"),s=i?{name:i,value:r.val()}:null;r.closest("form").data("ujs:submit-button",s)});r.delegate(n.formSubmitSelector,"ajax:send.rails",function(t){if(this==t.target)n.disableFormElements(e(this))});r.delegate(n.formSubmitSelector,"ajax:complete.rails",function(t){if(this==t.target)n.enableFormElements(e(this))});e(function(){n.refreshCSRFTokens()})}})(jQuery)
    </script>
    <script src="{{ URL::asset('js/translation/group.js') }}"></script>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li ><a href="{{route('translation-get-index')}}">Перевод</a></li>
                <li class="active"><span>{{$group}}</span></li>
            </ol>
            <div class="clearfix">
                <h1 class="pull-left">{{$group}}</h1>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="col-md-12">
                <div class="main-box no-header clearfix">
                    <div class="main-box-body clearfix">
                        <div class="alert alert-success success-import" style="display:none;">
                            <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
                        </div>
                        <div class="alert alert-success success-find" style="display:none;">
                            <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
                        </div>
                        <div class="alert alert-success success-publish" style="display:none;">
                            <p>Done publishing the translations for group '<?php echo $group ?>'!</p>
                        </div>
                        <div class="alert alert-success success-publish-all" style="display:none;">
                            <p>Done publishing the translations for all group!</p>
                        </div>
                        <?php if(Session::has('successPublish')) : ?>
                        <div class="alert alert-info">
                            <?php echo Session::get('successPublish'); ?>
                        </div>
                        <?php endif; ?>
                        <div class="text-center">
                            <div class="table-responsive">
                                <table class="table user-list table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        @if ($locales)
                                            @foreach ($locales as $locale)
                                                <th >{{ $langs[$locale] ?? $locale}}</th>
                                            @endforeach
                                            @if (isset($permissions['translation_delete_word']))
                                            <th>&nbsp;</th>
                                            @endif
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if ($translations)
                                            @foreach($translations as $key => $translation)
                                                <tr>
                                                    <td class="text-left">{{$key}}</td>
                                                    @if ($locales)
                                                        @foreach ($locales as $locale)
                                                            <td class="text-left">
                                                                @php $tr = $translation[$locale] ?? null; @endphp
                                                                @if (isset($permissions['translation_edit']))
                                                                    <a href="#edit"
                                                                       class="editable status-{{$tr->status ?? 0}} locale-{{ $locale }}"
                                                                       data-locale="{{ $locale }}" data-name="{{ $locale . "|" . htmlentities($key, ENT_QUOTES, 'UTF-8', false)}}"
                                                                       id="username" data-type="textarea" data-pk="{{ $tr->id ?? 0}}"
                                                                       data-url="{{$editUrl }}"
                                                                       data-title="Enter translation">{{$tr->value ?? ''}}</a>
                                                                @else
                                                                    {{$tr->value ?? ''}}
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        @if (isset($permissions['translation_delete_word']))
                                                        <td>
                                                            <a href="{{ route('translation-post-delete', [$group, $key]) }}"
                                                               class="delete-key"
                                                               data-confirm="Are you sure you want to delete the translations for '{{$key}}'?"><span
                                                                        class="glyphicon glyphicon-trash"></span></a>
                                                        </td>
                                                        @endif
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if (isset($permissions['translation_publish']))
                            <form class="form-inline form-publish" method="POST" action="{{route('translation-post-publish', $group)}}" data-remote="true" role="form" data-confirm="Are you sure you want to publish the translations group '<?php echo $group ?>? This will overwrite existing language files.">
                                <button type="submit" class="btn btn-success" data-disable-with="Publishing.." >Publish translations</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </div>

@endsection