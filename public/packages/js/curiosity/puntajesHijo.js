$(document).ready(function(){

  $curiosity.menu.setPaginaId('#menuPuntajes');

  $("#showHelp").click(function(){
    $("#helper").modal('show');
  });

  $.ajax({
    url: '/getsegs',
    type: 'POST'
  })
  .done(function(response) {
    // Creamos las secciones para cada hijo
    $.each(response, function(index, el) {
      makeSections(el['id'], el['hijo']);
    });
    // Agregamos las graficas para el seguimiento de
    // las actividades de cada hijo del padre
    $.each(response, function(index, el) {
      var fechas = new Array();
      var datos = new Array();
      var idhijo = el['id'];
      var existCero = false;
      $.each(el['seguimiento'], function(i, obj) {
        fechas.push(obj['fecha']);
        datos.push(obj['cantidad']);
        if (obj['cantidad'] == 0){
          existCero = true;
        }
      });
      if(!existCero){
        datos.push(0);
      }
      console.log(datos);
      makeChart(fechas, datos, idhijo);
    });
  })
  .fail(function(error) {
    console.log(error);
  });

  function makeSections(id, nombre){
    var html =
    "<div class='row' id='rw"+id+"' style='margin-bottom:20px;'>"+
      "<div class='col-md-6 col-xs-12 col-sm-12 contgrafh' style='padding-bottom:35px;'>"+
        "<h4 class='tit-nameh' id='tit-grafica'>"+
          "<i class='fa fa-line-chart'></i>&nbsp;&nbsp;"+
          ""+nombre+""+
        "</h4>"+
        "<div class='col-md-12 col-sm-12 col-xs-12' id='' style='padding-top:10px;' data-d="+id+">"+
          "<center><canvas class='thiscanvas'></canvas></center>"+
        "</div>"+
      "</div>"+
    "</div>";
    $("#seccionesHijos").append(html);
  }

  function makeChart ($fechas, $cantidades, $id){
    var $contenedor = $("#seccionesHijos > #rw"+$id+" > .contgrafh > div > center");
    var grafica = $contenedor.find('.thiscanvas');
    var myChart = new Chart(grafica, {
      type : 'bar',
      data : {
        labels : $fechas,
        datasets : [{
          label : 'Cantidad de juegos terminados en el día',
          data : $cantidades,
          backgroundColor : ['#259fba', '#259fba', '#259fba', '#259fba', '#259fba', '#259fba', '#259fba'],
          borderColor : ['#096d83', '#096d83', '#096d83', '#096d83', '#096d83', '#096d83', '#096d83'],
          borderWidth : [2, 2, 2, 2, 2, 2, 2]
        }]
      },
      animation : {
        animateScale : true
      },
      options : {
        responsive : true
      }
    });
  }

});
