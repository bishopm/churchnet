@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3>{{$tag}}</h3>
            <table class="table table-striped table-sm table-responsive">
                <tr><th colspan="2">Resources</th></tr>
                @forelse ($resources as $resource)
                    <tr><td><a href="{{route('resources.show',$resource->id)}}">{{$resource->title}}</a></td><td>{{$resource->description}}</td></tr>
                @empty
                    <tr><td colspan="2">There are no resources with this subject tag</td></tr>
                @endforlse
                <tr><th colspan="2">Pages</th></tr>
                @forelse ($pages as $page)
                    <tr><td colspan="2"><a href="{{route('pages.show',$page->id)}}">{{$page->title}}</a></td></tr>
                @empty
                    <tr><td colspan="2">There are no pages with this subject tag</td></tr>
                @endforlse
            </table>
        </div>
    </div>
</div>
@endsection