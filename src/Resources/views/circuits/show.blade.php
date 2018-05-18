@extends('churchnet::templates.frontend')

@section('title',$circuit->circuit)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h4>{{$circuit->circuitnumber}} {{$circuit->circuit}} <small><a href="{{url('/')}}/districts/{{$circuit->district_id}}">{{$circuit->district->district}} District</a></small></h4>
            @if ($circuit->office_contact)
                {{$circuit->office_contact}} 
            @endif
            @if ($plan)
                <a href="{{url('/')}}/plan/{{$circuit->slug}}" target="_blank">Current preaching plan</a>
            @endif
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h4>Societies</h4>
            <ul class="list-unstyled">
            @foreach ($circuit->societies as $society)
                <li><a href="{{url('/')}}/{{$circuit->slug}}/{{$society->slug}}">{{$society->society}}</a></li>
            @endforeach
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div style="width: 100%; height: 400px;">
                {!! Mapper::render() !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            @if (isset($Minister))
                <h3>Ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($Minister as $min)
                        <li>{{$min->title}} {{$min->firstname}} {{$min->surname}}</li>
                    @endforeach
                </ul>
            @endif
            @if (isset($Supernumerary))
                <h3>Supernumerary Ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($Supernumerary as $sup)
                        <li>{{$sup->title}} {{$sup->firstname}} {{$sup->surname}}</li>
                    @endforeach
                </ul>
            @endif
            <ul class="list-unstyled">
                @if ((isset($settings['superintendent'])) and ($settings['superintendent']<>""))
                    <li><i>Superintendent Minister:</i> {{$settings['superintendent']}}</li>
                @endif
                @if ((isset($settings['circuit_stewards'])) and ($settings['circuit_stewards']<>""))
                    <li><i>Circuit Stewards:</i>
                     @foreach(explode(',',$settings['circuit_stewards']) as $cs)
                        <li>- {{$cs}}</li>
                     @endforeach
                    </li>
                @endif
                @if ((isset($settings['circuit_secretary'])) and ($settings['circuit_secretary']<>""))
                    <li><i>Circuit Secretary:</i> {{$settings['circuit_secretary']}}</li>
                @endif
                @if ((isset($settings['circuit_treasurer'])) and ($settings['circuit_treasurer']<>""))
                    <li><i>Circuit Treasurer:</i> {{$settings['circuit_treasurer']}}</li>
                @endif
                @if ((isset($settings['local_preachers_secretary'])) and ($settings['local_preachers_secretary']<>""))
                    <li><i>Local Preachers Secretary:</i> {{$settings['local_preachers_secretary']}}</li>
                @endif
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            @if (isset($preachers))
                <h3>Local preachers</h3>
                @foreach ($preachers as $lp)
                    @if (!$loop->last)
                        {{$lp}}, 
                    @else
                        {{$lp}}.
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection