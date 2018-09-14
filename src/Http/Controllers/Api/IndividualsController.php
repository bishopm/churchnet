<?php

namespace Bishopm\Churchnet\Http\Controllers\Api;

use Bishopm\Churchnet\Repositories\IndividualsRepository;
use Bishopm\Churchnet\Repositories\TagsRepository;
use Bishopm\Churchnet\Models\Individual;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Models\Society;
use Bishopm\Churchnet\Models\Preacher;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Chat;
use Bishopm\Churchnet\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Bishopm\Churchnet\Http\Requests\CreateIndividualRequest;
use Bishopm\Churchnet\Http\Requests\UpdateIndividualRequest;
use Carbon\Carbon;

class IndividualsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $individual, $tags;

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
        $addressee = $request->firstname . ' ' . $request->surname;
        $household = Household::create(['addressee'=>$addressee, 'society_id'=>$request->society_id, 'sortsurname'=>$request->surname]);
        $individual = Individual::create(['firstname'=>$request->firstname, 'surname'=>$request->surname, 'sex'=>$request->sex, 'cellphone'=>$request->phone, 'household_id'=>$household->id]);
        $household->householdcell = $individual->id;
        $household->save();
        $user = User::where('phone', $request->phone)->first();
        $user->individual_id=$individual->id;
        return "Household and individual added";
    }

    public function journeyadd(Request $request)
    {
        if ($request->action=="add") {
            $individual = Individual::Create($request->except('action', 'id'));
        } else {
            $individual=Individual::find($request->id);
            $this->individual->update($individual, $request->except('action'));
        }
        return "Individual added / updated";
    }
    
    public function phone(Request $request)
    {
        $monday = date("Y-m-d", strtotime('Monday this week'));
        $individual = Individual::with('household.individuals', 'groups', 'household.society.circuit')->where('cellphone', $request->phone)->first();
        if (!$individual) {
            return "No individual";
        } elseif ($individual->household->society_id <> $request->society_id) {
            return "Wrong society";
        }
        $gids=array();
        foreach ($individual->groups as $group) {
            $gids[]=$group->id;
        }
        $chats = Chat::with('messages', 'chatable')
        ->where('chatable_type', 'Bishopm\Churchnet\Models\Society')->where('chatable_id', $individual->household->society_id)->where('created_at', '>=', $monday)
        ->orWhere('chatable_type', 'Bishopm\Churchnet\Models\Circuit')->where('chatable_id', $individual->household->society->circuit_id)->where('created_at', '>=', $monday)
        ->orWhere('chatable_type', 'Bishopm\Churchnet\Models\District')->where('chatable_id', $individual->household->society->circuit->district_id)->where('created_at', '>=', $monday)
        ->orWhere('chatable_type', 'Bishopm\Churchnet\Models\Group')->whereIn('chatable_id', $gids)->where('created_at', '>=', $monday)->get();
        $individual->chats=$chats;
        return $individual;
    }

    public function message($id)
    {
        $chat=Chat::with('messages.individual', 'chatable')->where('id', $id)->first();
        foreach ($chat->messages as $message) {
            $message->ago = Carbon::parse($message->created_at)->diffForHumans();
        }
        return $chat;
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
            return Individual::societymember($socs)->with('household.society')->doesntHave('person')->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
            })->get();
        } else {
            $socs=array($request->society);
            return Individual::societymember($socs)->with('household.society')->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->search . '%')->orWhere('firstname', 'like', '%' . $this->search . '%');
            })->get();
        }
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

    public function destroy(Individual $individual)
    {
        $this->individual->destroy($individual);
        return view('connexion::individuals.index')->withSuccess('The ' . $individual->individual . ' individual has been deleted');
    }
}
