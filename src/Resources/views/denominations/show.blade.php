@extends('churchnet::templates.frontend')

@section('title',$title)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm-6">
            <h3 class="title">{{$denomination->denomination}}</h3>
            @foreach ($denomination->individuals as $indiv)
                <b>{{$indiv->pivot->description}}:</b> {{$indiv->title}} {{$indiv->firstname}} {{$indiv->surname}}<br>
            @endforeach
            @if ($denomination->location) 
                @if ($denomination->location->description)
                    <b>{{$denomination->location->description}}</b><br>
                    @if ($denomination->location->address)
                        <b>Address:</b> {{$denomination->location->address}}<br>
                    @endif
                    @if ($denomination->location->phone)
                        <b>Phone: </b>{{$denomination->location->phone}}
                    @endif
                @endif
            @endif
        </div>
        <div class="col-sm-6">
            @if ($denomination->location) 
                @if (($denomination->location->latitude) && ($denomination->location->longitude))
                    <div style="width: 100%; height: 200px;">
                        {!! Mapper::render(1) !!}
                    </div>
                @endif
            @endif
        </div>
        <div class="col-sm-12"><hr></div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <ul class="list-unstyled">
            <h5>{{str_plural($denomination->provincial)}}</h5>
            @foreach ($denomination->districts as $district)
                <li><a href="{{url('/')}}/districts/{{$district->id}}">{{$district->id}} {{$district->district}}</a></li>
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