@extends('churchnet::templates.frontend')

@section('title',$circuit->circuit)

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
@stop

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h4>{{$circuit->circuitnumber}} {{$circuit->circuit}} <small><a href="{{url('/')}}/districts/{{$circuit->district_id}}">{{$circuit->district->district}} {{$circuit->district->denomination->provincial}}</a></small></h4>
            @if ($circuit->office_contact)
                {{$circuit->office_contact}} 
            @endif
            <a href="{{url('/')}}/plan/{{$circuit->slug}}" target="_blank">Current preaching plan</a>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h4>{{str_plural($circuit->district->denomination->local)}}</h4>
            @forelse ($circuit->societies as $society)
                @if ($loop->last)
                    <a href="{{url('/')}}/circuits/{{$circuit->slug}}/{{$society->slug}}">{{$society->society}}</a>.
                @else
                <a href="{{url('/')}}/circuits/{{$circuit->slug}}/{{$society->slug}}">{{$society->society}}</a>, 
                @endif
            @empty
                This circuit has not mapped any societies
            @endforelse
            <hr>
            <h4>Ministers</h4>
            @forelse ($ministers as $min)
                @if ($loop->last)
                    {{$min->individual->title}} {{$min->individual->firstname}} {{$min->individual->surname}}{{$min->supt}}.
                @else
                    {{$min->individual->title}} {{$min->individual->firstname}} {{$min->individual->surname}}{{$min->supt}}, 
                @endif
            @empty
                No ministers have been added to this circuit yet <br>
            @endforelse
            @if (count($supernumeraries))
                <b>Supernumerary ministers: </b>
                @foreach ($supernumeraries as $sup)
                    @if ($loop->last)
                        {{$sup->individual->title}} {{$sup->individual->firstname}} {{$sup->individual->surname}}.
                    @else
                        {{$sup->individual->title}} {{$sup->individual->firstname}} {{$sup->individual->surname}}, 
                    @endif
                @endforeach
            @endif
            @if (isset($stewards))
                <hr>
                <h4>Circuit stewards</h4>
                @forelse ($stewards as $stw)
                    @if ($loop->last)
                        {{$stw->title}} {{$stw->firstname}} {{$stw->surname}}.
                    @else
                        {{$stw->title}} {{$stw->firstname}} {{$stw->surname}}, 
                    @endif
                @empty
                    This circuit has not added any stewards to the system
                @endforelse
            @endif
            @if (isset($preachers))
                <hr>
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
                <div id="map" style="width: 100%; height: 400px;">
                    
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

@section('js')
    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
    <script>
    <?php
    if (isset($markers)) {
    ?>    
        var tilelayer = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 });
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
        var mymap = new L.Map('map', { 'center': [0, 0], 'zoom': 0, 'layers': [tilelayer, featureGroup] });  
        mymap.fitBounds(featureGroup.getBounds(), {padding: [25,25]});
        <?php
    }
        ?>
    </script> 
@stop