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
            @forelse ($circuit->societies as $society)
                @if ($loop->last)
                    <a href="{{url('/')}}/methodist/{{$circuit->slug}}/{{$society->slug}}">{{$society->society}}</a>.
                @else
                <a href="{{url('/')}}/methodist/{{$circuit->slug}}/{{$society->slug}}">{{$society->society}}</a>, 
                @endif
            @empty
                This circuit has not mapped any societies
            @endforelse
            <hr>
            @if (isset($ministers))
                <h4>Ministers</h4>
                @foreach ($ministers as $min)
                    @if ($loop->last)
                        {{$min->individual->title}} {{$min->individual->firstname}} {{$min->individual->surname}}{{$min->supt}}.
                    @else
                        {{$min->individual->title}} {{$min->individual->firstname}} {{$min->individual->surname}}{{$min->supt}}, 
                    @endif
                @endforeach
            @endif
            @if (count($supernumeraries))
                <b>Supernumerary ministers: </b>
                @foreach ($supernumeraries as $sup)
                    @if ($loop->last)
                        {{$sup->individual->title}} {{$sup->individual->firstname}} {{$sup->individual->surname}}.
                    @else
                        {{$sup->individual->title}} {{$sup->individual->firstname}} {{$sup->individual->surname}}, 
                    @endif
                @endforeach
                <hr>
            @endif
            @if (isset($stewards))
                <h4>Circuit stewards</h4>
                @foreach ($stewards as $stw)
                    @if ($loop->last)
                        {{$stw->title}} {{$stw->firstname}} {{$stw->surname}}.
                    @else
                        {{$stw->title}} {{$stw->firstname}} {{$stw->surname}}, 
                    @endif
                @endforeach
                <hr>
            @endif
            @if (isset($preachers))
                <h4>Local preachers</h4>
                @forelse ($preachers as $lp)
                    @if (!$loop->last)
                        {{$lp->individual->title}} {{substr($lp->individual->firstname,0,1)}} {{$lp->individual->surname}}, 
                    @else
                        {{$lp->individual->title}} {{substr($lp->individual->firstname,0,1)}} {{$lp->individual->surname}}.
                    @endif
                @empty
                    This circuit has not added any preachers to the system
                @endforelse
                <hr>
            @endif
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
            
        </div>
        <div class="col-xs-12 col-sm-6">

        </div>
    </div>
</div>
@endsection