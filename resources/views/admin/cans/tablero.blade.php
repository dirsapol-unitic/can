@extends('layouts.app')

@section('title', ' - ')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
	<div class="content">       
  <div class="row">
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-purple">
            <div class="inner">
              <h3>{!!$cantidad_normostock !!}</h3>

              <p>Normostock</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>2,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>        
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-maroon">
            <div class="inner">
              <h3>{!!$cantidad_substock !!}</h3>

              <p>Substock</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>3,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{!!$cantidad_sobrestock !!}</h3>

              <p>Sobrestock</p>
            </div>
            <div class="icon">
              <i class="fa fa-database"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>4,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">                                      
            <div class="inner">
              <h3>{!!$cantidad_desabastecido !!}</h3>

              <p>Desabastecimiento</p>
            </div>
            <div class="icon">
              <i class="fa fa-dropbox"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>5,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-orange">
            <div class="inner">
              <h3>{!!$cantidad_sinrotacion !!}</h3>

              <p>Sin Rotación</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>6,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{!!$cantidad_existente !!}</h3>

              <p>Existencia</p>
            </div>
            <div class="icon">
              <i class="fa fa-cubes"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>7,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{!!$cantidad_disponible !!}</h3>

              <p>Disponibilidad</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>8,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-navy">
            <div class="inner">
              <h3>{!!$total_items !!}</h3>

              <p>Total Registro</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-text"></i>
            </div>
            <a href="{!! route('cans.reportes',['id'=>1,'can_id'=>$can_id]) !!}" class="small-box-footer">Ver Información <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div> 
        <!--div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                      <div class="small-box bg-aqua">
                        <div class="inner">
                          <h3>150</h3>
                          {//!! Auth::user()->total_medicamentos() !!}
                          <p>Medicamentos Asignados</p>
                        </div>
                        <div class="icon">
                          <i class="fa fa-medkit"></i>
                        </div>
                        <a href="#" class="small-box-footer">Ver info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                      <div class="small-box bg-green">
                        <div class="inner">
                          <h3>53<sup style="font-size: 20px">%</sup></h3>
                          <p>Dispositivos Médicos Asignados</p>
                        </div>
                        <div class="icon">
                          <i class="fa fa-stethoscope"></i>
                        </div>
                        <a href="#" class="small-box-footer">Ver info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                      <div class="small-box bg-yellow">
                        <div class="inner">
                          <h3>44</h3>
                          <p>Registrar ICI - Medicamentos</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">Ingresar<i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                      <div class="small-box bg-red">
                        <div class="inner">
                          <h3>65</h3>
                          <p>Registrar ICI - Dispositivos</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">Ingresar <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                </div>
            </div>
        </div-->
    </div>

@stop


