{{ Form::bsText('setting_key','Setting key','Setting key') }}
{{ Form::bsText('setting_value','Setting value','Setting value') }}
<div class="form-group">
    <label for="Level" class="control-label">Level</label>
	<select class="form-control" onchange="levelchange(this);" name="level">
        <option selected value="Connexion">Connexion</option>
        <option value="Bishopm\Churchnet\Models\District">District</option>
        <option value="Bishopm\Churchnet\Models\Circuit">Circuit</option>
    </select>
</div>
<div class="form-group" id="districts" style="display:none;">
    <label for="Districts" class="control-label">Districts</label>
	<select class="form-control" name="district_id">
        <option></option>
        @foreach ($districts as $district)
            <option value="{{$district->id}}">{{$district->id}}00 {{$district->district}}</option>
        @endforeach
    </select>
</div>
<div class="form-group" id="circuits" style="display:none;">
    <label for="Circuits" class="control-label">Circuits</label>
	<select class="form-control" name="circuit_id">
        <option></option>
        @foreach ($circuits as $circuit)
            <option value="{{$circuit->id}}">{{$circuit->circuitnumber}} {{$circuit->circuit}}</option>
        @endforeach
    </select>
</div>