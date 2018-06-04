<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ReadingsRepository;
use Bishopm\Churchnet\Models\Cache;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

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
        $fin = $this->buildYear();
        $res['date']=date("j F Y", strtotime($fin['date']));
        $res['description']=$fin['lection']['description'] . ' [' . $fin['lection']['year'] . ']';
        $res['readings']=explode(';', $fin['lection']['readings']);
        return $res;
    }

    public function wholeYear()
    {
        $this->sunday = strtotime(date('Y-m-d', strtotime('sunday')));
        $this->lectionaryYear();
        $this->setUpArray();
        $this->buildYear();
        return $this->data;
    }

    public function reading($reading, $translation)
    {
        $this->translation = $translation;
        $reading = urldecode($reading);
        if ((strpos($reading, ',')>1) or (strpos($reading, '[')>1)) {
            $base = explode(':', $reading)[0] . ":";
            $book=substr($base, 0, strrpos($base, ' '));
            $chapter=substr($base, 1+strrpos($base, ' '), -1);
            $remainder = substr($reading, 1+strpos($reading, ':'));
            $sections = array_filter(preg_split('/[,[]+/', $remainder));
            foreach ($sections as $section) {
                if (strpos($section, ':')!==false) {
                    $chapter = substr($section, 0, strpos($section, ':'));
                    $section = substr($section, 1+strpos($section, ':'));
                }
                $dum = $book . " " . $chapter . ":" . $section;
                $readings[]=$this->fetchReading($base . $section);
            }
        } else {
            $readings[]=$this->fetchReading($reading);
        }
        return $readings;
    }

    private function fetchReading($reading)
    {
        $reading=trim($reading);
        if (substr($reading, -1)=="]") {
            $reading=substr($reading, 0, strlen($reading)-1);
            $dum['type']="optional";
        } else {
            $dum['type']="required";
        }
        $cache=Cache::where('ndx', $reading)->where('translation', $this->translation)->first();
        if ($cache) {
            return json_decode($cache->cached);
        } else {
            $api_secret='DE3446OVkzT6ASUVyr5iNeoTNbEuZwkPO4Wj1dft';
            $client = new Client(['auth' => [$api_secret,'']]);
            $query = 'https://bibles.org/v2/passages.js?q[]=' . urlencode($reading) . '&version=' . $this->translation;
            $response=json_decode($client->request('GET', $query)->getBody()->getContents(), true);
            $dum['reading']=$reading;
            $dum['text']=$response['response']['search']['result']['passages'][0]['text'];
            $dum['copyright']="Good News Bible. Scripture taken from the Good News Bible (Today's English Version Second Edition, UK/British Edition). Copyright © 1992 British & Foreign Bible Society. Used by permission. Revised Common Lectionary Daily Readings, copyright © 2005 Consultation on Common Texts. <a target=\"_blank\" href=\"http://www.commontexts.org\">www.commontexts.org</a>";
            $newcache = Cache::create(['ndx' => $reading, 'cached'=>json_encode($dum), 'translation'=>$this->translation]);
            $dum['source']="API";
            return $dum;
        }
    }

    private function lectionaryYear()
    {
        $yrs=array('a','b','c');
        $this->yr=date('Y', $this->sunday);
        $this->adventThisYear = $this->adventOne($this->yr);
        $ndx = $this->yr % 3;
        $this->advent=$this->adventThisYear;
        $this->nextAdvent=$this->adventOne($this->yr+1);
        $this->easteryr = $this->yr+1;
        if ($this->sunday < $this->adventThisYear) {
            $this->easteryr = $this->yr;
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
        $this->data[0]['lection']=$this->reading->findByDesc($this->lyear, 'Advent 1');
        $this->data[1]['lection']=$this->reading->findByDesc($this->lyear, 'Advent 2');
        $this->data[2]['lection']=$this->reading->findByDesc($this->lyear, 'Advent 3');
        $this->data[3]['lection']=$this->reading->findByDesc($this->lyear, 'Advent 4');
        $this->data[4]['lection']=$this->reading->findByDesc($this->lyear, 'First Sunday after Christmas Day');
        $this->data[5]['lection']=$this->reading->findByDesc($this->lyear, 'Epiphany Sunday');
        // Lent
        $eastersunday = DB::table('eastersundays')->whereRaw('SUBSTRING(eastersunday, 1,  4) = '.$this->easteryr)->first()->eastersunday;
        $this->data[$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Resurrection of the Lord - Easter Day');
        $this->data[-1+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Fifth Sunday in Lent');
        $this->data[-2+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Fourth Sunday in Lent');
        $this->data[-3+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Third Sunday in Lent');
        $this->data[-4+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Second Sunday in Lent');
        $this->data[-5+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'First Sunday in Lent');
        $this->data[-6+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Transfiguration Sunday');
        for ($i=6;$i<$this->weeks[$eastersunday]-6;$i++) {
            $this->data[$i]['lection']=$this->reading->findByDate($this->lyear, $this->data[$i]['date']);
        }
        $this->data[1+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Second Sunday of Easter');
        $this->data[2+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Third Sunday of Easter');
        $this->data[3+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Fourth Sunday of Easter');
        $this->data[4+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Fifth Sunday of Easter');
        $this->data[5+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Sixth Sunday of Easter');
        $this->data[6+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Seventh Sunday of Easter');
        $this->data[7+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Day of Pentecost');
        $this->data[8+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Trinity Sunday');
        $trinitysunday = $this->data[8+$this->weeks[$eastersunday]]['date'];
        $sundayaftertrinity = date("Y-m-d", strtotime($trinitysunday) + 604800);
        $weekaftertrinity = $this->weeks[$sundayaftertrinity];
        for ($i=$weekaftertrinity;$i<count($this->weeks);$i++) {
            $this->data[$i]['lection']=$this->reading->findByDate($this->lyear, $this->data[$i]['date']);
        }
        $week = $this->weeks[date("Y-m-d", $this->sunday)];
        return $this->data[$week];
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
                $fin.= "<td>" . $dat['lection']['readings'] . "</td>";
            }
            $fin.= "</tr>";
        }
        $fin.= "</table>";
        echo $fin;
    }
}
