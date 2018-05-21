@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <h3>{{$tag}}</h3>
            <table class="table table-striped table-sm table-responsive">
                @foreach ($resources as $resource)
                    <tr><td><a href="{{route('resources.show',$resource->id)}}">{{$resource->title}}</a></td><td>{{$resource->description}}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection