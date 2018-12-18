<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Meeting;
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
        $data['devotion']['pubdate']=date("d M Y", 24*3600 + strtotime($devotion->get_date()));
        return $data;
    }

    public function feeditems($society)
    {
        $data=array();
        $this->soc = Society::with('circuit.district')->find($society);
        $this->cir = $this->soc->circuit;
        $this->dis = $this->cir->district;
        $data['diary'] = Meeting::with('society')->where('meetable_type', 'Bishopm\Churchnet\Models\Society')->where('meetable_id', $this->soc->id)->where('meetingdatetime', '>=', time())->where('meetingdatetime', '<=', time() + 24*60*60*10)
                            ->orWhere('meetable_type', 'Bishopm\Churchnet\Models\Circuit')->where('meetable_id', $this->cir->id)->where('meetingdatetime', '>=', time())->where('meetingdatetime', '<=', time() + 24*60*60*10)
                            ->orWhere('meetable_type', 'Bishopm\Churchnet\Models\District')->where('meetable_id', $this->dis->id)->where('meetingdatetime', '>=', time())->where('meetingdatetime', '<=', time() + 24*60*60*10)->get();
        $data['diarycount']=count($data['diary']);
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
            foreach ($feed->get_items() as $item) {
                $itype=$item->get_description();
                $dum['title']=$item->get_title();
                $dum['content']=$item->get_content();
                $dum['author']=$item->get_author();
                $dum['pubdate']=date("d M Y", strtotime($item->get_date()));
                $dum['image']=$item->get_link();
                $data[$itype][]=$dum;
            }
        }
        return $data;
    }

    public function feedlibrary($society)
    {
        $data=array();
        $this->soc = Society::with('circuit.district')->find($society);
        $this->cir = $this->soc->circuit;
        $this->dis = $this->cir->district;
        $feeditems = Feeditem::where('library', 'yes')->with('feedpost')
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
        return $data;
    }

    public function myfeeds(Request $request)
    {
        $feeds = Feeditem::with('feedpost', 'distributable')
        ->where(function ($query) use ($request) {
            $query->where('distributable_type', 'Bishopm\Churchnet\Models\Society')->whereIn('distributable_id', $request->societies)
                  ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\Circuit')->where('distributable_id', $request->circuits)
                  ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\District')->where('distributable_id', $request->districts);
        })->has('feedpost')->get();
        foreach ($feeds as $feed) {
            if ($feed->distributable_type == 'Bishopm\Churchnet\Models\Society') {
                $feed->entity = $feed->distributable->society;
            } elseif ($feed->distributable_type == 'Bishopm\Churchnet\Models\Circuit') {
                $feed->entity = $feed->distributable->circuit;
            } elseif ($feed->distributable_type == 'Bishopm\Churchnet\Models\District') {
                $feed->entity = $feed->distributable->district;
            }
        }
        $data=array();
        foreach ($feeds as $feed) {
            $data[strtotime($feed->feedpost->publicationdate)][]=$feed;
        }
        return $data;
    }

    public function feedpost($id)
    {
        $post = Feedpost::with('feeditems')->find($id);
        $sss=array();
        $ccc=array();
        $ddd=array();
        foreach ($post->feeditems as $feeditem) {
            if ($feeditem->distributable_type == 'Bishopm\Churchnet\Models\Society') {
                $sss[] = (string)$feeditem->distributable_id;
            } elseif ($feeditem->distributable_type == 'Bishopm\Churchnet\Models\Circuit') {
                $ccc[] = (string)$feeditem->distributable_id;
            } elseif ($feeditem->distributable_type == 'Bishopm\Churchnet\Models\District') {
                $ddd[] = (string)$feeditem->distributable_id;
            }
        }
        $post->societies = $sss;
        $post->circuits = $ccc;
        $post->districts = $ddd;
        return $post;
    }

    public function feeditem($id)
    {
        $post = Feeditem::with('feedpost')->find($id);
        return $post;
    }

    public function store(Request $request)
    {
        $feedpost=Feedpost::create(
            [
                'category' => $request->post['category'],
                'title' => $request->post['title'],
                'body' => $request->post['body'],
                'publicationdate' => $request->post['publicationdate']
            ]
        );
        foreach ($request->circuits as $circuit) {
            Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Circuit', 'distributable_id' => $circuit, 'library' => $request->post['library']]);
        }
        foreach ($request->societies as $society) {
            $testsoc=Society::where('id', $society)->whereIn('circuit_id', $request->circuits)->count();
            if (!$testsoc) {
                Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Society', 'distributable_id' => $society, 'library' => $request->post['library']]);
            }
        }
        return "ok";
    }

    public function update(Request $request)
    {
        $feedpost=Feedpost::with('feeditems')->find($request->id);
        $feedpost->publicationdate = $request->post['publicationdate'];
        $feedpost->body = $request->post['body'];
        $feedpost->title = $request->post['title'];
        $feedpost->category = $request->post['category'];
        $feedpost->save();
        // Delete previous linked feed items
        $feeditemclear = Feeditem::where('feedpost_id', $feedpost->id)->delete();
        // Add amended linked feed items
        foreach ($request->circuits as $circuit) {
            Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Circuit', 'distributable_id' => $circuit, 'library' => $request->post['library']]);
        }
        foreach ($request->societies as $society) {
            $testsoc=Society::where('id', $society)->whereIn('circuit_id', $request->circuits)->count();
            if (!$testsoc) {
                Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Society', 'distributable_id' => $society, 'library' => $request->post['library']]);
            }
        }
        return "ok";
    }
}
