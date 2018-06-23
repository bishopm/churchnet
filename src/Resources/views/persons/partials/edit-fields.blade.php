<div class="text-center border border-primary rounded" style="padding: 10px; margin-bottom:10px">
  <div class="form-check form-check-inline">
    <input onchange="showpreacher()" class="form-check-input" type="radio" name="status" id="inlineRadio1" value="preacher"
    @if ($status == "preacher")
      checked
    @endif
    >
    <label class="form-check-label" for="inlineRadio1">Local preacher</label>
  </div>
  <div class="form-check form-check-inline">
    <input onchange="showminister()" class="form-check-input" type="radio" name="status" id="inlineRadio2" value="minister"
    @if ($status == "minister")
      checked
    @endif
    >
    <label class="form-check-label" for="inlineRadio2">Minister / Deacon / Biblewoman / Evangelist</label>
  </div>
  <div class="form-check form-check-inline">
    <input onchange="showleader()" class="form-check-input" type="radio" name="status" id="inlineRadio3" value="leader"
    @if ($status == "leader")
      checked
    @endif
    >
    <label class="form-check-label" for="inlineRadio3">Leader (not a preacher, minister etc)</label>
  </div>
</div>
{{ Form::bsText('title','Title','Title',$person->title) }}
{{ Form::bsText('firstname','First name / Initials','First name / Initials',$person->firstname) }}
{{ Form::bsText('title','Surname','Surname',$person->surname) }}
{{ Form::bsText('phone','Cellphone','Cellphone',$person->phone) }}
<div id="preacherdiv" style="display:none">
  @if ($person->preacher)
    {{ Form::bsText('fullplan','Full plan (or trial)','Full plan (or trial)',$person->preacher->fullplan) }}
  @else
    <div class="form-group">
      <label for="society">Society</label>
      <select name="society" class="selectize">
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
      <label for="positions">Circuit leadership role/s</label>
      <select name="positions[]" class="selectize" multiple>
        @foreach ($leadertags as $ltag)
          <option value="{{$ltag->id}}">{{$ltag->name}}</option>
        @endforeach
      </select>
    </div>
    <div class='form-group '>
      <label for="positions">Status</label>
      <select name="positions[]" class="selectize" multiple>
        @foreach ($preachertags as $ptag)
          <option value="{{$ptag->id}}">{{$ptag->name}}</option>
        @endforeach
      </select>
    </div>
    {{ Form::bsText('fullplan','Full plan (or trial)','Full plan (or trial)') }}
  @endif
</div>
<div id="ministerdiv" style="display:none">
  <div class="form-group">
    <label for="positions">Status</label>
    <select name="positions[]" class="selectize" multiple>
      @foreach ($ministertags as $mtag)
        <option value="{{$mtag->id}}">{{$mtag->name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div id="leaderdiv" style="display:none">
  <div class="form-group">
    <label for="society">Society</label>
    <select name="society" class="selectize">
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
    <label for="positions">Circuit leadership role/s</label>
    <select name="positions[]" class="selectize" multiple>
      @foreach ($leadertags as $ltag)
        <option value="{{$ltag->id}}">{{$ltag->name}}</option>
      @endforeach
    </select>
  </div>
</div>
<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(){
    if ('{{$status}}' == 'preacher'){
      showpreacher();
    }
    if ('{{$status}}' == 'minister'){
      showminister();
    }
    if ('{{$status}}' == 'leader'){
      showleader();
    }
  });
  function showpreacher() {
    document.getElementById("ministerdiv").style.display="none";
    document.getElementById("leaderdiv").style.display="none";
    document.getElementById("preacherdiv").style.display="block";
  }
  function showminister() {
    document.getElementById("ministerdiv").style.display="block";
    document.getElementById("leaderdiv").style.display="none";
    document.getElementById("preacherdiv").style.display="none";
  }
  function showleader() {
    document.getElementById("ministerdiv").style.display="none";
    document.getElementById("preacherdiv").style.display="none";
    document.getElementById("leaderdiv").style.display="block";
  }
</script>