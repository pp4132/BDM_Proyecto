Participantes:
Matrícula: 2011375
Nombre: Israel Nicolás Nicolás
Matrícula: 2018870
Nombre: Ari Kaory Rodriguez Cepeda

Requisitos:
- Se debe de modificar la variable max_allowed_packet del archvio my.ini de tu versión de mysql de preferencia a 128 mb.
- Modiifcar el archivo php.ini
    • upload_max_filesize = 128M
    • post_max_size = 128M
    • memory_limit = 256M
    • max_execution_time = 300
- Descargar mysql 5.7 o superior.
- Usar innoDB al momento de crear las tablas (para los transactions).
- Descargar XAMPP para correr el servidor de preferencia.
- Conexión a internet para unas funciones de bootstrap.

Estructura del proyecto:
Proyecto BDM/
  app/ --carpeta encargada de manejar lo relacionado a las vistas
	view/ --la Vista con todos los datos cargados en caso de no haber error.
	  panel.php --se encarga de ser intermediario entre el view y controller del panel
	  Index.html
	  Soporte.html
	  Registro.php
	  InicioSesion.html
	  PanelUsuario.php
	  sidebar.php --barra lateral izquierda con 
	  Siniestros.php
	  CrearSiniestros.php
	  Búsqueda.php
	  modal/ --ventanas modales para realizar acciones concretas
		Actualizar_reparaciones.php -- pertence a Siniestros.php
		Actualizar_siniestros.php -pertenece a Siniestros.php
		actualizarvehiculo.php --pertenece a PanelUsuario.php
		modificarusuario.php --pertenece a PanelUsuario.php
		registrarvehiculo.php --pertence a PanelUsuario.php

	controllers/ --Controladores que intermedian entre la vista y el modelo
	  PanelController.php -- mostrar datos de uso exclusivo desde el controlador
	  AuthController.php -- autenticación y filtro por roles
	  RegistroController.php -- solo registra asegurados
	  ReparacionController.php -- acceder a los procedure y view de mysql
	  UsuarioController.php -- acceder a los procedure y view de mysql
	  VehiculoController.php -- acceder a los procedure y view de mysql
	  SiniestroController.php --para CrearSiniestros.php
	  DetalleSiniestroController.php --para mostrar datos en Siniestros.php
	  ComentarioController.php --para listar y crear un comentario nuevo
	  MultimediaController.php -- permite acceder a los procedure y view en mysql
	  ActualizarSiniestroController.php -- actualizar siniestros exclusivamente
	  
	assets/ --para decoración
	  public/ --lo de Bootstrap
	  css/ --css propios
	  js/ -- para los .js propios
	  img/ -- imágenes de decoración
  auth/ -- autenticación
	login.php
	logout.php
	registro.php --intermediario entre el controller y la vista
	sesion.php --validación de sesión activa
  config/
	conexión.php --conexión
	config.php --rutas
  model/
 	--Antiguos archivos (en desuso)--
	guardar_siniestro.php
 	procesar_multimedia.php
	comentario_listado.php
	crearReparacion.php
	agregarComentario.php
	agregarVehiculo.php
	actualizarVehiculo.php
	actualizarusuario.php
	actualizarReparacion.php
	actualizar_siniestros.php 
	ESTADOS.php

	--Nuevos models
	Siniestro.php
	Multimedia.php
	Reparación.php
	Usuario.php
	Comentario.php
	Vehiculo.php
	
  .env --aqui debe de ir la conexión a la base de datos


Endpoints:
    /auth/login.php
        -POST. Logea a un usuario y crea la sesión
    
    /auth/logout.php
        -DELETE. Elimina la sesion
    
    /auth/registro.php
        -POST. Registrar un usuario nuevo

    /view/panel.php
        -GET. Obtiene los datos de los usuarios correspondientes

    /controllers/MultimediaController.php
        -POST. Sube o actualiza multimedia a la base de datos
        -GET. Obtiene la multimedia por siniestro o lo descarga
        -DELETE. Elimina la multimedia

    /controllers/ComentarioController.php
        -POST. Crea un nuevo comentario para el siniestro
        -GET. Obtiene los comentarios por siniestro

    /controllers/VehiculoController.php
        -POST. Sube o actualiza el vehiculo a la base de datos
        -GET. Obtiene el vehiculo por siniestro

    /controllers/SiniestroController.php
        -POST. Crea un nuevo siniestro

    /controllers/ActualizarSiniestroController.php
        -POST. Actualiza un siniestro existente    

    /controllers/DetalleSiniestroController.php
        -GET. Obtiene los datos del siniestro

    /controllers/UsuarioController.php
        -POST. Obtiene los datos o los actualiza

    /controllers/ReparacionController.php
        -POST. Obtiene los datos o los actializa
    
    /controllers/BusquedaController.php
        -GET. Obtiene los datos del siniestro y sus reparaciones