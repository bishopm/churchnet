@component('mail::message')
# {{$emaildata['title']}}

{!!$emaildata['body']!!}
@endcomponent