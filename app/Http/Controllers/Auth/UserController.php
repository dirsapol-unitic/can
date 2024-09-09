<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserClaveRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Establecimiento;
use App\Models\Responsable;
use App\Models\Servicio;
use App\Models\Distribucion;
use App\Models\Division;
use App\Models\Unidad;
use App\Models\Grado;
use App\Models\Rubro;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SoapClient;
use App\Models\Can;


class UserController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)

    {
        if (Auth::user()->rol == 1){
            $this->userRepository->pushCriteria(new RequestCriteria($request));
            $users = DB::table('users')->where('telefono','<>',999999999)->orderBy('establecimiento_id')->get();

            return view('admin.users.index')
                ->with('users', $users);    
        }
        else
        {
                    return redirect('/home');
        }

        
    }

    //public function index_responsable($can_id)
    public function index_responsable()
    {
        

        
        //$users = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<',6)->where('estado',1)->orderby('rol','asc')->get();
/*

        $users = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('can_id', $can_id)->where('rol','>',2)->where('rol','<',6)->orderby('rol','asc')->get();

        //dd($users);

        
        for($i=0;$i<7;$i++){
            for($j=0;$j<3;$j++){
                $user[$i][$j]="";    
            }
        }


       
        //ver 2 servicios_id nulos
        //$users = DB::table('users')->where('rol','>',1)->whereNotNull('name_rectificacion')->get();
        $users = DB::table('users')->where('rol',1)->get();

        foreach ($users as $key => $usuario) {

            DB::table('responsables')
                ->insert([
                        
                        'dni' => $usuario->dni,
                        'nombre' => $usuario->name,
                        'can_id'=> 3,
                        'establecimiento_id'=>$usuario->establecimiento_id,
                        'nombre_establecimiento'=>$usuario->nombre_establecimiento,
                        'grado_id'=> $usuario->grado_id,                        
                        'grado'=> $usuario->grado,
                        'rol'=> $usuario->rol,
                        'etapa'=>1,                        
                        'servicio_id'=> $usuario->servicio_id,
                        'nombre_servicio'=> $usuario->nombre_servicio,
                        'telefono'=> $usuario->telefono,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);

        }
   
        $users = DB::table('users')->where('rol','>',1)->whereNotNull('name_rectificacion')->get();
        

        
        foreach ($users as $key => $usuario) {

            DB::table('responsables')
                ->insert([
                        
                        'dni' => $usuario->dni_rectificacion,
                        'nombre' => $usuario->name_rectificacion,
                        'can_id'=> 3,
                        'establecimiento_id'=>$usuario->establecimiento_id,
                        'nombre_establecimiento'=>$usuario->nombre_establecimiento,
                        'grado_id'=> $usuario->grado_id,                        
                        'grado'=> $usuario->grado_rectificacion,
                        'rol'=> $usuario->rol,
                        'etapa'=>1,                        
                        'servicio_id'=> $usuario->servicio_id_rectificacion,
                        'nombre_servicio'=> $usuario->nombre_servicio_rectificacion,
                        'telefono'=> $usuario->telefono,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);

        }
        

        //select responsables.id from responsables inner join users on responsables.dni  = users.dni where users.id=984 and etapa=1
        

        
        $archivos = DB::table('archivos')->get();
        
        foreach ($archivos as $key => $archivo) {

            $busca_responsable = DB::table('responsables')
                        ->select('responsables.id')
                        ->join('users', 'responsables.dni', 'users.dni')
                        ->where('users.id',$archivo->user_id)                        
                        ->where('etapa',1)                        
                        ->get();

            DB::table('archivos')
                ->where('id',$archivo->id)
                ->update([
                    'responsable_id' => $busca_responsable->get(0)->id              
                ]);

        }
        
        

        $observaciones = DB::table('observaciones')->get();

        //dd($observaciones);
        
        foreach ($observaciones as $key => $observacion) {

            $busca_responsable = DB::table('responsables')
                        ->select('responsables.id')
                        ->join('users', 'responsables.dni', 'users.dni')
                        ->where('users.id',$observacion->user_id)                        
                        ->where('etapa',1)                        
                        ->get();

            DB::table('observaciones')
                ->where('id',$observacion->id)
                ->update([
                    'can_id' => 3,
                    'responsable_id' => $busca_responsable->get(0)->id              
                ]);

        }

        

        $responsables = DB::table('responsables')->where('etapa',1)->get();

        for($i=1; $i<900; $)
        
        $users = DB::table('users')->where('rol','>',1)->whereNotNull('name_rectificacion')->get();
        
        foreach ($users as $key => $usuario) {

            DB::table('responsables')
                ->insert([
                        
                        'dni' => $usuario->dni_rectificacion,
                        'nombre' => $usuario->name_rectificacion,
                        'can_id'=> 3,
                        'establecimiento_id'=>$usuario->establecimiento_id,
                        'nombre_establecimiento'=>$usuario->nombre_establecimiento,
                        'grado_id'=> $usuario->grado_id,                        
                        'grado'=> $usuario->grado_rectificacion,
                        'rol'=> $usuario->rol,
                        'etapa'=>2,                        
                        'servicio_id'=> $usuario->servicio_id_rectificacion,
                        'nombre_servicio'=> $usuario->nombre_servicio_rectificacion,
                        'telefono'=> $usuario->telefono,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);

        }


        */
        /*
        
        foreach ($users as $key => $usuario) {
            switch ($usuario->rol) {
                case 3:
                    switch ($usuario->servicio_id) {
                        case 1: $user[2][0]=$usuario->nombre;
                                $user[2][1]=1;
                                $user[2][2]=$usuario->id;
                            break;
                        
                        case 2: $user[5][0]=$usuario->nombre;
                                $user[5][1]=1;
                                $user[5][2]=$usuario->id;
                            break;

                        case 3: $user[3][0]=$usuario->nombre;
                                $user[3][1]=1;
                                $user[3][2]=$usuario->id;
                            break;

                        case 4: $user[4][0]=$usuario->nombre;
                                $user[4][1]=1;
                                $user[4][2]=$usuario->id;
                            break;

                        case 5: $user[6][0]=$usuario->nombre;
                                $user[6][1]=1;
                                $user[6][2]=$usuario->id;
                            break;

                    }
                    break;
                
                case 4:
                        $user[1][0]=$usuario->nombre;
                        $user[1][1]=1;
                        $user[1][2]=$usuario->id;
                    break;

                case 5:
                        $user[0][0]=$usuario->nombre;
                        $user[0][1]=1;
                        $user[0][2]=$usuario->id;
                    break;
            }
            
        }
        
        $cans = DB::table('cans')                
                ->where('id',$can_id)                
                ->get();    

        if(count($cans)>0)
            $stock=$cans->get(0)->stock;
        else
            return redirect(route('home.index'));
        
        return view('site.users.index')
            ->with('user', $user)
            ->with('stock', $stock)
            ->with('establecimiento_id', Auth::user()->establecimiento_id)
            ->with('can_id', $can_id); 

        */
    }

    public function index_responsable_stock($can_id)
    {
        $establecimiento = Establecimiento::find(Auth::user()->establecimiento_id);
        
        $nivel=$establecimiento->nivel_id;

        if($nivel==1){
            //$users = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<',6)->where('estado',1)->orderby('rol','asc')->get();
            $users = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('can_id', $can_id)->where('rol','>',2)->where('rol','<',6)->orderby('rol','asc')->get();
            for($i=0;$i<7;$i++){
                for($j=0;$j<3;$j++){
                    $user[$i][$j]="";    
                }
            }
            
            foreach ($users as $key => $usuario) {
                switch ($usuario->rol) {
                    case 4:
                            $user[1][0]=$usuario->nombre;
                            $user[1][1]=1;
                            $user[1][2]=$usuario->id;
                        break;

                    case 5:
                            $user[0][0]=$usuario->nombre;
                            $user[0][1]=1;
                            $user[0][2]=$usuario->id;
                        break;
                }
                
            }
        }
        else
        {
            //$users = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>=',7)->where('rol','<',11)->where('estado',1)->orderby('rol','asc')->get();
            $users = DB::table('responsables')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('can_id', $can_id)->where('rol','>=',7)->where('rol','<',11)->orderby('rol','asc')->get();

            for($i=0;$i<7;$i++){
                for($j=0;$j<3;$j++){
                    $user[$i][$j]="";    
                }
            }
            
            foreach ($users as $key => $usuario) {
                switch ($usuario->rol) {
                    case 7:
                            $user[1][0]=$usuario->nombre;
                            $user[1][1]=1;
                            $user[1][2]=$usuario->id;
                        break;

                    case 9:
                            $user[0][0]=$usuario->nombre;
                            $user[0][1]=1;
                            $user[0][2]=$usuario->id;
                        break;
                }                
            }
        }

        $cans = DB::table('cans')                
                ->where('id',$can_id)                
                ->get();    

        if(count($cans)>0)
            $extra=$cans->get(0)->extraordinario;
        else
            return redirect(route('home.index'));
        
                
        return view('site.users.index_stock')
            ->with('user', $user)
            ->with('extra', $extra)
            ->with('establecimiento_id', Auth::user()->establecimiento_id)
            ->with('can_id', $can_id);
    }

    public function index_responsable_rectificacion()
    {
        
/*        $users = DB::table('users')->where('rol','>',1)->whereNotNull('name_rectificacion')->get();

        

        foreach ($users as $key => $usuario) {

            DB::table('responsables')
                ->insert([
                        
                        'dni' => $usuario->dni_rectificacion,
                        'nombre' => $usuario->name_rectificacion,
                        'can_id'=> 3,
                        'establecimiento_id'=>$usuario->establecimiento_id,
                        'nombre_establecimiento'=>$usuario->nombre_establecimiento,
                        'grado_id'=> $usuario->grado_id,                        
                        'grado'=> $usuario->grado_rectificacion,
                        'rol'=> $usuario->rol,
                        'etapa'=>2,                        
                        'servicio_id'=> $usuario->servicio_id_rectificacion,
                        'nombre_servicio'=> $usuario->nombre_servicio_rectificacion,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);

        }
  */      
        
        $establecimiento = Establecimiento::find(Auth::user()->establecimiento_id);
        
        $nivel=$establecimiento->nivel_id;

        if($nivel==1){
            $users = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>',2)->where('rol','<',6)->where('estado',1)->orderby('rol','asc')->get();
            for($i=0;$i<7;$i++){
                for($j=0;$j<3;$j++){
                    $user[$i][$j]="";    
                }
            }
            
            foreach ($users as $key => $usuario) {
                switch ($usuario->rol) {
                    case 4:
                            $user[1][0]=$usuario->name_rectificacion;
                            $user[1][1]=$usuario->estado;
                            $user[1][2]=$usuario->id;
                        break;

                    case 5:
                            $user[0][0]=$usuario->name_rectificacion;
                            $user[0][1]=$usuario->estado;
                            $user[0][2]=$usuario->id;
                        break;
                }
                
            }
        }
        else
        {
            $users = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol','>=',7)->where('rol','<',11)->where('estado',1)->orderby('rol','asc')->get();

            for($i=0;$i<7;$i++){
                for($j=0;$j<3;$j++){
                    $user[$i][$j]="";    
                }
            }
            
            foreach ($users as $key => $usuario) {
                switch ($usuario->rol) {
                    case 7:
                            $user[1][0]=$usuario->name_rectificacion;
                            $user[1][1]=$usuario->estado;
                            $user[1][2]=$usuario->id;
                        break;

                    case 9:
                            $user[0][0]=$usuario->name_rectificacion;
                            $user[0][1]=$usuario->estado;
                            $user[0][2]=$usuario->id;
                        break;
                }
                
            }
        }
                
        return view('site.users.index_rectificacion')
            ->with('user', $user)
            ->with('establecimiento_id', Auth::user()->establecimiento_id)
            ->with('can_id', 3);
        
    }


    /**
     * Show the form for creating a new User.
     *
     * @return Response 
     */
    public function create()
    {
        //$establecimiento_id=Establecimiento::pluck('nombre_establecimiento','id');
        $establecimiento = DB::table('establecimientos')->get();
        $establecimiento_id = 0;
        
//        return view('myform',compact('countries'));
        $grado=Grado::pluck('descripcion','id')->all();
        //$farmacia=Servicio::pluck('nombre_servicio','id')->all();
        $tipo=1; /// Crear
        $num_can=DB::table('cans')->where('active',TRUE)->count();
        
        return view('admin.users.create',compact(["establecimiento","establecimiento_id","grado","tipo","num_can"]));
    
    }

    public function create_responsable($can_id,$valor_id)
    {
        $establecimiento_id=Auth::user()->establecimiento_id;
        $grado=Grado::pluck('descripcion','id')->all();

        $tipo=1; /// Crear
        $num_can=DB::table('cans')->where('active',TRUE)->count();
        
        return view('site.users.create',compact(["establecimiento","establecimiento_id","grado","tipo","num_can","valor_id","can_id"]));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        //Concatenamos  el nombre y apellido en mayuscula
        $name=strtoupper( $request->input("nombres")." ".$request->input("apellido_paterno")." ".$request->input("apellido_materno") ) ;
        $dni=$request->input("dni") ;
        $nombres=strtoupper( $request->input("nombres") ) ;
        $apellido_paterno=strtoupper( $request->input("apellido_paterno") ) ;
        $apellido_materno=strtoupper( $request->input("apellido_materno") ) ;
        $telefono=$request->input("telefono");
        $establecimiento_id=$request->input("establecimiento_id");
        $establecimiento = Establecimiento::findOrFail($establecimiento_id);
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;
        $grado_id=0;
        $grado=$request->input("grado");
        $email=$request->input("email");
        $password= bcrypt( $request->input("password") ); 
        $rol=$request->input("rol");
        $cip=$request->input("cip") ;
        $nivel=$establecimiento->nivel_id;
        if ($nivel>1)
        {
            $servicio_id=$request->input("servicio_id");    
            switch ($rol) {
                case 1: $nombre_servicio='Administrador';break;                
                case 2: $servicio_id=$request->input("servicio_id");    
                        $rubro = DB::table('servicios')
                                ->where('id',$servicio_id)
                                ->get();
                        $nombre_servicio=$rubro->get(0)->nombre_servicio;
                        break;
                case 3: $nombre_servicio='Productos Farmaceuticos'; break;                
                case 4: $nombre_servicio='Material Biomedico, Instrumental Quirugico y Productos Afines'; break;
                case 5: $nombre_servicio='Material e Insumos Dentales'; break;
                case 6: $nombre_servicio='Material e Insumos de Laboratorios'; break;
                case 7: $nombre_servicio='Farmacia I'; break;
                case 8: $nombre_servicio='Material Fotografico y Fonotecnico'; break; 
                case 11: $nombre_servicio='Ver Reportes'; break;
            }
        }
        else
        {   $servicio_id=0;
            $nombre_servicio='NO APLICA';
        }    

        $cans= Can::latest('id')->first();
        $can_id=$cans->id;
    
        DB::table('users')
                ->insert([
                        'name' => $name,
                        'dni' => $dni,
                        'nombres' => $nombres,
                        'apellido_paterno'=>$apellido_paterno,
                        'apellido_materno'=>$apellido_materno,
                        'telefono'=>$telefono,
                        'establecimiento_id'=>$establecimiento_id,
                        'nombre_establecimiento'=>$nombre_establecimiento,
                        'grado_id'=>$grado_id,
                        'email'=>$email,
                        'grado'=>$grado,
                        'rol'=>$rol,
                        'cip'=>$cip,
                        'password'=>$password,
                        'servicio_id'=>$servicio_id,
                        'nombre_servicio'=>$nombre_servicio,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);

        DB::table('responsables')
                ->insert([
                        'nombre' => $name,
                        'dni' => $dni,
                        'telefono'=>$telefono,
                        'establecimiento_id'=>$establecimiento_id,
                        'nombre_establecimiento'=>$nombre_establecimiento,
                        'grado_id'=>$grado_id,                        
                        'grado'=>$grado,
                        'rol'=>$rol,                        
                        'can_id'=> $can_id,
                        'etapa'=>1, 
                        'cip'=>$cip,                        
                        'servicio_id'=>$servicio_id,
                        'nombre_servicio'=>$nombre_servicio,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);
        
        $usuarios= User::latest('id')->first();
        $user_id=$usuarios->id;
        
        Flash::success('Usuario grabado con exito.');

        return redirect(route('users.index'));
    }

    public function store_responsable(Request $request)
    {
        

        //Concatenamos  el nombre y apellido en mayuscula
        $name=strtoupper( $request->input("nombres")." ".$request->input("apellido_paterno")." ".$request->input("apellido_materno") ) ;
        //Asignamos el dni
        $dni=$request->input("dni") ;
        //Asignamos el nombre
        $nombres=strtoupper( $request->input("nombres") ) ;
        //Asignamos el apellido
        $apellido_paterno=strtoupper( $request->input("apellido_paterno") ) ;
        //Asignamos el telefono
        $apellido_materno=strtoupper( $request->input("apellido_materno") ) ;
        
        //Asignamos el establecimiento
        $establecimiento_id=$request->input("establecimiento_id");
        $establecimiento_id=Auth::user()->establecimiento_id;
        //Buscamos la descripcion del grado
        $establecimiento = Establecimiento::findOrFail($establecimiento_id);
        //asignamos el nombre del establecimiento
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;
        //Buscamos el nombre del establecimiento

        $rol=$request->input("rol_id");
        //Asignamos el grado
        $grado_id=0;
        //asignamos el nombre del establecimiento
        $grado=$request->input("grado");

        $nivel=$establecimiento->nivel_id;
        
        if ($rol==5 || $rol==4)
        {
            $servicio_id=0;
            $nombre_servicio='NO APLICA';
        }
        else
        {   $servicio_id=$request->input("servicio_id");    
            $rubro = DB::table('servicios')
                    ->where('id',$servicio_id)
                    ->get();
            $nombre_servicio=$rubro->get(0)->nombre_servicio;
        }

        $contar=DB::table('users')->count();
        $email='email'.$contar.'@email.com';
        
        DB::table('users')
                ->insert([
                        'name' => $name,
                        'dni' => $dni,
                        'nombres' => $nombres,
                        'apellido_paterno'=>$apellido_paterno,
                        'apellido_materno'=>$apellido_materno,
                        'establecimiento_id'=>$establecimiento_id,
                        'nombre_establecimiento'=>$nombre_establecimiento,
                        'grado_id'=>$grado_id,
                        'grado'=>$grado,
                        'rol'=>$rol,
                        'email'=>$email,
                        'telefono'=>'999999999',
                        'password'=>'$password',
                        'servicio_id'=>$servicio_id,
                        'nombre_servicio'=>$nombre_servicio,
                        "created_at"=>Carbon::now(),
                        "updated_at"=>Carbon::now()
                    ]);

        $usuarios = DB::table('users')->where('establecimiento_id',Auth::user()->establecimiento_id)->where('rol',7)->where('estado',1)->get();
        $user_id=$usuarios->get(0)->id;

        Flash::success('Usuario grabado con exito.');

        return redirect(route('users.index_responsable',[$can_id]));
    }

    public function show($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('admin.users.show')->with('user', $user);
    }

    public function edit($id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        
        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }
        
        //$establecimiento_id=Establecimiento::pluck('nombre_establecimiento','id');
        $establecimiento = DB::table('establecimientos')->get();
        $establecimiento_id = $user->establecimiento_id;
                
        $nivel=Establecimiento::find($user->establecimiento_id);
        
        $tipo=2; //editar
        $estado=$user->estado;  

        $num_can=DB::table('cans')->where('active',TRUE)->count();
        if($nivel->nivel_id>1){
            $model_rubros= new Servicio();            
            $servicio_id = $user->servicio_id;           
            $servicio = $model_rubros->getServicio($establecimiento_id);
            $muestra=1;
            $rubro_id = $user->rol;   
        }
        else
        {
            $servicio = collect(['id' => '0','nombre_servicio' => 'NO APLICA']);
            $servicio_id = 0;           
            $muestra=0;
            $rubro_id = 7; 
        }
        
        return view('admin.users.edit')->with('user', $user)->with('establecimiento',$establecimiento)->with('servicio',$servicio)->with('tipo',$tipo)->with('servicio_id',$servicio_id)->with('num_can',$num_can)->with('establecimiento_id',$establecimiento_id)->with('muestra',$muestra)->with('estado',$estado)->with('rubro_id',$rubro_id);
    }

    public function edit_responsable($id,$valor_id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        
        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }
        
        //$establecimiento_id=Establecimiento::pluck('nombre_establecimiento','id');
                
        $estado=$user->estado;  

        $num_can=DB::table('cans')->where('active',TRUE)->count();
        $model_rubros= new Servicio();            
        $servicio_id = $user->servicio_id;           
        $rol_id = $user->rol;  
        $establecimiento_id=Auth::user()->establecimiento_id;         
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;
        
        

        return view('site.users.edit')->with('user', $user)->with('servicio_id',$servicio_id)->with('rol_id', $rol_id)->with('num_can',$num_can)->with('nivel',$nivel)->with('estado',$estado)->with('valor_id',$valor_id);
    }

    public function edit_responsable_rectificacion($id,$valor_id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        
        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }
        
        //$establecimiento_id=Establecimiento::pluck('nombre_establecimiento','id');
                
        $estado=$user->estado;  

        $num_can=DB::table('cans')->where('active',TRUE)->count();
        $model_rubros= new Servicio();            
        $servicio_id = $user->servicio_id;           
        $rol_id = $user->rol;  
        $establecimiento_id=Auth::user()->establecimiento_id;         
        $establecimiento = Establecimiento::find($establecimiento_id);
        $nivel=$establecimiento->nivel_id;
        
        

        return view('site.users.edit_rectificacion')->with('user', $user)->with('servicio_id',$servicio_id)->with('rol_id', $rol_id)->with('num_can',$num_can)->with('nivel',$nivel)->with('estado',$estado)->with('valor_id',$valor_id);
    }


    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }

        
        //Concatenamos  el nombre y apellido en mayuscula
        $name=strtoupper( $request->input("nombres")." ".$request->input("apellido_paterno")." ".$request->input("apellido_materno") ) ;
        //Asignamos el dni
        $dni=$request->input("dni") ;
        //Asignamos el nombre
        $nombres=strtoupper( $request->input("nombres") ) ;
        //Asignamos el apellido
        $apellido_paterno=strtoupper( $request->input("apellido_paterno") ) ;
        //Asignamos el apellido
        $apellido_materno=strtoupper( $request->input("apellido_materno") ) ;
        //Asignamos el telefono
        $telefono=$request->input("telefono");
        //Asignamos el establecimiento
        $establecimiento_id=$request->input("establecimiento_id");
        //Buscamos la descripcion del grado
        $establecimiento = Establecimiento::findOrFail($establecimiento_id);
        //asignamos el nombre del establecimiento
        $nombre_establecimiento=$establecimiento->nombre_establecimiento;
        //Asignamos el grado
        $grado_id=0;
        //Buscamos el nombre del establecimiento
        $grado = $request->input("grado");
        //asignamos el nombre del establecimiento

        //Asignamos el email
        $email=$request->input("email");
        
        $rol=$request->input("rol");

        $cip=$request->input("cip");

        $estado = $request->input("estado");
        if($estado!="")$estado = 1;
        else $estado=0;
        
        //dd($rol);
        $nivel=$establecimiento->nivel_id;        

        if ($nivel>1)
        {
            $servicio_id=$request->input("servicio_id");    

            switch ($rol) {
                case 1:
                    $nombre_servicio='Administrador';
                    break;
                
                case 2:
                        
                        //Buscamos el rubro
                        $rubro = DB::table('servicios')
                                ->where('id',$servicio_id)
                                ->get();
                        
                        //asignamos el nombre del establecimiento
                        $nombre_servicio=$rubro->get(0)->nombre_servicio;
                        break;

                case 3:
                    $nombre_servicio='Productos Farmaceuticos';
                    break;
                
                case 4:
                    $nombre_servicio='Material Biomedico, Instrumental Quirugico y Productos Afines';
                    break;

                case 5:
                    $nombre_servicio='Material e Insumos Dentales';
                    break;

                case 6:
                    $nombre_servicio='Material e Insumos de Laboratorios';
                    break;

                case 7:
                    $nombre_servicio='Farmacia I';
                    break;

                case 8:
                    $nombre_servicio='Material Fotografico y Fonotecnico';
                    break;

                case 11:
                    $nombre_servicio='Ver Reportes';
                    break;

                    
            }
        }
        else
        {   $servicio_id=0;
            $nombre_servicio='Farmacia I';
        }

        //dd($request);

        if (is_null($request->input("password")))
        {
            
            DB::table('users')
                ->where('id', $id )
                        ->update([
                                'name' => $name,
                                'dni' => $dni,
                                'nombres' => $nombres,
                                'apellido_paterno'=>$apellido_paterno,
                                'apellido_materno'=>$apellido_materno,
                                'telefono'=>$telefono,
                                'establecimiento_id'=>$establecimiento_id,
                                'nombre_establecimiento'=>$nombre_establecimiento,
                                'grado_id'=>$grado_id,
                                'email'=>$email,
                                'rol'=>$rol,
                                'estado'=>$estado,
                                'cip'=>$cip,
                                'grado'=>$grado,
                                'servicio_id'=>$servicio_id,
                                'nombre_servicio'=>$nombre_servicio,
                            ]);    
        }
        else
        {
            //Asignamos el password
            $password= bcrypt( $request->input("password") ); 
            DB::table('users')
                ->where('id', $id )
                        ->update([
                                'name' => $name,
                                'dni' => $dni,
                                'nombres' => $nombres,
                                'apellido_paterno'=>$apellido_paterno,
                                'apellido_materno'=>$apellido_materno,
                                'telefono'=>$telefono,
                                'establecimiento_id'=>$establecimiento_id,
                                'nombre_establecimiento'=>$nombre_establecimiento,
                                'grado_id'=>$grado_id,
                                'email'=>$email,
                                'rol'=>$rol,
                                'cip'=>$cip,
                                'grado'=>$grado,
                                'password'=>$password,
                                'servicio_id'=>$servicio_id,
                                'nombre_servicio'=>$nombre_servicio,
                                'estado'=>$estado,
                            ]);
        }    

        
        Flash::success('Usuario actualizado correctamente.');

        return redirect(route('users.index'));
    }

    public function update_responsable($id, Request $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }

        
        //Concatenamos  el nombre y apellido en mayuscula
        $name=strtoupper( $request->input("nombres")." ".$request->input("apellido_paterno")." ".$request->input("apellido_materno") ) ;
        //Asignamos el dni
        $dni=$request->input("dni") ;
        //Asignamos el nombre
        $nombres=strtoupper( $request->input("nombres") ) ;
        //Asignamos el apellido
        $apellido_paterno=strtoupper( $request->input("apellido_paterno") ) ;
        //Asignamos el apellido
        $apellido_materno=strtoupper( $request->input("apellido_materno") ) ;
        
        $grado_id=0;
        //Buscamos el nombre del establecimiento
        $grado = $request->input("grado");
        //asignamos el nombre del establecimiento

        $rol=$request->input("rol_id");

        

        $estado = $request->input("estado");
        if($estado!="")$estado = 1;
        else $estado=0;
        
        
        if ($rol==5 || $rol==4)
        {
            $servicio_id=0;
            $nombre_servicio='NO APLICA';
        }
        else
        {   
            if ($rol==7 || $rol==10)
            {
                $servicio_id=0;
                $nombre_servicio='NO APLICA';
            }
            else
            {
                if ($rol==9){
                    $servicio_id=0;
                    $nombre_servicio='NO APLICA';
                }
                else
                {    
                    $servicio_id=$request->input("servicio_id");    
                    $rubro = DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
                    $nombre_servicio=$rubro->get(0)->nombre_servicio;
                }
            }
                
        }

        DB::table('users')
            ->where('id', $id )
                    ->update([
                            'name' => $name,
                            'dni' => $dni,
                            'nombres' => $nombres,
                            'apellido_paterno'=>$apellido_paterno,
                            'apellido_materno'=>$apellido_materno,
                            'rol'=>$rol,
                            'estado'=>$estado,
                            'grado'=>$grado,
                            'servicio_id'=>$servicio_id,
                            'nombre_servicio'=>$nombre_servicio,
                        ]); 
        
        
        Flash::success('Responsable actualizado correctamente.');

        return redirect(route('users.index_responsable_stock'));
    }

    public function update_responsable_rectificacion($id, Request $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }

        
        //Concatenamos  el nombre y apellido en mayuscula
        $name=strtoupper( $request->input("nombres_rectificacion")." ".$request->input("apellido_paterno_rectificacion")." ".$request->input("apellido_materno_rectificacion") ) ;
        //Asignamos el dni
        $dni=$request->input("dni_rectificacion") ;
        //Asignamos el nombre
        $nombres=strtoupper( $request->input("nombres_rectificacion") ) ;
        //Asignamos el apellido
        $apellido_paterno=strtoupper( $request->input("apellido_paterno_rectificacion") ) ;
        //Asignamos el apellido
        $apellido_materno=strtoupper( $request->input("apellido_materno_rectificacion") ) ;
        
        $grado_id=0;
        //Buscamos el nombre del establecimiento
        $grado = $request->input("grado_rectificacion");
        //asignamos el nombre del establecimiento

        $rol=$request->input("rol_id");

        $estado = $request->input("estado");
        if($estado!="")$estado = 1;
        else $estado=0;
        
        

        if ($rol==5 || $rol==4)
        {
            $servicio_id=0;
            $nombre_servicio='NO APLICA';
        }
        else
        {   
            if ($rol==7 || $rol==10)
            {
                $servicio_id=0;
                $nombre_servicio='NO APLICA';
            }
            else
            {
                if ($rol==9){
                    $servicio_id=0;
                    $nombre_servicio='NO APLICA';
                }
                else
                { 
                    $servicio_id=$request->input("servicio_id");    
                    $rubro = DB::table('servicios')
                            ->where('id',$servicio_id)
                            ->get();
                    $nombre_servicio=$rubro->get(0)->nombre_servicio;
                }
            }
                
        }

        DB::table('users')
            ->where('id', $id )
                    ->update([
                            'name_rectificacion' => $name,
                            'dni_rectificacion' => $dni,
                            'nombres_rectificacion' => $nombres,
                            'apellido_paterno_rectificacion'=>$apellido_paterno,
                            'apellido_materno_rectificacion'=>$apellido_materno,
                            'rol'=>$rol,
                            'estado'=>$estado,
                            'grado_rectificacion'=>$grado,
                            'servicio_id_rectificacion'=>$servicio_id,
                            'nombre_servicio_rectificacion'=>$nombre_servicio,
                        ]);    
        
        Flash::success('Responsable actualizado correctamente.');

        return redirect(route('users.index_responsable_rectificacion'));
    }

    public function destroy($id)
    {
        $responsable = DB::table('responsables')
                        ->where('user_id',$id)
                        ->delete();
    
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User no encontrado');

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

        Flash::success('Usuario borrado correctamente.');

        return redirect(route('users.index'));
    }  

    public function getDivision(Request $request, $establecimiento_id){
        
        if($request->ajax()){
            $establecimientos = Establecimiento::find($establecimiento_id);
                
            if($establecimientos->nivel_id==1){                
                    $division = DB::table('rubros')
                                        ->join('establecimiento_rubro','establecimiento_rubro.rubro_id','rubros.id')
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->get();
            }
            else
            {
                    //$division = Division::where('establecimiento_id',$establecimiento_id)
                    //                    ->get();
                    $division = DB::table('divisions')
                                        ->join('division_establecimiento','division_establecimiento.division_id','divisions.id')
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->get();
            }    
            
            return response()->json($division);        
        }
    }
    public function getMiDivision(Request $request, $user, $establecimiento_id){
        
            $establecimientos = Establecimiento::find($establecimiento_id);
                
            if($establecimientos->nivel_id==1){                
                    $division = DB::table('rubros')
                                        ->join('establecimiento_rubro','establecimiento_rubro.rubro_id','rubros.id')
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->get();
            }
            else
            {
                    $division = DB::table('divisions')
                                        ->join('division_establecimiento','division_establecimiento.division_id','divisions.id')
                                        ->where('establecimiento_id',$establecimiento_id)
                                        ->get();
            }    
            
            return response()->json($division);   

            
    }


    public function getUnidad(Request $request, $division_id){
        
            $unidad = DB::table('dpto')
                            ->where('dpto.division_establecimiento_id',$division_id)
                            ->get();               
        return response()->json($unidad);
    }

    public function getMiUnidad(Request $request, $user, $division_id){
        
        $unidad = DB::table('dpto')
                            ->where('dpto.division_establecimiento_id',$division_id)
                            ->get();               

        return response()->json($unidad);
    }

    public function getServicio(Request $request, $unidad_id){
        
        if($request->ajax()){
 
            $servicio = DB::table('serv')
                        ->where('dpto_id',$unidad_id)
                        ->get();
        }    
            
        return response()->json($servicio);
    }

    public function getMiServicio(Request $request, $user, $unidad_id){
     
        $servicio = DB::table('serv')
                        ->where('dpto_id',$unidad_id)
                        ->get();
        
        return response()->json($servicio);
    }

    public function editar_clave($id)
    {
        if($id==Auth::user()->id){
            $user = $this->userRepository->findWithoutFail($id);
        
            if (empty($user)) {
                Flash::error('Usuario no encontrado');
                return view('home');
            }

            return view('site.users.editar_clave')->with('user', $user);
        }
        else
        {
            return view('home');
        }
    }

   public function update_clave(UpdateUserClaveRequest $request,$id)
    {
        
        if (Hash::check($request->mypassword, Auth::user()->password)){
                DB::table('users')
                ->where('id', $id )
                        ->update(['password' => bcrypt($request->password)]);

                Flash::success('Contraseña actualizada correctamente');
                return view('home');
                
        }
        else
        {   
            Flash::error('No se ha podido cambiar la contraseña, la contraseña ingresada no es correcta');
            return view('home');
            
        }
    }

    public function subir_foto(Request $request,$id)
    {
        $input = $request->all();
        
        if ($request->hasFile('photo')){
            $input['photo'] = '/upload/photo/'.str_slug($id, '-').'.'.$request->photo->getClientOriginalExtension();
            $name_photo=str_slug($id, '-').'.'.$request->photo->getClientOriginalExtension();
            $request->photo->move(public_path('/upload/photo/'), $input['photo']);
        }

        
        DB::table('users')
                ->where('id', $id )
                        ->update(['photo' => $name_photo]);

        return view('home');
    }

    /*
    public function buscar_personal_dni($nro_doc, $tipo_doc) {
        
        $beneficiario=DB::connection('pgsql2')
                    ->table('beneficiarios')
                    ->select('beneficiarios.*')
                    ->where('nrodocafiliado',$nro_doc)
                    ->where('nomtipdocafiliado',$tipo_doc)
                    ->get();

        return $beneficiario;
    }
    */

    public function buscar_personal_dni($nro_doc, $tipo_doc) {
        
        $location_URL = 'https://sigcp.policia.gob.pe:7071/TitularFamiliarWS.svc';
        $wsdl = 'https://sigcp.policia.gob.pe:7071/TitularFamiliarWS.svc?singleWsdl';

        $client = new SoapClient($wsdl, array(
            'location' => $location_URL,
            'uri'      => "",
            'trace'    => 1,            
            ));
        
        $busca_datos = $client->BuscarTitularFamiliar(['TipoBusqueda' => 1,'Documento' => $nro_doc,'Usuario' => '31081306','Clave' => '60318013']);
       
        $json = json_encode($busca_datos);
        $beneficiario_encontrado = json_decode($json,TRUE);
        $beneficiario["dni"]='0';        

        $dni_beneficiario=$nro_doc;
        $cont=0;
        $ncont_titular=0;
        $ncont_familiar=0;

        if($beneficiario_encontrado['BuscarTitularFamiliarResult']!=null){
            if(count($beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'])<13){
                $ncont_titular=count($beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']);                
            }
            if($ncont_titular>1){                
                $beneficiario["dni"]=$dni_beneficiario;
                $beneficiario["nombres"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['NOMBRES'];
                $beneficiario["paterno"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['PATERNO'];
                $beneficiario["materno"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['MATERNO'];
                $beneficiario["carne"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['CARNE'];
                $beneficiario["grado"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['GRADO'];
            }
            else
            {
                $beneficiario["dni"]=$dni_beneficiario;
                foreach($beneficiario_encontrado as $beneficiario1){
                    $beneficiario["nombres"]=$beneficiario1['TitularFamiliar']['NOMBRES'];
                    $beneficiario["paterno"]=$beneficiario1['TitularFamiliar']['PATERNO'];
                    $beneficiario["materno"]=$beneficiario1['TitularFamiliar']['MATERNO'];
                    $beneficiario["carne"]=$beneficiario1['TitularFamiliar']['CARNE'];
                    $beneficiario["grado"]=$beneficiario1['TitularFamiliar']['GRADO'];
                }
            }
        }
        else
        {
            $busca_datos = $client->BuscarTitularFamiliar(['TipoBusqueda' => 3,'Documento' => $nro_doc,'Usuario' => '32089474','Clave' => '47498023']);
            $json = json_encode($busca_datos);
            $beneficiario_encontrado = json_decode($json,TRUE);
            if($beneficiario_encontrado['BuscarTitularFamiliarResult']!=null){
                if(count($beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'])<13){ 
                    $ncont_familiar=count($beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']);                    
                }
                if($ncont_titular>1){  
                    $beneficiario["dni"]=$dni_beneficiario;
                    $beneficiario["nombres"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['NOMBRES'];
                    $beneficiario["paterno"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['PATERNO'];
                    $beneficiario["materno"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['MATERNO'];
                    $beneficiario["carne"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['CARNE'];
                    $beneficiario["grado"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar'][0]['GRADO'];
                }
                else
                {
                    $beneficiario["dni"]=$dni_beneficiario;
                    $beneficiario["nombres"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']['NOMBRES'];
                    $beneficiario["paterno"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']['PATERNO'];
                    $beneficiario["materno"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']['MATERNO'];
                    $beneficiario["carne"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']['CARNE'];
                    $beneficiario["grado"]=$beneficiario_encontrado['BuscarTitularFamiliarResult']['TitularFamiliar']['GRADO'];
                }
            }   
        }
        return $beneficiario;
        
    }

    


}
