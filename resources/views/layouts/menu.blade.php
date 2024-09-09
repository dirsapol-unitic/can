@if (Auth::user()->rol==1) <!--Administrador-->
<ul class="sidebar-menu">
    <li class="header">MENU</li>
    <li class="{{ Request::is('home*') ? 'active' : '' }}">
        <a href="{{ url('/home') }}">
            <i class="fa fa-fw fa-home "></i>
            <span><small>Inicio</small></span>
        </a>    
    </li>   
    <li class="{{ Request::is('cans*') ? 'active' : '' }}">
        <a href="{!! route('cans.index') !!}"> <i class="fa fa-truck"></i> <span><small>CAN</small></span>
        </a>  
    </li>
    <li class="{{ Request::is('petitorios*') ? 'active' : '' }}">
        <a href="{!! route('petitorios.index') !!}">
            <i class="fa fa-fw fa-medkit"></i>
            <span>Petitorio</span>
        </a>
    </li>
    <li class="{{ Request::is('servicio*') ? 'active' : '' }}">
                <a href="{!! route('servicios.index') !!}"><i class="fa fa-th-large"></i><span>Rubros</span></a>
            </li>
    <li class="{{ Request::is('e*') ? 'active' : '' }}">
        <a href="{!! route('establecimientos.index') !!}">
            <i class="fa fa-fw fa-industry "></i>
            <span><small>Establecimientos</small></span>
        </a>    
    </li>
    <li class="{{ Request::is('users*') ? 'active' : '' }}">
        <a href="{!! route('users.index') !!}">
            <i class="fa fa-fw fa-user"></i>
            <span><small>Usuarios</small></span>
        </a>    
    </li>   
    <li class=" treeview">
        <a href="#">
            <i class="fa fa-book"></i><span>Manual llenar CAN</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="{{ Request::is('petitorios*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',1) !!}"><i class="fa fa-book"></i><span>Ingresar - Salir</span></a>
                        </li>
                        <li class="{{ Request::is('tipoDispositivoMedicos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',9) !!}"><i class="fa fa-book"></i><span>Entorno Trabajo Nivel I</span></a>
                        </li>
                        <li class="{{ Request::is('tipoDispositivoMedicos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',2) !!}"><i class="fa fa-book"></i><span>Entorno Trabajo Nivel II</span></a>
                        </li>
                        <li class="{{ Request::is('tipoDispositivoMedicos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',12) !!}"><i class="fa fa-book"></i><span>Entorno Trabajo Responsable</span></a>
                        </li>                        
                        <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',3) !!}"><i class="fa fa-book"></i><span>Asignar Productos</span></a>
                        </li> 
                        <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',4) !!}"><i class="fa fa-book"></i><span>Registrar Productos</span></a>
                        </li> 
                        <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',13) !!}"><i class="fa fa-book"></i><span>Activar Servicios Nivel II</span></a>
                        </li> 
                        <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',14) !!}"><i class="fa fa-book"></i><span>Visualizar Productos</span></a>
                        </li> 
                        <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',5) !!}"><i class="fa fa-book"></i><span>Descargar CAN</span></a>
                        </li>
                        <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',8) !!}"><i class="fa fa-book"></i><span>Subir Archivo</span></a>
                        </li>
                        <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',6) !!}"><i class="fa fa-book"></i><span>Cambiar Contraseña</span></a>
                        </li>
                        
        </ul>
        <li class=" treeview
            {{ Request::is('p*') ? 'active' : '' }}">
            <a href="{!! route('estimacion.manual',7) !!}"><i class="fa fa-book"></i><span>Petitorio</span></a>
        </li> 
    </li>
