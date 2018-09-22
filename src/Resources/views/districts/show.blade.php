@extends('churchnet::templates.frontend')

@section('title','MCSA Districts')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h1 class="title">{{$district->district}} District</h1>
            <ul class="list-unstyled">
            @foreach ($district->circuits as $circuit)
                <li><a href="{{url('/')}}/circuits/{{$circuit->slug}}">{{$circuit->circuitnumber}} {{$circuit->circuit}}</a> ({{count($circuit->societies)}})</li>
            @endforeach
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div style="width: 100%; height: 400px;">
                {!! Mapper::render() !!}
            </div>
        </div>
    </div>
</div>
@endsection