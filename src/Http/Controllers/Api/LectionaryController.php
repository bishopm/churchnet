<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ReadingsRepository;
use Illuminate\Support\Facades\DB;

class LectionaryController extends Controller
{
    private $reading;

    public function __construct(ReadingsRepository $reading)
    {
        $this->reading = $reading;
    }

    public function sunday()
    {
        $this->sunday = strtotime(date('Y-m-d', strtotime('sunday')));
        $this->lectionaryYear();
        $this->setUpArray();
        $this->buildYear();
    }

    private function lectionaryYear()
    {
        $yrs=array('a','b','c');
        $this->yr=date('Y', $this->sunday);
        $this->adventThisYear = $this->adventOne($this->yr);
        $ndx = $this->yr % 3;
        $this->advent=$this->adventThisYear;
        $this->nextAdvent=$this->adventOne($this->yr+1);
        if ($this->sunday < $this->adventThisYear) {
            $ndx = $ndx -1;
            $this->advent=$this->adventOne($this->yr-1);
            $this->nextAdvent=$this->adventOne($this->yr);
        }
        if ($ndx < 0) {
            $ndx = 2;
        }
        $this->lyear = $yrs[$ndx];
    }

    private function adventOne($yr)
    {
        return strtotime('last Thursday of November ' . $yr . ' + 3 days');
    }

    private function setUpArray()
    {
        $this->data[0]['date']=date("Y-m-d", $this->advent);
        $i=1;
        while (strtotime($this->data[$i-1]['date']) < $this->nextAdvent-604800) {
            $this->data[$i]['date']=date("Y-m-d", (604800+strtotime($this->data[$i-1]['date'])));
            $i++;
        }
    }

    private function buildYear()
    {
        // Advent
        $this->data[0]['lection']=$this->reading->locate($this->lyear, 'Advent 1');
        $this->data[1]['lection']=$this->reading->locate($this->lyear, 'Advent 2');
        $this->data[2]['lection']=$this->reading->locate($this->lyear, 'Advent 3');
        $this->data[3]['lection']=$this->reading->locate($this->lyear, 'Advent 4');

        // Lent
        $x = array_search('2018-04-01', $this->data);
        dd($x);
        dd($this->data);
    }
}
