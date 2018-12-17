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
        <div class="card" style="border:none;">
            <h3>Welcome to church.net.za</h3>
            <p>We're collecting useful resources for local church ministry. Have a look around and then feel free to make your own contribution :)</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div id="myCanvasContainer">
                <canvas width="375" height="375" style="width:100%" id="myCanvas">
                    <p>{!! $cloud !!}</p>
                </canvas>
            </div>
        </div>
        <div class="col-md-4">
            @if ((Auth::user()) and (Auth::user()->level<>'user'))
                <h3>Recent users</h3>
                <div>
                    @foreach ($users as $user)
                        <li>{{$user->name}} ({{$user->created_at}})</a></li>
                    @endforeach
                </div>
            @endif
            <h3>Added recently</h3>
            <div>
                @foreach ($recentresources as $recent)
                    <li><a href="{{route('resources.show',$recent->id)}}">{{$recent->title}}</a></li>
                @endforeach
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('/vendor/bishopm/js/tagcanvas.js')}}"></script>
<script type="text/javascript">
var options = {
  weight: true,
  textColour: 'red',
  weightFrom: 'size',
  initial: [-0.080, 0.280],
  maxSpeed: 0.04
};
window.onload = function() {
    try {
      TagCanvas.Start('myCanvas','',options);
    } catch(e) {
      // something went wrong, hide the canvas container
      document.getElementById('myCanvasContainer').style.display = 'none';
    }
};
</script>
@stop