</ul>    
@else
    @if (Auth::user()->rol==2) <!--Llenar CAN-->
    <ul class="sidebar-menu">
        <li class="header">MENU</li>
        @if (Auth::user()->establecimiento_id < 4 ||  Auth::user()->establecimiento_id==30 || Auth::user()->establecimiento_id==69) <!--Llenar CAN-->
            <li class=" treeview
                {{ Request::is('p*') ? 'active' : '' }}">
                <a href="{{ url('estimacion_servicio') }}">
                    <i class="fa fa-fw fa-home "></i>
                    <span><small>Listar</small></span>
                </a>    
            </li>            
            <li class=" treeview">
                <a href="#">
                    <i class="fa fa-book"></i><span>Manual</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('petitorios*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',1) !!}"><i class="fa fa-book"></i><span>Ingresar - Salir</span></a>
                    </li>
                    <li class="{{ Request::is('tipoDispositivoMedicos*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',9) !!}"><i class="fa fa-book"></i><span>Entorno Trabajo</span></a>
                    </li>
                    <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',10) !!}"><i class="fa fa-book"></i><span>Asignar Productos</span></a>
                    </li> 
                    <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',4) !!}"><i class="fa fa-book"></i><span>Registrar Productos</span></a>
                    </li> 
                    <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',5) !!}"><i class="fa fa-book"></i><span>Descargar CAN</span></a>
                    </li>
                    <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',6) !!}"><i class="fa fa-book"></i><span>Cambiar Contraseña</span></a>
                    </li>
                </ul>
            </li>
            <li class=" treeview
                {{ Request::is('p*') ? 'active' : '' }}">
                <a href="{!! route('estimacion.manual',7) !!}"><i class="fa fa-book"></i><span>Petitorio</span></a>
            </li> 
            <li class=" treeview
                {{ Request::is('p*') ? 'active' : '' }}">
                <a href="{!! route('estimacion.manual',20) !!}"><i class="fa fa-phone"></i><span>Soporte y Consulta</span></a>
            </li> 
        @endif
    </ul>
    @else
        @if (Auth::user()->rol>=3 and Auth::user()->rol<=6 || Auth::user()->rol==8 ) <!--Responsable de Farmacia-->
        <li class=" treeview
                            {{ Request::is('p*') ? 'active' : '' }}">
                            <a href="{{ url('listar_can_servicio') }}">
                                <i class="fa fa-fw fa-home "></i>
                                <span><small>Listar</small></span>
                            </a>    
                        </li>
                    </li>
                    <li class=" treeview
                    {{ Request::is('p*') ? 'active' : '' }}">
                    <a href="{!! route('estimacion.manual',20) !!}"><i class="fa fa-phone"></i><span>Soporte y Consulta</span></a>
                    </li> 

        @else

            @if (Auth::user()->rol==7) <!--Responsable de Farmacia-->

                <ul class="sidebar-menu">
                    <li class="header">MENU</li>
                    @if (Auth::user()->establecimiento_id < 4 ||  Auth::user()->establecimiento_id==30 || Auth::user()->establecimiento_id==69) <!--Listar CAN-->
                        <li class=" treeview
                            {{ Request::is('p*') ? 'active' : '' }}">
                            <a href="{{ url('listar_can') }}">
                                <i class="fa fa-fw fa-home "></i>
                                <span><small>Listar</small></span>
                            </a>    
                        </li>                                                  
                        <li class=" treeview">
                            <a href="#">
                                <i class="fa fa-book"></i><span>Manual</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="{{ Request::is('petitorios*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',1) !!}"><i class="fa fa-book"></i><span>Ingresar - Salir</span></a>
                                </li>
                                <li class="{{ Request::is('tipoDispositivoMedicos*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',12) !!}"><i class="fa fa-book"></i><span>Entorno Trabajo</span></a>
                                </li>
                                <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',13) !!}"><i class="fa fa-book"></i><span>Activar Servicios</span></a>
                                </li> 
                                <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',14) !!}"><i class="fa fa-book"></i><span>Visualizar Productos</span></a>
                                </li> 
                                <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',5) !!}"><i class="fa fa-book"></i><span>Descargar CAN</span></a>
                                </li>
                                <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',8) !!}"><i class="fa fa-book"></i><span>Subir Archivo</span></a>
                                </li>                        
                                <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                    <a href="{!! route('estimacion.manual',6) !!}"><i class="fa fa-book"></i><span>Cambiar Contraseña</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class=" treeview
                            {{ Request::is('p*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',7) !!}"><i class="fa fa fa-file-excel-o"></i><span>Petitorio</span></a>
                        </li>
                        <li class=" treeview
                        {{ Request::is('p*') ? 'active' : '' }}">
                        <a href="{!! route('estimacion.manual',20) !!}"><i class="fa fa-phone"></i><span>Soporte y Consulta</span></a>
                        </li> 
                    @else
                       
                                <li class=" treeview
                                    {{ Request::is('p*') ? 'active' : '' }}">
                                    <a href="{{ url('listar_can') }}">
                                        <i class="fa fa-fw fa-home "></i>
                                        <span><small>Listar</small></span>
                                    </a>    
                                </li>                                                              
                                <li class=" treeview">
                                <a href="#">
                                    <i class="fa fa-book"></i><span>Manual</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ Request::is('petitorios*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',1) !!}"><i class="fa fa-book"></i><span>Ingresar - Salir</span></a>
                                    </li>
                                    <li class="{{ Request::is('tipoDispositivoMedicos*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',2) !!}"><i class="fa fa-book"></i><span>Entorno Trabajo</span></a>
                                    </li>
                                    <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',21) !!}"><i class="fa fa-book"></i><span> Responsables</span></a>
                                    </li>
                                    <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',3) !!}"><i class="fa fa-book"></i><span>Asignar Productos</span></a>
                                    </li> 
                                    <li class="{{ Request::is('tipoUsos*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',4) !!}"><i class="fa fa-book"></i><span>Registrar Productos</span></a>
                                    </li> 
                                    <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',5) !!}"><i class="fa fa-book"></i><span>Descargar CAN</span></a>
                                    </li>
                                    <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',8) !!}"><i class="fa fa-book"></i><span>Subir Archivo</span></a>
                                    </li>
                                    <li class="{{ Request::is('unidadMedidas*') ? 'active' : '' }}">
                                        <a href="{!! route('estimacion.manual',8) !!}"><i class="fa fa-book"></i><span>Subir Archivo</span></a>
                                    </li>
                                    
                                </ul>
                            </li>
                            <li class=" treeview
                            {{ Request::is('p*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',7) !!}"><i class="fa fa fa-file-excel-o"></i><span>Petitorio</span></a>
                        </li>
                            <li class=" treeview
                            {{ Request::is('p*') ? 'active' : '' }}">
                            <a href="{!! route('estimacion.manual',20) !!}"><i class="fa fa-phone"></i><span>Soporte y Consulta</span></a>
                        </li> 
                    @endif   
            @else
                    @if (Auth::user()->rol==11) <!--Ver Reporte-->
                        <li class="header">MENU</li>
                        <li class="{{ Request::is('home*') ? 'active' : '' }}">
                            <a href="{{ url('/home') }}">
                                <i class="fa fa-fw fa-home "></i>
                                <span><small>Inicio</small></span>
                            </a>    
                        </li>   
                        <li class="{{ Request::is('cans*') ? 'active' : '' }}">
                            <a href="{!! route('cans.index') !!}"> <i class="fa fa-truck"></i> <span><small>CAN</small></span>
                            </a>  
                        </li>
                        <li class="{{ Request::is('petitorios*') ? 'active' : '' }}">
                            <a href="{!! route('petitorios.index') !!}">
                                <i class="fa fa-fw fa-medkit"></i>
                                <span>Petitorio</span>
                            </a>
                        </li>
                    @endif    
                </ul>
                
            @endif    
        @endif    
    @endif
@endif





