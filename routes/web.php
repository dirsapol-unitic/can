<?php

use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
  
    //Ruta del Home de Administración
    Route::get('/home', 'HomeController@index')->name('home.index');

    //Rutas del Usuario
    Route::resource('users', 'Admin\UserController');
    Route::get('responsables/{can_id}', 'Admin\UserController@index_responsable')->name('users.index_responsable');
    Route::get('responsables_stocks/{can_id}', 'Admin\UserController@index_responsable_stock')->name('users.index_responsable_stock');
    Route::get('responsables_rectificacion/{can_id}', 'Admin\UserController@index_responsable_rectificacion')->name('users.index_responsable_rectificacion');
    Route::get('responsables/create/{can_id}/{opcion}', 'Admin\UserController@create_responsable')->name('users.create_responsable');
    Route::get('responsables_servicios/create/{can_id}/{servicio_id}/{establecimiento_id}', 'Admin\UserController@create_responsable_servicios')->name('users.create_responsable_servicios');
    Route::post('responsables/guardar_responsable/', 'Admin\UserController@store_responsable')->name('users.store_responsable');
    Route::put('responsables/guardar_responsable/', 'Admin\UserController@store_responsable')->name('users.store_responsable');
    Route::patch('responsables/guardar_responsable/', 'Admin\UserController@store_responsable')->name('users.store_responsable');

    Route::post('responsables_servicios/guardar_responsable/', 'Admin\UserController@store_responsable_servicios')->name('users.store_responsable_servicios');
    Route::put('responsables_servicios/guardar_responsable/', 'Admin\UserController@store_responsable_servicios')->name('users.store_responsable_servicios');
    Route::patch('responsables_servicios/guardar_responsable/', 'Admin\UserController@store_responsable_servicios')->name('users.store_responsable_servicios');
    
    Route::get('responsables/edit_rectificacion/{id}/{valor_id}', 'Admin\UserController@edit_responsable_rectificacion')->name('users.edit_responsable_rectificacion');
    
    //Route::get('responsables/actualizar_responsable/{id}', 'Admin\UserController@update_responsable')->name('users.update_responsable');
    Route::get('responsables/edit_responsable/{id}/{opcion}', 'Admin\UserController@edit_responsable')->name('users.edit_responsable');
    Route::post('responsables/edit_responsable/{id}', 'Admin\UserController@update_responsable')->name('users.update_responsable');
    //Route::put('responsables/actualizar_responsable/{id}', 'Admin\UserController@update_responsable')->name('users.update_responsable');
    Route::patch('responsables/edit_responsable/{id}', 'Admin\UserController@update_responsable')->name('users.update_responsable');

    Route::get('responsables/edit_responsable_servicio/{id}/{valor_id}', 'Admin\UserController@edit_responsable_servicio')->name('users.edit_responsable_servicio');
    Route::post('responsables/edit_responsable_servicio/{id}', 'Admin\UserController@update_responsable_servicio')->name('users.update_responsable_servicio');
    Route::patch('responsables/edit_responsable_servicio/{id}', 'Admin\UserController@update_responsable_servicio')->name('users.update_responsable_servicio');


    Route::post('responsables/actualizar_responsable/{id}', 'Admin\UserController@update_responsable_rectificacion')->name('users.update_responsable_rectificacion');
    Route::put('responsables/actualizar_responsable/{id}', 'Admin\UserController@update_responsable_rectificacion')->name('users.update_responsable_rectificacion');
    Route::patch('responsables/actualizar_responsable/{id}', 'Admin\UserController@update_responsable_rectificacion')->name('users.update_responsable_rectificacion');

    Route::get('can/perfil/editar_clave/{id}', 'Admin\UserController@editar_clave')->name('users.editar_clave');
    Route::patch('can/perfil/editar_clave/{id}', 'Admin\UserController@update_clave')->name('users.update_clave');
    Route::patch('can/perfil/subir_foto/{id}', 'Admin\UserController@subir_foto')->name('users.subir_foto');
    Route::get('buscar_personal_dni/{nro_doc}/{tipo_doc}', 'Admin\UserController@buscar_personal_dni')->name('users.buscar_personal_dni');
    
    //////////Establecimientos//////////////////////
    Route::resource('establecimientos', 'Admin\EstablecimientoController');
        Route::get('establecimiento/dispositivos/{establecimiento_id}', 'Admin\EstablecimientoController@ver_dispositivos')->name('establecimientos.ver_dispositivos');
    Route::get('establecimiento/asignar_dispositivos/{establecimiento_id}', 'Admin\EstablecimientoController@asignar_dispositivos')->name('establecimientos.asignar_dispositivos');
    Route::put('establecimiento/asignar_dispositivos/{establecimiento_id}', 'Admin\EstablecimientoController@guardar_dispositivos')->name('establecimientos.guardar_dispositivos');
    Route::patch('establecimiento/asignar_dispositivos/{establecimiento_id}', 'Admin\EstablecimientoController@guardar_dispositivos')->name('establecimientos.guardar_dispositivos');

    Route::get('establecimiento/rubros/{establecimiento_id}', 'Admin\EstablecimientoController@ver_rubros')->name('establecimientos.ver_rubros');
    Route::get('establecimiento/asignar_rubros/{establecimiento_id}', 'Admin\EstablecimientoController@asignar_rubros')->name('establecimientos.asignar_rubros');
    Route::put('establecimiento/asignar_rubros/{establecimiento_id}', 'Admin\EstablecimientoController@guardar_rubros')->name('establecimientos.guardar_rubros');
    Route::patch('establecimiento/asignar_rubros/{establecimiento_id}', 'Admin\EstablecimientoController@guardar_rubros')->name('establecimientos.guardar_rubros');

    Route::get('establecimiento/division/{establecimiento_id}', 'Admin\EstablecimientoController@ver_division')->name('establecimientos.ver_division');
    Route::get('establecimiento/asignar_divisions/{establecimiento_id}', 'Admin\EstablecimientoController@asignar_divisions')->name('establecimientos.asignar_divisions');
    Route::put('establecimiento/asignar_divisions/{establecimiento_id}', 'Admin\EstablecimientoController@guardar_divisions')->name('establecimientos.guardar_divisions');
    Route::patch('establecimiento/asignar_divisions/{establecimiento_id}', 'Admin\EstablecimientoController@guardar_divisions')->name('establecimientos.guardar_divisions');

    //////////////////Servicios//////////////////////////////
    Route::resource('servicios', 'Admin\ServicioController');    
    Route::get('servicio/medicamentos/{servicio_id}', 'Admin\ServicioController@ver_medicamentos')->name('servicios.ver_medicamentos');
    Route::get('servicio/insertar_servicio/{servicio_id}', 'Admin\ServicioController@insertar_servicio')->name('servicios.insertar_servicio');
    Route::get('servicio/asignar_medicamentos/{servicio_id}', 'Admin\ServicioController@asignar_medicamentos')->name('servicios.asignar_medicamentos');
    Route::put('servicio/asignar_medicamentos/{servicio_id}', 'Admin\ServicioController@guardar_medicamentos')->name('servicios.guardar_medicamentos');    
    Route::patch('servicio/asignar_medicamentos/{servicio_id}', 'Admin\ServicioController@guardar_medicamentos')->name('servicios.guardar_medicamentos');
    Route::get('servicio/dispositivos/{servicio_id}', 'Admin\ServicioController@ver_dispositivos')->name('servicios.ver_dispositivos');    
    Route::get('servicio/asignar_dispositivos/{servicio_id}', 'Admin\ServicioController@asignar_dispositivos')->name('servicios.asignar_dispositivos');
    Route::put('servicio/asignar_dispositivos/{servicio_id}', 'Admin\ServicioController@guardar_dispositivos')->name('servicios.guardar_dispositivos');
    Route::patch('servicio/asignar_dispositivos/{servicio_id}', 'Admin\ServicioController@guardar_dispositivos')->name('servicios.guardar_dispositivos');
    Route::get('exportServicio/{type}/{servicio_id}/{tipo}', 'Admin\ServicioController@exportServicio')->name('servicios.exportServicio');
    Route::get('descargar-servicios/{servicio_id}/{tipo}', 'Admin\ServicioController@pdf_servicio')->name('servicios.pdf_servicio');

    //Route::put('servicio/cargar_datos/{servicio_id}', 'Admin\ServicioController@cargar_datos_medicamentos')->name('servicios.cargar_datos_medicamentos');
        
//////////////////////////////////////////////////////////////////////////////////////
    
    Route::resource('regions', 'Admin\RegionController');
    Route::resource('nivels', 'Admin\NivelController');
    Route::resource('categorias', 'Admin\CategoriaController');
    Route::resource('tipoEstablecimientos', 'Admin\TipoEstablecimientoController');
    Route::resource('tipoInternamientos', 'Admin\TipoInternamientoController');
    Route::resource('disas', 'Admin\DisaController');
    Route::resource('departamentos','Admin\DepartamentoController');
    Route::resource('provincias', 'Admin\ProvinciaController');
    Route::get('provincias/{id}','Admin\DistritoController@getProvincias')->name('distritos.getProvincias');
    
    ///////////////////////////DISTRITOS///////////////////////////
    Route::resource('distritos', 'Admin\DistritoController');
    Route::get('distritos/{distrito}/miprovincia/{departamento_id}','Admin\DistritoController@getProvincias')->name('distritos.getMiProvincias');
    Route::get('distritos/miprovincia/{departamento_id}','Admin\DistritoController@getMiProvincias')->name('distritos.getMiProvincias');
    ///////////////////////////////////////////////////////////////
    Route::resource('tipoDispositivoMedicos', 'Admin\TipoDispositivoMedicoController');
    Route::resource('unidadMedidas', 'Admin\UnidadMedidaController');
    Route::resource('tipoUsos', 'Admin\TipoUsoController');
    Route::resource('petitorios', 'Admin\PetitorioController');
    Route::get('exportPetitorio/{type}', 'Admin\PetitorioController@exportPetitorio')->name('petitorios.exportPetitorio');

    Route::resource('grados', 'Admin\GradoController');
    Route::resource('years', 'Admin\YearController');

    Route::get('users/midivision/{establecimiento_id}','Admin\UserController@getDivision')->name('users.getDivision');
    Route::get('users/{user}/midivision/{establecimiento_id}','Admin\UserController@getMiDivision')->name('users.getMiDivision');
    Route::get('users/miunidad/{division_id}','Admin\UserController@getUnidad')->name('users.getUnidad');
    Route::get('users/{user}/miunidad/{division_id}','Admin\UserController@getMiUnidad')->name('users.getMiUnidad');
    Route::get('users/miservicio/{servicio_id}','Admin\UserController@getServicio')->name('users.getServicio');
    Route::get('users/{user}/miservicio/{servicio_id}','Admin\UserController@getMiServicio')->name('users.getMiServicio');
    

