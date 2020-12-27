@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $emaildata['website']])
            {{$emaildata['title']}}
        @endcomponent        
    @endslot
{{-- Body --}}
{!!$emaildata['body']!!}
{{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset
{{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            {{$emaildata['society']}} | {{$emaildata['website']}}
        @endcomponent
    @endslot
@endcomponent