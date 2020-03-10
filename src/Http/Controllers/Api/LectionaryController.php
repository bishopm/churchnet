<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Repositories\ReadingsRepository;
use Bishopm\Churchnet\Models\Cache;
use Bishopm\Churchnet\Models\Customreading;
use Bishopm\Churchnet\Models\Readingplan;
use Bishopm\Churchnet\Models\Dailyreading;
use Bishopm\Churchnet\Models\Denomination;
use Bishopm\Churchnet\Models\Setting;
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

    public function sunday($society="",$date="")
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
        if ($society) {
            $res['customreadings'] = explode(';',Customreading::where('servicedate',$fin['date'])->where('society_id',$society)->first()->customreading);
        }
        $res['description']=$fin['lection']['description'] . ' [' . $fin['lection']['year'] . '] - ' . $fin['lection']['colour'];
        $res['readings']=explode(';', $fin['lection']['readings']);
        $res['colour']=$fin['lection']['colour'];
        $res['denominations']=Denomination::orderBy('denomination')->get();
        $res['version']=Setting::where('setting_key','journey_version')->first()->setting_value;
        $res['updatenotes']=Setting::where('setting_key','journey_updatenotes')->first()->setting_value;
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

    private function fixbook($book) {
        $bookonly = substr($book,0,strrpos($book," "));
        $books = array(
            'Genesis'=>'GEN','Exodus'=>'EXO','Leviticus'=>'LEV','Numbers'=>'NUM','Deuteronomy'=>'DEU',
            'Joshua'=>'JOS','Judges'=>'JDG','Ruth'=>'RUT','1 Samuel'=>'1SA','2 Samuel'=>'2SA',
            '1 Kings'=>'1KI','2 Kings'=>'2KI','1 Chronicles'=>'1CH','2 Chronicles'=>'2CH','Ezra'=>'EZR',
            'Nehemiah'=>'NEH','Esther'=>'EST','Job'=>'JOB','Psalm'=>'PSA','Proverbs'=>'PRO',
            'Ecclesiastes'=>'ECC','Song of Songs'=>'SNG','Isaiah'=>'ISA','Jeremiah'=>'JER',
            'Lamentations'=>'LAM','Ezekiel'=>'EZK','Daniel'=>'DAN','Hosea'=>'HOS','Joel'=>'JOL',
            'Amos'=>'AMO','Obadiah'=>'OBA','Jonah'=>'JON','Micah'=>'MIC','Nahum'=>'NAM',
            'Habakkuk'=>'HAB','Zephaniah'=>'ZEP','Haggai'=>'HAG','Zechariah'=>'ZEC','Malachi'=>'MAL',
            'Matthew'=>'MAT','Mark'=>'MRK','Luke'=>'LUK','John'=>'JHN','Acts'=>'ACT','Romans'=>'ROM',
            '1 Corinthians'=>'1CO','2 Corinthians'=>'2CO','Galatians'=>'GAL','Ephesians'=>'EPH',
            'Philippians'=>'PHP','Colossians'=>'COL','1 Thessalonians'=>'1TH','2 Thessalonians'=>'2TH',
            '1 Timothy'=>'1TI','2 Timothy'=>'2TI','Titus'=>'TIT','Philemon'=>'PHM','Hebrews'=>'HEB',
            'James'=>'JAS','1 Peter'=>'1PE','2 Peter'=>'2PE','1 John'=>'1JN','2 John'=>'2JN',
            '3 John'=>'3JN','Jude'=>'JUD','Revelation'=>'REV'
        );
        return $books[$bookonly];
    }

    public function reading($reading, $translation)
    {
        $this->translation = $translation;
        $readingset=explode(' or ', trim(urldecode($reading)));
        $readings=array();
        foreach ($readingset as $thisreading) {
            // This breaks up each contiguous section
            $passages=explode(",",$thisreading);
            $book = $this->fixbook($thisreading);
            if (strpos($thisreading,":")) {
                $chapsection = substr($thisreading,0,strpos($thisreading,":"));
                $chapter = substr($chapsection,1+strrpos($chapsection," "));
            } else {
                $chapter = substr($thisreading,1+strrpos($thisreading," "));
            }
            $passages[0]= substr($passages[0],1+strrpos($passages[0]," "));
            foreach ($passages as $ndx=>$passage){
                $optional="";
                if (substr($passage, 0, 1)=="[") {
                    $optional='*';
                }
                $reading = str_replace(":",".",$reading);
                if (substr($passage, -1)=="]") {
                    $optional='';
                }
                $passage = str_replace("a","",$passage);
                $passage = str_replace("b","",$passage);
                $passage = str_replace("c","",$passage);
                // Add abbreviated book
                if (!strpos($passage,":")){
                    if ($ndx > 0){
                        $passage = $book . "." . $chapter . "." . $passage;
                        $passage = str_replace("-","-" . $book . "." . $chapter . ".",$passage);
                    } else {
                        $passage = $book . "." . $chapter;
                    }
                } else {
                    $chapter = substr($passage,0,strpos($passage,":"));
                    $passage = $book . "." . $passage;
                    if (substr_count($passage,":")>1){
                        $passage = str_replace("-","-" . $book . ".",$passage);
                    } else {
                        $passage = str_replace("-","-" . $book . "." . $chapter . ".",$passage);
                    }
                }
                $passage=str_replace(":",".",$passage);
                $readings[$thisreading]['passages'][] = $optional . $passage;
            }
            $readings[$thisreading] = $this->fetchReading($thisreading,$readings[$thisreading]['passages']);
        }
        return array('texts'=>$readings,'titles'=>$readingset);
    }

    private function fetchReading($reading,$queries)
    {
        $cache=Cache::where('ndx', $reading)->where('translation', $this->translation)->first();
        if ($cache) {
            return json_decode($cache->cached);
        } else {
            $client = new Client(['headers'=>['api-key'=>'0e2c41d04752c8243d12d20ca342df1d']]);
            if ($this->translation == 'CEV') {
                $copyright="Contemporary English Version © American Bible Society. All rights reserved. ";
                $url = "https://api.scripture.api.bible/v1/bibles/555fef9a6cb31151-01/verses/";
            } elseif ($this->translation == 'KJV') {
                $copyright="King James (Authorized) Version. All rights reserved. ";
                $url = "https://api.scripture.api.bible/v1/bibles/de4e12af7f28f599-01/verses/";
            } elseif ($this->translation == 'ASV') {
                $copyright="American Standard Version. © American Bible Society ";
                $url = "https://api.scripture.api.bible/v1/bibles/06125adad2d5898a-01/verses/";
            } else {
                $copyright="Good News Translation® (Today’s English Version, Second Edition) © 1992 American Bible Society. All rights reserved. ";
                $url = "https://api.scripture.api.bible/v1/bibles/61fd76eafa1577c2-02/verses/";
            }
            try {
                $fin['title']=$reading;
                $fin['copyright']=$copyright . "Revised Common Lectionary Readings, copyright © 2005 Consultation on Common Texts. <a target=\"_blank\" href=\"http://www.commontexts.org\">www.commontexts.org</a>";
                $fin['text']=array();
                foreach ($queries as $query) {
                    if (strpos($query, '[')!==false) {
                        $dum['type']="optional";
                    } else {
                        $dum['type']="required";
                    }
                    $query=str_replace('[', '', $query);
                    $query=str_replace(']', '', $query);
                    if (!strpos($reading,":")){
                        $dum['section']=json_decode($client->request('GET', str_replace("verses","chapters",$url) . $query . "?include-chapter-numbers=true")->getBody()->getContents())->data->content . " ";
                    } else {
                        $dum['section']=json_decode($client->request('GET', $url . $query . "?include-chapter-numbers=true")->getBody()->getContents())->data->content . " ";
                    }
                    $fin['text'][]=$dum;
                }
                if (count($fin['text'])) {
                    $newcache = Cache::create(['ndx' => $reading, 'cached'=>json_encode($fin), 'translation'=>$this->translation]);
                }
            } catch (GuzzleException $e) {
                $dum['text'] = "Sorry - we're not able to access this Bible translation at the moment, please try again later";
                return $dum;
            }
            return $fin;
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
