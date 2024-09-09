@extends('layouts.app')
@section('css')
    <style type="text/css">
        th, td { white-space: nowrap; font-size: 11px;}
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }      
        .form-control{
            font-size: 10px;
        }
         
    </style>
@stop

@section('content')
    <section class="content-header">
        <h3 class="pull-left">Reportes  </h3>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div>
                            @if($id==1)
                                @include('admin.cans.reportes.consolidado_cantidad')                                
                            @endif
                            @if($id==2)
                                @include('admin.cans.reportes.normostock')                                 
                            @endif    
                            @if($id==3)
                                @include('admin.cans.reportes.substock')                                
                            @endif    
                            @if($id==4)
                                @include('admin.cans.reportes.sobrestock')                                
                            @endif        
                            @if($id==5)
                                @include('admin.cans.reportes.sinrotacion')                                                                
                            @endif        
                            @if($id==6)
                                @include('admin.cans.reportes.desabastecimiento')                                                                
                            @endif        
                            @if($id==7)
                                @include('admin.cans.reportes.existencia')                                
                            @endif        
                            @if($id==8)
                                @include('admin.cans.reportes.disponible')                                
                            @endif        
                        </div>
                    </div>
                </div>        
                    
            </div>
        </div>
        <div class="text-center"></div>
    </div>  
@endsection
@section('scripts')
<script>
  
  $(document).ready( function () {
  
  var table = $('#example').DataTable({
        "responsive": true,
        "order": [[ 0, "asc" ]],
        "paging":   false,
        "pageLength": 100,  

        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                '$'+pageTotal +' ( $'+ total +' total)'
            );
        }
    });

    $("#example thead th").each( function ( i ) {
        
        if ($(this).text() !== '') {
            var isStatusColumn = (($(this).text() == 'Status') ? true : false);
            var select = $('<select class="form-control"><option value=""></option></select>')
                .appendTo( $(this).empty() )
                .on( 'change', function () {
                    var val = $(this).val();
                    
                    table.column( i )
                        .search( val ? '^'+$(this).val()+'$' : val, true, false )
                        .draw();
                } );
            
            // Get the Status values a specific way since the status is a anchor/image
            if (isStatusColumn) {
                var statusItems = [];
                
                /* ### IS THERE A BETTER/SIMPLER WAY TO GET A UNIQUE ARRAY OF <TD> data-filter ATTRIBUTES? ### */
                table.column( i ).nodes().to$().each( function(d, j){
                    var thisStatus = $(j).attr("data-filter");
                    if($.inArray(thisStatus, statusItems) === -1) statusItems.push(thisStatus);
                } );
                
                statusItems.sort();
                                
                $.each( statusItems, function(i, item){
                    select.append( '<option value="'+item+'">'+item+'</option>' );
                });

            }
            // All other non-Status columns (like the example)
            else {
                table.column( i ).data().unique().sort().each( function ( d, j ) {  
                    select.append( '<option value="'+d+'">'+d+'</option>' );
                } );    
            }
            
        }
    } );
} );


    


</script>
@stop