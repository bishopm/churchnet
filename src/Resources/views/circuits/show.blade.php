@extends('churchnet::templates.frontend')

@section('title',$circuit->circuit)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h4>{{$circuit->circuitnumber}} {{$circuit->circuit}} <small><a href="{{url('/')}}/methodist/districts/{{$circuit->district_id}}">{{$circuit->district->district}} District</a></small></h4>
            @if ($circuit->office_contact)
                {{$circuit->office_contact}} 
            @endif
            @if ($plan)
                <a href="{{url('/')}}/methodist/plan/{{$circuit->slug}}" target="_blank">Current preaching plan</a>
            @endif
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h4>Societies</h4>
            <ul class="list-unstyled">
            @forelse ($circuit->societies as $society)
                <li><a href="{{url('/')}}/methodist/{{$circuit->slug}}/{{$society->slug}}">{{$society->society}}</a></li>
            @empty
                This circuit has not mapped any societies
            @endforelse
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            @if (count($circuit->societies))
                <div style="width: 100%; height: 400px;">
                    {!! Mapper::render() !!}
                </div>
            @else
                When societies are added, they will appear on a map here
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            @if ((isset($Circuit_minister)) or (isset($Superintendent_minister)))
                <h3>Ministers</h3>
                <ul class="list-unstyled">
                    @if (isset($Superintendent_minister))
                        <li>{{$Superintendent_minister[0]->title}} {{$Superintendent_minister[0]->firstname}} {{$Superintendent_minister[0]->surname}} [Supt]</li>
                    @endif
                    @foreach ($Circuit_minister as $min)
                        <li>{{$min->title}} {{$min->firstname}} {{$min->surname}}</li>
                    @endforeach
                </ul>
            @endif
            @if (isset($Supernumerary_minister))
                <h3>Supernumerary Ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($Supernumerary_minister as $sup)
                        <li>{{$sup->title}} {{$sup->firstname}} {{$sup->surname}}</li>
                    @endforeach
                </ul>
            @endif
            @if (isset($Circuit_steward))
                <h3>Circuit stewards</h3>
                <ul class="list-unstyled">
                    @foreach ($Circuit_steward as $stw)
                        <li>{{$stw->title}} {{$stw->firstname}} {{$stw->surname}}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="col-xs-12 col-sm-6">
            @if (isset($preachers))
                <h3>Local preachers</h3>
                @forelse ($preachers as $lp)
                    @if (!$loop->last)
                        {{$lp}}, 
                    @else
                        {{$lp}}.
                    @endif
                @empty
                    This circuit has not added any preachers to the system
                @endforelse
            @endif
        </div>
    </div>
</div>
@endsection