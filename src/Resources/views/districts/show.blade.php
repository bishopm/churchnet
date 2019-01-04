@extends('churchnet::templates.frontend')

@section('title',$title)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm-6">
            <h1 class="title">{{$title}} <small><a href="{{route('denominations.show', $district->denomination->slug)}}">{{$district->denomination->abbreviation}}</a></small></h1>
            @foreach ($district->individuals as $indiv)
                <b>{{$indiv->pivot->description}}:</b> {{$indiv->title}} {{$indiv->firstname}} {{$indiv->surname}}<br>
            @endforeach
            @if ($district->location) 
                @if ($district->location->description)
                    <b>{{$district->location->description}}:</b> {{$district->location->address}}<br>
                    @if ($district->location->phone)
                        <b>Phone:</b> {{$district->location->phone}}
                    @endif
                @endif
            @endif
        </div>
        <div class="col-sm-6">
            @if ($district->location) 
                @if (($district->location->latitude) && ($district->location->longitude))
                    <div style="width: 100%; height: 200px;">
                        {!! Mapper::render(1) !!}
                    </div>
                @endif
            @endif
        </div>
    </div>
    <div class="col-sm-12"><hr></div>
    <div class="row">
        <div class="col-sm-6">
            <h4>{{str_plural($district->denomination->regional)}}</h4>
            <ul class="list-unstyled">
            @foreach ($district->circuits as $circuit)
                <li><a href="{{url('/')}}/circuits/{{$circuit->slug}}">{{$circuit->circuitnumber}} {{$circuit->circuit}}</a> ({{count($circuit->societies)}})</li>
            @endforeach
            </ul>
        </div>
        <div class="col-sm-6">
            <div style="width: 100%; height: 400px;">
                {!! Mapper::render(0) !!}
            </div>
        </div>
    </div>
</div>
@endsection