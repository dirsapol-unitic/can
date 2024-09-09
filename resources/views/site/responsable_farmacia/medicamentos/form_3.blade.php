<script type="text/javascript">
    
    function calculo() {
        document.getElementById('cpma_1').value = document.getElementById('cpma').value;
        document.getElementById('cpma_2').value = document.getElementById('cpma').value;
    }

    function suma_meses() {
        var total1 = 0;
        
        $(".suma_mes").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo').innerHTML = total1;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    function suma_meses1() {
        var total2 = 0;
        
        $(".suma_mes1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total2 += 0;
            } else {
              total2 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo1').innerHTML = total2;
        document.getElementById('sFalta1').innerHTML = document.getElementById('sNecesidad1').innerHTML - document.getElementById('sProrroteo1').innerHTML;
    }

    function suma_meses2() {
        var total3 = 0;
        
        
        $(".suma_mes2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total3 += 0;
            } else {
              total3 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo2').innerHTML = total3;
        document.getElementById('sFalta2').innerHTML = document.getElementById('sNecesidad2').innerHTML - document.getElementById('sProrroteo2').innerHTML;
    }

    function sumar_necesidad() {
        var total1 = 0; var total2 = 0; var dividir = 0; var total3 = 0; var total4 = 0;
        
        $(".suma_mes").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        $(".necesidad").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total2 += 0;
            } else {
              total2 += parseFloat($(this).val());
            }
        });

        document.getElementById('sProrroteo').innerHTML = total1;
        document.getElementById('sNecesidad').innerHTML = total2;
        document.getElementById('sFalta').innerHTML = document.getElementById('sNecesidad').innerHTML - document.getElementById('sProrroteo').innerHTML;
    }

    function sumar_necesidad2() {
        var total3 = 0;
        var total4 = 0;
        
        
        $(".suma_mes1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total3 += 0;
            } else {
              total3 += parseFloat($(this).val());
            }
        });
        $(".necesidad1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total4 += 0;
            } else {
              total4 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo1').innerHTML = total3;
        document.getElementById('sNecesidad1').innerHTML = total4;
        document.getElementById('sFalta1').innerHTML = document.getElementById('sNecesidad1').innerHTML - document.getElementById('sProrroteo1').innerHTML;
        
    }

    function sumar_necesidad3() {
        var total5 = 0;
        var total6 = 0;
        
        $(".suma_mes2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total5 += 0;
            } else {
              total5 += parseFloat($(this).val());
            }
        });
        $(".necesidad2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total6 += 0;
            } else {
              total6 += parseFloat($(this).val());
            }
        });
        document.getElementById('sProrroteo2').innerHTML = total5;
        document.getElementById('sNecesidad2').innerHTML = total6;
        document.getElementById('sFalta2').innerHTML = document.getElementById('sNecesidad2').innerHTML - document.getElementById('sProrroteo2').innerHTML;
    }

        

    const getDividir = () =>{
        limpiar_imput1();
      
        var necesidad_input; var valor; 
        necesidad_input = document.getElementById('necesidad_anual').value;
      
        
        var quo = Math.floor(necesidad_input/12);
        var valor = necesidad_input%12; 
        var rem = 1;
        if(quo == 0 ){
            quo =1; rem =0;
        }
        else
        {
            document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = document.getElementById('mes9').value = document.getElementById('mes10').value = document.getElementById('mes11').value = document.getElementById('mes12').value = quo;
        }
            
        
        console.log('Igual = ',quo,'Resto = ',rem,'Necesidad = ',valor);

        switch(valor){
            case 1 : document.getElementById('mes1').value = quo + rem;break;
            case 2 : document.getElementById('mes1').value = document.getElementById('mes2').value = quo + rem;break;
            case 3 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value = quo + rem;break;
            case 4 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = quo + rem;break;
            case 5 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = quo + rem;break;
            case 6 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = quo + rem;break;
            case 7 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = quo + rem;break;
            case 8 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = quo + rem;break;
            case 9 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = document.getElementById('mes9').value = quo + rem;break;
            case 10 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = document.getElementById('mes9').value = document.getElementById('mes10').value = quo + rem; break;
            case 11 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = document.getElementById('mes9').value = document.getElementById('mes10').value = document.getElementById('mes11').value = quo + rem;break;
            case 12 : document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = document.getElementById('mes9').value = document.getElementById('mes10').value = document.getElementById('mes11').value = document.getElementById('mes12').value = quo + rem; break;

        }
        sumar_necesidad();
    }

    function limpiar_imput1() {
        document.getElementById('mes1').value = document.getElementById('mes2').value = document.getElementById('mes3').value =document.getElementById('mes4').value = document.getElementById('mes5').value = document.getElementById('mes6').value = document.getElementById('mes7').value = document.getElementById('mes8').value = document.getElementById('mes9').value = document.getElementById('mes10').value = document.getElementById('mes11').value = document.getElementById('mes12').value = 0;
    }
    function limpiar_imput2() {
        document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = document.getElementById('mes9_1').value = document.getElementById('mes10_1').value = document.getElementById('mes11_1').value = document.getElementById('mes12_1').value = 0;
    }
    function limpiar_imput3() {
        document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = document.getElementById('mes9_2').value = document.getElementById('mes10_2').value = document.getElementById('mes11_2').value = document.getElementById('mes12_2').value = 0;
    }
    const getDividir1 = () =>{
        limpiar_imput2();
      
        var necesidad_input; var valor; var necesidad_anual_inicial=0;
        necesidad_input = document.getElementById('necesidad_anual_1').value;
        
        necesidad_anual_inicial = document.getElementById('necesidad_anual').value;
        necesidad_anual_limite = Math.round(necesidad_anual_inicial * 0.05) + parseInt(necesidad_anual_inicial);
        console.log('necesidad_input = ',necesidad_input,'necesidad_anual_limite = ',necesidad_anual_limite);
        if ( necesidad_input > necesidad_anual_limite ) {            
            swal("La Necesidad Anual del Año 2 no debe de superar el 5% de la Necesidad Anual del Año 1");              
            return false;
        }
        
        var quo = Math.floor(necesidad_input/12);
        var valor = necesidad_input%12; 
        var rem = 1;
        if(quo == 0 ){
            quo =1; rem =0;
        }
        else
        {
            document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = document.getElementById('mes9_1').value = document.getElementById('mes10_1').value = document.getElementById('mes11_1').value = document.getElementById('mes12_1').value = quo;
        }
            
        
        console.log('necesidad_anual_limite = ',necesidad_anual_limite,'necesidad_input = ',necesidad_input,'Necesidad = ',valor);

        switch(valor){
            case 1 : document.getElementById('mes1_1').value = quo + rem;break;
            case 2 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = quo + rem;break;
            case 3 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value = quo + rem;break;
            case 4 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = quo + rem;break;
            case 5 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = quo + rem;break;
            case 6 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = quo + rem;break;
            case 7 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = quo + rem;break;
            case 8 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = quo + rem;break;
            case 9 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = document.getElementById('mes9_1').value = quo + rem;break;
            case 10 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = document.getElementById('mes9_1').value = document.getElementById('mes10_1').value = quo + rem; break;
            case 11 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = document.getElementById('mes9_1').value = document.getElementById('mes10_1').value = document.getElementById('mes11_1').value = quo + rem;break;
            case 12 : document.getElementById('mes1_1').value = document.getElementById('mes2_1').value = document.getElementById('mes3_1').value =document.getElementById('mes4_1').value = document.getElementById('mes5_1').value = document.getElementById('mes6_1').value = document.getElementById('mes7_1').value = document.getElementById('mes8_1').value = document.getElementById('mes9_1').value = document.getElementById('mes10_1').value = document.getElementById('mes11_1').value = document.getElementById('mes12_1').value = quo + rem; break;

        }
        sumar_necesidad2();
    }
    const getDividir2 = () =>{
        limpiar_imput3();
      
        var necesidad_input; var valor; var necesidad_anual_inicial=0;
        necesidad_input = document.getElementById('necesidad_anual_2').value;
        necesidad_anual_inicial = document.getElementById('necesidad_anual').value;
        necesidad_anual_limite = Math.round(necesidad_anual_inicial * 0.1) + parseInt(necesidad_anual_inicial);
        if ( necesidad_input > necesidad_anual_limite ) {    
            swal("La Necesidad Anual del Año 3 no debe de superar el 10% de la Necesidad Anual del Año 1");              
            return false;
        }

        var quo = Math.floor(necesidad_input/12);
        var valor = necesidad_input%12; 
        var rem = 1;
        if(quo == 0 ){
            quo =1; rem =0;
        }
        else
        {
            document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = document.getElementById('mes9_2').value = document.getElementById('mes10_2').value = document.getElementById('mes11_2').value = document.getElementById('mes12_2').value = quo;
        }
            
        
        console.log('necesidad_anual_limite = ',necesidad_anual_limite,'necesidad_input = ',necesidad_input,'Necesidad = ',valor);

        switch(valor){
            case 1 : document.getElementById('mes1_2').value = quo + rem;break;
            case 2 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = quo + rem;break;
            case 3 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value = quo + rem;break;
            case 4 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = quo + rem;break;
            case 5 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = quo + rem;break;
            case 6 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = quo + rem;break;
            case 7 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = quo + rem;break;
            case 8 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = quo + rem;break;
            case 9 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = document.getElementById('mes9_2').value = quo + rem;break;
            case 10 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_1').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = document.getElementById('mes9_2').value = document.getElementById('mes10_2').value = quo + rem; break;
            case 11 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = document.getElementById('mes9_2').value = document.getElementById('mes10_2').value = document.getElementById('mes11_2').value = quo + rem;break;
            case 12 : document.getElementById('mes1_2').value = document.getElementById('mes2_2').value = document.getElementById('mes3_2').value =document.getElementById('mes4_2').value = document.getElementById('mes5_2').value = document.getElementById('mes6_2').value = document.getElementById('mes7_2').value = document.getElementById('mes8_2').value = document.getElementById('mes9_2').value = document.getElementById('mes10_2').value = document.getElementById('mes11_2').value = document.getElementById('mes12_2').value = quo + rem; break;

        }
        sumar_necesidad2();
    }

    function getCopiar1() {
        document.getElementById('cpma_1').value = document.getElementById('cpma').value;

        var cinco_porciento = Math.round(document.getElementById('necesidad_anual').value*0.05);
        necesidad_input_1 = document.getElementById('necesidad_anual').value;
        necesidad_input = parseInt(necesidad_input_1) + parseInt(cinco_porciento);
        document.getElementById('necesidad_anual_1').value = necesidad_input;
        
        //diez_porciento = Math.round(document.getElementById('necesidad_anual').value*0.1);
        console.log('cinco_porciento = ',cinco_porciento,'necesidad_input_1 = ',necesidad_input_1,'necesidad_input = ',necesidad_input);

        document.getElementById('mes1_1').value = parseInt(Math.round(document.getElementById('mes1').value*0.05)) + parseInt(document.getElementById('mes1').value);
        document.getElementById('mes2_1').value = parseInt(Math.round(document.getElementById('mes2').value*0.05)) + parseInt(document.getElementById('mes2').value);
        document.getElementById('mes3_1').value = parseInt(Math.round(document.getElementById('mes3').value*0.05)) + parseInt(document.getElementById('mes3').value);
        document.getElementById('mes4_1').value = parseInt(Math.round(document.getElementById('mes4').value*0.05)) + parseInt(document.getElementById('mes4').value);
        document.getElementById('mes5_1').value = parseInt(Math.round(document.getElementById('mes5').value*0.05)) + parseInt(document.getElementById('mes5').value);
        document.getElementById('mes6_1').value = parseInt(Math.round(document.getElementById('mes6').value*0.05)) + parseInt(document.getElementById('mes6').value);
        document.getElementById('mes7_1').value = parseInt(Math.round(document.getElementById('mes7').value*0.05)) + parseInt(document.getElementById('mes7').value);
        document.getElementById('mes8_1').value = parseInt(Math.round(document.getElementById('mes8').value*0.05)) + parseInt(document.getElementById('mes8').value);
        document.getElementById('mes9_1').value = parseInt(Math.round(document.getElementById('mes9').value*0.05)) + parseInt(document.getElementById('mes9').value);
        document.getElementById('mes10_1').value = parseInt(Math.round(document.getElementById('mes10').value*0.05)) + parseInt(document.getElementById('mes10').value);
        document.getElementById('mes11_1').value = parseInt(Math.round(document.getElementById('mes11').value*0.05)) + parseInt(document.getElementById('mes11').value);
        document.getElementById('mes12_1').value = parseInt(Math.round(document.getElementById('mes12').value*0.05)) + parseInt(document.getElementById('mes12').value);


        var total1 = 0; var total2 = 0; var falta = 0; var par = 0;
        
        $(".suma_mes1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        $(".necesidad1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total2 += 0;
            } else {
              total2 += parseFloat($(this).val());
            }
        });

        var valor1=0; var mes_dato; var dato1;
        total3 = total2-total1;
        
        if(total3!=0){
            
            if(total3>0 && total3<13){
                valor1=13-total3;
                for(let i=valor1; i<13;i++){
                    mes_dato= 'mes'+i+'_'+1 ;
                    dato1= document.getElementById(mes_dato).value;
                    document.getElementById(mes_dato).value = parseInt(dato1) + 1;
                }
                
            }
            else{
                valor1=13+total3;
                for(let i=valor1; i<13;i++){
                    mes_dato= 'mes'+i+'_'+1 ;  
                    dato1= document.getElementById(mes_dato).value;
                    document.getElementById(mes_dato).value = parseInt(dato1) - 1;
                }
            } 
            total2 = 0 ;
            $(".necesidad1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
                  total2 += 0;
                } else {
                  total2 += parseFloat($(this).val());
                }
            });
        } 

        var total1=0; var total3=0;
        $(".suma_mes1").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        
        
        document.getElementById('sProrroteo1').innerHTML = total1;
        document.getElementById('sNecesidad1').innerHTML = total2;
        document.getElementById('sFalta1').innerHTML = document.getElementById('sNecesidad1').innerHTML - document.getElementById('sProrroteo1').innerHTML;

        
    }

    function getCopiar2() {
        document.getElementById('cpma_2').value = document.getElementById('cpma').value;

        var diez_porciento = Math.round(document.getElementById('necesidad_anual').value*0.1);
        necesidad_input_1 = document.getElementById('necesidad_anual').value;
        necesidad_input = parseInt(necesidad_input_1) + parseInt(diez_porciento);
        document.getElementById('necesidad_anual_2').value = necesidad_input;
        
        //diez_porciento = Math.round(document.getElementById('necesidad_anual').value*0.1);

        document.getElementById('mes1_2').value = parseInt(Math.round(document.getElementById('mes1').value*0.1)) + parseInt(document.getElementById('mes1').value);
        document.getElementById('mes2_2').value = parseInt(Math.round(document.getElementById('mes2').value*0.1)) + parseInt(document.getElementById('mes2').value);
        document.getElementById('mes3_2').value = parseInt(Math.round(document.getElementById('mes3').value*0.1)) + parseInt(document.getElementById('mes3').value);
        document.getElementById('mes4_2').value = parseInt(Math.round(document.getElementById('mes4').value*0.1)) + parseInt(document.getElementById('mes4').value);
        document.getElementById('mes5_2').value = parseInt(Math.round(document.getElementById('mes5').value*0.1)) + parseInt(document.getElementById('mes5').value);
        document.getElementById('mes6_2').value = parseInt(Math.round(document.getElementById('mes6').value*0.1)) + parseInt(document.getElementById('mes6').value);
        document.getElementById('mes7_2').value = parseInt(Math.round(document.getElementById('mes7').value*0.1)) + parseInt(document.getElementById('mes7').value);
        document.getElementById('mes8_2').value = parseInt(Math.round(document.getElementById('mes8').value*0.1)) + parseInt(document.getElementById('mes8').value);
        document.getElementById('mes9_2').value = parseInt(Math.round(document.getElementById('mes9').value*0.1)) + parseInt(document.getElementById('mes9').value);
        document.getElementById('mes10_2').value = parseInt(Math.round(document.getElementById('mes10').value*0.1)) + parseInt(document.getElementById('mes10').value);
        document.getElementById('mes11_2').value = parseInt(Math.round(document.getElementById('mes11').value*0.1)) + parseInt(document.getElementById('mes11').value);
        document.getElementById('mes12_2').value = parseInt(Math.round(document.getElementById('mes12').value*0.1)) + parseInt(document.getElementById('mes12').value);


        var total1 = 0; var total2 = 0; var falta = 0; var par = 0;
        
        $(".suma_mes2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        $(".necesidad2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total2 += 0;
            } else {
              total2 += parseFloat($(this).val());
            }
        });

        var valor1=0; var mes_dato; var dato1;
        total3 = total2-total1;
        
        if(total3!=0){
            
            if(total3>0 && total3<13){
                valor1=13-total3;
                for(let i=valor1; i<13;i++){
                    mes_dato= 'mes'+i+'_'+2 ;
                    dato1= document.getElementById(mes_dato).value;
                    document.getElementById(mes_dato).value = parseInt(dato1) + 1;
                }
                
            }
            else{
                valor1=13+total3;
                for(let i=valor1; i<13;i++){
                    mes_dato= 'mes'+i+'_'+2 ;  
                    dato1= document.getElementById(mes_dato).value;
                    document.getElementById(mes_dato).value = parseInt(dato1) - 1;
                }
            } 
            total2 = 0 ;
            $(".necesidad2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
                  total2 += 0;
                } else {
                  total2 += parseFloat($(this).val());
                }
            });
        } 

        var total1=0; var total3=0;
        $(".suma_mes2").each(function() {
            if (isNaN(parseFloat($(this).val()))) {
              total1 += 0;
            } else {
              total1 += parseFloat($(this).val());
            }
        });
        
        
        document.getElementById('sProrroteo2').innerHTML = total1;
        document.getElementById('sNecesidad2').innerHTML = total2;
        document.getElementById('sFalta2').innerHTML = document.getElementById('sNecesidad2').innerHTML - document.getElementById('sProrroteo2').innerHTML;


        
    }
