<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Establecimiento;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Lang;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
     
     
        protected $redirectTo = '/home'; 
     

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    
    public function redirectPath()
    {
        
        if( auth()->user()->estado==1):

            $establecimiento = Establecimiento::find(auth()->user()->establecimiento_id);
            $nivel=$establecimiento->nivel_id;
            
            switch (auth()->user()->rol) {
                case 1: return '/cans';    
                        break;
                        
                case 2: 
                        if($nivel==1){
                            return '/estimacion';            
                        }
                        else
                        {
                            return '/estimacion_servicio';    
                        }
                    
                        break;

                case 3: if($nivel==1){
                            return '/listar_can';            
                        }
                        else
                        {
                            return '/listar_can_servicio';    
                        }

                case 4: if($nivel==1){
                            return '/listar_can';            
                        }
                        else
                        {
                            return '/listar_can_servicio';    
                        }
                        break;

                case 5: if($nivel==1){
                            return '/listar_can';            
                        }
                        else
                        {
                            return '/listar_can_servicio';    
                        }
                        break;
                        
                case 6: if($nivel==1){
                            return '/listar_can';            
                        }
                        else
                        {
                            return '/listar_can_servicio';    
                        }
                        break;

                case 7: 
                        return '/listar_can';            
                        

                case 8: if($nivel==1){
                            return '/listar_can';            
                        }
                        else
                        {
                            return '/listar_can_servicio';    
                        }
                        break;
            }
        else:
                
                return    auth()->logout();
            
        endif;
    }
}
