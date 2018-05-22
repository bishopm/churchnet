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
            <h3>Welcome to our resource site for local church ministry</h3><p>We have {{$resourcecount}} resources listed on our site. Have a look around (use the search box above or click on a subject link below) and then make your own contribution :)</p>
        </div>
    </div>
    <div class="row">
        <div class="col-3 bg-light">
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
        <div class="col-9">
            {!!$cloud->render()!!}
        </div>
    </div>
</div>
@endsection