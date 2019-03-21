@extends('churchnet::templates.frontend')

@section('title',$society->society)

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
@stop

@section('content')
<div class="container mt-5">
    <div class="box">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <h1 class="title">{{$society->society}} <span class="subtitle small"><a href="{{url('/')}}/circuits/{{$society->circuit->slug}}">{{$society->circuit->circuit}}</a></span></h1>
                <ul class="list-unstyled">
                    @if ($society->location and $society->location->address)
                        <li>{{$society->location->address}}</li>
                    @endif
                    @if ($society->location and $society->location->phone)
                        <li><b>Phone:</b> {{$society->location->phone}}</li>
                    @endif
                    @if ($society->email)
                        <li><b>Email:</b> {{$society->email}}</li>
                    @endif
                    @if ($society->website)
                        <li><b>Website:</b> <a target="_blank" href="{{$society->website}}">{{$society->website}}</a></li>
                    @endif
                    @if ($society->location and $society->location->latitude)
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
                <div id="map1" style="width: 100%; height: 400px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <?php if (isset($society->location)) {
    ?>
    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
    <script>
        var mymap = L.map('map1').setView([{{$society->location->latitude}}, {{$society->location->longitude}}], 13);
        var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mymap);
        var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 18 });
        var allMaps = { "Street map": streets, "Satellite image": satellite };
        L.control.layers(allMaps).addTo(mymap);
        L.marker([{{$society->location->latitude}}, {{$society->location->longitude}}]).addTo(mymap);
    </script>
    <?php
} ?>
@stop