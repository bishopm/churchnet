@extends('churchnet::templates.frontend')

@section('title','MCSA Districts')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h1 class="title">District {{$district->id}}: {{$district->district}}</h1>
            <ul>
            @foreach ($district->circuits as $circuit)
                <li><a href="{{url('/')}}/methodist/circuits/{{$circuit->slug}}">{{$circuit->circuitnumber}} {{$circuit->circuit}}</a> ({{count($circuit->societies)}})</li>
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