//////////////////////////////////////////////////////
    Route::resource('cans', 'Admin\CanController');    

    Route::get('can/match/{id}/{establecimiento_id}/{tipo}', 'Admin\CanController@ejecutar_matchado')->name('farmacia_servicios.ejecutar_matchado');
    Route::get('can/necesidad/', 'Admin\CanController@actualiza_necesidad')->name('farmacia_servicios.actualiza_necesidad');
    Route::get('can/final/{can_id}/{establecimiento_id}', 'Admin\CanController@actualiza_final')->name('farmacia_servicios.actualiza_final');
    
    Route::get('servicios_mostrar_med/{id}', 'Admin\CanController@show_medicamentos')->name('cans.show_medicamentos');

    Route::get('mostrar_medicamento_nivel_1/{id}/{opcion}/{can_id}', 'Admin\CanController@show_medicamentos_nivel_1')->name('cans.show_medicamentos_nivel_1');
    Route::get('mostrar_medicamento_nivel_2/{id}/{opcion}/{can_id}', 'Admin\CanController@show_medicamentos_nivel_2')->name('cans.show_medicamentos_nivel_2');
    Route::get('mostrar_medicamento_ajuste_nivel_2/{id}', 'Admin\CanController@show_medicamentos_ajuste_nivel_2')->name('cans.show_medicamentos_ajuste_nivel_2');
    Route::get('mostrar_medicamento_nivel_3/{id}/{opcion}/{can_id}', 'Admin\CanController@show_medicamentos_nivel_3')->name('cans.show_medicamentos_nivel_3'); 
       
    Route::get('listar_archivos_can/{can_id}/{establecimiento_id}', 'Admin\CanController@listar_archivos_can')->name('cans.listar_archivos_can');
    Route::get('listar_archivos_can_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@listar_archivos_can_servicio')->name('cans.listar_archivos_can_servicio');
    Route::get('listar_archivos_can_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Admin\CanController@listar_archivos_can_rubro')->name('cans.listar_archivos_can_rubro');
    Route::get('subiendo_archivos/{can_id}/{establecimiento_id}', 'Admin\CanController@subiendo_archivos_can')->name('cans.subiendo_archivos_can');
    Route::get('subiendo_archivos_rubro/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@subiendo_archivos_rubro')->name('cans.subiendo_archivos_rubro');
    
    Route::patch('farmacia/archivo/subir_archivo_correccion/{id}/{can_id}/{file_id}', 'Admin\CanController@subir_archivo_correccion')->name('cans.subir_archivo_correccion');
    //Route::patch('farmacia/archivo/subir_rubro/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@subir_archivo_correccion')->name('cans.subir_archivo_correccion');
    Route::patch('farmacia/archivo/subir_archivo_rubro/{id}/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@subir_archivo_rubro')->name('cans.subir_archivo_rubro');

    Route::get('consolidado_medicamento_nacional/{id}/{tipo}', 'Admin\CanController@consolidado_medicamento_nacional')->name('cans.consolidado_medicamento_nacional');
    Route::get('consolidado_nacional/{id}/{tipo}', 'Admin\CanController@consolidado_nacional')->name('cans.consolidado_nacional');
    Route::get('consolidado_nacional_tipo/{id}/{tipo}', 'Admin\CanController@consolidado_nacional_tipo')->name('cans.consolidado_nacional_tipo');
    Route::get('consolidado_nacional_tipo2/{id}/{tipo}', 'Admin\CanController@consolidado_nacional_tipo2')->name('cans.consolidado_nacional_tipo2');
    Route::get('consolidado_region/{id}/{tipo}', 'Admin\CanController@consolidado_region_producto')->name('cans.consolidado_region_producto');
    Route::get('consolidado_establecimiento2/{id}/{tipo}', 'Admin\CanController@consolidado_establecimiento_producto2')->name('cans.consolidado_establecimiento_producto2');
    Route::get('consolidado_establecimiento/{id}/{tipo}', 'Admin\CanController@consolidado_establecimiento_producto')->name('cans.consolidado_establecimiento_producto');
    Route::get('consolidado_establecimiento_producto_tipo_dispositivo/{id}/{tipo_dispositivo}', 'Admin\CanController@consolidado_establecimiento_producto_tipo_dispositivo')->name('cans.consolidado_establecimiento_producto_tipo_dispositivo');
    Route::get('rubros_total/{id}', 'Admin\CanController@establecimientos_can')->name('cans.establecimientos_can');
    Route::get('establecimiento_can_servicio/{id}/{tipo}', 'Admin\CanController@establecimientos_servicio_can')->name('cans.establecimientos_servicio_can');
    Route::get('establecimientos_servicio_can_tipo/{id}/{tipo}', 'Admin\CanController@establecimientos_servicio_can_tipo')->name('cans.establecimientos_servicio_can_tipo');
    Route::get('establecimientos_can_2020/{id}', 'Admin\CanController@establecimientos_can_2020')->name('cans.establecimientos_can_2020');
    Route::get('establecimientos_can/{id}', 'Admin\CanController@establecimientos_cans')->name('cans.establecimientos_cans');
    Route::get('reportes_servicios_can_ipress/{id}/{tipo}/{establecimiento_id}', 'Admin\CanController@reportes_servicios_can')->name('cans.reportes_servicios_can');
    Route::get('nivel_total/{id}', 'Admin\CanController@nivel_total')->name('cans.nivel_total');
    Route::get('can/establecimientos/mostrar_servicios/{can_id}/{establecimiento_id}', 'Admin\CanController@mostrar_servicios')->name('cans.mostrar_servicios');
    Route::get('can/establecimientos/mostrar_rubros/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@mostrar_rubros')->name('cans.mostrar_rubros');
    
    Route::get('can/activar_can_establecimiento/{can_id}/{establecimiento_id}', 'Admin\CanController@activar_can_establecimiento')->name('cans.activar_can_establecimiento');
    Route::get('can/activar_can_establecimiento_stock/{can_id}/{establecimiento_id}', 'Admin\CanController@activar_can_establecimiento_stock')->name('cans.activar_can_establecimiento_stock');
    Route::get('can/activar_can_establecimiento_rectificacion/{can_id}/{establecimiento_id}', 'Admin\CanController@activar_can_establecimiento_rectificacion')->name('cans.activar_can_establecimiento_rectificacion');
    
    Route::patch('can/activar_can_establecimiento/{can_id}/{establecimiento_id}', 'Admin\CanController@update_can_establecimiento')->name('cans.update_can_establecimiento');
    Route::patch('can/activar_can_establecimiento_stock/{can_id}/{establecimiento_id}', 'Admin\CanController@update_can_establecimiento_stock')->name('cans.update_can_establecimiento_stock');
    Route::patch('can/activar_can_establecimiento_rectificacion/{can_id}/{establecimiento_id}', 'Admin\CanController@update_can_establecimiento_rectificacion')->name('cans.update_can_establecimiento_rectificacion');

    Route::patch('can/update_can_rubro_establecimiento/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@update_can_rubro_establecimiento')->name('cans.update_can_rubro_establecimiento');
    
    Route::get('can/ampliacion_ipress/{can_id}/{establecimiento_id}', 'Admin\CanController@ampliacion_ipress')->name('cans.ampliacion_ipress');
    
    Route::patch('can/ampliacion_ipress/{can_id}/{establecimiento_id}', 'Admin\CanController@update_user_tiempo')->name('cans.update_user_tiempo');

    Route::get('can/ampliacion_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@ampliacion_servicio')->name('cans.ampliacion_servicio');
    
    Route::patch('can/ampliacion_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@update_servicio_tiempo')->name('cans.update_servicio_tiempo');

    Route::get('can/ampliacion_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Admin\CanController@ampliacion_rubro')->name('cans.ampliacion_rubro');
    
    Route::patch('can/ampliacion_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Admin\CanController@update_rubro_tiempo')->name('cans.update_rubro_tiempo');
    
    //Route::get('can/activar_can_establecimiento/{can_id}/{establecimiento_id}', 'Admin\CanController@activar_can_establecimiento')->name('cans.activar_can_establecimiento');
    //Route::patch('can/activar_can_establecimiento/{can_id}/{establecimiento_id}', 'Admin\CanController@update_can_establecimiento')->name('cans.update_can_establecimiento');




    //Route::patch('can/activar_can_establecimiento/{can_id}/{establecimiento_id}', 'Admin\CanController@update_user_tiempo')->name('cans.update_user_tiempo');
    Route::get('can/activar_can_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@activar_servicio_establecimiento')->name('cans.activar_servicio_establecimiento');
    Route::get('can/activar_establecimiento_servicio_rectificacion/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@activar_servicio_rectificacion_establecimiento')->name('cans.activar_servicio_rectificacion_establecimiento');
    Route::get('can/activar_can_rubro/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@activar_rubro_establecimiento')->name('cans.activar_rubro_establecimiento');
    Route::get('can/ampliacion_ipress/{can_id}/{establecimiento_id}', 'Admin\CanController@ampliacion_ipress')->name('cans.ampliacion_ipress');
    Route::patch('can/activar_can_establecimiento/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@update_servicio_establecimiento')->name('cans.update_servicio_establecimiento');
    Route::patch('can/activar_can_establecimiento_servicio_rectificacion/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@update_servicio_establecimiento_rectificacion')->name('cans.update_servicio_establecimiento_rectificacion');
    Route::get('can/habilitar_can_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@habilitar_servicio_establecimiento')->name('cans.habilitar_servicio_establecimiento');
    Route::patch('can/habilitar_can_establecimiento/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@update_habilitar_servicio')->name('cans.update_habilitar_servicio');
    Route::get('can/medicamentos/{can_id}/{establecimiento_id}', 'Admin\CanController@dispositivos_estimacion')->name('cans.dispositivos_estimacion');
    Route::get('can/dispositivos/{can_id}/{establecimiento_id}', 'Admin\CanController@dispositivos')->name('cans.dispositivos');
    Route::get('can/medicamentos_rubros/{can_id}/{establecimiento_id}/{rubro_id}', 'Admin\CanController@medicamentos_rubros')->name('cans.medicamentos_rubros');
    Route::get('can/dispositivos_rubros/{can_id}/{establecimiento_id}/{rubro_id}', 'Admin\CanController@dispositivos_rubros')->name('cans.dispositivos_rubros');
    Route::get('can/medicamentos_servicios/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@medicamentos_servicios')->name('cans.medicamentos_servicios');
    Route::get('can/medicamentos_servicios_rectificacion/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@medicamentos_servicios_rectificacion')->name('cans.medicamentos_servicios_rectificacion');
    Route::get('can/medicamentos_servicios_rubros/{can_id}/{establecimiento_id}/{rubro_id}', 'Admin\CanController@medicamentos_servicios_rubros')->name('cans.medicamentos_servicios_rubros');
    Route::get('can/dispositivos_servicios/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@dispositivos_servicios')->name('cans.dispositivos_servicios');
    Route::get('can/dispositivos_servicios_rectificacion/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@dispositivos_servicios_rectificacion')->name('cans.dispositivos_servicios_rectificacion');
    Route::get('can/servicio/pdf_rectificacion_admin/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Admin\CanController@pdf_servicio_rectificacion_administrador')->name('cans.pdf_servicio_rectificacion_administrador');    
    Route::get('can/dispositivos_servicios_rubros/{can_id}/{establecimiento_id}/{servicio_id}', 'Admin\CanController@dispositivos_servicios_rubros')->name('cans.dispositivos_servicios_rubros');
    Route::get('can/estimacion/medicamentos_estimaciones/{can_id}/{establecimiento_id}', 'Admin\CanController@medicamentos_estimaciones')->name('cans.medicamentos_estimaciones');
    Route::get('can/estimacion/dispositivos_estimaciones/{can_id}/{establecimiento_id}', 'Admin\CanController@dispositivos_estimaciones')->name('cans.dispositivos_estimaciones');
    Route::get('can/estimacion/dispositivos_rectificaciones/{can_id}/{establecimiento_id}', 'Admin\CanController@dispositivos_rectificaciones')->name('cans.dispositivos_rectificaciones');
    Route::get('can/estimacion/medicamentos_rectificaciones/{can_id}/{establecimiento_id}', 'Admin\CanController@medicamentos_rectificaciones')->name('cans.medicamentos_rectificaciones');
    Route::get('can/estimacion/medicamentos_consolidados/{can_id}/{establecimiento_id}', 'Admin\CanController@medicamentos_consolidados')->name('cans.medicamentos_consolidados');
    Route::get('can/estimacion/dispositivos_consolidados/{can_id}/{establecimiento_id}', 'Admin\CanController@dispositivos_consolidados')->name('cans.dispositivos_consolidados');

    Route::get('can/establecimientos/medicamentos/{can_id}/{establecimiento_id}', 'Admin\CanController@medicamentos')->name('cans.medicamentos');
    
    Route::get('can/estimacion/fechas/{can_id}/{establecimiento_id}', 'Admin\CanController@estimacions_fecha')->name('cans.estimacions_fecha');    
    Route::get('can/estimacion/dispositivos/{can_id}/{establecimiento_id}', 'Admin\CanController@dispositivos_estimacions')->name('cans.dispositivos_estimacions');
    Route::get('can/estimacion/productos/editar/{id}/{establecimiento_id}/{destino}', 'Admin\CanController@editar_producto')->name('cans.editar_producto');
    Route::patch('can/estimacion/productos/update/{id}/{establecimiento_id}/{precio}/{can_id}/{destino}/{servicio_id}', 'Admin\CanController@update_producto')->name('cans.update_producto');
    Route::delete('can/estimacion/productos/eliminar/{id}/{establecimiento_id}/{can_id}/{destino}/{servicio_id}', 'Admin\CanController@eliminar_items')->name('cans.eliminar_items');
    Route::get('estimacion/descargar_productos/{can_id}/{establecimiento_id}/{indicador}', 'Admin\CanController@descargar_productos')->name('cans.descargar_productos');    
    Route::get('estimacion-export-nivel1/{can_id}/{establecimiento_id}/{rubro_id}/{opt}/{type}', 'Admin\CanController@exportEstimacionDataNivel1')->name('cans.exportEstimacionDataNivel1');
    Route::get('estimacion-export-nivel2y3/{can_id}/{establecimiento_id}/{servicio_id}/{opt}/{type}', 'Admin\CanController@exportEstimacionDataNivel2y3')->name('cans.exportEstimacionDataNivel2y3');        
    Route::get('comite-export-nivel2y3/{can_id}/{establecimiento_id}/{servicio_id}/{type}', 'Admin\CanController@exportEstimacionDataComiteNivel2y3')->name('cans.exportEstimacionDataComiteNivel2y3');        
    Route::get('consolidado/export-consolidador/{can_id}/{establecimiento_id}/{opt}/{type}', 'Admin\CanController@exportDataConsolidado')->name('cans.exportDataConsolidado');
    Route::get('productos/export-modificados/{can_id}/{establecimiento_id}/{type}', 'Admin\CanController@exportDataModificado')->name('cans.exportDataModificado');

    Route::get('consolidado/estimacion-export/{can_id}/{establecimiento_id}/{opt}/{type}', 'Admin\CanController@exportDataEstimacion')->name('cans.exportDataEstimacion');
    
    Route::get('consolidado/establecimiento-export-consolidador/{descripcion}/{can_productos}/{type}', 'Admin\CanController@exportEstimacionDataConsolidadoEstablecimiento')->name('cans.exportEstimacionDataConsolidadoEstablecimiento');

    Route::get('descargar-productos/{can_id}/{establecimiento_id}/{tipo}/{servicio_id}', 'Admin\CanController@pdf')->name('cans.pdf');
    Route::get('descargar-productos-previo/{can_id}/{establecimiento_id}/{tipo}', 'Admin\CanController@pdf_previo')->name('cans.pdf_previo');
    Route::get('descargar-productos-rectificacion/{can_id}/{establecimiento_id}/{tipo}', 'Admin\CanController@pdf_rectificacion')->name('cans.pdf_rectificacion');

    Route::get('can/nuevo_producto/{can_id}/{establecimiento_id}/{destino}', 'Admin\CanController@nuevo_medicamento_dispositivo')->name('cans.nuevo_medicamento_dispositivo');
    Route::post('grabar/nuevo_producto/{establecimiento_id}/{can_id}/{destino}', 'Admin\CanController@grabar_nuevos_medicamentos_dispositivos')->name('cans.grabar_nuevos_medicamentos_dispositivos');
    Route::put('grabar/nuevo_producto/{establecimiento_id}/{can_id}/{destino}', 'Admin\CanController@grabar_nuevos_medicamentos_dispositivos')->name('cans.grabar_nuevos_medicamentos_dispositivos');
    Route::patch('grabar/nuevo_producto/{establecimiento_id}/{can_id}/{destino}', 'Admin\CanController@grabar_nuevos_medicamentos_dispositivos')->name('cans.grabar_nuevos_medicamentos_dispositivos');
    
    ///////////////////////////////ESTIMACION////////////////////////////////////////////////////7
    Route::resource('estimacion', 'Site\EstimacionNivel1Controller');
    Route::put('estimacion/grabar/{id}', 'Site\EstimacionNivel1Controller@grabar')->name('estimacion.grabar');
    Route::patch('estimacion/grabar/{id}', 'Site\EstimacionNivel1Controller@grabar')->name('estimacion.grabar');
    Route::delete('estimacion/eliminar/{id}', 'Site\EstimacionNivel1Controller@eliminar')->name('estimacion.eliminar');
    Route::get('nivel1/cargar/datos_rubro/{can_id}/{establecimiento_id}/{tipo}/{cerrado}', 'Site\EstimacionNivel1Controller@cargar_datos_rubro')->name('estimacion.cargar_datos_rubro');
    Route::post('nivel1/grabar/nuevos/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel1Controller@grabar_nuevos_medicamentos_dispositivos')->name('estimacions.grabar_nuevos_medicamentos_dispositivos');
    Route::put('nivel1/grabar/nuevos/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel1Controller@grabar_nuevos_medicamentos_dispositivos')->name('estimacions.grabar_nuevos_medicamentos_dispositivos');
    Route::patch('nivel1/grabar/nuevos/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel1Controller@grabar_nuevos_medicamentos_dispositivos')->name('estimacions.grabar_nuevos_medicamentos_dispositivos');    
    Route::get('nivel1/estimacion/nuevo/producto/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel1Controller@nuevo_medicamento_dispositivo')->name('estimacion.nuevo_medicamento_dispositivo');
    Route::get('nivel1/estimacion/nuevo/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel1Controller@calcular_indicadores')->name('estimacion.calcular_indicadores');
    Route::get('nivel1/estimacion/calcular_disponibilidad/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel1Controller@calcular_disponibilidad')->name('estimacion.calcular_disponibilidad');
    Route::get('can/nuevo/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel1Controller@nuevo_medicamento_dispositivo')->name('estimacion.nuevo_medicamento_dispositivo');
    Route::get('can/nuevo/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel1Controller@calcular_indicadores')->name('estimacion.calcular_indicadores');
    Route::get('can/calcular_disponibilidad/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel1Controller@calcular_disponibilidad')->name('estimacion.calcular_disponibilidad');
    Route::post('grabar/nuevo/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel1Controller@grabar_nuevo_medicamento_dispositivo')->name('estimacion.grabar_nuevo_medicamento_dispositivo');
    Route::put('grabar/nuevo/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel1Controller@grabar_nuevo_medicamento_dispositivo')->name('estimacion.grabar_nuevo_medicamento_dispositivo');
    Route::patch('grabar/nuevo/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel1Controller@grabar_nuevo_medicamento_dispositivo')->name('estimacion.grabar_nuevo_medicamento_dispositivo');
    Route::put('grabar/{id}', 'Site\EstimacionNivel1Controller@grabar')->name('estimacion.grabar');
    Route::patch('grabar/{id}', 'Site\EstimacionNivel1Controller@grabar')->name('estimacion.grabar');
    Route::get('api/contact/{can_id}/{establecimiento_id}/{tipo}/{cerrado}', 'Site\EstimacionNivel1Controller@apiContact')->name('api.contact');
    Route::get('can/manual/{id}', 'Site\EstimacionNivel1Controller@manual')->name('estimacion.manual');
    Route::get('can/reportes/{id}/{can_id}', 'Admin\CanController@reportes')->name('cans.reportes');
    Route::get('can/dashboard/{can_id}', 'Admin\CanController@tablero_control')->name('cans.tablero_control');
    Route::get('can/cerrar_medicamento/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel1Controller@cerrar_medicamento')->name('cans.cerrar_medicamento');
    Route::get('can/establecimiento/cerrar_medicamento_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\EstimacionNivel1Controller@cerrar_medicamento_servicio')->name('cans.cerrar_medicamento_servicio');
    Route::get('can/dispositivos/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel1Controller@cargar_dispositivos')->name('cans.cargar_dispositivos');
    Route::get('can/cerrar_dispositivos/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel1Controller@cerrar_dispositivos')->name('cans.cerrar_dispositivos');
    Route::get('can/cerrar_dispositivos_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\EstimacionNivel1Controller@cerrar_dispositivos_servicio')->name('cans.cerrar_dispositivos_servicio');
    Route::get('estimacion-export-previo/{can_id}/{establecimiento_id}/{opt}/{type}', 'Site\EstimacionNivel1Controller@exportEstimacionDataPrevio')->name('estimacion.exportEstimacionDataPrevio');
    Route::get('estimacion/excel-export/{can_id}/{establecimiento_id}/{opt}/{type}', 'Site\EstimacionNivel1Controller@exportEstimacionData')->name('estimacion.exportEstimacionData');    
    Route::get('establecimiento/can/asignar_medicamentos/{establecimiento_id}/{servicio_id}/{distribucion_id}', 'Site\EstimacionNivel1Controller@asignar_medicamentos')->name('estimacion.asignar_medicamentos');
    Route::get('estimacion/medicamentos/{can_id}/{establecimiento_id}/{tipo}/{medicamento_cerrado}', 'Site\EstimacionNivel1Controller@cargar_medicamentos')->name('estimacion.cargar_medicamentos');
    Route::get('estimacion/cerrar_medicamento/{can_id}/{establecimiento_id}/{tipo}', 'Site\EstimacionNivel1Controller@cerrar_medicamento')->name('estimacion.cerrar_medicamento');    
    
    Route::put('estimacion/can/asignar_medicamentos_ipress/{establecimiento_id}/{can_id}/{tipo}', 'Site\EstimacionNivel1Controller@guardar_medicamentos_asignados')->name('estimacions.guardar_medicamentos_asignados');
    Route::patch('estimacion/can/asignar_medicamentos_ipress/{establecimiento_id}/{distribucion_id}/{tipo}', 'Site\EstimacionNivel1Controller@guardar_medicamentos_asignados')->name('estimacions.guardar_medicamentos_asignados');

    Route::get('estimacion/medicamentos/descargar/{tipo}/{can_id}', 'Site\EstimacionNivel1Controller@descargar_estimacion')->name('estimacion.descargar_estimacion');
    
    Route::get('estimacion/pdf-export/{can_id}/{establecimiento_id}/{opt}', 'Site\EstimacionNivel1Controller@pdf_estimacion')->name('estimacion.pdf_estimacion');
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////EstimacionNivel2_3Controller////////////////////////////////////////////////////////////////////7777
    
    Route::resource('estimacion_servicio', 'Site\EstimacionNivel2_3Controller');

    Route::put('servicio/grabar/{id}', 'Site\EstimacionNivel2_3Controller@grabar')->name('estimacion_servicio.grabar');
    Route::patch('servicio/grabar/{id}', 'Site\EstimacionNivel2_3Controller@grabar')->name('estimacion_servicio.grabar');

    Route::get('listar_observaciones_nivel2y3/{can_id}', 'Site\EstimacionNivel2_3Controller@listar_observaciones_nivel2y3')->name('estimacion_servicio.listar_observaciones_nivel2y3');
    Route::get('listar_archivos_servicios/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_archivos_nivel2y3')->name('farmacia_servicios.listar_archivos_nivel2y3');
    

    Route::put('servicio/grabar_necesidad/{id}', 'Site\EstimacionNivel2_3Controller@grabar_necesidad')->name('estimacion_servicio.grabar_necesidad');
    Route::patch('servicio/grabar_necesidad/{id}', 'Site\EstimacionNivel2_3Controller@grabar_necesidad')->name('estimacion_servicio.grabar_necesidad');

    Route::delete('estimacion_servicio/eliminar/{id}', 'Site\EstimacionNivel2_3Controller@eliminar')->name('estimacion_servicio.eliminar');
    Route::get('servicio/api/contact/{can_id}/{establecimiento_id}/{tipo}/{cerrado}', 'Site\EstimacionNivel2_3Controller@apiContact')->name('api.contact');
    Route::get('servicio/api/contact_rectificacion/{can_id}/{establecimiento_id}/{tipo}/{cerrado}', 'Site\EstimacionNivel2_3Controller@apiContactRectificacion')->name('api.contact_rectificacion');
    Route::post('servicio/grabar/nuevos/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel2_3Controller@grabar_nuevos_medicamentos_dispositivos')->name('estimacion_servicio.grabar_nuevos_medicamentos_dispositivos');
    Route::put('servicio/grabar/nuevos/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel2_3Controller@grabar_nuevos_medicamentos_dispositivos')->name('estimacion_servicio.grabar_nuevos_medicamentos_dispositivos');
    Route::patch('servicio/grabar/nuevos/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel2_3Controller@grabar_nuevos_medicamentos_dispositivos')->name('estimacion_servicio.grabar_nuevos_medicamentos_dispositivos');    
    Route::get('servicio/estimacion/nuevo/producto/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel2_3Controller@nuevo_medicamento_dispositivo')->name('estimacion_servicio.nuevo_medicamento_dispositivo');
    Route::get('servicio/estimacion/nuevo/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@calcular_indicadores')->name('estimacion_servicio.calcular_indicadores');
    Route::get('servicio/estimacion/calcular_disponibilidad/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel2_3Controller@calcular_disponibilidad')->name('estimacion_servicio.calcular_disponibilidad');
    Route::get('servicio/can/nuevo/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel2_3Controller@nuevo_medicamento_dispositivo')->name('estimacion_servicio.nuevo_medicamento_dispositivo');
    Route::get('servicio/can/nuevo/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@calcular_indicadores')->name('estimaciones.calcular_indicadores');
    Route::get('servicio/can/calcular_disponibilidad/{can_id}/{establecimiento_id}/{destino}', 'Site\EstimacionNivel2_3Controller@calcular_disponibilidad')->name('estimacion_servicio.calcular_disponibilidad');
    Route::post('servicio/grabar/nuevo/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel2_3Controller@grabar_nuevo_medicamento_dispositivo')->name('estimacion_servicio.grabar_nuevo_medicamento_dispositivo');
    Route::put('servicio/grabar/nuevo/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel2_3Controller@grabar_nuevo_medicamento_dispositivo')->name('estimacion_servicio.grabar_nuevo_medicamento_dispositivo');
    Route::patch('servicio/grabar/nuevo/{establecimiento_id}/{can_id}/{servicio_id}/{destino}', 'Site\EstimacionNivel2_3Controller@grabar_nuevo_medicamento_dispositivo')->name('estimacion_servicio.grabar_nuevo_medicamento_dispositivo');
    Route::put('servicio/grabar/{id}', 'Site\EstimacionNivel2_3Controller@grabar')->name('estimaciones.grabar');
    Route::patch('servicio/grabar/{id}', 'Site\EstimacionNivel2_3Controller@grabar')->name('estimaciones.grabar');
    Route::get('servicio/api/contact/{can_id}/{establecimiento_id}/{tipo}/{cerrado}', 'Site\EstimacionNivel2_3Controller@apiContact')->name('api.contact');
    Route::get('servicio/can/manual/{id}', 'Site\EstimacionNivel2_3Controller@manual')->name('estimaciones.manual');
    Route::get('servicio/can/reportes/{id}/{can_id}', 'Admin\EstimacionNivel2_3Controller@reportes')->name('cans.reportes');
    Route::get('servicio/can/dashboard/{can_id}', 'Admin\EstimacionNivel2_3Controller@tablero_control')->name('cans.tablero_control');
    Route::get('servicio/can/cerrar_medicamento/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@cerrar_medicamento')->name('cans.cerrar_medicamento');
    Route::get('servicio/can/establecimiento/cerrar_medicamento_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\EstimacionNivel2_3Controller@cerrar_medicamento_servicio')->name('cans.cerrar_medicamento_servicio');
    Route::get('servicio/can/dispositivos/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@cargar_dispositivos')->name('cans.cargar_dispositivos');
    Route::get('servicio/can/cerrar_dispositivos/{can_id}/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@cerrar_dispositivos')->name('cans.cerrar_dispositivos');
    Route::get('servicio/can/cerrar_dispositivos_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\EstimacionNivel2_3Controller@cerrar_dispositivos_servicio')->name('cans.cerrar_dispositivos_servicio');
    Route::get('servicio/estimacion-export-previo/{can_id}/{establecimiento_id}/{opt}/{type}', 'Site\EstimacionNivel2_3Controller@exportEstimacionDataPrevio')->name('estimacion_servicio.exportEstimacionDataPrevio');
    Route::get('servicio/estimacion-export/{can_id}/{establecimiento_id}/{opt}/{type}/{servicio_id?}', 'Site\EstimacionNivel2_3Controller@exportEstimacionData')->name('estimacion_servicio.exportEstimacionData');
    Route::get('servicio/establecimiento/can/asignar_medicamentos/{establecimiento_id}/{servicio_id}/{distribucion_id}', 'Site\EstimacionNivel2_3Controller@asignar_medicamentos')->name('estimacion_servicio.asignar_medicamentos');
    Route::get('servicio/medicamentos/{can_id}/{establecimiento_id}/{tipo}', 'Site\EstimacionNivel2_3Controller@cargar_medicamentos_servicios')->name('estimacion_servicio.cargar_medicamentos_servicios');
    Route::get('servicio/estimacion/medicamentos/rectificacion/{can_id}/{establecimiento_id}/{tipo}/{medicamento_cerrado}', 'Site\EstimacionNivel2_3Controller@cargar_productos_rectificacion')->name('estimacion_servicio.cargar_productos_rectificacion');
    Route::get('servicio/estimacion/cerrar_medicamento/{can_id}/{establecimiento_id}/{tipo}', 'Site\EstimacionNivel2_3Controller@cerrar_medicamento')->name('estimacion_servicio.cerrar_medicamento');
    Route::get('servicio/estimacion/cerrar_medicamento/rectificacion/{can_id}/{establecimiento_id}/{tipo}', 'Site\EstimacionNivel2_3Controller@cerrar_medicamento_rectificacion')->name('estimacion_servicio.cerrar_medicamento_rectificacion');
    Route::put('servicio/estimacion/can/asignar_medicamentos_ipress/{establecimiento_id}/{can_id}/{tipo}', 'Site\EstimacionNivel2_3Controller@guardar_medicamentos_asignados')->name('estimacion_servicio.guardar_medicamentos_asignados');
    Route::patch('servicio/estimacion/can/asignar_medicamentos_ipress/{establecimiento_id}/{distribucion_id}/{tipo}', 'Site\EstimacionNivel2_3Controller@guardar_medicamentos_asignados')->name('estimacion_servicio.guardar_medicamentos_asignados');
    Route::get('servicio/descargar/{tipo}/{can_id}', 'Site\EstimacionNivel2_3Controller@descargar_servicio')->name('estimacion_servicio.descargar_servicio');
    Route::get('servicio/descargar/rectificacion_can/{tipo}/{can_id}', 'Site\EstimacionNivel2_3Controller@descargar_servicio_rectificacion')->name('estimacion_servicio.descargar_servicio_rectificacion');
    Route::get('servicio/pdf-export/{can_id}/{establecimiento_id}/{opt}/{id_user?}/{ano}', 'Site\EstimacionNivel2_3Controller@pdf_estimacion_servicio')->name('estimacion_servicio.pdf_estimacion_servicio');    
    Route::get('servicio/pdf_rectificacion/{can_id}/{establecimiento_id}/{opt}', 'Site\EstimacionNivel2_3Controller@pdf_servicio_rectificacion')->name('estimacion_servicio.pdf_servicio_rectificacion');    
    //////////////////////////////ResponsableFarmacia///////////////////////////////////////////////////
    Route::resource('farmacia', 'Site\ResponsableFarmaciaController');
    Route::delete('farmacia/eliminar/{id}', 'Site\ResponsableFarmaciaController@eliminar')->name('farmacia.eliminar');
    Route::get('farmacia/actualiza_can/{id}/{establecimiento_id}', 'Site\ResponsableFarmaciaController@actualizar_can_2027')->name('farmacia.actualizar_can_2027');
    Route::get('listar_can', 'Site\ResponsableFarmaciaController@index')->name('farmacia.index');
    Route::get('listar_distribucion/{can_id}', 'Site\ResponsableFarmaciaController@listar_distribucion')->name('farmacia.listar_distribucion');
    Route::get('listar_misservicios/{can_id}', 'Site\ResponsableFarmaciaController@listar_servicios')->name('farmacia.listar_servicios');
    Route::get('api/contacto/{can_id}/{establecimiento_id}/{tipo}/{servicio_id}', 'Site\ResponsableFarmaciaController@apiContacto')->name('api.contacto');
    Route::get('estimacion/farmacia/descargar/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@descargar_consolidado_farmacia')->name('farmacia.descargar_consolidado_farmacia');
    Route::get('estimacion/farmacia/descargar_rubro/{tipo}/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\ResponsableFarmaciaController@descargar_estimacion_farmacia')->name('farmacia.descargar_estimacion_farmacia');

    Route::get('farmacia/ver_producto_consolidado/{establecimiento_id}/{can_id}/{tipo}', 'Site\ResponsableFarmaciaController@ver_producto_consolidado')->name('farmacia.ver_producto_consolidado');

    Route::get('farmacia/estimacion/medicamentos/{can_id}/{tipo}', 'Site\ResponsableFarmaciaController@cargar_medicamentos')->name('farmacia.cargar_medicamentos');
    Route::get('farmacia/estimacion/medicamentos/rectificar/{can_id}/{tipo}/', 'Site\ResponsableFarmaciaController@cargar_medicamentos_rectificacion')->name('farmacia.cargar_medicamentos_rectificacion');
    Route::get('estimacion/farmacia/editar_consolidado/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@editar_consolidado_farmacia')->name('farmacia.editar_consolidado_farmacia');

    Route::patch('departamento/servicio/asignar_servicios/{unidad_id}/{division_id}/{establecimiento_id}', 'Admin\UnidadController@guardar_servicios')->name('unidads.guardar_servicios');

    Route::get('farmacia/ver_producto/{establecimiento_id}/{can_id}/{tipo}/{servicio_id}', 'Site\ResponsableFarmaciaController@ver_producto')->name('farmacia.ver_producto');

    Route::get('farmacia/noaplica/{can_id}/{tipo}', 'Site\ResponsableFarmaciaController@cerrar_no_aplica')->name('farmacia.cerrar_no_aplica');

    Route::get('farmacia/activar_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Site\ResponsableFarmaciaController@activar_rubro')->name('farmacia.activar_rubro');
    
    Route::get('nivel1/estimacion-export-consolidado/{can_id}/{establecimiento_id}/{opt}/{type}/{valor}', 'Site\ResponsableFarmaciaController@exportEstimacionDataConsolidada')->name('farmacia.exportEstimacionDataConsolidada');

   // Route::get('estimacion/farmacia/editar_consolidado/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@editar_consolidado_farmacia')->name('farmacia.editar_consolidado_farmacia');

    Route::get('estimacion/farmacia/ver_consolidado/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@ver_consolidado_farmacia')->name('farmacia.ver_consolidado_farmacia');
    Route::get('estimacion/farmacia/ver_consolidado_stock/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@ver_stock_farmacia')->name('farmacia.ver_stock_farmacia');

    Route::get('estimacion/farmacia/ver_rectificacion_farmacia/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@ver_rectificacion_farmacia')->name('farmacia.ver_rectificacion_farmacia');

    Route::get('estimacion/farmacia/ver_stock_nivel_1/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@ver_stock_nivel_1')->name('farmacia.ver_stock_nivel_1');

    Route::get('estimacion/farmacia/ver_rectificacion_nivel_1/{tipo}/{can_id}', 'Site\ResponsableFarmaciaController@ver_rectificacion_nivel_1')->name('farmacia.ver_rectificacion_nivel_1');

    Route::get('estimacion/cargar/datos_consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaController@cargar_datos_consolidado')->name('farmacia.cargar_datos_consolidado');
    Route::get('estimacion/cargar/datos_rectificacion/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaController@cargar_datos_rectificacion')->name('farmacia.cargar_datos_rectificacion');
    Route::get('estimacion/nivel1/cerrar_medicamento_consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaController@cerrar_medicamento_consolidado')->name('farmacia.cerrar_medicamento_consolidado');

    Route::get('estimacion/nivel1/cerrar_medicamento_stock/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaController@cerrar_medicamento_stock')->name('farmacia.cerrar_medicamento_stock');
    
    Route::get('estimacion/nivel1/cerrar_medicamento_rectificacion/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaController@cerrar_medicamento_rectificacion')->name('farmacia.cerrar_medicamento_rectificacion');

    Route::put('farmacia/grabar/{id}', 'Site\ResponsableFarmaciaController@grabar')->name('farmacia.grabar');
    Route::patch('farmacia/grabar/{id}', 'Site\ResponsableFarmaciaController@grabar')->name('farmacia.grabar');
    Route::put('farmacia/grabar_stock/{id}', 'Site\ResponsableFarmaciaController@grabar_stock')->name('farmacia.grabar_stock');
    Route::patch('farmacia/grabar_stock/{id}', 'Site\ResponsableFarmaciaController@grabar_stock')->name('farmacia.grabar_stock');
        
    //Route::post('farmacia/grabar_rectificacion/{id}', 'Site\ResponsableFarmaciaController@grabar_rectificacion')->name('farmacia.grabar_rectificacion');
    Route::put('farmacia/grabar_rectificacion/{id}', 'Site\ResponsableFarmaciaController@grabar_rectificacion')->name('farmacia.grabar_rectificacion');
    Route::patch('farmacia/grabar_rectificacion/{id}', 'Site\ResponsableFarmaciaController@grabar_rectificacion')->name('farmacia.grabar_rectificacion');

    Route::get('farmacia/pdf-export/{can_id}/{establecimiento_id}/{opt}/{servicio_id}/{ano}', 'Site\ResponsableFarmaciaController@pdf_estimacion_nivel1')->name('farmacia.pdf_estimacion_nivel1');
    Route::get('farmacia/pdf-export-stock/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Site\ResponsableFarmaciaController@pdf_estimacion_nivel_1')->name('farmacia.pdf_estimacion_nivel_1');

    Route::get('farmacia/pdf-export-can-rectificacion/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Site\ResponsableFarmaciaController@pdf_rectificacion_nivel_1')->name('farmacia.pdf_rectificacion_nivel_1');
    
    Route::get('farmacia/can/nuevo/{can_id}/{establecimiento_id}/{destino}', 'Site\ResponsableFarmaciaController@nuevo_medicamento_dispositivo')->name('farmacia.nuevo_medicamento_dispositivo');
    Route::post('farmacia/grabar/nuevos/{establecimiento_id}/{can_id}/{destino}', 'Site\ResponsableFarmaciaController@grabar_nuevo_medicamento_dispositivo')->name('farmacia.grabar_nuevo_medicamento_dispositivo');
    Route::put('farmacia/grabar/nuevos/{establecimiento_id}/{can_id}/{destino}', 'Site\ResponsableFarmaciaController@grabar_nuevo_medicamento_dispositivo')->name('farmacia.grabar_nuevo_medicamento_dispositivo');
    Route::patch('farmacia/grabar/nuevos/{establecimiento_id}/{can_id}/{destino}', 'Site\ResponsableFarmaciaController@grabar_nuevo_medicamento_dispositivo')->name('farmacia.grabar_nuevo_medicamento_dispositivo');

    Route::put('farmacia/can/asignar_medicamentos_ipress/{can_id}/{tipo}', 'Site\ResponsableFarmaciaController@guardar_medicamentos_asignados')->name('farmacia.guardar_medicamentos_asignados');
    Route::patch('farmacia/can/asignar_medicamentos_ipress/{can_id}/{tipo}', 'Site\ResponsableFarmaciaController@guardar_medicamentos_asignados')->name('farmacia.guardar_medicamentos_asignados');

    //////////////////////////////ResponsableFarmacia Nivel 2 y 3//////////////////////////////////////////////
    Route::resource('farmacia_servicios', 'Site\ResponsableFarmaciaNivel2_3Controller');
    Route::get('show_farmacia/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@show_farmacia')->name('nacional.show_farmacia');
    Route::get('listar_can_servicio', 'Site\ResponsableFarmaciaNivel2_3Controller@index')->name('farmacia_servicios.index');
    Route::get('listar_medicos/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_medicos')->name('farmacia_servicios.listar_medicos');
    Route::get('show_avance/{petitorio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@show_avance')->name('farmacia_servicios.show_avance');
    Route::delete('farmacia_servicios/eliminar/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@eliminar')->name('farmacia_servicios.eliminar');
    Route::get('listar_servicios/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_servicios')->name('farmacia_servicios.listar_servicios');
    Route::get('listar_responsables_servicios/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_responsables_servicios')->name('farmacia_servicios.listar_responsables_servicios');
    Route::get('listar_archivos/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_archivos_nivel1')->name('farmacia_servicios.listar_archivos_nivel1');
    Route::get('listar_archivos_comite/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_archivos_comite')->name('farmacia_servicios.listar_archivos_comite');
    Route::get('listar_observaciones/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_observaciones_nivel1')->name('farmacia_servicios.listar_observaciones_nivel1');
    Route::get('listar_observaciones_comite/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_observaciones_comite')->name('farmacia_servicios.listar_observaciones_comite');

    //Route::get('listar_archivos', 'Site\ResponsableFarmaciaNivel2_3Controller@listar_archivos_servicios')->name('farmacia_servicios.listar_archivos_servicios');
    Route::get('farmacia_servicios/lista_productos_servicios/{id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@productos_servicio_tipo')->name('farmacia_servicios.productos_servicio_tipo');
    Route::get('farmacia_servicios/lista_productos_servicios_new/{id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@productos_servicio_tipo_mof')->name('farmacia_servicios.productos_servicio_tipo_mof');

    Route::patch('farmacia/archivo/subir_archivo/{id}/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@subir_archivo')->name('farmacia_servicios.subir_archivo');
    Route::patch('farmacia/archivo/subir_archivo_comite/{id}/{can_id}/{rubro_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@subir_archivo_comite')->name('farmacia_servicios.subir_archivo_comite');
    Route::get('farmacia/eliminar_archivo/{id}/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@eliminar_archivo')->name('farmacia_servicios.eliminar_archivo');

    Route::get('farmacia/eliminar_observacion/{can_id}/{establecimiento_id}/{rubro_id}/{id}', 'Admin\CanController@eliminar_observacion')->name('cans.eliminar_observacion');
    
    //Route::get('api/consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@apiConsolidado')->name('farmacia_servicios.consolidado');
    Route::get('estimacion/farmacia_servicios/descargar/{tipo}/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@descargar_consolidado_farmacia_servicios')->name('farmacia_servicios.descargar_consolidado_farmacia_servicios');
    
    Route::get('estimacion/farmacia_servicios/ver_farmacia_servicios/{tipo}/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@ver_farmacia_servicios')->name('farmacia_servicios.ver_farmacia_servicios');

    Route::get('ver_can_servicio/{can_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@establecimientos_servicio_can')->name('farmacia_servicios.establecimientos_servicio_can');

    Route::get('estimacion/farmacia_servicios/ver_consolidado/{tipo}/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@ver_consolidado_farmacia_servicios')->name('farmacia_servicios.ver_consolidado_farmacia_servicios');

    Route::get('estimaciones/servicios/descargas/{tipo}/{can_id}/{establecimiento_id}/{servicio_id}/{id_user}', 'Site\ResponsableFarmaciaNivel2_3Controller@descargar_estimacion_farmacia_servicios')->name('farmacia_servicios.descargar_estimacion_farmacia_servicios');
        Route::get('estimaciones/servicios/ver_descargas/{tipo}/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@descargar_estimacion_farmacia_servicios_ver')->name('farmacia_servicios.descargar_estimacion_farmacia_servicios_ver');
    Route::get('farmacia_servicios/ver_producto_consolidado/{establecimiento_id}/{can_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@ver_producto_consolidado')->name('farmacia_servicios.ver_producto_consolidado');
    Route::get('farmacia_servicios/ver_consolidado_ipress/{tipo}/{can_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@ver_consolidado_ipress')->name('farmacia_servicios.ver_consolidado_ipress');
    Route::get('can/servicio/pdf_rectificacion_farma/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_servicio_rectificacion_farmacia')->name('farmacia_servicios.pdf_servicio_rectificacion_farmacia');    
    
    Route::get('farmacia_servicios/ver_producto/{establecimiento_id}/{can_id}/{tipo}/{servicio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@ver_producto')->name('farmacia_servicios.ver_producto');

    Route::get('farmacia_servicios/activar_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@activar_rubro')->name('farmacia_servicios.activar_rubro');
    Route::patch('farmacia_servicios/activar_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@update_petitorio_rubro')->name('farmacia_servicios.update_petitorio_rubro');
    Route::get('estimacion-export-consolidado-nivel2y3/{can_id}/{establecimiento_id}/{opt}/{type}', 'Site\ResponsableFarmaciaNivel2_3Controller@exportEstimacionDataConsolidada')->name('farmacia_servicios.exportEstimacionDataConsolidada');
    Route::get('estimacion-export-previo-consolidado-nivel2y3/{can_id}/{establecimiento_id}/{opt}/{type}', 'Site\ResponsableFarmaciaNivel2_3Controller@exportEstimacionDataConsolidadaPrevio')->name('farmacia_servicios.exportEstimacionDataConsolidadaPrevio');

    Route::get('farmacia_servicios/cargar/datos_consolidado_servicio/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@cargar_datos_consolidado')->name('farmacia_servicios.cargar_datos_consolidado');

    Route::put('farmacia_servicios/grabar/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar')->name('farmacia_servicios.grabar');
    Route::patch('farmacia_servicios/grabar/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar')->name('farmacia_servicios.grabar');
    Route::post('farmacia_servicios/grabar/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar')->name('farmacia_servicios.grabar');


    Route::get('farmacia_servicios/estimacion/cerrar_medicamento_consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@cerrar_medicamento_consolidado')->name('farmacia_servicios.cerrar_medicamento_consolidado');

    Route::get('farmacia_servicios/pdf-export/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_estimacion_nivel2y3')->name('farmacia_servicios.pdf_estimacion_nivel2y3');

    Route::get('farmacia_servicios/pdf-export-final/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_final_estimacion_nivel2y3')->name('farmacia_servicios.pdf_final_estimacion_nivel2y3');

    Route::get('farmacia_servicios/pdf-export-can-consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_final_estimacion_nivel2y3_modificado')->name('farmacia_servicios.pdf_final_estimacion_nivel2y3_modificado');

    Route::get('farmacia_servicios/pdf-export-final-3/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_final_estimacion_nivel3')->name('farmacia_servicios.pdf_final_estimacion_nivel3');

    Route::get('farmacia_servicios/pdf-export-final-3-consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_final_estimacion_nivel3_modificado')->name('farmacia_servicios.pdf_final_estimacion_nivel3_modificado');



    Route::put('farmacia_servicios/grabar/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar')->name('farmacia_servicios.grabar');
    Route::patch('farmacia_servicios/grabar/{id}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar')->name('farmacia_servicios.grabar');
    //Route::get('farmacia_servicios/pdf-export/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@pdf_estimacion_nivel1')->name('farmacia_servicios.pdf_estimacion_nivel1');
    Route::get('farmacia_servicios/can/nuevo/{can_id}/{establecimiento_id}/{destino}', 'Site\ResponsableFarmaciaNivel2_3Controller@nuevo_medicamento_dispositivo')->name('farmacia_servicios.nuevo_medicamento_dispositivo');
    Route::post('farmacia_servicios/grabar/nuevos/{establecimiento_id}/{can_id}/{destino}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar_nuevo_medicamento_dispositivo')->name('farmacia_servicios.grabar_nuevo_medicamento_dispositivo');
    Route::put('farmacia_servicios/grabar/nuevos/{establecimiento_id}/{can_id}/{destino}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar_nuevo_medicamento_dispositivo')->name('farmacia_servicios.grabar_nuevo_medicamento_dispositivo');
    Route::patch('farmacia_servicios/grabar/nuevos/{establecimiento_id}/{can_id}/{destino}', 'Site\ResponsableFarmaciaNivel2_3Controller@grabar_nuevo_medicamento_dispositivo')->name('farmacia_servicios.grabar_nuevo_medicamento_dispositivo');


    //////////////////////////////ResponsableIpress///////////////////////////////////////////////////
