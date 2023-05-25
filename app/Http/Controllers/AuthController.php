<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Mail\Mailable;
use App\Mail\ExampleMailable;
use App\Mail\ForgotPasswordMailable;
use App\Helpers\Web;
use App\Models\User;


class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','account_activation','process_account_activation','forgot_password','process_forgot_password']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {

        $this->validate($request, [
            'nama'=>'required|min:5',
            'email' => 'required|string|unique:users',            
            'password' => 'required|string',
        ]);

        $user = new User();
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_telp = $request->nomor_telepon;
        $user->password = Hash::make($request->password);
        $user->save();

        $credentials = $request->only(['email', 'password']);

       $data = Mail::send(new ExampleMailable('Account activation',$request->email));
          return response()->json([
            'message'=>'Pendaftaran berhasil, tautan untuk aktivasi telah di kirim ke email '.$request->email,
            'code'=>200
        ]);

     
    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // $credentials = $request->only(['email', 'password']);
        $token = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }else{
            
            $getUser = DB::table('users')->where('email',$request->email)->first();
            if($getUser->email_verified_at!=null){
                return $this->respondWithToken($token);        
            }else{
                 return response()->json([
                     'message' => 'Akun belum aktif, silahkan aktivasi email',
                     'code'=>401,
                     'url-activation'=>url('account-activation',['id'=>$getUser->id])
                     ]);
            }
            
        }

        
    }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json([
                'code'=>200,
                'message'=>'ok',
                'data'=>[
                        'nama'=>auth()->user()->nama,
                        'email'=>auth()->user()->email,
                        'no_telp'=>auth()->user()->no_telp,
                        'foto'=>auth()->user()->foto,
                    ]
            ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

        /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function account_activation($id)
    {
        
        $getUser = DB::table('users')->where('id',$id)->first();
        
        if ($getUser) {
              $data = Mail::send(new ExampleMailable('Account activation',$getUser->email));
              return response()->json([
                'message'=>'Tautan telah di kirim ke email '.$getUser->email,
                'code'=>200
            ]);
        }else{
            return response()->json([
                'message'=>'User tidak di temukan',
                'code'=>404
            ]);
        }

    }
    
    public function process_account_activation($id,$url){
        $update = DB::select("UPDATE users SET email_verified_at = now() WHERE id = '".$id."' ");
         return response()->json([
                'message'=>'Email berhasil di aktivasi',
                'code'=>200
            ]);
    }
    
    public function forgot_password(Request $request){
        $this->validate($request,[
            'email'=>"required|email"    
        ]);
        
        $user = DB::table('users')->where('email',$request->email)->first();
        if($user){
           $data = Mail::send(new ForgotPasswordMailable('Account activation',$request->email));
              return response()->json([
                'message'=>'Tautan untuk ganti password telah di kirim ke email '.$request->email,
                'code'=>200
            ]);
        }else{
            return response()->json([
                'message'=>'Email tidak di temukan',
                'code'=>404
            ]);
        }
    }

    public function process_forgot_password($id,$url){
        return $id;
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = null;
        if(isset(auth()->email)){
            $user=[
                'nama'=>auth()->user()->nama,
                'email'=>auth()->user()->email,
                'no_telp'=>auth()->user()->no_telp,
                'foto'=>auth()->user()->foto,
            ];
            
        }
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}