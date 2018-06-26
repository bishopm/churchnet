{{ Form::bsHidden('circuit_id',$circuit) }}
{{ Form::bsText('title','Title','Title') }}
{{ Form::bsText('firstname','First name / Initials','First name / Initials') }}
{{ Form::bsText('surname','Surname','Surname') }}
{{ Form::bsText('phone','Cellphone','Cellphone') }}
<div class="form-group">
  <label for="status">Role</label>
  <select onchange="setStatus()" id="status" name="status" class="selectize">
    @foreach ($roles as $role)
      @if ($role=="leader")
        <option selected value="leader">Leader</option>
      @else
        <option value="{{$role}}">{{ucfirst($role)}}</option>
      @endif
    @endforeach
  </select>
</div>

<div id="ministerdiv" style="display:none">
  <div class="form-group">
    <label for="mtags">Status</label>
    <select name="mtags[]" class="selectize" multiple>
      @foreach ($ministertags as $mtag)
        <option>{{$mtag->name}}</option>
      @endforeach
    </select>
  </div>
</div>

<div id="preacherdiv" style="display:none">
  <div class="form-group">
    <label for="ptags">Status</label>
    <select name="ptags[]" class="selectize" multiple>
      @foreach ($preachertags as $ptag)
        <option>{{$ptag->name}}</option>
      @endforeach
    </select>
  </div>
  {{ Form::bsText('fullplan','Full plan (or trial)','Full plan (or trial)') }}
</div>

<div id="leaderdiv" style="display:none">
  <div class="form-group">
    <label for="society_id">Society</label>
    <select name="society_id" class="selectize">
      @foreach ($societies as $society)
        <option value="{{$society->id}}">{{$society->society}}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="ltags">Circuit leadership role/s (if applicable)</label>
    <select name="ltags[]" class="selectize" multiple>
      @foreach ($leadertags as $ltag)
        <option>{{$ltag->name}}</option>
      @endforeach
    </select>
  </div>
</div>
<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("leaderdiv").style.display="block";
  });
  function setStatus(){
    selected = document.getElementById('status').value;
    if (selected == 'leader') {
      document.getElementById("ministerdiv").style.display="none";
      document.getElementById("preacherdiv").style.display="none";
      document.getElementById("leaderdiv").style.display="block";
    } else if ((selected == 'preacher') || (selected == 'evangelist') || (selected == 'biblewoman')) {
      document.getElementById("ministerdiv").style.display="none";
      document.getElementById("leaderdiv").style.display="block";
      document.getElementById("preacherdiv").style.display="block";
    } else {
      document.getElementById("ministerdiv").style.display="block";
      document.getElementById("leaderdiv").style.display="none";
      document.getElementById("preacherdiv").style.display="none";
    }
  }
</script>