Route::resource('ipress', 'Site\ResponsableIpressController');
    Route::get('listar_can_ipress', 'Site\ResponsableIpressController@index')->name('ipress.index');
    Route::get('ipress/mostrar/{id}', 'Site\ResponsableIpressController@mostrar')->name('ipress.mostrar');
    Route::get('listar_servicios_ipress/{can_id}', 'Site\ResponsableIpressController@listar_servicios')->name('ipress.listar_servicios');
    //Route::get('api/consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableIpressController@apiConsolidado')->name('ipress.consolidado');
    Route::get('estimacion/ipress/descargar/{tipo}/{can_id}', 'Site\ResponsableIpressController@descargar_consolidado_farmacia_servicios')->name('ipress.descargar_consolidado_farmacia_servicios');
    
    Route::get('estimacion/ipress/editar_consolidado/{tipo}/{can_id}', 'Site\ResponsableIpressController@editar_consolidado_farmacia_servicios')->name('ipress.editar_consolidado_farmacia_servicios');

    Route::get('estimacion/ipress/ver_consolidado/{tipo}/{can_id}/{user_id}', 'Site\ResponsableIpressController@ver_consolidado_farmacia_servicios')->name('ipress.ver_consolidado_farmacia_servicios');

    Route::get('estimaciones/servicios/ipress/descargas/{tipo}/{can_id}/{establecimiento_id}/{servicio_id}/{dni}', 'Site\ResponsableIpressController@descargar_estimacion_farmacia_servicios')->name('ipress.descargar_estimacion_farmacia_servicios');

    Route::get('ipress/ver_producto_consolidado/{establecimiento_id}/{can_id}/{tipo}', 'Site\ResponsableIpressController@ver_producto_consolidado')->name('ipress.ver_producto_consolidado');
    
    Route::get('ipress/ver_producto/{establecimiento_id}/{can_id}/{tipo}/{servicio_id}', 'Site\ResponsableIpressController@ver_producto')->name('ipress.ver_producto');

    Route::get('ipress/activar_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Site\ResponsableIpressController@activar_rubro')->name('ipress.activar_rubro');
    Route::patch('ipress/activar_rubro/{can_id}/{establecimiento_id}/{rubro_id}', 'Site\ResponsableIpressController@update_petitorio_rubro')->name('ipress.update_petitorio_rubro');
    Route::get('estimacion-export-consolidado-nivel1/{can_id}/{establecimiento_id}/{opt}/{type}/{user_id}', 'Site\ResponsableIpressController@exportEstimacionDataConsolidada')->name('ipress.exportEstimacionDataConsolidada');

    Route::get('ipress/cargar/datos_consolidado_servicio/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableIpressController@cargar_datos_consolidado')->name('ipress.cargar_datos_consolidado');

    Route::put('ipress/grabar/{id}', 'Site\ResponsableIpressController@grabar')->name('ipress.grabar');
    Route::patch('ipress/grabar/{id}', 'Site\ResponsableIpressController@grabar')->name('ipress.grabar');

    Route::get('ipress/estimacion/cerrar_medicamento_consolidado/{can_id}/{establecimiento_id}/{tipo}', 'Site\ResponsableIpressController@cerrar_medicamento_consolidado')->name('ipress.cerrar_medicamento_consolidado');

    Route::get('ipress/pdf-export/{can_id}/{establecimiento_id}/{opt}/{servicio_id}/{user_id}', 'Site\ResponsableIpressController@pdf_estimacion_ipress')->name('ipress.pdf_estimacion_ipress');
    Route::get('ipress/pdf-export-consolidado/{can_id}/{establecimiento_id}/{opt}/{servicio_id}/{user_id}', 'Site\ResponsableIpressController@pdf_estimacion_ipress2')->name('ipress.pdf_estimacion_ipress2');

    ////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////ResponsableRedRegion///////////////////////////////////////////////////
    Route::resource('region', 'Site\ResponsableRedController');
    Route::get('listar_can_region', 'Site\ResponsableRedController@index')->name('region_region.index');
    Route::get('listar_red/{can_id}', 'Site\ResponsableRedController@listar_red')->name('region.listar_red');
    Route::get('red/listar_distribucion/{can_id}/{establecimiento_id}', 'Site\ResponsableRedController@listar_distribucion')->name('region.listar_distribucion');    
    Route::get('red/farmacia/descargar/{tipo}/{can_id}','Site\ResponsableRedController@descargar_consolidado_farmacia')->name('region.descargar_consolidado_farmacia');
    Route::get('red/servicio/descargar/{tipo}/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\ResponsableRedController@descargar_estimacion_farmacia')->name('region.descargar_estimacion_farmacia');
    Route::get('red_region/ver_producto_consolidado/{establecimiento_id}/{can_id}/{tipo}', 'Site\ResponsableRedController@ver_producto_consolidado')->name('region.ver_producto_consolidado');
    Route::get('red_region/ver_producto/{establecimiento_id}/{can_id}/{tipo}/{servicio_id}', 'Site\ResponsableFarmaciaController@ver_producto')->name('region.ver_producto');
    
    //////////////////////////////ResponsableNacional///////////////////////////////////////////////////
    Route::resource('nacional', 'Site\ResponsableNacionalController');
    Route::get('show_ipress/{id}', 'Site\ResponsableNacionalController@show_ipress')->name('nacional.show_ipress');
    Route::get('listar_can_nacional', 'Site\ResponsableNacionalController@index')->name('nacional.index');
    Route::get('nacional/listar_nacional/{can_id}', 'Site\ResponsableNacionalController@listar_nacional')->name('nacional.listar_nacional');
    Route::get('nacional/listar_red/{can_id}/{region_id}', 'Site\ResponsableNacionalController@listar_red')->name('nacional.listar_red');
    Route::get('nacional/red_region/listar_distribucion/{can_id}/{establecimiento_id}', 'Site\ResponsableNacionalController@listar_distribucion')->name('nacional.listar_distribucion');    
    Route::get('nacional/consolidado/descargar/{tipo}/{can_id}', 'Site\ResponsableNacionalController@descargar_consolidado_nacional')->name('nacional.descargar_consolidado_nacional');
    Route::get('nacional/red_region/consolidado/descargar/{tipo}/{can_id}/{region_id}', 'Site\ResponsableNacionalController@descargar_consolidado_region')->name('nacional.descargar_consolidado_region');
    Route::get('nacional/ipress/consolidado/descargar/{tipo}/{can_id}/{id_establecimiento}', 'Site\ResponsableNacionalController@descargar_consolidado_ipress')->name('nacional.descargar_consolidado_ipress');
    Route::get('nacional/rubros_servicios/consolidado/descargar/{tipo}/{can_id}/{id_establecimiento}/{servicio_id}', 'Site\ResponsableNacionalController@ver_rubros_servicios')->name('nacional.ver_rubros_servicios');    
    Route::get('nacional/rubros_servicios/informacion/{tipo}/{can_id}/{id_establecimiento}/{petitorio_id}', 'Site\ResponsableNacionalController@ver_servicio_rubro')->name('nacional.ver_servicio_rubro');    
    Route::get('nacional/red_region/farmacia/descargar/{tipo}/{can_id}', 'Site\ResponsableNacionalController@descargar_consolidado_farmacia')->name('nacional.descargar_consolidado_farmacia');
    Route::get('nacional/red_region/servicio/descargar/{tipo}/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\ResponsableNacionalController@descargar_estimacion_farmacia')->name('nacional.descargar_estimacion_farmacia');
    Route::get('nacional/red_region/ver_producto_consolidado/{establecimiento_id}/{can_id}/{tipo}', 'Site\ResponsableNacionalController@ver_producto_consolidado')->name('nacional.ver_producto_consolidado');
    Route::get('nacional/red_region/ver_producto/{establecimiento_id}/{can_id}/{tipo}/{servicio_id}', 'Site\ResponsableNacionalController@ver_producto')->name('nacional.ver_producto');
    
    Route::get('nacional/pdf-export/{can_id}/{establecimiento_id}/{opt}/{servicio_id}', 'Site\ResponsableNacionalController@pdf_estimacion_nacional')->name('nacional.pdf_estimacion_nacional');

    Route::get('estimacion-export-consolidado-nacional/{can_id}/{establecimiento_id}/{opt}/{type}', 'Site\ResponsableNacionalController@exportEstimacionDataConsolidada')->name('nacional.exportEstimacionDataConsolidada');
    
    ///////////////////////////////////////////////////////////
    
    Route::resource('divisions', 'Admin\DivisionController');
    Route::get('division/departamentos/{division_id}/{establecimiento_id}/', 'Admin\DivisionController@ver_departamentos')->name('divisions.ver_departamentos');
    Route::get('division/departamentos/asignar_departamentos/{division_id}/{establecimiento_id}', 'Admin\DivisionController@asignar_departamentos')->name('divisions.asignar_departamentos');
    Route::put('division/departamentos/asignar_departamentos/{division_id}/{establecimiento_id}', 'Admin\DivisionController@guardar_departamentos')->name('divisions.guardar_departamentos');
    Route::patch('division/departamentos/asignar_departamentos/{division_id}/{establecimiento_id}', 'Admin\DivisionController@guardar_departamentos')->name('divisions.guardar_departamentos');

    Route::delete('division/departamento/eliminar/{id}/{div_id}/{establecimiento_id}', 'Admin\DivisionController@eliminar_unidad')->name('divisions.eliminar_unidad');
    /*
    Route::get('division/create/{establecimiento_id}', 'Admin\DivisionController@create')->name('divisions.create');
    Route::get('division/{id}/edit/{establecimiento_id}', 'Admin\DivisionController@edit')->name('divisions.edit');
*/
/////////////////////////////////////////////////////////////////////////////////
    Route::resource('unidads', 'Admin\UnidadController');    

    Route::get('unidad/listar_unidad/{division_id}/{establecimiento_id}', 'Admin\UnidadController@listar_unidad')->name('unidads.listar_unidad');
    //Route::get('unidad/create/{division_id}', 'Admin\UnidadController@create')->name('unidads.create');
    //Route::get('unidad/{id}/edit/{division_id}', 'Admin\UnidadController@edit')->name('unidads.edit');

    Route::get('departamento/servicios/{unidad_id}/{division_id}/{establecimiento_id}', 'Admin\UnidadController@ver_servicios')->name('unidads.ver_servicios');
    Route::get('departamento/servicios/asignar_servicios/{unidad_id}/{division_id}/{establecimiento_id}', 'Admin\UnidadController@asignar_servicios')->name('unidads.asignar_servicios');
    Route::put('departamento/servicios/asignar_servicios/{unidad_id}/{division_id}/{establecimiento_id}', 'Admin\UnidadController@guardar_servicios')->name('unidads.guardar_servicios');
    Route::patch('departamento/servicio/asignar_servicios/{unidad_id}/{division_id}/{establecimiento_id}', 'Admin\UnidadController@guardar_servicios')->name('unidads.guardar_servicios');

    Route::delete('departamento/servicio/eliminar/{id}/{dpto_id}/{div_id}/{establecimiento_id}', 'Admin\UnidadController@eliminar_servicio')->name('unidads.eliminar_servicio');

