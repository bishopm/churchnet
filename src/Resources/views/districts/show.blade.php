@extends('churchnet::templates.frontend')

@section('title',$title)

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
@stop


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
                        <b>Phone:</b> {{$district->location->phone}}<br>
                    @endif
                @endif
            @endif
            <a href="{{url('/')}}/districts/{{$district->id}}/ministers">{{$district->denomination->provincial}} Clergy</a>
        </div>
        <div class="col-sm-6">
            @if ($district->location) 
                @if (($district->location->latitude) && ($district->location->longitude))
                    <div id="map1" style="width: 100%; height: 200px;">
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
            <div id="map2" style="width: 100%; height: 400px;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
    <script>
        <?php if (isset($district->location)) {
    ?>  var mymap = L.map('map1').setView([{{$district->location->latitude}}, {{$district->location->longitude}}], 13);
        var streets1 = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mymap);
        var satellite1 = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 18 });
        var allMaps1 = { "Street map": streets1, "Satellite image": satellite1 };
        L.control.layers(allMaps1).addTo(mymap);
        L.marker([{{$district->location->latitude}}, {{$district->location->longitude}}]).addTo(mymap);
        <?php
}
if (isset($markers)) {
    ?>        
        var tilelayer = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 });
        var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 18 });
        var allMaps = { "Street map": tilelayer, "Satellite image": satellite };
        var featureGroup = L.featureGroup([
        <?php
        $i=0;
    foreach ($markers as $marker) {
        $i++;
        $lat = $marker['lat'];
        $lng = $marker['lng'];
        $tle = $marker['title'];
        echo "L.marker([$lat,$lng]).bindPopup('" . $tle . "')";
        if ($i < count($markers)) {
            echo ", ";
        } else {
            echo "]);\n";
        }
    } ?>
        var mymap2 = new L.Map('map2', { 'center': [0, 0], 'zoom': 0, 'layers': [tilelayer, featureGroup] });  
        L.control.layers(allMaps).addTo(mymap2);
        mymap2.fitBounds(featureGroup.getBounds(), {padding: [25,25]});
        <?php
}
        ?>
    </script> 
@stop