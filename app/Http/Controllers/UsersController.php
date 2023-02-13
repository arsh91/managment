<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Users;
use App\Models\Departments;
use App\Models\Roles;
use App\Models\Managers;

class UsersController extends Controller
{

    /**
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
		$usersData = Users::join('managers', 'users.id', '=', 'managers.user_id')->where('managers.parent_user_id',auth()->user()->id)->get([ 'managers.user_id','users.*']);
		//database query
		$users_Data=Users::with('role','department')->orderBy('id','desc')->get();  //database query
		$roleData=Roles::orderBy('id','desc')->get();//database query
		$departmentData = Departments::orderBy('id','desc')->get();
        return view('users.index',compact('usersData','roleData','departmentData','users_Data'));
    }
	 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	 public function store(Request $request)
    {	
		$validator = \Validator::make($request->all(), [
            'user_name' => 'required', 
			'last_name'=>'required', 
			'email'=>'required|unique:users', 
			'password'=>'required|confirmed:', 
			'phone'=>'required|unique:users', 
			'joining_date'=>'required', 
			'birth_date'=>'required', 
			'profile_picture'=>'required', 
			'role_select'=>'required', 
			'department_select'=>'required', 
			'address'=>'required', 
		
        ]);
 
        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
					
		$validate = $validator->valid();	

		$salaried=null;		 
		if (isset($validate['salaried'])) 
		{
		   $salaried = $validate['addsalary'];
		}			
         $users =Users::create([
		
            'first_name' => $validate['user_name'],
			'last_name' => $validate['last_name'], 
			'email' => $validate['email'],
			'password' => $validate['password'],
			'salary'=>$salaried ,
			'address'=>$validate['address'].', '.$validate['city'].', '.$validate['state'].', '.$validate['zip'],
			'phone'=>$validate['phone'],
			'department_id'=>$validate['department_select'],
			'role_id'=>$validate['role_select'],
			'phone'=>$validate['phone'],
			'joining_date'=>$validate['joining_date'],
			'birth_date'=>$validate['birth_date'],					
			'profile_picture'=>$validate['profile_picture'],						
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
		
		if (isset($validate['manager_select']))
		{				
			foreach($validate['manager_select'] as $manager)
			{				
				$managers =Managers::create([					
					'user_id' => $users->id,
					'parent_user_id' => $manager,
				]);
			}		
		}		
		$request->session()->flash('message','User added successfully.');
        return Response()->json(['status'=>200, 'users'=>$users]);
    }
	 /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
	 public function edit(Request $request)
    {   
        $users = Users::where(['id' => $request->id])->first();
		$managerSelectOptions = Users::where('id', '!=' , $request->id)->orderBy('id','desc')->get();
		$Managers = Managers::where(['user_id' => $request->id])->get();
		
        return Response()->json(['users' =>$users, 'Managers' =>$Managers,'managerSelectOptions' =>$managerSelectOptions]);
    }
	
	 /**
     * Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
		public function update(Request $request)
    {
		$validator = \Validator::make($request->all(), [
            'edit_username' => 'required',
			'edit_lastname' => 'required',
			'edit_email'=>'required',
			'edit_phone'=>'required',		
			'role_select'=>'required',
			'department_select'=>'required',
			'edit_password'=>'required',
			'address'=>'required',
        ]);
 
        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
		
		$validate = $validator->valid();
		$salaried=null;		 
		if (isset($validate['edit_salaried'])) 
			{
			   $salaried = $validate['edit_salary'];
			}		
		
        Users::where('id', $request->users_id)
        ->update([
		   'first_name' => $validate['edit_username'],        
		    'last_name' => $validate['edit_lastname'],
			'email' => $validate['edit_email'],
			'phone' => $validate['edit_phone'],
			'salary' =>$salaried,
			'role_id'=> $validate['role_select'],
			'department_id'=>$validate['department_select'],
			'address' =>$validate['address'],
			'password' => $validate['edit_password'],
        ]);
		
		$checkManagersExist=Managers::where(['user_id' =>$request->users_id])->get(); 
		if (!empty($checkManagersExist)){
		      Managers::where('user_id', $request->users_id)->delete();			
		}
		if (isset($validate['manager_select'])){			
			foreach($validate['manager_select'] as $updatemanager)
			{			
				$managers =Managers::create([					
					'user_id' => $request->users_id,
					'parent_user_id' => $updatemanager,
				]);
			}		
		}
		$request->session()->flash('message','User updated successfully.');
        return Response()->json(['status'=>200]);
    }
	  /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
	 public function destroy(Request $request)
    {
		
        $Users = Users::where('id',$request->id)->delete();
		$request->session()->flash('message','User deleted successfully.');
        return Response()->json($Users);
    }
	 public function updateUserStatus(Request $request)
	 {
		 
		  Users::where('id', $request->dataId)
			->update([
            'status' => $request->status
			 ]);
			 $request->session()->flash('message','User status updated  successfully.');
		     return Response()->json(['status'=>200]);	
	 }
	     
}