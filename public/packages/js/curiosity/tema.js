$(document).ready(function() {
  $curiosity.menu.setPaginaId('#menuNivel');

  $(".objetoPointer").click(function(event) {
    if($(this).data('estatus') == "unlock"){
      window.location.href="/actividad"+$(this).data('id');
    }
    else{
      $curiosity.noty("Disponible próximamente", "warning");
    }
  });
});
