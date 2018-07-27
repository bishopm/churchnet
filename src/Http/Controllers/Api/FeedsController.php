<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Feeditem;
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
        $prayer=$feed->get_items()[0];
        $data['prayer']['title']=$prayer->get_title();
        $data['prayer']['content']=$prayer->get_content();
        $devotion=$feed->get_items()[1];
        $data['devotion']['title']=$devotion->get_title();
        $data['devotion']['content']=$devotion->get_content();
        return $data;
    }

    public function feeditems($society)
    {
        $this->soc = Society::with('circuit.district')->find($society);
        $this->cir = $this->soc->circuit;
        $this->dis = $this->cir->district;
        $this->monday = date("Y-m-d", strtotime('Monday this week'));
        $feeditems = Feeditem::with('feedpost')->where('publicationdate', '=', $this->monday)
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
            $data[$item->category][]=$item;
        }
        return $data;
    }
}
