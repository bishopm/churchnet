@extends('churchnet::templates.frontend')

@section('title',$title)

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
@stop

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
                    <div id="map1" style="width: 100%; height: 200px;">
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
            <div id="map2" style="width: 100%; height: 400px;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
    <script>
        var mymap = L.map('map1').setView([{{$denomination->location->latitude}}, {{$denomination->location->longitude}}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mymap);
        L.marker([{{$denomination->location->latitude}}, {{$denomination->location->longitude}}]).addTo(mymap);
        var mymap2 = L.map('map2').setView([{{$denomination->location->latitude}}, {{$denomination->location->longitude}}], 4);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mymap2);
        <?php
        foreach ($markers as $marker) {
            $lat = $marker['lat'];
            $lng = $marker['lng'];
            $tle = $marker['title'];
            echo "L.marker([$lat,$lng]).addTo(mymap2).bindPopup('" . $tle . "');";
        }
        ?>
    </script> 
@stop