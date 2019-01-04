@extends('churchnet::templates.frontend')

@section('title',$society->society)

@section('content')
<div class="container mt-5">
    <div class="box">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <h1 class="title">{{$society->society}} <span class="subtitle small"><a href="{{url('/')}}/circuits/{{$society->circuit->slug}}">{{$society->circuit->circuit}}</a></span></h1>
                <ul class="list-unstyled">
                    @if ($society->location->address)
                        <li>{{$society->location->address}}</li>
                    @endif
                    @if ($society->location->phone)
                        <li><b>Phone:</b> {{$society->location->phone}}</li>
                    @endif
                    @if ($society->email)
                        <li><b>Email:</b> {{$society->email}}</li>
                    @endif
                    @if ($society->website)
                        <li><b>Website:</b> <a target="_blank" href="{{$society->website}}">{{$society->website}}</a></li>
                    @endif
                    @if ($society->location->latitude)
                        <li>{{$society->location->latitude}}&#176;, {{$society->location->longitude}}&#176;</li>
                    @endif
                </ul>
                <hr>
                <h1 class="subtitle">Service times</h1>
                <ul class="list-unstyled">
                    @forelse ($society->services as $service)
                        <li><b>{{$service->servicetime}}</b> ({{$service->language}})</li>
                    @empty
                        No services have been set up for this society yet
                    @endforelse
                </ul>
                @if (count($stewards))
                <h3>Society stewards</h3>
                <ul class="list-unstyled">
                    @foreach ($stewards as $stw)
                        <li>{{$stw->individual->title}} {{$stw->individual->firstname}} {{$stw->individual->surname}}</li>
                    @endforeach
                </ul>
            @endif
            </div>
            <div class="col-xs-12 col-sm-6">
                <div style="width: 100%; height: 400px;">
                    {!! Mapper::render() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection