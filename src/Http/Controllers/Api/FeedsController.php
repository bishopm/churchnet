<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Group;
use Bishopm\Churchnet\Models\Meeting;
use Bishopm\Churchnet\Models\Dailyreading;
use Bishopm\Churchnet\Models\Feed;
use Bishopm\Churchnet\Models\Feeditem;
use Bishopm\Churchnet\Models\Feedpost;
use Bishopm\Churchnet\Models\Feedable;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Reminder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Feeds;

class FeedsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function mysubscriptions(Request $request) {
        if ($request->state == false) {
            $feed = Feedable::where('feed_id',$request->feed_id)->where('feedable_id',$request->user_id)->where('feedable_type','Bishopm\Churchnet\Models\User')->first()->delete();
            return "Subscription deleted";
        } else {
            $feed = Feedable::create(['feed_id' => $request->feed_id, 'feedable_id' => $request->user_id, 'feedable_type' => 'Bishopm\Churchnet\Models\User']);
            return $feed;
        }
    }

    public function userfeed(Request $request)
    {
        if ($request->individual) {
            $user = User::where('individual_id', $request->individual)->first();
            if ($user) {
                $userid = $user->id;
            } else {
                $userid = '';    
            }
        } else {
            $userid = '';
        }
        $feeds = Feed::orderBy('title')->get();
        $society = Society::with('circuit.district')->find($request->society);
        if ($society) {
            $allfeeds = Feedable::with('feed')->where('feedable_id', $society->id)->where('feedable_type', 'Bishopm\Churchnet\Models\Society')
                ->orWhere('feedable_id', $society->circuit_id)->where('feedable_type', 'Bishopm\Churchnet\Models\Circuit')
                ->orWhere('feedable_id', $society->circuit->district_id)->where('feedable_type', 'Bishopm\Churchnet\Models\District')
                ->orWhere('feedable_id', $userid)->where('feedable_type', 'Bishopm\Churchnet\Models\User')
                ->get();
            $myfeeds = array();
            foreach ($allfeeds->sortBy('feed.title') as $ff) {
                $myfeeds[$ff['feed']['id']] = $ff['feedable_type'];
                $feed = Feeds::make($ff['feed']['feedurl']);
                $thisfeed = array();
                $thisfeed['title'] = $ff['feed']['title'];
                $thisfeed['permalink'] = $feed->get_permalink();
                $thisfeed['logo'] = array('url'=>$feed->get_image_url(), 'width'=>$feed->get_image_width(), 'height'=>$feed->get_image_height());
                $thisfeed['items'] = array();
                foreach ($feed->get_items() as $item) {
                    if ($ff['feed']['frequency'] == "daily") {
                        $timeago = time() - (24 * 60 * 60);
                    } else {
                        $timeago = time() - (24 * 60 * 60 * 7);
                    }
                    $thisitem = array();
                    if ($item->get_date('U') > $timeago) {
                        $thisitem['body'] = $item->get_content();
                        $thisitem['title'] = $item->get_title();
                        $thisitem['image'] = $item->get_link();
                        $thisitem['description'] = $ff['feed']['description'];
                        $thisitem['author'] = $item->get_author()->name;
                        $thisitem['pubdate'] = Carbon::parse(date('D, d M Y H:i:s',strtotime($item->get_date())))->diffForHumans();
                        if ($ff['feed']['category'] == 'sermon') {
                            $thisitem['enclosure'] = $item->get_enclosure();
                        }
                        $thisfeed['items'][] = $thisitem;
                    }
                }
                if (count($thisfeed['items'])) {
                    $data[$ff['feed']['category']][] = $thisfeed;
                }
            }
            foreach ($feeds as $feed) {
                if (array_key_exists($feed->id, $myfeeds)) {
                    $feed->subs = $myfeeds[$feed->id];
                }
                $data['feeds'][] = $feed;
            }
            $data['events'] = Group::where('society_id', $society->id)->where('eventdatetime', '>', time())->get();
            $data['diary'] = Meeting::with('society:society,id')->where('meetable_type', 'Bishopm\Churchnet\Models\Society')->where('meetable_id', $society->id)->where('meetingdatetime', '>=', time())->where('meetingdatetime', '<=', time() + 24 * 60 * 60 * 10)
                ->orWhere('meetable_type', 'Bishopm\Churchnet\Models\Circuit')->where('meetable_id', $society->circuit->id)->where('meetingdatetime', '>=', time())->where('meetingdatetime', '<=', time() + 24 * 60 * 60 * 10)
                ->orWhere('meetable_type', 'Bishopm\Churchnet\Models\District')->where('meetable_id', $society->circuit->district->id)->where('meetingdatetime', '>=', time())->where('meetingdatetime', '<=', time() + 24 * 60 * 60 * 10)
                ->orderBy('meetingdatetime', 'ASC')->get();
            $data['diarycount'] = count($data['diary']);
            $this->monday = date("Y-m-d", strtotime('Monday this week'));
            $feeditems = Feeditem::monday($this->monday)->with('feedpost')
                ->where(function ($query) use ($society) {
                    $query->where('distributable_type', 'Bishopm\Churchnet\Models\Society')->where('distributable_id', $society->id)
                        ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\Circuit')->where('distributable_id', $society->circuit->id)
                        ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\District')->where('distributable_id', $society->circuit->district->id);
                })->get();
            foreach ($feeditems as $item) {
                if ($item->distributable_type == "Bishopm\Churchnet\Models\District") {
                    $item->source = $society->circuit->district->district . " District";
                } elseif ($item->distributable_type == "Bishopm\Churchnet\Models\Circuit") {
                    $item->source = $society->circuit->circuit;
                } else {
                    $item->source = $society->society;
                }
                $data[$item->feedpost->category][] = $item;
            }    
        }
        $data['dailyreading'] = Dailyreading::with('readingplan')->where('readingplan_id',1)->first();
        $data['feeds'] = $feeds;
        $data['userid'] = $userid;
        $data['reminders'] = Reminder::where('user_id', $userid)->orderBy('created_at', 'DESC')->get();
        $data['remindercount'] = count($data['reminders']);
        return $data;
    }

    public function feedlibrary($society)
    {
        $data = array();
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
            if ($item->distributable_type == "Bishopm\Churchnet\Models\District") {
                $item->source = $this->dis->district . " District";
            } elseif ($item->distributable_type == "Bishopm\Churchnet\Models\Circuit") {
                $item->source = $this->cir->circuit;
            } else {
                $item->source = $this->soc->society;
            }
            $data[$item->feedpost->category][] = $item;
        }
        $tot = 0;
        if (isset($data['song'])) {
            $tot = $tot + count($data['song']);
            unset($data['song']);
        }
        if (isset($data['liturgy'])) {
            $tot = $tot + count($data['liturgy']);
            unset($data['liturgy']);
        }
        $data['songs'] = $tot;
        return $data;
    }

    public function hymns($society)
    {
        $data = array();
        $this->soc = Society::with('circuit.district')->find($society);
        $this->cir = $this->soc->circuit;
        $this->dis = $this->cir->district;
        $feeditems = Feeditem::where('library', 'yes')->with('feedpost')->whereHas('feedpost', function ($q) {
            $q->where('category', '=', 'song')->orWhere('category', '=', 'liturgy');
        })->where(function ($query) {
            $query->where('distributable_type', 'Bishopm\Churchnet\Models\Society')->where('distributable_id', $this->soc->id)
                ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\Circuit')->where('distributable_id', $this->cir->id)
                ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\District')->where('distributable_id', $this->dis->id);
        })->get();
        foreach ($feeditems as $item) {
            $data[$item->feedpost->category][$item->feedpost->title] = ['title' => $item->feedpost->title, 'id' => $item->feedpost_id];
        }
        if (isset($data['song'])) {
            asort($data['song']);
        }
        if (isset($data['liturgy'])) {
            asort($data['liturgy']);
        }
        return $data;
    }

    public function myfeeds(Request $request)
    {
        $feeds = Feeditem::with('feedpost', 'distributable')
            ->where(function ($query) use ($request) {
                $query->where('distributable_type', 'Bishopm\Churchnet\Models\Society')->whereIn('distributable_id', $request->societies)
                    ->orWhere('distributable_type', 'Bishopm\Churchnet\Models\Circuit')->whereIn('distributable_id', $request->circuits)
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
        $data = array();
        foreach ($feeds as $feed) {
            $data[strtotime($feed->feedpost->publicationdate)][] = $feed;
        }
        return array_reverse($data);
    }

    public function feedpost($id)
    {
        $post = Feedpost::with('feeditems')->find($id);
        $sss = array();
        $ccc = array();
        $ddd = array();
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
        $feedpost = Feedpost::create(
            [
                'category' => $request->post['category'],
                'title' => $request->post['title'],
                'body' => $request->post['body'],
                'publicationdate' => $request->post['publicationdate']
            ]
        );
        if (isset($request->circuits)) {
            foreach ($request->circuits as $circuit) {
                Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Circuit', 'distributable_id' => $circuit, 'library' => $request->post['library']]);
            }
            foreach ($request->societies as $society) {
                $testsoc = Society::where('id', $society)->whereIn('circuit_id', $request->circuits)->count();
                if (!$testsoc) {
                    Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Society', 'distributable_id' => $society, 'library' => $request->post['library']]);
                }
            }
        } elseif (isset($request->synod)) {
            Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Synod', 'distributable_id' => $request->synod, 'library' => 'yes']);
        }
        return "ok";
    }

    public function update(Request $request)
    {
        $feedpost = Feedpost::with('feeditems')->find($request->id);
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
            $testsoc = Society::where('id', $society)->whereIn('circuit_id', $request->circuits)->count();
            if (!$testsoc) {
                Feeditem::create(['feedpost_id' => $feedpost->id, 'distributable_type' => 'Bishopm\Churchnet\Models\Society', 'distributable_id' => $society, 'library' => $request->post['library']]);
            }
        }
        return "ok";
    }
}
