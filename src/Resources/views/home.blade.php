@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<style media="screen" type="text/css">
    .size1 { font-size: 70%; }
    .size2 { font-size: 80%; }
    .size3 { font-size: 90%; }
    .size4 { font-size: 100%; }
    .size5 { font-size: 110%; }
    .size6 { font-size: 120%; }
    .size7 { font-size: 130%; }
    .size8 { font-size: 140%; }
    .size9 { font-size: 150%; }
    .size10 { font-size: 160%; }
</style>
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <p><h3>Welcome to church.net.za</h3> We're collecting useful resources for local church ministry. Have a look around and then feel free to register and make your own contribution :)</p>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            {!!$cloud->render()!!}
        </div>
        <div class="col-3 bg-dark">
            <h3>Added recently</h3>
            <h5>Resources</h5>
            @foreach ($recentresources as $recent)
                <li><a href="{{route('resources.show',$recent->id)}}">{{$recent->title}}</a></li>
            @endforeach
            <h5>Pages</h5>
            @foreach ($recentpages as $recentp)
                <li><a href="{{route('resources.show',$recentp->id)}}">{{$recentp->title}}</a></li>
            @endforeach
        </div>
        
    </div>
</div>
@endsection