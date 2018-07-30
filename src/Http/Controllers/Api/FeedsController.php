<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Feeditem;
use Bishopm\Churchnet\Models\Feedpost;
use Illuminate\Http\Request;
use SimplePie;

class FeedsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */


    public function ffdl()
    {
        $feed = new SimplePie();
        $feed->set_cache_location(storage_path() . '/simplepie_cache');
        $feed->handle_content_type();
        $feed->set_feed_url(array('http://faithfordailyliving.org'));
        $feed->init();
        $devotion=$feed->get_items()[1];
        $data['devotion']['title']=$devotion->get_title();
        $data['devotion']['content']=$devotion->get_content();
        $data['devotion']['pubdate']=date("d M Y",24*3600 + strtotime($devotion->get_date()));
        return $data;
    }

    public function feeditems($society)
    {
        $data=array();
        $this->soc = Society::with('circuit.district')->find($society);
        $this->cir = $this->soc->circuit;
        $this->dis = $this->cir->district;
        $this->monday = date("Y-m-d", strtotime('Monday this week'));
        $feeditems = Feeditem::monday($this->monday)->with('feedpost')
        ->where(function ($query) {
            $query->where('distributable_type', 'Bishopm\Churchnet\Models\Society')->where('distributable_id', $this->soc->id)
                  ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\Circuit')->where('distributable_id', $this->cir->id)
                  ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\District')->where('distributable_id', $this->dis->id);
        })->get();
        foreach ($feeditems as $item) {
            if ($item->distributable_type=="Bishopm\Churchnet\Models\District") {
                $item->source=$this->dis->district . " District";
            } elseif ($item->distributable_type=="Bishopm\Churchnet\Models\Circuit") {
                $item->source=$this->cir->circuit;
            } else {
                $item->source=$this->soc->society;
            }
            $data[$item->feedpost->category][]=$item;
        }
        if ($this->soc->journey) {
            $feed = new SimplePie();
            $feed->set_cache_location(storage_path() . '/simplepie_cache');
            $feed->handle_content_type();
            $feed->set_feed_url(array($this->soc->journey));
            $feed->init();
            foreach ($feed->get_items() as $item){
                $itype=$item->get_description();
                $dum['title']=$item->get_title();
                $dum['content']=$item->get_content();
                $dum['author']=$item->get_author();
                $dum['pubdate']=date("d M Y",strtotime($item->get_date()));
                $dum['image']=$item->get_link();
                $data[$itype][]=$dum;
            }
        }
        return $data;
    }

    public function store (Request $request)
    {
        $feedpost=Feedpost::create($request->post);
        foreach ($request->circuits as $circuit){
            Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Circuit', 'distributable_id' => $circuit]);
        }
        foreach ($request->societies as $society){
            $testsoc=Society::where('id',$society)->whereIn('circuit_id',$request->circuits)->count();
            if (!$testsoc){
                Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Society', 'distributable_id' => $society]);
            }
        }
        return "ok";
    }
}
