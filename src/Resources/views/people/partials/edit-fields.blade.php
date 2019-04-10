{{ Form::bsText('title','Title','Title',$person->individual->title) }}
{{ Form::bsText('firstname','First name / Initials','First name / Initials',$person->individual->firstname) }}
{{ Form::bsText('surname','Surname','Surname',$person->individual->surname) }}
{{ Form::bsText('phone','Cellphone','Cellphone',$person->individual->cellphone) }}
<div class="form-group">
  <label for="status">Role</label>
  <select onchange="setStatus()" id="status" name="status" class="selectize">
    @foreach ($roles as $role)
      @if ($person->status==$role)
        <option selected value="{{$role}}">{{ucfirst($role)}}</option>
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
        @if (in_array($mtag->name,$person->mtags))
          <option selected>{{$mtag->name}}</option>
        @else 
          <option>{{$mtag->name}}</option>
        @endif
      @endforeach
    </select>
  </div>
</div>

<div id="preacherdiv" style="display:none">
  <div class="form-group">
    <label for="ptags">Status</label>
    <select name="ptags[]" class="selectize" multiple>
      @foreach ($preachertags as $ptag)
        @if (in_array($ptag->name,$person->ptags))
          <option selected>{{$ptag->name}}</option>
        @else
          <option>{{$ptag->name}}</option>
        @endif
      @endforeach
    </select>
  </div>
  {{ Form::bsText('inducted','Full plan (or trial)','Full plan (or trial)',$person->inducted) }}
</div>

<div id="leaderdiv" style="display:none">
  <div class="form-group">
    <label for="society_id">Society</label>
    <select name="society_id" class="selectize">
      @foreach ($societies as $society)
        @if ($person->society_id==$society->id)
          <option selected value="{{$society->id}}">{{$society->society}}</option>
        @else
          <option value="{{$society->id}}">{{$society->society}}</option>
        @endif
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="ltags">Circuit leadership role/s (if applicable)</label>
    <select name="ltags[]" class="selectize" multiple>
      @foreach ($leadertags as $ltag)
        @if (in_array($ltag->name,$person->ltags))
          <option selected>{{$ltag->name}}</option>
        @else
          <option>{{$ltag->name}}</option>
        @endif
      @endforeach
    </select>
  </div>
</div>
<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(){
    if (('{{$person->status}}' == 'preacher') || ('{{$person->status}}' == 'evangelist') || ('{{$person->status}}' == 'biblewoman')){
      document.getElementById("leaderdiv").style.display="block";
      document.getElementById("preacherdiv").style.display="block";      
    }
    if (('{{$person->status}}' == 'minister') || ('{{$person->status}}' == 'deacon')){
      document.getElementById("ministerdiv").style.display="block";
    }
    if ('{{$person->status}}' == 'leader') {
      document.getElementById("leaderdiv").style.display="block";
    }
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