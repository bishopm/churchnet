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
{{ Form::bsText('office_contact','Circuit office contact details','Circuit office contact details',$circuit->office_contact) }}