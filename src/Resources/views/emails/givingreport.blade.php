@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $data['website']])
            {{$data['title']}}
        @endcomponent        
    @endslot
{{-- Body --}}
Dear Planned Giver **{{$data['pg']}}**

On behalf of the {{$data['churchabbr']}} leadership and community, we are writing to thank you so much for making ministry possible here by participating in our Planned Giving programme. 

Please find below for your records a breakdown of your planned giving for the last {{$data['scope']}}. 

This mail is sent out regularly to help you to track your giving and to assure you that the amounts you give are being received and properly accounted for. 

This email is automatically generated by our system to preserve anonymity, but please feel free to contact the church office if you have any questions or concerns.

# Last {{$data['scope']}}
@if (isset($data['current']))
@component('mail::table')
| Date received | Amount | 
| -----|-----------:| 
@foreach ($data['current'] as $payment)
|{{$payment->paymentdate}}|{{number_format($payment->amount,2)}}|
@endforeach
@endcomponent
@else
No payments received during this period
@endif

@if (isset($data['historic']))
# Other {{$data['pgyr']}} payments (prior to the last {{$data['scope']}})

@component('mail::table')
| Date received | Amount | 
| -----|-----------:| 
@foreach ($data['historic'] as $payment)
|{{$payment->paymentdate}}|{{number_format($payment->amount,2)}}|
@endforeach
@endcomponent
@endif

May God bless and encourage you as you continue to serve Him.

Thank you!


**{{$data['churchabbr']}}**

*Where your treasure is, there your heart will be also (Mt 6:21)*
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
            {{$data['society']}} Methodist Church | {{$data['website']}}
        @endcomponent
    @endslot
@endcomponent