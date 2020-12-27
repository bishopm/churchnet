@extends('churchnet::templates.frontend')

@section('title',"Welcome to church.net.za - home of the Connexion App")

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3>{{$tag}}</h3>
            <table class="table table-striped table-sm table-responsive">
                <tr><th colspan="2">Resources</th><th>Subjects</th></tr>
                @forelse ($resources as $resource)
                    <tr><td><a href="{{route('resources.show',$resource->id)}}">{{$resource->title}}</a></td><td>{{$resource->description}}</td>
                        <td>
                        @forelse ($resource->tags as $rtag)
                            <span style="padding-left:5px;"><a href="{{url('/')}}/tag/{{$rtag->normalized}}">{{$rtag->name}}</a></span>
                        @empty
                        <span style="padding-left:5px;">&nbsp;</span>
                        @endforelse
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">There are no resources with this subject tag</td></tr>
                @endforelse
                @if (count($pages))
                    <tr><th colspan="2">Pages</th></tr>
                    @foreach ($pages as $page)
                        <tr><td colspan="3"><a href="{{route('pages.show',$page->id)}}">{{$page->title}}</a></td></tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
</div>
@endsection