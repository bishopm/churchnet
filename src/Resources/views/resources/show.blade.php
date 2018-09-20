@extends('churchnet::templates.frontend')

@section('css')
    <meta id="token" name="token" value="{{ csrf_token() }}" />
@endsection
  
@section('title',$resource->resource)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <h2>
                {{$resource->title}}
            </h2>
            <div>
                @foreach ($resource->tags as $tag)
                    <span class="badge badge-pill bg-primary"><a style="color:white; text-decoration:none;" href="{{route('tag',$tag->normalized)}}">{{$tag->name}}</a></span>
                @endforeach
                @if (Auth::user()->level <> 'user')
                    <a href="{{route('admin.resources.edit', $resource->id)}}"><i class="fa fa-sm fa-edit"></i></a>
                @endif
                <hr>
            </div>
            <p><a target="_blank" href="{!!$resource->url!!}"><i class="fa fa-lg fa-globe"></i></a> {{$resource->description}}</p>
        </div>
    </div>
    @auth
        @include('churchnet::shared.comments')
    @endauth
</div>
@endsection

@section('js')
  <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">
  <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>
  @include('churchnet::shared.commentsjs', ['url' => route('admin.resources.addcomment',$resource->id)])
@endsection