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

    public function sunday($date="")
    {
        if ($date=="") {
            $this->sunday = strtotime(date('Y-m-d', strtotime('sunday')));
        } else {
            $this->sunday = strtotime($date);
        }
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

    private function epiphany($yr)
    {
        return strtotime('first Sunday of January ' . $yr);
    }

    private function setUpArray()
    {
        $this->data[0]['date']=date("Y-m-d", $this->advent);
        $i=1;
        while (strtotime($this->data[$i-1]['date']) < $this->nextAdvent-604800) {
            $this->data[$i]['date']=date("Y-m-d", (604800+strtotime($this->data[$i-1]['date'])));
            $i++;
        }
        foreach ($this->data as $key=>$dat) {
            $this->weeks[$dat['date']]=$key;
        }
    }

    private function buildYear()
    {
        // Advent
        $this->data[0]['lection']=$this->reading->locate($this->lyear, 'Advent 1');
        $this->data[1]['lection']=$this->reading->locate($this->lyear, 'Advent 2');
        $this->data[2]['lection']=$this->reading->locate($this->lyear, 'Advent 3');
        $this->data[3]['lection']=$this->reading->locate($this->lyear, 'Advent 4');

        $this->data[4]['lection']=$this->reading->locate($this->lyear, 'First Sunday after Christmas');
        $this->data[5]['lection']=$this->reading->locate($this->lyear, 'Epiphany');
        if (substr(date('Y-m-d', $this->epiphany($this->yr)), 9, 1)<>'7') {
            $this->data[6]['lection']=$this->reading->locate($this->lyear, 'First Sunday after the Epiphany (Ordinary 1)');
            $ndx=7;
        } else {
            $ndx=6;
        }
        $this->data[$ndx]['lection']=$this->reading->locate($this->lyear, 'Second Sunday after the Epiphany (Ordinary 2)');

        // Lent
        $eastersunday = DB::table('eastersundays')->whereRaw('SUBSTRING(eastersunday, 1,  4) = '.$this->yr)->first()->eastersunday;
        $this->data[$this->weeks[$eastersunday]]['lection']=$this->reading->locate($this->lyear, 'Easter Sunday');
        $this->data[-1+$this->weeks[$eastersunday]]['lection']=$this->reading->locate($this->lyear, 'Fifth Sunday in Lent');
        $this->data[-2+$this->weeks[$eastersunday]]['lection']=$this->reading->locate($this->lyear, 'Fourth Sunday in Lent');
        $this->data[-3+$this->weeks[$eastersunday]]['lection']=$this->reading->locate($this->lyear, 'Third Sunday in Lent');
        $this->data[-4+$this->weeks[$eastersunday]]['lection']=$this->reading->locate($this->lyear, 'Second Sunday in Lent');
        $this->data[-5+$this->weeks[$eastersunday]]['lection']=$this->reading->locate($this->lyear, 'First Sunday in Lent');
        $this->testData();
    }

    private function testData()
    {
        $fin ="<h1>" . $this->yr . "</h1><table>";
        foreach ($this->data as $key=>$dat) {
            $fin.= "<tr>";
            $fin.= "<td>" . $key . "</td>";
            $fin.= "<td>" . $dat['date'] . "</td>";
            if (isset($dat['lection'])) {
                $fin.= "<td>" . $dat['lection']['description'] . "</td>";
            }
            $fin.= "</tr>";
        }
        $fin.= "</table>";
        echo $fin;
    }
}
