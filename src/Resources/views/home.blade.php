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
            <canvas id='wordcanvas' style="width:100%; height: 70%;"></canvas>
        </div>
        <div class="col-md-4">
            <a class="btn btn-secondary" href="{{route('admin.resources.create')}}">Add new content</a>
            <h3 style="margin-top:15px;">Churches</h3>
            @foreach ($denominations as $denomination)
                <li><a href="{{route('denominations.show',$denomination->slug)}}">{{$denomination->denomination}}</a></li>
            @endforeach
            <h3 style="margin-top:15px;">Added recently</h3>
            <div>
                @foreach ($recentresources as $recent)
                    <li><a href="{{route('resources.show',$recent->id)}}">{{$recent->title}}</a></li>
                @endforeach
            </div>
            @if ((Auth::user()) and (Auth::user()->level<>'user'))
                <h3 style="margin-top:15px;">Recent users</h3>
                <div>
                    @foreach ($users as $user)
                        <li>{{$user->name}} ({{$user->created_at}})</a></li>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('js')
<script src="{{asset('/vendor/bishopm/js/wordcloud.js')}}"></script>
<script type="text/javascript">
    WordCloud(document.getElementById('wordcanvas'), { list: {!! json_encode($words) !!}, click: (item) => { window.location = 'tag/' + item[0]; } });
</script>
@stop
