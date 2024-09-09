<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Establecimiento;
use DB;
use Flash;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Handle the event
     * @param  Login  $event
     * @return void
     */

    public function handle(Login $event)
    {
        if(Auth::user()->first_login == NULL){
            $event->user->first_login = new Carbon;

            $establecimiento = Establecimiento::find(Auth::user()->establecimiento_id);
            
            $nivel=$establecimiento->nivel_id;

            if($nivel==1)
                $event->user->fin_first_login = $event->user->first_login->addDays(3);
            else
            {
                if($nivel==2)
                {   if(Auth::user()->rol==2)
                        $event->user->fin_first_login = $event->user->first_login->addDays(3);
                    else
                        $event->user->fin_first_login = $event->user->first_login->addDays(3);
                }
                else
                {    if(Auth::user()->rol==2)
                        $event->user->fin_first_login = $event->user->first_login->addDays(3);
                     else
                        $event->user->fin_first_login = $event->user->first_login->addDays(3);
                }
            }
            $event->user->first_login = new Carbon;
        }
        

        $event->user->last_login = new Carbon;
        $event->user->save();
        
    }
}
