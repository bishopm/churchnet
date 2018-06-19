@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3>Search results: <i>{{$search}}</i></h3>
            <table class="table table-striped table-sm table-responsive">
                <tr><th colspan="2">Resources</th></tr>
                @forelse ($resources as $resource)
                    <tr><td><a href="{{route('resources.show',$resource->id)}}">{{$resource->title}}</a></td><td>{{$resource->description}}</td></tr>
                @empty
                    <tr><td colspan="2">No resources meet these search criteria</td></tr>
                @endforelse
                <tr><th colspan="2">Pages</th></tr>
                @forelse ($pages as $page)
                    <tr><td colspan="2"><a href="{{route('pages.show',$page->id)}}">{{$page->title}}</a></td></tr>
                @empty
                    <tr><td colspan="2">No pages meet these search criteria</td></tr>
                @endforelse
                <tr><th colspan="2">Subjects</th></tr>
                @forelse ($tags as $tag)
                    <tr><td colspan="2"><a href="{{route('tag',$tag->slug)}}">{{strtoupper($tag->name)}}</a></td></tr>
                @empty
                    <tr><td colspan="2">No subjects meet these search criteria</td></tr>
                @endforelse
            </table>
        </div>
    </div>
</div>
@endsection