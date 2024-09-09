<div class="form-group">
    <label for="email" class="col-md-3 control-label">Foto (160X160)</label>
    <div class="col-md-6">
        <input type="file" id="photo" name="photo" class="form-control" required="required">
        <span class="help-block with-errors"></span>
        <br/><br/>
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Subir Foto', ['class' => 'btn btn-success']) !!}
</div>

