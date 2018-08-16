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
            @if (isset($ministers))
                <h3>Circuit ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($ministers as $min)
                        <li>{{$min->individual->title}} {{$min->individual->firstname}} {{$min->individual->surname}}</li>
                    @endforeach
                </ul>
            @endif
            @if (isset($supernumeraries))
                <h3>Supernumerary ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($supernumeraries as $sup)
                        <li>{{$sup->individual->title}} {{$sup->individual->firstname}} {{$sup->individual->surname}}</li>
                    @endforeach
                </ul>
            @endif
            @if (isset($stewards))
                <h3>Circuit stewards</h3>
                <ul class="list-unstyled">
                    @foreach ($stewards as $stw)
                        <li>{{$stw->individual->title}} {{$stw->individual->firstname}} {{$stw->individual->surname}}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="col-xs-12 col-sm-6">
            @if (isset($preachers))
                <h3>Local preachers</h3>
                @forelse ($preachers as $lp)
                    @if (!$loop->last)
                        {{$lp->individual->title}} {{substr($lp->individual->firstname,0,1)}} {{$lp->individual->surname}}, 
                    @else
                        {{$lp->title}} {{substr($lp->individual->firstname,0,1)}} {{$lp->individual->surname}}.
                    @endif
                @empty
                    This circuit has not added any preachers to the system
                @endforelse
            @endif
        </div>
    </div>
</div>
@endsection