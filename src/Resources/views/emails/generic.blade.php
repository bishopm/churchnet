@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://church.net.za'])
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
            {{$emaildata['society']}} Methodist Church | {{$emaildata['website']}}
        @endcomponent
    @endslot
@endcomponent