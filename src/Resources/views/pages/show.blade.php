@extends('churchnet::templates.frontend')

@section('title',$page->page)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-sm">
            <h2>
                {{$page->title}}
                @if ((Auth::user()) and (Auth::user()->level == 'admin'))
                    <small><a href="{{route('admin.pages.edit', $page->id)}}"><i class="fa fa-sm fa-edit"></i></a></small>
                @endif
            </h2>
            @foreach ($page->tags as $tag)
                <span class="badge bg-dark"><a style="color:white;text-decoration:none;" href="{{route('tag',$tag->normalized)}}">{{$tag->name}}</a></span>
            @endforeach
            <hr>
            {!!$page->body!!}
        </div>
    </div>
</div>
@endsection