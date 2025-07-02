<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\str;
use Illuminate\Support\Carbon;
use Mail;
use App\Models\User;
use App\Models\Plan;
use App\Models\PasswordReset;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $data = User::latest('id')->get();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('image',function($row){
                if($row->image != ''){
                    $img = '<img src="'.asset("public/user/".$row->image).'" width="70px">';
                }else{
                    $img = '<img src="'.asset("public/user/default.png").'" width="70px">';
                }
                return $img;
            })
            ->editColumn('status', function($row){
                if($row->status == '1'){
                    $status = '<label class="badge badge-gradient-info">Active</label>';
                }else{
                    $status = '<label class="badge badge-gradient-danger">Inactive</label>';
                }
                return $status;
            })
            ->editColumn('created_at', function($row){
                return date('d M, Y',strtotime($row->created_at));
            })
            ->addColumn('action', function($row){
                $btn = '<a href="users/'.$row->id.'/edit" class="btn btn-gradient-success btn-sm">Edit</a>';
                return $btn;
            })
            ->rawColumns(['image','status','action'])
            ->make(true);
        }
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $user = User::where('id',$id)->first();
        return view('admin.users.edit',['user'=>$user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
       // $id = session()->get('id');
        $request->validate([
            'username'=> 'required',
            'phone'=> 'required',
        ]);

        $user = User::where(['id'=>$id])->update([
            "username"=>$request->username,
            "phone"=>$request->phone,
            "country"=>$request->country,
            "state"=> $request->state,
            "city"=>$request->city,
            "status"=>$request->status,
        ]);
        return '1';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function update_image(Request $request){
        $id = Auth::id();
   
        if($request->img != ''){
            $path = public_path().'/user/';
          
            if($request->old_img != ''  && $request->old_img != null){
                $file_old = $path.$request->old_img;
                if(file_exists($file_old)){
                    unlink($file_old);
                }
            }
          
            $file = $request->img;
            $image = rand().$request->img->getClientOriginalName();
            $file->move($path, $image);
        }else{
            $image = $request->old_img;
        }
        $user = User::where(['id'=>$id])->update([
            "image"=>$image,
        ]);
        return '1';

    }

    public function signup(Request $request){
        if(Auth::check()){
            return redirect('profile');
        }
        if($request->input()){
            $request->validate([
                'username'=>'required',
                'country'=>'required',
                'phone'=>'required',
                'email'=>'required|email|unique:users,email',
                'password'=>'required',
            ]);

            $user = new User();
            $user->username = $request->input("username");
            $user->country = $request->input("country");
            $user->phone = $request->input("phone");
            $user->email = $request->input("email");
            $user->password = Hash::make($request->input("password"));
            $result = $user->save();
            return $result;
        }else{
            return view('public.signup');
        }
    }

    public function login(Request $req){
        if(Auth::check()){
            return redirect('profile');
        }
        if($req->input()){
            $req->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if (Auth::attempt(['email' => $req->email, 'password' => $req->password, 'status' => 1])) {
              
                return "1";
            }else if (Auth::attempt(['email' => $req->email, 'password' => $req->password, 'status' => 0])) {
         
                return 'Your account is blocked by Site Administrator.';
            } else {
                return 'Email Address and Password Not Matched.';
            }
        }else{
            return view('public.login');
        }
    }

    public function change_password(Request $request){
        if(!Auth::check()){
            return redirect('login');
        }
        if($request->input()){
            $request->validate([
                'old_password'=> 'required',
                'new_pass'=> 'required',
                'confirm_password'=> 'required',
            ]);
            // Attempt to authenticate the user with the provided credentials
            $select = [
                'email' => Auth::user()->email,
                'password' => $request->old_password
            ];
            // Check if the old password matches the current password
            if(Auth::attempt($select)){
                $id = Auth::id();
                $update = DB::table('users')->where('id',$id)->update([
                    'password'=>Hash::make($request->new_pass)
                ]);
                return 1;
            }else{
               return 'Please Enter Correct Old Password';
            }
        }else{
            return view('public.change-password');
        }
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    public function forgot_password(Request $request){
        if(!Auth::check()){
            if($request->input()){
                try{
                    $user = User::where('email',$request->email)->first();
                    if($user){
                        if($user->status == '0'){
                            return json_encode(['error'=>'Your account is blocked by Site Administrator']);
                        }
                        $check = PasswordReset::where('email',$request->email)->first();
                        if($check){
                            return json_encode(['success'=>'Email Already Sent, Please check your mail to reset your password']);
                        }
                        $token = Str::random(40);
                        $domain = URL::to('/');
                        $url = $domain.'/reset-password?token='.$token;

                        $data['url'] = $url;
                        $data['email'] = $request->email;
                        $data['title'] = 'Password Reset';
                        $data['body'] = 'Please click on below link to reset you password.';

                        Mail::send('public.forgotPasswordMail',['data'=>$data],function($message) use ($data){
                                $message->to($data['email'])->subject($data['title']);
                        });
                        $dataTime = Carbon::now()->format('Y-m-d H:i:s');
                        PasswordReset::updateOrCreate(
                            ['email' => $request->email],
                            [
                            'email' => $request->email,
                            'token'=> $token,
                            'created_at' => $dataTime
                            ]
                        );
                        return json_encode(['success'=>'Please check your mail to reset your password']);
                    }else{
                        return json_encode(['error'=>'Email Does Not Exists!']);
                    }
                    }catch(\Exception $e){
                        return response()->json(['error',$e->getMessage()]);
                    }
            }else{
                return view('public.forgot-password');
            }
        }else{
            return redirect('profile');
        }
    }

    public function reset_password(Request $request){
        $resetData = PasswordReset::where('token',$request->token)->first();
        if($request->token && $resetData){
            $user = User::where('email',$resetData->email)->get();
            return view('public.reset-password',compact('user'));
        }else{
            return abort('404');
        }
    }

    public function reset_passwordUpdate(Request $request){
        $request->validate([
            'password'=> 'required',
            'confirm_password'=> 'required',
        ]);

        $data = User::where(['id'=>$request->id])->update([
            "password" => Hash::make($request->input("password")),
        ]);
        $user = User::where('id',$request->id)->first();
                PasswordReset::where('email',$user->email)->delete();
        return '1';
        //return 'Your Password has been reset successfully.';
    }


    public function profile(){
        if(!Auth::check()){
            return redirect('login');
        }
        if(Auth::check()){
            $user = Auth::id();
            $user = User::select('users.*')->WHERE(['id'=> $user])->first();
          //return $user;
            return view('public.profile',['user'=>$user]);
        }else{
            return redirect('/login');
        }
    }

    public function profileUpdate(Request $request)
    {
        //
        $id = Auth::id();
        $request->validate([
            'username'=> 'required',
            'phone'=> 'required',
        ]);

        $user = User::where(['id'=>$id])->update([
            "username"=>$request->username,
            "phone"=>$request->phone,
            "country"=>$request->country,
            "state"=> $request->state,
            "city"=>$request->city,
        ]);
        return '1';
    }

    public function checkout(Request $request,$slug){
        if(Auth::check()){
            $plan = Plan::select('plans.*','categories.title as category','categories.title_slug as category_slug')
                    ->leftJoin('categories','plans.category','=','categories.id')
                    ->where('plans.title_slug',$slug)->first();
            return view('public.checkout',['plan'=>$plan]);
        }else{
            return redirect('login');
        }
    }

    public function booking(){
         if(Auth::check()){
            $user = Auth::id();
            $booking = Booking::with('plan')->where('booking.user_id',$user)->get();
            return view('public.my_booking',['booking'=>$booking]);
        }else{
            return redirect('login');
        }

    }
}
