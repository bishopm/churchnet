<?php

namespace Bishopm\Churchnet\Http\Controllers;

use App\Http\Controllers\Controller, MediaUploader;
use Bishopm\Churchnet\Models\User;
use Bishopm\Churchnet\Models\Role;
use Bishopm\Churchnet\Models\Household;
use Bishopm\Churchnet\Repositories\UsersRepository;
use Bishopm\Churchnet\Repositories\IndividualsRepository;
use Bishopm\Churchnet\Http\Requests\CreateUserRequest;
use Bishopm\Churchnet\Http\Requests\UpdateUserRequest;
use Bishopm\Churchnet\Notifications\ProfileUpdated;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;
use Auth;

class UsersController extends Controller {

	private $user,$individuals;

	public function __construct(UsersRepository $user, IndividualsRepository $individuals)
    {
        $this->user = $user;
        $this->individuals = $individuals;
    }

	public function index()
	{
        $users = $this->user->all();
   		return view('connexion::users.index',compact('users'));
	}

    public function activate()
    {
        $users = $this->user->inactive();
        return view('connexion::users.activate',compact('users'));
    }

    public function activateuser($id)
    {
        $user=$this->user->activate($id);
        $webrole=Role::where('slug','web-user')->first()->id;
        $user->roles()->attach($webrole);
        $hid=$user->individual->household_id;
        $household=Household::withTrashed()->where('id',$hid)->first();
        $household->restore();
        UserVerification::generate($user);
        UserVerification::send($user, 'Welcome!');
        return redirect()->route('admin.users.activate')
            ->withSuccess('User has been activated');
    }

	public function edit(User $user)
    {
        $uroles= $user->roles;
        $data['userroles']=array();
        foreach ($uroles as $ur){
            $data['userroles'][]=$ur->id;
        }
        $data['roles']=Role::all();
        $data['individuals'] = $this->individuals->all();
        $data['user'] = $user;
        return view('connexion::users.edit', $data);
    }

	public function show($user)
	{
		if ($user=="current"){
			$data['user']=User::find(1);
		} else {
			$data['user']=User::find($user);
		}
		return view('connexion::users.show',$data);
	}	

    public function create()
    {
        $data['roles']=Role::all();
        $data['individuals'] = $this->individuals->all();

        return view('connexion::users.create',$data);
    }

    public function store(CreateUserRequest $request)
    {
        $user=User::create($request->except('password','role_id'));
        if ($request->input('password')<>""){
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();
        $user->roles()->attach($request->role_id);
        return redirect()->route('admin.users.index')
            ->withSuccess('New user added');
    }
	
    public function update($user, UpdateUserRequest $request)
    {
        $user=User::find($user);
        if (null!==$request->input('profile')){
            $individual=$user->individual;
            $fname=$individual->id;
            $individual->service_id=$request->input('service_id');
            $individual->image=$request->input('image');
            $individual->save();
            $user->bio=$request->input('bio');
            $user->save();
            //$user->notify(new ProfileUpdated($user));
            return redirect()->route('webuser.edit',$individual->slug)->withSuccess('User profile has been updated');
        } else {
            $user->fill($request->except('role_id','profile'));
            $user->save();
            $user->roles()->detach();
            $user->roles()->attach($request->role_id);        
            //$user->notify(new ProfileUpdated($user));
            return redirect()->route('admin.users.index')->withSuccess('User has been updated');
        }
    }

}
 