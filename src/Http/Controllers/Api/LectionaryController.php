<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ReadingsRepository;
use Bishopm\Churchnet\Models\Cache;
use Bishopm\Churchnet\Models\Readingplan;
use Bishopm\Churchnet\Models\Dailyreading;
use Bishopm\Churchnet\Models\Denomination;
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
        $res['description']=$fin['lection']['description'] . ' [' . $fin['lection']['year'] . '] - ' . $fin['lection']['colour'];
        $res['readings']=explode(';', $fin['lection']['readings']);
        $res['colour']=$fin['lection']['colour'];
        $res['denominations']=Denomination::orderBy('denomination')->get();
        if ($fin['lection']['description'] == "First Sunday in Lent") {
            $res['extras'][date("j F Y", strtotime($fin['date'])-4*24*3600)]=$this->reading->findByDesc($this->lyear, 'Ash Wednesday');
        } elseif ($fin['lection']['description'] == "Resurrection of the Lord - Easter Day") {
            $res['extras'][date("j F Y", strtotime($fin['date'])-6*24*3600)]=$this->reading->findByDesc($this->lyear, 'Holy Week Monday');
            $res['extras'][date("j F Y", strtotime($fin['date'])-5*24*3600)]=$this->reading->findByDesc($this->lyear, 'Holy Week Tuesday');
            $res['extras'][date("j F Y", strtotime($fin['date'])-4*24*3600)]=$this->reading->findByDesc($this->lyear, 'Holy Week Wednesday');
            $res['extras'][date("j F Y", strtotime($fin['date'])-3*24*3600)]=$this->reading->findByDesc($this->lyear, 'Maundy Thursday');
            $res['extras'][date("j F Y", strtotime($fin['date'])-2*24*3600)]=$this->reading->findByDesc($this->lyear, 'Good Friday');
        } elseif ($fin['lection']['description'] == "Seventh Sunday of Easter") {
            $res['extras'][date("j F Y", strtotime($fin['date'])-3*24*3600)]=$this->reading->findByDesc($this->lyear, 'Ascension Day');
        } elseif (($fin['lection']['description'] == "First Sunday after Christmas Day") and (substr($fin['date'], 5)<>"01-01")) {
            $res['extras'][date("Y", strtotime($fin['date'])) . "-12-24"]=$this->reading->findByDesc($this->lyear, 'Christmas Eve');
            $res['extras'][date("Y", strtotime($fin['date'])) . "-12-25"]=$this->reading->findByDesc($this->lyear, 'Christmas Day');
        } elseif ($fin['lection']['description'] == "Christmas Day") {
            $res['extras'][date("Y", strtotime($fin['date'])) . "-12-24"]=$this->reading->findByDesc($this->lyear, 'Christmas Eve');
        }

        return $res;
    }

    public function wholeYear($ldate="")
    {
        if ($ldate=="") {
            $this->sunday = strtotime(date('Y-m-d', strtotime('sunday')));
        } else {
            $this->sunday = strtotime($ldate);
        }
        $this->lectionaryYear();
        $this->setUpArray();
        $this->buildYear();
        return $this->data;
    }

    public function readingplans()
    {
        return Readingplan::orderBy('title')->get();
    }

    public function dailyreading ($plan,$id)
    {
        return Dailyreading::with('readingplan')->where('readingplan_id',$plan)->where('readingday',$id)->first();
    }

    public function reading($reading, $translation)
    {
        $this->translation = $translation;
        $reading = trim(urldecode($reading));
        $readingset=explode(' or ', $reading);
        foreach ($readingset as $thisreading) {
            $readings=array();
            if ((strpos($thisreading, ',')>0) or (strpos($thisreading, '[')!==false)) {
                $base = explode(':', $thisreading)[0] . ":";
                $book=substr($base, 0, strrpos($base, ' '));
                $chapter=substr($base, 1+strrpos($base, ' '), -1);
                $remainder = substr($thisreading, 1+strpos($thisreading, ':'));
                $sections = explode(',', $remainder);
                $optional='';
                foreach ($sections as $section) {
                    if (substr($section, 0, 1)=="[") {
                        $optional='*';
                    }
                    if (strpos($section, ':')!==false) {
                        // ie there is a change of chapter in this section
                        if (strpos($section, ':') < strpos($section, '-')) {
                            $chapter = substr($section, 0, strpos($section, ':'));
                            $section = substr($section, 1+strpos($section, ':'));
                            $readings[]=$this->fetchReading($optional . $book . ' ' . $chapter . ':' . $section);
                        } else {
                            $readings[]=$this->fetchReading($optional . $base . $section);
                            $chapter = substr($section, 1+strpos($section, '-'), strpos($section, ':')-1-strpos($section, '-'));
                        }
                    } else {
                        $readings[]=$this->fetchReading($optional . $base . $section);
                    }
                    //$readings[]=$optional . $base . $section;
                    if (substr($section, -1)=="]") {
                        $optional='';
                    }
                }
            } else {
                $readings[]=$this->fetchReading($thisreading);
            }
            $data[$thisreading]=$readings;
        }
        return $data;
    }

    private function fetchReading($reading)
    {
        $reading=str_replace('[', '', $reading);
        $reading=str_replace(']', '', $reading);
        if (strpos($reading, '*')!==false) {
            $reading=str_replace('*', '', $reading);
            $dum['type']="optional";
        } else {
            $dum['type']="required";
        }
        $cache=Cache::where('ndx', $reading)->where('translation', $this->translation)->first();
        if ($cache) {
            return json_decode($cache->cached);
        } else {
            $api_secret='DE3446OVkzT6ASUVyr5iNeoTNbEuZwkPO4Wj1dft';
            $client = new Client(['auth' => [$api_secret,''],'verify' => false]);
            $query = 'https://bibles.org/v2/passages.js?q[]=' . urlencode($reading) . '&version=' . $this->translation;
            try {
                $response=json_decode($client->request('GET', $query)->getBody()->getContents(), true);
                $dum['reading']=$reading;
                $dum['text']=$response['response']['search']['result']['passages'][0]['text'];
                if ($this->translation == "eng-MSG") {
                    $dum['copyright']="Scripture taken from The Message. Copyright © 1993, 1994, 1995, 1996, 2000, 2001, 2002. Used by permission of NavPress Publishing Group. ";
                } else {
                    $dum['copyright']="Good News Bible. Scripture taken from the Good News Bible (Today's English Version Second Edition, UK/British Edition). Copyright © 1992 British & Foreign Bible Society. Used by permission. ";
                }
                $dum['copyright'].= "Revised Common Lectionary Readings, copyright © 2005 Consultation on Common Texts. <a target=\"_blank\" href=\"http://www.commontexts.org\">www.commontexts.org</a>";
                $newcache = Cache::create(['ndx' => $reading, 'cached'=>json_encode($dum), 'translation'=>$this->translation]);
                $dum['source']="API";
            } catch (GuzzleException $e) {
                $dum['text'] = "Sorry - we're not able to access bibles.org at the moment, please try again later";
                return $dum;
            }
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
        if (substr($this->data[4]['date'], 5) == "12-25") {
            $this->data[4]['lection']=$this->reading->findByDesc($this->lyear, 'Christmas Day');
        } else {
            $this->data[4]['lection']=$this->reading->findByDesc($this->lyear, 'First Sunday after Christmas Day');
        }
        $this->data[5]['lection']=$this->reading->findByDesc($this->lyear, 'Epiphany Sunday');
        // Lent
        $eastersunday = DB::table('eastersundays')->whereRaw('SUBSTRING(eastersunday, 1,  4) = '.$this->easteryr)->first()->eastersunday;
        $this->data[$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Resurrection of the Lord - Easter Day');
        $this->data[-1+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Sixth Sunday in Lent');
        $this->data[-2+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Fifth Sunday in Lent');
        $this->data[-3+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Fourth Sunday in Lent');
        $this->data[-4+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Third Sunday in Lent');
        $this->data[-5+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Second Sunday in Lent');
        $this->data[-6+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'First Sunday in Lent');
        $this->data[-7+$this->weeks[$eastersunday]]['lection']=$this->reading->findByDesc($this->lyear, 'Transfiguration Sunday');
        for ($i=6;$i<$this->weeks[$eastersunday]-7;$i++) {
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