////////////////////////////////////////////////////////////////////////////////////////////////

    Route::resource('femas', 'Admin\FemaController');

    Route::resource('restricions', 'Admin\RestricionController');
    
    Route::resource('rubros', 'Admin\RubroController');
    Route::get('rubros/medicamentos/{rubro_id}', 'Admin\RubroController@ver_medicamentos')->name('rubros.ver_medicamentos');
    Route::get('rubros/asignar_medicamentos/{rubro_id}', 'Admin\RubroController@asignar_medicamentos')->name('rubros.asignar_medicamentos');
    Route::put('rubros/asignar_medicamentos/{rubro_id}', '
        Admin\RubroController@guardar_medicamentos')->name('rubros.guardar_medicamentos');
    Route::patch('rubros/asignar_medicamentos/{rubro_id}', 'Admin\RubroController@guardar_medicamentos')->name('rubros.guardar_medicamentos');

    Route::get('rubros/dispositivos/{rubro_id}', 'Admin\RubroController@ver_dispositivos')->name('rubros.ver_dispositivos');
    Route::get('rubros/asignar_dispositivos/{rubro_id}', 'Admin\RubroController@asignar_dispositivos')->name('rubros.asignar_dispositivos');
    Route::put('rubros/asignar_dispositivos/{rubro_id}', '
        Admin\RubroController@guardar_dispositivos')->name('rubros.guardar_dispositivos');
    Route::patch('rubros/asignar_dispositivos/{rubro_id}', 'Admin\RubroController@guardar_dispositivos')->name('rubros.guardar_dispositivos');
    Route::get('exportRubro/{type}/{rubro_id}/{tipo}', 'Admin\RubroController@exportRubro')->name('rubros.exportRubro');
    Route::get('descargar-rubro/{rubro_id}/{tipo}', 'Admin\RubroController@pdf_rubro')->name('rubros.pdf_rubro');

    Route::get('cargarrubros/{id}', 'Admin\ServicioController@cargarrubros')->name('servicios.cargarrubros');

    Route::get('cargarprovincias/{id}', 'Admin\ProvinciaController@cargarprovincias')->name('provincias.cargarprovincias');
    Route::get('cargardistritos/{id_dpto}/{id_prov}', 'Admin\DistritoController@cargardistritos')->name('distritos.cargardistritos');


    Route::get('actualiza_petitorio/', 'Admin\PetitorioController@actualiza_petitorio')->name('petitorios.actualiza_petitorio');
    Route::get('actualiza_estimacion/', 'Admin\PetitorioController@actualiza_estimacion')->name('petitorios.actualiza_estimacion');
    Route::get('actualiza_estimacion_servicio/', 'Admin\PetitorioController@actualiza_estimacion_servicio')->name('petitorios.actualiza_estimacion_servicio');

    Route::get('can/ejecutar/{tipo}', 'Site\ResponsableFarmaciaController@ejecutar_new_insercion')->name('farmacia.ejecutar_new_insercion');
    Route::get('can/actualiza/{establecimiento_id}/{tipo}', 'Site\ResponsableFarmaciaController@actualiza_new_insercion')->name('farmacia.actualiza_new_insercion');
    Route::get('can/inserta_can/', 'Site\ResponsableFarmaciaController@actualiza_data_nivel_2')->name('farmacia.actualiza_data_nivel_2');
    Route::get('can/petitorio_nivel2/{establecimiento_id}', 'Site\ResponsableFarmaciaController@actualiza_data_petitorio')->name('farmacia.actualiza_data_petitorio');

    Route::patch('farmacia/estimacion/atenciones/{establecimiento_id}', 'Site\ResponsableFarmaciaController@update_atencion')->name('farmacia.update_atencion');
    
    //------------------------------------------------------------------------
    Route::patch('farmacia/servicios/actualizar_atenciones/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@update_atenciones')->name('estimacion_servicio.update_atenciones');
    Route::get('servicios/atenciones/{can_id}', 'Site\EstimacionNivel2_3Controller@editar_atenciones_c')->name('estimacion_servicio.editar_atenciones_c');
    Route::get('estimacion_servicio-export-nivel2y3/{can_id}/{establecimiento_id}/{servicio_id}/{opt}/{type}/{valor}', 'Site\EstimacionNivel2_3Controller@exportDataNivel2y3')->name('estimacion_servicio.exportDataNivel2y3');      
    Route::get('farmacia/activar_servicio/{can_id}/{establecimiento_id}/{servicio_id}', 'Site\ResponsableFarmaciaNivel2_3Controller@activar_servicio')->name('cans.activar_servicio');
    Route::get('farmacia/estimacion/consultorios/{can_id}', 'Site\ResponsableFarmaciaController@atencion_consultorios')->name('farmacia.atencion_consultorios');
    
    /**********
      Route::patch('farmacia/estimacion/atenciones/{establecimiento_id}', 'Site\ResponsableFarmaciaController@update_atencion')->name('farmacia.update_atencion');

    Route::get('farmacia/estimacion/consultorios/{can_id}', 'Site\ResponsableFarmaciaController@atencion_consultorios')->name('farmacia.atencion_consultorios');

    Route::patch('farmacia/servicios/actualizar_atenciones/{establecimiento_id}', 'Site\EstimacionNivel2_3Controller@update_atenciones')->name('estimacion_servicio.update_atenciones');
    Route::get('farmacia/servicios/atenciones/{can_id}', 'Site\EstimacionNivel2_3Controller@editar_atenciones')->name('estimacion_servicio.editar_atenciones');
    Route::get('estimacion_servicio-export-nivel2y3/{can_id}/{establecimiento_id}/{servicio_id}/{opt}/{type}', 'Site\EstimacionNivel2_3Controller@exportDataNivel2y3')->name('estimacion_servicio.exportDataNivel2y3');
    */
   
});

