@extends('churchnet::templates.frontend')

@section('title','MCSA Districts')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h1 class="title">MCSA Districts</h1>
            <ul>
            @foreach ($districts as $district)
                <li><a href="{{url('/')}}/districts/{{$district->id}}">{{$district->id}} {{$district->district}}</a></li>
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