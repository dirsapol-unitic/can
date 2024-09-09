<?php 
    if($valor==1):
        $multianual=0;
        $stock=0;
        $extraordinario=0;
        $tiempo = 0;
    else:
        $multianual=$can->multianual;
        $stock=$can->stock;
        $extraordinario=$can->extraordinario;
        $tiempo=$can->tiempo;
    endif;

    ?>
<div class="form-group col-sm-12">
    <!-- Nombre -->
    <div class="form-group col-sm-3">
        {!! Form::label('nombre_can', 'Nombre CAN:') !!}
        {!! Form::text('nombre_can', null, ['class' => 'form-control']) !!}
    </div>
    <!-- Mes Field -->
    <div class="form-group col-sm-2">
        {!! Form::label('mes', 'Mes:') !!}
        {!! Form::select('mes_id',$mes , null, ['class' => 'form-control select2']) !!}    
        
    </div>
    <!-- Ano Field -->
    @if($valor==1)
    <div class="form-group col-sm-2">
        {!! Form::label('ano', 'Ano:') !!}
        <select class="form-control select2" id="year_id" name="year_id"
          tabindex="5" style="width: 100%">
          <option value="10">2024</option>
          <option value="9">2023</option>
          <option value="8">2022</option>
        </select>
    </div>
    @else
    <div class="form-group col-sm-2">
        {!! Form::label('ano', 'Ano:') !!}
        {!! Form::select('year_id', $ano, null, ['class' => 'form-control select2']) !!}
    </div>
    @endif
    <div class="form-group col-sm-2">
        {!! Form::label('tiempo', 'Tiempo:') !!}
        <select class="form-control select2" id="tiempo" name="tiempo"
          tabindex="5" style="width: 100%">
          <option value="1" <?php if($tiempo==1) echo "selected"; ?> >1 años</option>
          <option value="2" <?php if($tiempo==2) echo "selected"; ?> >2 años</option>
          <option value="3" <?php if($tiempo==3) echo "selected"; ?> >3 años</option>
        </select>
    </div>
    

    
    <!-- Submit Field -->
</div>
<div class="form-group col-sm-12">
    <div class="form-group col-sm-2">
        <br/>
        {!! Form::label('Multianual', 'Multianual:') !!}
        <input type="checkbox" value="1" name="multianual" <?php if($multianual == 1)echo 'checked="checked"';?>/>
    </div>
    <div class="form-group col-sm-1">
        <br/>
        {!! Form::label('Stock', 'Stock:') !!}
        <input type="checkbox" value="1" name="stock" <?php if($stock == 1)echo 'checked="checked"';?>/>
    </div>
    <div class="form-group col-sm-2">
        <br/>
        {!! Form::label('Extraordinario', 'Extraordinario:') !!}
        <input type="checkbox" value="1" name="extraordinario" <?php if($extraordinario == 1)echo 'checked="checked"';?>/>
    </div>
</div>
<div class="form-group col-sm-12">
    <button type="submit" value="Guardar" class="btn btn-success">Guardar <i class="fa fa-save"></i></button>
    <a href="{!! route('cans.index') !!}" class="btn btn-danger">Cancelar <i class="glyphicon glyphicon-remove"></i></a>
    
</div>