</script>
<div class="modal" id="modal-form" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_contact" method="post" class="form-horizontal" data-toggle="validator" enctype="multipart/form-data">
                {{ csrf_field() }} {{ method_field('POST') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"> &times; </span>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="box-body">
                    <input type="hidden" id="id" name="id">
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-11 form-group has-error"> 
                            <div class="input-group">
                                <span class="input-group-addon" style="font-size:12px;"><b> Producto</b></span>
                                <textarea style="font-size:12px;" rows="2" id="descripcion" placeholder="descripcion"  name="descripcion"  class="form-control">                                    
                                </textarea>
                                <span class="help-block with-errors"></span>
                            </div>                           
                        </div>
                    </div>
                    @if($nivel==1)
                    <div class="col-md-12">
                        <table id="estimacion" class="table table-striped">
                            <thead>
                                <tr><th colspan="5" bgcolor="#D4E6F1" style="text-align:center;">PRORRATEO AÑO 1</th><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" tabindex="1" type="number" min="0" step="any" id="cpma" placeholder="CPMA"   name="cpma" required class="form-control cpm" onkeyup="calculo();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Abril</b></span>
                                            <input style="font-size:12px;"  tabindex="6"  type="number" min="0" id="mes4" placeholder="Abril"  name="mes4" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Setiembre</b></span>
                                            <input style="font-size:12px;"  tabindex="11"  type="number" min="0" id="mes9" placeholder="Setiembre"  name="mes9" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" tabindex="2" type="number" min="0" id="necesidad_anual" placeholder="Necesidad Anual"  name="necesidad_anual" required  class="form-control necesidad" onkeyup="sumar_necesidad();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Mayo</b></span>
                                            <input style="font-size:12px;"  tabindex="7"  type="number" min="0" id="mes5" placeholder="Mayo"  name="mes5" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Octubre</b></span>
                                            <input style="font-size:12px;"  tabindex="12"  type="number" min="0" id="mes10" placeholder="Octubre"  name="mes10" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Enero</b></span>
                                            <input style="font-size:12px;"  tabindex="3"  type="number" min="0" id="mes1" placeholder="Enero"  name="mes1" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Junio</b></span>
                                            <input style="font-size:12px;"  tabindex="8"  type="number" min="0" id="mes6" placeholder="Junio"  name="mes6" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Noviembre</b></span>
                                            <input style="font-size:12px;"  tabindex="13" type="number" min="0" id="mes11" placeholder="Noviembre"  name="mes11"  required class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Febrero</b></span>
                                            <input style="font-size:12px;" tabindex="4"  type="number" min="0" id="mes2" placeholder="Febrero"  name="mes2" required   class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Julio</b></span>
                                            <input style="font-size:12px;"  tabindex="9"  type="number" min="0" id="mes7" placeholder="Julio"  name="mes7" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Diciembre</b></span>
                                            <input style="font-size:12px;"  tabindex="14" type="number" min="0" id="mes12" placeholder="Diciembre"  name="mes12"  required class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Marzo</b></span>
                                            <input style="font-size:12px;"  tabindex="5"  type="number" min="0" id="mes3" placeholder="Marzo"  name="mes3" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Agosto</b></span>
                                            <input style="font-size:12px;"  tabindex="10"  type="number" min="0" id="mes8" placeholder="Agosto"  name="mes8" required  class="form-control suma_mes" onkeyup="suma_meses();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <button type="button"  tabindex="15"  onclick="getDividir()"> 
                                              Dividir
                                            </button> 
                                            <button type="button"  tabindex="16"  onclick="limpiar_imput1()"> 
                                              Limpiar
                                            </button> 
                                        </div> 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;"><b><span>Necesidad: </span> <span id="sNecesidad"></span></b></td>
                                    <td style="text-align:center;"><b><span>Total Prorrateo: </span> <span id="sProrroteo"></span></b></td>
                                    <td colspan="2"  style="text-align:center;"><b><span>Falta para completar su Prorrateo: </span> <span id="sFalta"></span></b></td>
                                    <td style="text-align:center;"></td>
                                <tr>
                                <tr><td colspan="5" bgcolor="#00a65a" style="color:#FCFBFB; text-align:center;"><b>PRORRATEO AÑO 2</b></td><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;"  tabindex="17" type="number" min="0" step="any" id="cpma_1" placeholder="CPMA"   name="cpma_1" required class="form-control">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Abril</b></span>
                                            <input style="font-size:12px;"  tabindex="22" type="number" min="0" id="mes4_1" placeholder="Abril"  name="mes4_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Setiembre</b></span>
                                            <input style="font-size:12px;"  tabindex="27" type="number" min="0" id="mes9_1" placeholder="Setiembre"  name="mes9_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;"  tabindex="18"  type="number" min="0" id="necesidad_anual_1" placeholder="Necesidad Anual"  name="necesidad_anual_1" required class="form-control necesidad1" onkeyup="sumar_necesidad2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Mayo</b></span>
                                            <input style="font-size:12px;"  tabindex="23" type="number" min="0" id="mes5_1" placeholder="Mayo"  name="mes5_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Octubre</b></span>
                                            <input style="font-size:12px;"  tabindex="28" type="number" min="0" id="mes10_1" placeholder="Octubre"  name="mes10_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Enero</b></span>
                                            <input style="font-size:12px;"  tabindex="19"  type="number" min="0" id="mes1_1" placeholder="Enero"  name="mes1_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Junio</b></span>
                                            <input style="font-size:12px;"  tabindex="24" type="number" min="0" id="mes6_1" placeholder="Junio"  name="mes6_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Noviembre</b></span>
                                            <input style="font-size:12px;"  tabindex="29" type="number" min="0" id="mes11_1" placeholder="Noviembre"  name="mes11_1"  required class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Febrero</b></span>
                                            <input style="font-size:12px;"  tabindex="20"  type="number" min="0" id="mes2_1" placeholder="Febrero"  name="mes2_1" required   class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Julio</b></span>
                                            <input style="font-size:12px;"  tabindex="25" type="number" min="0" id="mes7_1" placeholder="Julio"  name="mes7_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Diciembre</b></span>
                                            <input style="font-size:12px;"  tabindex="30" type="number" min="0" id="mes12_1" placeholder="Diciembre"  name="mes12_1"  required class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Marzo</b></span>
                                            <input style="font-size:12px;"  tabindex="21"  type="number" min="0" id="mes3_1" placeholder="Marzo"  name="mes3_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Agosto</b></span>
                                            <input style="font-size:12px;"  tabindex="26" type="number" min="0" id="mes8_1" placeholder="Agosto"  name="mes8_1" required  class="form-control suma_mes1" onkeyup="suma_meses1();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <button type="button"  tabindex="31"  onclick="getCopiar1()"> 
                                              Copiar
                                            </button>
                                            <button type="button"   tabindex="32" onclick="getDividir1()"> 
                                              Dividir
                                            </button> 
                                            <button type="button"   tabindex="33" onclick="limpiar_imput2()"> 
                                              Limpiar
                                            </button> 
                                        </div> 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;"><b><span>Necesidad: </span> <span id="sNecesidad1"></span></b></td>
                                    <td style="text-align:center;"><b><span>Total Prorrateo: </span> <span id="sProrroteo1"></span></b></td>
                                    <td colspan="2"  style="text-align:center;"><b><span>Falta para completar su Prorrateo: </span> <span id="sFalta1"></span></b></td>
                                    <td style="text-align:center;"></td>
                                <tr>
                                <tr><td colspan="5" bgcolor="#AC1C51" style="color:#FCFBFB; text-align:center;"><b> PRORRATEO AÑO 3</b></td><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" tabindex="34" type="number" min="0" step="any" id="cpma_2" placeholder="CPMA"   name="cpma_2" required class="form-control">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Abril</b></span>
                                            <input style="font-size:12px;" tabindex="39" type="number" min="0" id="mes4_2" placeholder="Abril"  name="mes4_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Setiembre</b></span>
                                            <input style="font-size:12px;" tabindex="44" type="number" min="0" id="mes9_2" placeholder="Setiembre"  name="mes9_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" tabindex="35" type="number" min="0" id="necesidad_anual_2" placeholder="Necesidad Anual"  name="necesidad_anual_2" required class="form-control necesidad2" onkeyup="sumar_necesidad3();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Mayo</b></span>
                                            <input style="font-size:12px;" tabindex="40" type="number" min="0" id="mes5_2" placeholder="Mayo"  name="mes5_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Octubre</b></span>
                                            <input style="font-size:12px;" tabindex="44" type="number" min="0" id="mes10_2" placeholder="Octubre"  name="mes10_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Enero</b></span>
                                            <input style="font-size:12px;" tabindex="36" type="number" min="0" id="mes1_2" placeholder="Enero"  name="mes1_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Junio</b></span>
                                            <input style="font-size:12px;" tabindex="41" type="number" min="0" id="mes6_2" placeholder="Junio"  name="mes6_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Noviembre</b></span>
                                            <input style="font-size:12px;" tabindex="45" type="number" min="0" id="mes11_2" placeholder="Noviembre"  name="mes11_2"  required class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Febrero</b></span>
                                            <input style="font-size:12px;" tabindex="37" type="number" min="0" id="mes2_2" placeholder="Febrero"  name="mes2_2" required   class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Julio</b></span>
                                            <input style="font-size:12px;" tabindex="42" type="number" min="0" id="mes7_2" placeholder="Julio"  name="mes7_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Diciembre</b></span>
                                            <input style="font-size:12px;" tabindex="46" type="number" min="0" id="mes12_2" placeholder="Diciembre"  name="mes12_2"  required class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Marzo</b></span>
                                            <input style="font-size:12px;" tabindex="38" type="number" min="0" id="mes3_2" placeholder="Marzo"  name="mes3_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Agosto</b></span>
                                            <input style="font-size:12px;" tabindex="43" type="number" min="0" id="mes8_2" placeholder="Agosto"  name="mes8_2" required  class="form-control suma_mes2" onkeyup="suma_meses2();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                        <div class="form-group has-error input-group">
                                            <button type="button"  tabindex="47" onclick="getCopiar2()"> 
                                              Copiar
                                            </button> 
                                            <button type="button" tabindex="48" onclick="getDividir2()"> 
                                              Dividir
                                            </button>
                                            <button type="button" tabindex="49" onclick="limpiar_imput3()"> 
                                              Limpiar
                                            </button> 
                                        </div> 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;"><b><span>Necesidad: </span> <span id="sNecesidad2"></span></b></td>
                                    <td style="text-align:center;"><b><span>Total Prorrateo: </span> <span id="sProrroteo2"></span></b></td>
                                    <td colspan="2"  style="text-align:center;"><b><span>Falta para completar su Prorrateo: </span> <span id="sFalta2"></span></b></td>
                                    <td style="text-align:center;"></td>
                                    <input type="hidden" name="nivel_farma" id="nivel_farma" value="1">
                                <tr>
                            </thead>
                        </table>
                    </div>

                    @else
                    <div class="col-md-12">
                        <table id="estimacion" class="table table-striped">
                            <thead>
                                <tr><th colspan="5" bgcolor="#D4E6F1" style="text-align:center;">PRORRATEO AÑO 1</th><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" type="number" min="0" step="any" id="cpma" placeholder="CPMA"   name="cpma" required class="form-control cpm" onkeyup="calculo();">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" readonly type="number" min="0" id="necesidad_anual" placeholder="Necesidad Anual"  name="necesidad_anual"  class="form-control necesidad">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td colspan="5" bgcolor="#00a65a" style="color:#FCFBFB; text-align:center;"><b>PRORRATEO AÑO 2</b></td><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" type="number" min="0" step="any" id="cpma_1" placeholder="CPMA"   name="cpma_1" class="form-control">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" readonly type="number" min="0" id="necesidad_anual_1" placeholder="Necesidad Anual"  name="necesidad_anual_1"   class="form-control necesidad1">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td colspan="5" bgcolor="#AC1C51" style="color:#FCFBFB; text-align:center;"><b> PRORRATEO AÑO 3</b></td><tr>
                                <tr> 
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> CPMA</b></span>
                                            <input style="font-size:12px;" type="number" min="0" step="any" id="cpma_2" placeholder="CPMA"   name="cpma_2" required class="form-control">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                            <input type="hidden" name="nivel_farma" id="nivel_farma" value="2">
                                        </div> 
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="form-group has-error input-group">
                                            <span class="input-group-addon" style="font-size:12px;"><b> Necesidad</b></span>
                                            <input style="font-size:12px;" readonly type="number" min="0" id="necesidad_anual_2" placeholder="Necesidad Anual"  name="necesidad_anual_2" class="form-control necesidad2">
                                            <span style="font-size:9px;" class="help-block with-errors"></span>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    
                    @endif
                    
                    
                        

                   
                </div>
                <div class="modal-footer">
                    <button id="guardar" class="btn btn-primary btn-save">Guardar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    

</script>
