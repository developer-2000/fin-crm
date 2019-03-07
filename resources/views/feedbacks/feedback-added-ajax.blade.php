<span style="padding-left: 15px; color: #929292">{{'Отзыв оставлен :'}}. <br>. {{$order->feedback_created_at}}</span>
<br>
<a href="{{ route('feedback', $feedbackId) }}"> @lang('feedbacks.go-to-recall')</a>
