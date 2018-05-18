{{ Form::bsText('circuit','Circuit name','Circuit name') }}
{{ Form::bsText('circuitnumber','Circuit number','Circuit number') }}
<div class="form-group" id="districts">
    <label for="Districts" class="control-label">Districts</label>
	<select class="form-control" name="district_id">
        <option></option>
        @foreach ($districts as $district)
            <option value="{{$district->id}}">{{$district->id}}00 {{$district->district}}</option>
        @endforeach
    </select>
</div>
{{ Form::bsText('office_contact','Circuit office contact details','Circuit office contact details') }}