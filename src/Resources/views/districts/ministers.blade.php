@extends('churchnet::templates.frontend')

@section('title',$title)

@section('css')
@stop


@section('content')
<div class="container mt-5">
    <h3>{{$title}}</h3>

        <table>
        @forelse ($ministers as $minister)
            <tr>
                <td>{!!$minister['name']!!}</b></td>
                <td>
                    @if (isset($minister['tags']))
                        {{implode(', ',$minister['tags'])}}
                    @endif
                </td>
                <td><a href="{{url('/')}}/circuits/{{$minister['circuit']['name']['slug']}}">{{$minister['circuit']['name']['circuit']}}</a></td>
            </tr>
        @empty
            No ministers have been added yet.
        @endforelse
        </table>

</div>
@endsection

@section('js')
@stop