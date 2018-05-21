@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            Welcome to the churchnet wiki
            <h3>Added recently</h3>
            @foreach ($recents as $recent)
                <li><a href="{{route('resources.show',$recent->id)}}">{{$recent->title}}</a></li>
            @endforeach
        </div>
    </div>
</div>
@endsection