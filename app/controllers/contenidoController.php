<?php

/**
 *
 */
class contenidoController extends BaseController
{

  function getInicio(){
    if(!Auth::user()->hasRole('padre_free') && !Auth::user()->hasRole('demo_padre') && !Auth::user()->hasRole('padre')){
      if (Auth::user()->hasRole('hijo') ||
      Auth::user()->hasRole('hijo_free') ||
      Auth::user()->hasRole('demo_hijo') &&
      Auth::user()->flag == 1){
        // Si el hijo es la primera vez en iniciar sesion
        // le mostramos una vista en la cual seleccionará su avatar
        $avatars = array(
          "avatars" => DB::table('avatars')->join('avatars_estilos', 'avatars.id', '=', 'avatars_estilos.avatars_id')
           ->join('secuencias', 'secuencias.avatar_estilo_id', '=', 'avatars_estilos.id')
           ->join('tipos_secuencias', 'tipos_secuencias.id', '=', 'secuencias.tipo_secuencia_id')
           ->where('avatars.active', '=', '1')
           ->where('avatars_estilos.active', '=', '1')
           ->where('avatars_estilos.is_default', '=', '1')
           ->where('secuencias.active', '=', '1')
           ->where('tipos_secuencias.nombre', '=', 'esperar')
           ->select('avatars.nombre', 'avatars_estilos.preview', 'avatars_estilos.id as yd')
           ->groupBy('avatars_estilos.id')
           ->get()
        );
        DB::table('users_skins')->insert(array(
          'uso' => 1,
          'skin_id' => Auth::user()->skin_id,
          'user_id' => Auth::user()->id
        ));
        return View::make('vista_selectAvatar', $avatars);
      }
      else{
        $rol = Auth::user()->roles[0]->name;
        $grados = nivel::where('active', '=', '1')->get();
        $actividades = archivo::join('actividades', 'actividades.id', '=', 'archivos.actividad_id')
        ->where('actividades.active', '=', '1')
        ->where('archivos.active', '=', '1')
        ->select('actividades.id')
        ->groupBy('actividades.id')
        ->get();
        $flagRank = array();
        foreach ($actividades as $key => $value) {
          $promedio = round(DB::table('hijo_califica_actividades')
          ->where('actividad_id', '=', $value->id)
          ->avg('calificacion'));
          $actividad = archivo::join('actividades', 'actividades.id', '=', 'archivos.actividad_id')
          ->join('temas', 'temas.id', '=', 'actividades.tema_id')
          ->join('bloques', 'bloques.id', '=', 'temas.bloque_id')
          ->join('inteligencias', 'inteligencias.id', '=', 'bloques.inteligencia_id')
          ->join('niveles', 'niveles.id', '=', 'inteligencias.nivel_id')
          ->where('actividades.id', '=', $value->id)
          ->where('actividades.active', '=', '1')
          ->where('archivos.active', '=', '1')
          ->where('temas.active', '=', '1')
          ->where('bloques.active', '=', '1')
          ->where('inteligencias.active', '=', '1')
          ->where('niveles.active', '=', '1')
          ->where('actividades.estatus', '=', 'unlock')
          ->where('temas.estatus', '=', 'unlock')
          ->where('bloques.estatus', '=', 'unlock')
          ->where('inteligencias.estatus', '=', 'unlock')
          ->where('niveles.estatus', '=', 'unlock')
          ->where('ext', '=', 'php')
          ->select('actividades.*', 'archivos.nombre as nombreFile', 'temas.nombre as nombreTema', 'bloques.nombre as nombreBloque', 'inteligencias.nombre as nombreInteligencia', 'niveles.nombre as nombreNivel', 'temas.isPremium as premium')
          ->get();
          if (count($actividad) > 0){
            array_push($flagRank, array('act' => $actividad, 'promedio' => $promedio));
          }
        }
        // Ordenamos cada actividad segun su promedio
        for ($i=0; $i < count($flagRank); $i++) {
          $isMayor = null;
          $mayor = 0;
          $pos = 0;
          $vuelta = 0;
          for ($e = 0; $e < count($flagRank); $e++) {
            if ($flagRank[$i]['promedio'] < $flagRank[$e]['promedio']){
              $temp = $flagRank[$i];
              $flagRank[$i] = $flagRank[$e];
              $flagRank[$e] = $temp;
            }
          }
        }
        //Agregamos la cantidad de actividades segun el limite establecido
        // segun su mayor promedio
        $ranking = array();
        $limite = 4;
        $index = count($flagRank);
        if ($index > $limite) { $iters = $limite; }
        else { $iters = $index; }
        for ($i = 0; $i < $iters; $i++) {
          array_push($ranking, $flagRank[$index - 1]['act'][0]);
          $index--;
        }
        $nuevos = archivo::join('actividades', 'actividades.id', '=', 'archivos.actividad_id')
        ->join('temas', 'temas.id', '=', 'actividades.tema_id')
        ->join('bloques', 'bloques.id', '=', 'temas.bloque_id')
        ->join('inteligencias', 'inteligencias.id', '=', 'bloques.inteligencia_id')
        ->join('niveles', 'niveles.id', '=', 'inteligencias.nivel_id')
        ->where('actividades.active', '=', '1')
        ->where('archivos.active', '=', '1')
        ->where('temas.active', '=', '1')
        ->where('bloques.active', '=', '1')
        ->where('inteligencias.active', '=', '1')
        ->where('niveles.active', '=', '1')
        ->where('actividades.estatus', '=', 'unlock')
        ->where('temas.estatus', '=', 'unlock')
        ->where('bloques.estatus', '=', 'unlock')
        ->where('inteligencias.estatus', '=', 'unlock')
        ->where('niveles.estatus', '=', 'unlock')
        ->where('ext', '=', 'php')
        ->select('actividades.*', 'archivos.nombre as nombreFile', 'temas.nombre as nombreTema', 'bloques.nombre as nombreBloque', 'inteligencias.nombre as nombreInteligencia', 'niveles.nombre as nombreNivel', 'temas.isPremium as premium', 'actividades.wallpaper')
        ->orderBy('actividades.id', 'desc')
        ->limit(5)
        ->get();
        $populares = archivo::join('actividades', 'actividades.id', '=', 'archivos.actividad_id')
        ->join('temas', 'temas.id', '=', 'actividades.tema_id')
        ->join('bloques', 'bloques.id', '=', 'temas.bloque_id')
        ->join('inteligencias', 'inteligencias.id', '=', 'bloques.inteligencia_id')
        ->join('niveles', 'niveles.id', '=', 'inteligencias.nivel_id')
        ->where('actividades.active', '=', '1')
        ->where('archivos.active', '=', '1')
        ->where('temas.active', '=', '1')
        ->where('bloques.active', '=', '1')
        ->where('inteligencias.active', '=', '1')
        ->where('niveles.active', '=', '1')
        ->where('actividades.estatus', '=', 'unlock')
        ->where('temas.estatus', '=', 'unlock')
        ->where('bloques.estatus', '=', 'unlock')
        ->where('inteligencias.estatus', '=', 'unlock')
        ->where('niveles.estatus', '=', 'unlock')
        ->where('ext', '=', 'php')
        ->select('actividades.*', 'archivos.nombre as nombreFile', 'temas.nombre as nombreTema', 'bloques.nombre as nombreBloque', 'inteligencias.nombre as nombreInteligencia', 'niveles.nombre as nombreNivel', 'temas.isPremium as premium')
        ->orderBy('vistos', 'desc')
        ->limit(4)
        ->get();

        // Juegos recomendables para el alumno dependiendo los resultados mas bajos
        if(Auth::user()->hasRole('hijo') || Auth::user()->hasRole('hijo_free') || Auth::user()->hasRole('demo_hijo')){
            $recomendables = archivo::join('actividades', 'archivos.actividad_id', '=', 'actividades.id')
              ->join('hijo_realiza_actividades', 'hijo_realiza_actividades.actividad_id', '=', 'actividades.id')
              ->join('temas', 'temas.id', '=', 'actividades.tema_id')
              ->join('bloques', 'bloques.id', '=', 'temas.bloque_id')
              ->join('inteligencias', 'inteligencias.id', '=', 'bloques.inteligencia_id')
              ->join('niveles', 'niveles.id', '=', 'inteligencias.nivel_id')
              ->join('hijos','hijos.id','=','hijo_realiza_actividades.hijo_id')
              ->join('personas','hijos.persona_id','=','personas.id')
              ->where('actividades.active', '=', '1')
              ->where('archivos.active', '=', '1')
              ->where('temas.active', '=', '1')
              ->where('bloques.active', '=', '1')
              ->where('inteligencias.active', '=', '1')
              ->where('niveles.active', '=', '1')
              ->where('actividades.estatus', '=', 'unlock')
              ->where('temas.estatus', '=', 'unlock')
              ->where('bloques.estatus', '=', 'unlock')
              ->where('inteligencias.estatus', '=', 'unlock')
              ->where('niveles.estatus', '=', 'unlock')
              ->where('ext', '=', 'php')
              ->where('personas.user_id',Auth::user()->id)
              ->select(DB::raw("actividades.nombre, actividades.estatus, AVG( hijo_realiza_actividades.promedio ) AS  'promedio', actividades.*, archivos.nombre as nombreFile, temas.nombre as nombreTema, bloques.nombre as 'nombreBloque', inteligencias.nombre as 'nombreInteligencia', niveles.nombre as 'nombreNivel', temas.isPremium as 'premium'"))
              ->groupBy('actividades.nombre')
              ->orderBy('promedio')
              ->limit(3)
              ->get();
        }
        else{
            $recomendables = array();
        }

        $videos = video::join('actividades', 'videos.actividad_id', '=', 'actividades.id')
        ->join('temas', 'actividades.tema_id', '=', 'temas.id')
        ->join('bloques', 'temas.bloque_id', '=', 'bloques.id')
        ->join('inteligencias', 'bloques.inteligencia_id', '=', 'inteligencias.id')
        ->join('niveles', 'inteligencias.nivel_id', '=', 'niveles.id')
        ->select('videos.code_embed', 'temas.bg_color as color')
        ->orderBy('videos.id', 'desc')
        ->limit(4)
        ->get();

        // return array(
        //   'rol' => $rol,
        //   'grados' => $grados,
        //   'ranking' => $ranking,
        //   'nuevos' => $nuevos,
        //   'populares' => $populares
        // );
        $now = date("Y-m-d");
        $meta = new metaController();
        $metas = $meta->getAll();
        $miMeta = $meta->getMetaHijo();
        if (!$meta->hasMetaToday()){
          DB::table('avances_metas')->insert(array(
            'avance' => 0,
            'fecha' => $now,
            'avance_id' => $miMeta->metaAsignedId
          ));
        }

        return View::make('vista_home_actividades')->with(array(
          'rol' => $rol,
          'grados' => $grados,
          'ranking' => $ranking,
          'nuevos' => $nuevos,
          'populares' => $populares,
          'recomendables' => $recomendables,
          'videos' => $videos
        ));
      }
    }
    else {
      return View::make('vista_perfil');
    }
  }

  function getInteligencias($idGrade){
    $inteligencias = inteligencia::where("active", '=', "1")->where("nivel_id", "=", $idGrade)->get();
    return View::make("vista_contenido")->with("inteligencias", $inteligencias);
  }

  function getAllVideos(){
    return video::select('code_embed')->get();
  }


}




 ?>
