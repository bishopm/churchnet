@if (Auth::user()->level == "admin")
    {{ Form::bsText('circuit','Circuit name','Circuit name',$circuit->circuit) }}
    {{ Form::bsText('circuitnumber','Circuit number','Circuit number',$circuit->circuitnumber) }}
    <div class="form-group" id="districts">
        <label for="Districts" class="control-label">Districts</label>
        <select class="form-control" name="district_id">
            <option></option>
            @foreach ($districts as $district)
                @if ($circuit->district_id==$district->id)
                    <option selected value="{{$district->id}}">{{$district->id}}00 {{$district->district}}</option>
                @else
                    <option value="{{$district->id}}">{{$district->id}}00 {{$district->district}}</option>
                @endif
            @endforeach
        </select>
    </div>
    {{ Form::bsText('slug','Slug','Slug',$circuit->slug) }}
@endif
<h5>{{$circuit->circuit}}</h5>
<h5>{{$circuit->circuitnumber}}</h5>
<h5>{{$circuit->district->district}} District</h5>
{{ Form::bsText('office_contact','Circuit office contact details','Circuit office contact details',$circuit->office_contact) }}