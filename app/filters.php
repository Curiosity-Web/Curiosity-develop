<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('/login');
		}
	}
});
/*
|----------------------------------------------
|  Only Session
|------------------------------------------------
| Filtro para validar que solo haya uno conectado
| en ese usuario
|-----------------------------------------------
*/
Route::filter('only_session',function(){
   $session_real=User::where('id','=',Auth::user()->id)->select('id_session')->get();
   if(isset($session_real)){
       if($session_real[0]->id_session != Session::get('sessionId') ){
            Auth::logout();
            return Redirect::guest('/');
       }
   }
});
/*
|----------------------------------------------
|  Unauth
|________________________________________________
|
|  Si el usuario ya inicio session se le redireciona
|  al panel de inicio y ya no accede a login
|-----------------------------------------------
*/
Route::filter('unauth',function(){
   if(!Auth::guest()){
      return Redirect::To('/perfil');
   }
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| Validaciones de Acceso
|--------------------------------------------------------------------------
|
|
|--------------------------------------------------------------------------
*/

Route::filter('gestionar_niveles',function(){
   if(!Entrust::can('gestionar_niveles')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_inteligencias',function(){
   if(!Entrust::can('gestionar_inteligencias')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_bloques',function(){
   if(!Entrust::can('gestionar_bloques')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_temas',function(){
   if(!Entrust::can('gestionar_temas')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_actividades',function(){
   if(!Entrust::can('gestionar_actividades')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_escuelas',function(){
   if(!Entrust::can('gestionar_actividades')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_profesores',function(){
   if(!Entrust::can('gestionar_actividades')){
       return View::make('view-error404');
   }
});

Route::filter('realizar_actividades',function(){
   if(!Entrust::can('realizar_actividades')){
       return View::make('view-error404');
   }
});

Route::filter('gestionar_avatar',function(){
   if(!Entrust::can('gestionar_avatar')){
       return View::make('view-error404');
   }
});

Route::filter('gestion_data_padre',function(){
   if(!Entrust::can('gestion_data_padre')){
       return View::make('view-error404');
   }
});

Route::filter('utilizar_tienda',function(){
   if(!Entrust::can('utilizar_tienda')){
       return View::make('view-error404');
   }
});
