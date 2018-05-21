@extends('churchnet::templates.frontend')

@section('title',$resource->resource)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <h2>{{$resource->title}}</h2>
            <p>{{$resource->description}}</p>
            @foreach ($resource->tags as $tag)
                <span><a href="{{route('tag',$tag->slug)}}">{{$tag->name}}</a></span>
            @endforeach
        </div>
    </div>
</div>
@endsection