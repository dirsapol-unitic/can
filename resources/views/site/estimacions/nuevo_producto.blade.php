		<p class="c2 c10"><span class="c5">Después de seleccionar los productos médicos, podremos visualizar lo siguiente.</span>
			</p>
		<br/>
		<p class="c2"><span class="c1"><b>1.- MENSAJE:</b></span><span> Este mensaje aparecerá por única vez después de haber hecho la asignación de medicamentos/dispositivos médicos.</span><span class="c6 c1"> </span>
		</p>
		<ul>
		<p>
		<img alt="" src='{{ asset ("/images/image45.jpg") }}' height="50%" width="50%"></span></p>
		</ul>
		<br/>
		<p class="c7"><span class="c1"><b>2.- NUEVO: </b></span><span class="c5">Este botón estará activo siempre y cuando hayan medicamentos/dispositivos médicos para registrar, en la parte superior derecha de dicho botón aparece la cantidad de medicamentos/dispositivos médicos que hay en lista para poder agregar. Esta opción solo me permitirá registrar medicamento por medicamento.</span></p>
		<ul>
		<p class="c7"><img alt="" src='{{ asset ("/images/image12.jpg") }}' height="30%" width="30%" title=""></span></p><p class="c7 c4"><span class="c6 c1"></span></p>
		<p class="c7 c4"><span class="c6 c1"></span></p><p class="c7 c4"><span class="c6 c1"></span></p><p class="c7 c4"><span class="c6 c1"></span></p><p class="c7"><span class="c5">Al hacer clic en el botón NUEVO me aparecerá la siguiente ventana.</span></p><p class="c0"><span class="c6 c1"></span></p>
		<p class="c2"><img alt="" src='{{ asset ("/images/image43.jpg") }}' height="30%" width="30%" ></span></p>
		
		<p class="c3"><span class="c1"><b>1.-NOMBRE DEL MEDICAMENTO/DISPOSITIVO MEDICO:</b> </span><span class="c5">Al hacer clic en la cajita de texto, podemos ir escribiendo. A medida que vamos editando se iran filtrando todos los medicamentos o dispositivos médicos (segun que estemos añadiendo). Seleccionamos el medicamento/dispositivo médico que queremos agregar a nuestra lista del CAN, para hacer la selección más rápida.</span></p><p class="c3"><img alt="" src='{{ asset ("/images/image24.jpg") }}' height="30%" width="30%" ></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p>
		<br/>
		<p class="c3"><span class="c1"><b>2.- CPMA: </b></span><span class="c5">El consumo promedio mensual ajustado, está dado por el movimiento de consumo de un producto en el mes que se calcula por un periodo de tiempo aproximado de los últimos 6 meses para hacer referencia al promedio de dicho movimiento. Es decir es el promedio de consumo de un producto en los últimos 6 meses (Con más detalle sobre este tema, visualizar la cartilla). Hay que tener en cuenta que no se guardara el CPMA si tiene valor 0, le saldrá una ventana con dicha alerta.</span>
		</p>
		<p>
		<img alt="" src='{{ asset ("/images/image14.jpg") }}' height="30%" width="30%"></span></p>
		<p><b>
			Es muy importante a tener en cuenta este valor, ya que de ello nosotros como UGPFDMPS, sabremos si el medicamento / dispositivo medico tiene movimiento en el año.</b>
		</p>
		
		<p class="c3 c4"><span class="c5"></span></p><p class="c3 c4"><span class="c5"></span></p><p class="c3 c4"><span class="c5"></span></p><p class="c3"><span class="c1 c12">El CPMA puede ser registrado con valores decimales
			(Tener en cuenta solo aquellos productos que vienen en caja, en frascos, etc; por ejemplo gotas, o cepillos que su consumo esta dado de esa forma)
		, es decir con punto y no con coma.Ejemplo 3.5</span></p>

		<p class="c3"><span class="c1"><b>3.- STOCK ACTUAL: </b></span><span>Es la cantidad de un medicamentos/dispositivo médico que tiene en el momento que llena su CAN en farmacia. Este valor en entero.</span></p>
		<p class="c3"><span class="c1"><b>4.- NECESIDAD ANUAL: </b></span><span>La necesidad anual es una proyección, calculado por el CPMA multiplicado por 12; es decir al momento de que se ingresa el CPMA, el aplicativo web le mostrara dicha proyección (Necesidad=CPMA*12); pero esta casilla de texto es modificable, es decir se podrá colocar una cantidad menor a la proyectada</span><span class="c6 c1">; pero  no se le permitirá guardar el medicamento/dispositivo medico si sobrepasa dicha proyección. </span></p>
		<p><b>La Necesidad anual es la cantidad de productos que usa al año, y no se piense que es la cantidad que se va a comprar</b></p>
		<p class="c3"><span class="c5">Cuando el CPMA tiene decimales por ejemplo 0.3, la necesidad anual saldrá con valor entero. </span></p>
		<p><span>
		<img alt="" src='{{ asset ("/images/image21.jpg") }}' height="30%" width="30%"></span></p>
		<br/>
		<p class="c3"><span class="c5">Cambiando necesidad anual &lt; a 12 veces el CPMA</span></p>
		<p><span><img alt="" src='{{ asset ("/images/image22.jpg") }}' height="30%" width="30%" title=""></span></p>
		<p class="c3"><span class="c5">Cambiando necesidad anual &gt; a 12 veces el CPMA</span></p>
		<p><span><img alt="" src='{{ asset ("/images/image19.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c3"><img alt="" src='{{ asset ("/images/image23.jpg") }}' height="30%" width="30%" ></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p><p class="c3 c4"><span class="c6 c1"></span></p>
		<p class="c3"><span class="c1"><b>5.- PRORRATEO EN MESES: </b></span><span>Es la distribución en meses de la cantidad del medicamento/dispositivo médico (Enero – Diciembre) con respecto a la necesidad anual </span><span class="c23 c1">(VALORES ENTEROS).</span><span class="c5">  Dicha distribución debe ser exactamente igual a la necesidad anual es decir no debe ser menor, ni mayor a la registrada.</span>
		</p>
		<p>
		<img alt="" src='{{ asset ("/images/image26.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c3 c4"><span class="c6 c1"></span></p>
		<p class="c3"><span class="c1"><b>6.- JUSTIFICACION: </b></span><span class="c5">Es una descripción por la cual se está pidiendo el medicamento, si es que antes no tenía, o en su defecto por la cantidad colocada, por ejemplo que hay un nuevo especialista y por eso se rquiere dicho producto. Esta opción no es obligatoria.</span></p>
		<p class="c3"><span class="c1"><b>7.- NECESIDAD: </b></span><span class="c5">Es el valor de su necesidad anual registrada.</span>
		</p>
		<p><img alt="" src='{{ asset ("/images/image15.jpg") }}' height="30%" width="30%"></span></p><p class="c3 c4"><span class="c6 c1"></span></p>
		<p class="c3"><span class="c1"><b>8.- TOTAL PRORRATEO: </b></span><span>Es la suma del prorrateo en meses, que tiene que coincidir con su necesidad, este valor se ira modificando a medida que se vayan registrando en los meses. </span><span class="c6 c1">No se le permitirá guardar si el prorrateo es distinto a su necesidad.</span>
		</p>
		<p><img alt="" src='{{ asset ("/images/image16.jpg") }}' height="30%" width="30%"></span></p><p class="c3 c4"><span class="c6 c1"></span></p>
		<p class="c3"><span class="c1"><b>9.- FALTA PARA COMPLETAR SU PRORRATEO: </b></span><span class="c5">Es la diferencia que falta para completar el prorrateo. Si tiene como valor cero, indica que ha completado su distribución; si tiene valor positivo, es porque aún falta completar en la distribución, y si tiene valor negativo, es porque se ha sobrepasado en la distribución.</span>
		</p>
		<p>
		<img alt="" src='{{ asset ("/images/image17.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c3 c4"><span class="c6 c1"></span></p><p class="c3"><span class="c1"><b>10.- GUARDAR/CANCELAR: </b></span><span>Puede hacer clic en el botón guardar para completar su registro o clic en el botón cancelar para anular dicho ingreso.</span></p>
	</ul>
	<br/>
		<p class="c2"><span class="c1"><b>3.- DESCARGAR AVANCE: </b></span><span>Haciendo clic en este botón, podrá visualizar en formato Excel, el registro del CAN.</span><span class="c1"> </span><span class="c5">Dicho registro mostrara los medicamentos/dispositivos médicos que tienen valor, es decir del listado que se tiene; vera solo de lo que ha llenado. Este Excel servirá como guía de lo que está registrando.</span>
		</p>
		<ul>
		<p><img alt="" src='{{ asset ("/images/image42.jpg") }}' height="30%" width="30%"></span></p>
		</ul>
		<p class="c7"><span class="c1"><b>4.- CERRAR PETITORIO: </b></span><span class="c5">Una vez que se haya terminado de registrar todos los medicamentos o dispositivos médicos, se procederá al cierre, la cual haciendo clic en el botón de Cerrar Petitorio, nos preguntara si queremos cerrar el petitorio, si es afirmativo, clic en el botón “Si, cerrar” y si aún queremos seguir registrando clic en el botón “Cancel”.</span>
		</p>
		<ul>
		<p>
		<img alt="" src='{{ asset ("/images/image25.jpg") }}' height="30%" width="30%" ></p><p class="c7 c4"><span class="c6 c1"></span></p>
		<p class="c7 c4"><span class="c6 c1"></span></p><p class="c7"><span class="c5">Al final nos llevara a la siguiente ventana donde podemos ver el siguiente mensaje con los productos llenados.</span>
		</p>
		<p>
		<img alt="" src='{{ asset ("/images/image49.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c2"><span class="c5">En la parte principal se observara el cambio de estado según corresponda, es decir si cerro medicamentos o dispositivo médicos.</span>
		</p>
		<p><img alt="" src='{{ asset ("/images/image28.jpg") }}' height="30%" width="30%"></span></p>
		</ul>
		<p class="c7"><span class="c1"><b>5.- BUSCAR. </b></span><span class="c5">Es una caja de texto la cual podemos hacer la búsqueda del medicamento/dispositivo médico que deseamos seleccionar para la edición/eliminación.  A medida que se ingrese carácter por carácter se ira filtrando la correspondencia del texto ingresado.</span></p>
		<p class="c7"><span class="c5">Este proceso se repetirá cada vez que desee editar un medicamento y no desea hacerlo deslizando el scroll del lado izquierdo para buscarlo. Si dicho medicamento/dispositivo no se encontrara tiene la opción de agregar dicho medicamento en el botón NUEVO y añadirlo.</span></p>
		<p class="c7"><span class="c5">En la parte inferior de la búsqueda nos mostrara el número de entradas que coincide con la búsqueda, asi como el total de entradas (medicamentos/dispositivo medico) que tiene en el listado.</span></p>
		<ul>
		<p class="c2"><img alt="" src='{{ asset ("/images/image47.jpg") }}' height="30%" width="30%"></span></p>
		</ul>
		<br/>
		<p class="c2"><span class="c1"><b>6.- BOTON EDITAR: </b></span><span class="c5">Al hacer clic en dicho botón ubicado en la parte izquierda de cada medicamento/dispositivo, nos permitirá ingresar los valores para el registro del producto en su CAN. En la ventana emergente, se muestra los datos del producto a editar.</span></p>
	<ul>
		<p class="c7"><img alt="" src='{{ asset ("/images/image30.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c7"><span>Este formulario es similar al formulario de </span><span class="c1">NUEVO</span><span>, la única diferencia está en que aquí se podrá hacer las modificaciones que desee hasta cerrar el petitorio. Tiene los mismos parámetros a llenar y el mismo concepto que el formulario de </span><span class="c1">NUEVO</span><span class="c5">, con las mismas validaciones.</span>
		</p>
		<p>
		<img alt="" src='{{ asset ("/images/image44.jpg") }}' height="30%" width="30%"></span></p><p class="c7"><span class="c5">Al guardar nos saldrá el siguiente mensaje de actualización del producto.</span>
			</p>
		<p><img alt="" src='{{ asset ("/images/image34.jpg") }}' height="30%" width="30%"></span>
		</p>
		<p><img alt="" src='{{ asset ("/images/image48.jpg") }}' height="30%" width="30%"></span>
		</p><p>
			<img alt="" src='{{ asset ("/images/image27.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c7 c4"><span class="c5"></span></p><p class="c7 c4"><span class="c5"></span></p><p class="c7 c4"><span class="c5"></span></p><p class="c7 c4"><span class="c5"></span></p><p class="c7 c4"><span class="c5"></span></p><!--p class="c7 c4"><span class="c5"></span></p><p class="c2"><span class="c5">Si por algún motivo, nos saliera el siguiente mensaje, le rogamos comunicarse con la UPFDMPS para revisar dicho inconveniente.</span></p-->
		<hr style="page-break-before:always;display:none;"><p class="c0"><span class="c6 c1"></span></p><p class="c2"><span class="c5">Repetimos este proceso hasta culminar con todos los medicamentos que desee.</span></p><p class="c2"><span class="c1"><b>BOTON ELIMINAR.</b> </span><span class="c5">Al hacer clic en dicho botón ubicado en la parte izquierda de cada medicamento/dispositivo, nos permitirá borrar un producto del listado de su CAN.</span></p><p class="c7"><span class="c1"> </span><span class="c5">La ventana emergente que nos sale, preguntara si queremos eliminar dicho, si es afirmativo, clic en el botón “Si, eliminar” y si aún queremos conservarlo  clic en el botón “Cancel”.</span>
		</p>
		<p align="cente">
		<img alt="" src='{{ asset ("/images/image36.jpg") }}' height="30%" width="30%"></span></p>
		<p class="c0"><span class="c6 c1"></span></p><p class="c0"><span class="c6 c1"></span></p><p class="c0"><span class="c6 c1"></span></p><p class="c0"><span class="c6 c1"></span></p><p class="c0"><span class="c6 c1"></span></p><p class="c0"><span class="c6 c1"></span></p><p class="c0"><span class="c6 c1"></span></p><p class="c2"><span>Este botón es importante cuando hemos editado un producto y  </span><span class="c1 c21">por algún motivo ya  no queremos que aparezca en nuestro CAN.</span><span class="c5">  </span></p><p class="c2"><span class="c1 c17">“Recuerde que solo aparecerán los medicamentos/dispositivos médicos que tienen registro, los productos que tienen valores Ceros no serán admitidos.”, y no será necesario eliminarlos.</span></p>
	</ul>
	<br/>
		<p class="c7"><span class="c1"><b>7.- LISTA DE MEDICAMENTOS. </b></span><span class="c5">Es la relación de medicamentos/dispositivos médicos que hemos seleccionado anteriormente. Aquí se procederá al registro de los productos correspondiente a su nivel que desea hacer el requerimiento para su CAN.</span></p>
		<br/>
		<p class="c2"><span class="c1"><b> 8.- TOTAL DE MEDICAMENTOS. </b></span><span>Es el total de entradas de los productos que ha considerado para su CAN.</span><span class="c6 c1"> </span></p><hr style="page-break-before:always;display:none;"><p class="c0"><span class="c1 c15"></span></p>

