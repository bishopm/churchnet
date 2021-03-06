@extends('churchnet::templates.frontend')

@section('title',$resource->resource)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <h2>
                {{$resource->title}}
                @foreach ($resource->tags as $tag)
                    <small><span class="badge bg-dark"><a href="{{route('tag',$tag->slug)}}">{{$tag->name}}</a></span></small>
                @endforeach
            </h2>
            <p><a target="_blank" href="{!!$resource->url!!}"><i class="fa fa-lg fa-globe"></i></a> {{$resource->description}}</p>
        </div>
    </div>
</div>
@endsection