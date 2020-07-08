<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\IndividualsRepository;
use Bishopm\Churchnet\Repositories\TagsRepository;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Circuit;
use Bishopm\Churchnet\Models\District;
use Bishopm\Churchnet\Models\Payment;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Chat;
use Bishopm\Churchnet\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cviebrock\EloquentTaggable\Models\Tag;
use Bishopm\Churchnet\Notifications\SlackNotification;

class IndividualsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $individual;
    private $tags;

    public function __construct(IndividualsRepository $individual, TagsRepository $tags)
    {
        $this->individual = $individual;
        $this->tags = $tags;
    }

    public function index()
    {
        return Individual::all();
    }

    public function addcombined(Request $request)
    {
        if ($request->title) {
            $addressee = $request->title . ' ' . $request->firstname . ' ' . $request->surname;
        } else {
            $addressee = $request->firstname . ' ' . $request->surname;
        }
        $household = Household::create(['addressee' => $addressee, 'society_id' => $request->society_id, 'sortsurname' => $request->surname]);
        if ($request->title) {
            $individual = Individual::create(['firstname' => $request->firstname, 'surname' => $request->surname, 'sex' => $request->sex, 'cellphone' => $request->phone, 'title' => $request->title, 'household_id' => $household->id]);
        } else {
            $individual = Individual::create(['firstname' => $request->firstname, 'surname' => $request->surname, 'sex' => $request->sex, 'cellphone' => $request->phone, 'household_id' => $household->id]);
        }
        $household->householdcell = $individual->id;
        $household->save();
        if ($request->adduser == 'yes') {
            $user = User::where('phone', $request->phone)->first();
            if ($user) {
                $user->individual_id = $individual->id;
                $user->name = $request->firstname . ' ' . $request->surname;
                $user->save();
            } else {
                $user = User::create([
                    'individual_id' => $individual->id,
                    'name' => $request->firstname . ' ' . $request->surname,
                    'phone' => $request->phone,
                    'phonetoken' => $request->phonetoken,
                    'lastaccess' => date('Y-m-d H:i:s'),
                    'level' => 5
                ]);
            }
            $admin=User::where('level',1)->first();
            $admin->notify(new SlackNotification($user->name . ' has just registered on the Journey App :)'));
        } else {
            $soc = Society::find($request->society_id);
            $individual->society = $soc->society;
        }
        return $individual;
    }

    public function givers($id)
    {
        $this->society = $id;
        $givers = Individual::where('giving', '<>', '0')->where('giving', '<>', '')->whereHas('household', function ($q) {
            $q->where('society_id', $this->society);
        })->select('giving','surname','firstname')->orderBy('giving')->get();
        $data = array();
        $dum = array();
        foreach ($givers as $giver) {
            if (!in_array($giver->giving, $dum)) {
                $dum[$giver->giving]['number'] = $giver->giving;
            }
            if (array_key_exists('name', $dum[$giver->giving])) {
                $dum[$giver->giving]['name']=$dum[$giver->giving]['name'] . ' and ' . $giver->firstname;
            } else {
                $dum[$giver->giving]['name']=$giver->surname . ", " . $giver->firstname;
            }
        }
        $data['givers'] = $dum;
        $data['society'] = Society::find($id)->society;
        return $data;
    }

    public function leaders($circuit)
    {
        $socs = Society::where('circuit_id', $circuit)->pluck('id')->toArray();
        $data = array();
        $data['individuals'] = Individual::societymember($socs)->with('tags')->whereHas('tags', function ($query) {
            $query->where('type', 'leader');
        })->get();
        $data['tags'] = Tag::where('type', 'leader')->orderBy('name')->get();
        return $data;
    }

    public function image($id, Request $request)
    {
        $file = $request->file('file');
        $image_name = time() . "_" . $id . '.jpg';
        $file->move(public_path() . '/vendor/bishopm/images/profile', $image_name);
        $indiv = Individual::find($id);
        if ($indiv->image) {
            $fname = public_path() . '/vendor/bishopm/images/profile/' . $indiv->image;
            if (file_exists($fname)) {
                unlink($fname);
            }
        }
        $indiv->image = $image_name;
        $indiv->save();
        return Individual::with('household.individuals', 'groups', 'household.society.circuit')->where('id', $id)->first();
    }

    public function editleaders(Request $request)
    {
        if ($request->leader['action'] == 'Add') {
            if ($request->addnew == false) {
                $indiv = Individual::with('tags')->where('id', $request->leader['individual_id'])->first();
            } else {
                $indiv = $this->addcombined(new Request($request->individual));
            }
            foreach ($request->leader['tags'] as $tagid) {
                $tag = Tag::find($tagid)->name;
                return array($indiv, $tag);
                $indiv->tag($tag);
            }
            return "Leader added";
        } else {
            $indiv = Individual::with('tags')->where('id', $request->leader['individual_id'])->first();
            foreach ($indiv->tags as $etag) {
                if ($etag->type == 'leader') {
                    $indiv->untag($etag->name);
                }
            }
            foreach ($request->leader['tags'] as $tagid) {
                $tag = Tag::find($tagid)->name;
                $indiv->tag($tag);
            }
            return "Leader updated";
        }
    }

    public function journeyadd(Request $request)
    {
        if ($request->action == "add") {
            $individual = Individual::Create($request->except('action', 'id'));
        } else {
            $individual = Individual::find($request->id);
            $this->individual->update($individual, $request->except('action'));
        }
        return "Individual added / updated";
    }

    public function checkphone(Request $request)
    {
        return Individual::select('firstname', 'surname', 'cellphone', 'email', 'id')->where('cellphone', $request->phone)->first();
    }

    public function phone(Request $request)
    {
        $data['individual'] = Individual::select('firstname', 'household_id', 'surname', 'cellphone', 'title', 'id')
            ->with('household.individuals:household_id,firstname,surname,cellphone,birthdate,sex,title,id,image,memberstatus,email', 'groups:groupname')
            ->where('cellphone', $request->phone)->first();
        if (!$data['individual']) {
            return "No individual";
        } elseif ((isset($request->society_id)) and ($data['individual']->household->society_id <> $request->society_id)) {
            return "Wrong society";
        }
        $data['society'] = $data['individual']->household->society;
        unset($data['society']['sms_pw']);
        unset($data['society']['sms_user']);
        unset($data['society']['giving_user']);
        unset($data['society']['giving_lag']);
        unset($data['society']['giving_reports']);
        unset($data['society']['birthday_group']);
        $gids = array();
        $user = User::where('individual_id', $data['individual']->id)->first();
        if ($user) {
            $user->last_access = date('Y-m-d H:i:s');
            $user->save();
        }
        foreach ($data['individual']->groups as $group) {
            $gids[] = $group->id;
        }
        return $data;
    }

    public function message($id)
    {
        $chat = Chat::with('messages.individual', 'chatable')->where('id', $id)->first();
        foreach ($chat->messages as $message) {
            $message->ago = Carbon::parse($message->created_at)->diffForHumans();
        }
        return $chat;
    }

    public function giving(Request $request)
    {
        $indiv = Individual::with('household.individuals')->find($request->id);
        $data = array();
        $data['name'] = $indiv->firstname . ' ' . $indiv->surname;
        $dum = array();
        $data['pg'] = intval($indiv->giving);
        if ($data['pg']) {
            $data['history'] = Payment::where('pgnumber', $data['pg'])->where('society_id', $request['society'])->get();
        }
        if ($indiv->giving !== 0) {
            $taken = Individual::societymember(array($request->society))->where('giving', '>', 0)->orderBy('giving')->select('giving')->get();
            foreach ($taken as $take) {
                if (!in_array(intval($take->giving), $dum)) {
                    $dum[] = intval($take->giving);
                }
            }
            sort($dum);
            $data['available'] = array();
            $val = 1;
            while (count($data['available']) < 20) {
                if (!in_array($val, $dum)) {
                    $data['available'][] = $val;
                }
                $val++;
            }
            $data['householdpg'] = array();
            foreach ($indiv->household->individuals as $ind) {
                if ($ind->giving > 0) {
                    if (array_key_exists($ind->giving, $data['householdpg'])) {
                        $data['householdpg'][$ind->giving] = $data['householdpg'][$ind->giving] . ', ' . $ind->firstname;
                    } else {
                        $data['householdpg'][$ind->giving] = $ind->firstname;
                    }
                }
            }
        }
        return $data;
    }

    public function updategiving(Request $request)
    {
        $indiv = Individual::find($request->id);
        $indiv->giving = $request->pgnumber['value'];
        $indiv->save();
        return "All done";
    }

    public function addmessage(Request $request)
    {
        $msg = Message::create([
            'individual_id' => $request->message['individual_id'],
            'chat_id' => $request->message['chat_id'],
            'chat' => $request->message['chat']
        ]);
        $newmsg = Message::with('individual')->find($msg->id);
        $newmsg->ago = Carbon::parse($newmsg->created_at)->diffForHumans();
        return $newmsg;
    }

    public function search(Request $request)
    {
        $this->search = $request->search;
        $socs = Society::where('circuit_id', $request->circuit)->pluck('id')->toArray();
        if (isset($request->circuit)) {
            return Individual::societymember($socs)->with('household.society')->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
            })->orderBy('surname')->orderBy('firstname')->get();
        } else {
            $socs = array($request->society);
            return Individual::societymember($socs)->with('household.society')->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
            })->orderBy('surname')->orderBy('firstname')->get();
        }
    }

    public function church($society)
    {
        $soc = array($society);
        return Individual::societymember($soc)->where('memberstatus', 'Member')->where('image', '<>', '')->orderBy('surname')->orderBy('firstname')->get();
    }

    public function searchnonpreachers(Request $request)
    {
        $this->search = $request->search;
        $socs = Society::where('circuit_id', $request->circuit)->pluck('id')->toArray();
        if (isset($request->circuit)) {
            return Individual::societymember($socs)->with('household.society')->doesntHave('person')->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
            })->get();
        } else {
            $socs = array($request->society);
            return Individual::societymember($socs)->with('household.society')->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
            })->get();
        }
    }

    public function searchguestpreachers(Request $request)
    {
        $this->search = $request->search;
        $circuits = District::find(Circuit::find($request->circuit)->district_id)->circuits;
        $soc = array();
        foreach ($circuits as $circuit) {
            if ($circuit->id !== $request->circuit) {
                foreach ($circuit->societies as $soc) {
                    $socs[] = $soc->id;
                }
            }
        }
        return Individual::societymember($socs)->with('household.society')->where(function ($query) {
            $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
        })->get();
    }

    public function query($individual, Request $request)
    {
        return DB::select(DB::raw($request->sql))->toArray();
    }

    public function edit(Individual $individual)
    {
        return view('connexion::individuals.edit', compact('individual'));
    }

    public function create()
    {
        return view('connexion::individuals.create');
    }

    public function show($no)
    {
        return $this->individual->find($no);
    }

    public function store(Request $request)
    {
        $indiv = $this->individual->create($request->except('roles'));
        foreach ($request->roles as $role) {
            $tag = $this->tags->find($role);
            $indiv->tag($tag->name);
        }
        $household = $indiv->household;
        if ($household->sortsurname == '') {
            $household->sortsurname = $indiv->surname;
            $household->save();
        }
        return $indiv;
    }

    public function update($id, Request $request)
    {
        $indiv = $this->individual->find($id);
        $indiv->update($request->except('roles'));
        $indiv->detag();
        foreach ($request->roles as $role) {
            $tag = $this->tags->find($role);
            $indiv->tag($tag->name);
        }
        return $indiv;
    }

    public function destroy(Request $request)
    {
        $indiv = Individual::find($request->id);
        if ($request->reason == 'delete') {
            $indiv->forceDelete();
            return "Individual has been deleted";
        } elseif ($request->reason == 'archive') {
            $indiv->delete();
            return "Individual has been archived";
        } else {
            if (isset($request->deathdate)) {
                $indiv->household->specialdays()->create([
                    'household_id' => $indiv->household_id,
                    'anniversarytype' => 'Death',
                    'anniversarydate' => $request->deathdate,
                    'details' => $indiv->firstname . '\'s death'
                ]);
                $indiv->forceDelete();
                return "Death has been recorded and the anniversary noted";
            } else {
                $indiv->forceDelete();
                return "Individual has been deleted";
            }
        }
    }
}
