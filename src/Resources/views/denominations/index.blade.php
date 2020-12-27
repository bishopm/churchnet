@extends('churchnet::templates.frontend')

@section('title','Churches')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="title">Churches</h1>
            <ul class="list-unstyled">
            @foreach ($denominations as $denomination)
                <li><a href="{{url('/')}}/churches/{{$denomination->slug}}">{{$denomination->denomination}}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection