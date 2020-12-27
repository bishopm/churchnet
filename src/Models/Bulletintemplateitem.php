<?php

namespace Bishopm\Churchnet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bulletintemplateitem extends Model
{
    protected $guarded = array('id');

    public function bulletintemplate()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Bulletintemplate');
    }

    public function bulletinwidget()
    {
        return $this->belongsTo('Bishopm\Churchnet\Models\Bulletinwidget');
    }
}
