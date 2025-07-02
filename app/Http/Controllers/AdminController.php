<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use App\Models\Plan;
use App\Models\Booking;
use App\Models\Comment;
use App\Models\ReviewRating;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function username(){
        return 'username';
    }
    public function index(Request $request){

        if($request->input()){
			$request->validate([
				'username'=>'required',
				'password'=>'required',
			]);
            if (Auth::guard('admin')->attempt(['username'=>$request->username,'password'=>$request->password])) {
                // Log in the admin
                return '1';
            }else{
                return response()->json(['password'=>'Username and Password does not matched']);
            }
	    }else{
			return view('admin.admin');
		}
    }

    public function dashboard(){
        $user = User::Select("*")->get();
		$userCount = $user->count();

		$plan = Plan::Select("*")->get();
		$planCount = $plan->count();

		$booking = Booking::Select("*")->get();
		$bookingCount = $booking->count();

		$comment =  Comment::select(['b_comments.*','users.username as user','blogs.title as blog'])
					->LeftJoin('users','b_comments.user_id','=','users.id')
					->LeftJoin('blogs','b_comments.blog_id','=','blogs.id')
					->orderBy('b_comments.id','desc')->limit(5)->get();

		$reviewRating =  ReviewRating::select(['review_ratings.*','users.username as user','plans.title as plan'])
						->LeftJoin('users','review_ratings.user_id','=','users.id')
						->LeftJoin('plans','review_ratings.plan_id','=','plans.id')
						->orderBy('review_ratings.review_id','desc')->limit(5)->get();

	   return view('admin.dashboard',['user'=>$userCount,'plan'=>$planCount,'booking'=>$bookingCount,'comment'=>$comment,'reviewRating'=>$reviewRating]);
    }

	public function logout(Request $request)
{
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/'); // ⬅️ Redirects to homepage
}
}
