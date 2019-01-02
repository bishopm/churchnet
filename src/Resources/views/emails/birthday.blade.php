@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://church.net.za'])
            {{$emaildata['subject']}}
        @endcomponent        
    @endslot
{{-- Body --}}
Hi {{$emaildata['recipient']}}

{!!$emaildata['emailmessage']!!}

Thank you :)
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
            [ChurchNet](https://church.net.za/) - resources for the local church
        @endcomponent
    @endslot
@endcomponent