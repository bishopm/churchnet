{{ Form::bsText('setting_key','Setting key','Setting key',$setting->setting_key) }}
{{ Form::bsText('setting_value','Setting value','Setting value',$setting->setting_value) }}
<div class="form-group">
    <label for="Level" class="control-label">Level</label>
	<select class="form-control" onchange="levelchange(this);" name="level">
        @if ($setting->relatable_type=="Bishopm\Churchnet\Models\Circuit")
            <option value="Connexion">Connexion</option>
            <option value="Bishopm\Churchnet\Models\District">District</option>
            <option selected value="Bishopm\Churchnet\Models\Circuit">Circuit</option>
        @elseif ($setting->relatable_type=="Bishopm\Churchnet\Models\District")
            <option value="Connexion">Connexion</option>
            <option selected value="Bishopm\Churchnet\Models\District">District</option>
            <option value="Bishopm\Churchnet\Models\Circuit">Circuit</option>
        @else
            <option selected value="Connexion">Connexion</option>
            <option value="Bishopm\Churchnet\Models\District">District</option>
            <option value="Bishopm\Churchnet\Models\Circuit">Circuit</option>
        @endif
    </select>
</div>
<div class="form-group" id="districts" style="display:none;">
    <label for="Districts" class="control-label">Districts</label>
	<select class="form-control" name="district_id">
        <option></option>
        @foreach ($districts as $district)
            @if ($setting->relatable_id==$district->id)
                <option selected value="{{$district->id}}">{{$district->id}}00 {{$district->district}}</option>
            @else
                <option value="{{$district->id}}">{{$district->id}}00 {{$district->district}}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group" id="circuits" style="display:none;">
    <label for="Circuits" class="control-label">Circuits</label>
	<select class="form-control" name="circuit_id">
        <option></option>
        @foreach ($circuits as $circuit)
            @if ($setting->relatable_id==$circuit->id)
                <option selected value="{{$circuit->id}}">{{$circuit->circuitnumber}} {{$circuit->circuit}}</option>
            @else
                <option value="{{$circuit->id}}">{{$circuit->circuitnumber}} {{$circuit->circuit}}</option>
            @endif
        @endforeach
    </select>
</div>