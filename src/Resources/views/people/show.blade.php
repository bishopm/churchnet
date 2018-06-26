@extends('churchnet::templates.frontend')

@section('title',$page->page)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <h2>
                {{$page->title}}
                @foreach ($page->tags as $tag)
                    <small><span class="badge bg-dark"><a href="{{route('tag',$tag->slug)}}">{{$tag->name}}</a></span></small>
                @endforeach
            </h2>
            {!!$page->body!!}
        </div>
    </div>
</div>
@endsection