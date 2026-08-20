CREATE DATABASE bd_siniestros
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

--TABLAS
CREATE TABLE `roles` (
  `IdRol` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico del rol',
  `Nombre` varchar(50) NOT NULL COMMENT 'Nombre del rol dentro del sistema (Supervisor, Ajustador, Asegurado)',
  PRIMARY KEY (`IdRol`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Cat├ílogo de roles de usuarios';

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico del usuario',
  `IdRol` int(11) NOT NULL COMMENT 'Referencia al rol del usuario',
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre(s) del usuario',
  `Apellidos` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Apellidos del usuario',
  `Alias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre de usuario o alias para login',
  `FechaNacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento del usuario',
  `Foto` longblob COMMENT 'Fotograf├¡a del usuario en formato binario',
  `Genero` enum('M','F','Otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'G├®nero del usuario',
  `Email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Correo electr├│nico del usuario',
  `Contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contrase├▒a del usuario encriptada',
  `Telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'N├║mero telef├│nico del usuario',
  `Activo` tinyint(1) DEFAULT '1' COMMENT 'Indica si el usuario est├í activo en el sistema',
  `FechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creaci├│n del registro',
  PRIMARY KEY (`IdUsuario`),
  UNIQUE KEY `Email` (`Email`),
  KEY `FK_Usuario_Rol` (`IdRol`),
  CONSTRAINT `FK_Usuario_Rol` FOREIGN KEY (`IdRol`) REFERENCES `roles` (`IdRol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla que almacena la informaci├│n de los usuarios del sistema';

CREATE TABLE `siniestros` (
  `IdSiniestro` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico del siniestro',
  `IdVehiculo` int(11) NOT NULL COMMENT 'Referencia al veh├¡culo involucrado',
  `IdAjustador` int(11) DEFAULT NULL COMMENT 'Referencia al usuario ajustador asignado',
  `Folio` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Folio ├║nico del siniestro',
  `Estado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Estado actual del siniestro (pendiente, en proceso, cerrado, etc.)',
  `Descripcion` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'Descripci├│n detallada del siniestro',
  `FechaReporte` datetime DEFAULT NULL COMMENT 'Fecha en que se report├│ el siniestro',
  `FechaAsignacion` datetime DEFAULT NULL COMMENT 'Fecha en que se asign├│ el ajustador',
  `FechaCierre` datetime DEFAULT NULL COMMENT 'Fecha en que se cerr├│ el siniestro',
  `Ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ubicaci├│n donde ocurri├│ el siniestro',
  `MontoEstimado` decimal(10,2) DEFAULT NULL COMMENT 'Monto estimado de los da├▒os',
  `MontoAprobado` decimal(10,2) DEFAULT NULL COMMENT 'Monto aprobado para reparaci├│n',
  `Tipo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdSiniestro`),
  UNIQUE KEY `Folio` (`Folio`),
  KEY `FK_Siniestro_Vehiculo` (`IdVehiculo`),
  KEY `FK_Siniestro_Ajustador` (`IdAjustador`),
  KEY `idx_siniestro_folio` (`Folio`),
  KEY `idx_siniestro_estado` (`Estado`),
  CONSTRAINT `FK_Siniestro_Ajustador` FOREIGN KEY (`IdAjustador`) REFERENCES `usuarios` (`IdUsuario`),
  CONSTRAINT `FK_Siniestro_Vehiculo` FOREIGN KEY (`IdVehiculo`) REFERENCES `vehiculos` (`IdVehiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla que almacena los siniestros reportados';

CREATE TABLE `vehiculos` (
  `IdVehiculo` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico del veh├¡culo',
  `IdAsegurado` int(11) NOT NULL COMMENT 'Referencia al usuario asegurado propietario del veh├¡culo',
  `Tipo` varchar(50) DEFAULT NULL COMMENT 'Tipo de veh├¡culo (auto, moto, cami├│n, etc.)',
  `Marca` varchar(50) DEFAULT NULL COMMENT 'Marca del veh├¡culo',
  `Modelo` varchar(50) DEFAULT NULL COMMENT 'Modelo del veh├¡culo',
  `Anio` int(11) DEFAULT NULL COMMENT 'A├▒o de fabricaci├│n del veh├¡culo',
  `Placas` varchar(20) DEFAULT NULL COMMENT 'N├║mero de placas del veh├¡culo',
  `NumeroPoliza` varchar(50) DEFAULT NULL COMMENT 'N├║mero de p├│liza de seguro',
  `NumeroSerie` varchar(100) DEFAULT NULL COMMENT 'N├║mero de serie (VIN) del veh├¡culo',
  PRIMARY KEY (`IdVehiculo`),
  KEY `FK_Vehiculo_Asegurado` (`IdAsegurado`),
  CONSTRAINT `FK_Vehiculo_Asegurado` FOREIGN KEY (`IdAsegurado`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Tabla que almacena los veh├¡culos asegurados';


CREATE TABLE `reparaciones` (
  `IdReparacion` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico de la reparaci├│n',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Referencia al siniestro asociado',
  `IdUsuarioCreador` int(11) NOT NULL,
  `FechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `DescripcionTrabajo` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'Descripci├│n del trabajo realizado',
  `Taller` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del taller que realiz├│ la reparaci├│n',
  `FechaInicio` datetime DEFAULT NULL COMMENT 'Fecha de inicio de la reparaci├│n',
  `FechaFin` datetime DEFAULT NULL COMMENT 'Fecha de finalizaci├│n de la reparaci├│n',
  `CostoFinal` decimal(10,2) DEFAULT NULL COMMENT 'Costo final de la reparaci├│n',
  `Estado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Estado de la reparaci├│n',
  PRIMARY KEY (`IdReparacion`),
  KEY `FK_Reparacion_Siniestro` (`IdSiniestro`),
  KEY `idx_reparacion_taller` (`Taller`),
  CONSTRAINT `FK_Reparacion_Siniestro` FOREIGN KEY (`IdSiniestro`) REFERENCES `siniestros` (`IdSiniestro`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla que almacena las reparaciones de los siniestros';

CREATE TABLE `multimedia` (
  `IdArchivo` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico del archivo',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Referencia al siniestro asociado',
  `IdSubidoPor` int(11) NOT NULL COMMENT 'Usuario que subi├│ el archivo',
  `Tipo` varchar(50) DEFAULT NULL COMMENT 'Tipo de archivo (imagen, video, documento)',
  `NombreArchivo` varchar(255) DEFAULT NULL COMMENT 'Nombre original del archivo',
  `TipoMime` varchar(100) DEFAULT NULL COMMENT 'Tipo MIME del archivo (image/jpeg, etc.)',
  `Archivo` longblob COMMENT 'Contenido binario del archivo',
  `FechaSubida` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se subi├│ el archivo',
  `IdComentario` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdArchivo`),
  KEY `FK_Multimedia_Siniestro` (`IdSiniestro`),
  KEY `FK_Multimedia_Usuario` (`IdSubidoPor`),
  KEY `FK_Multimedia_Comentario` (`IdComentario`),
  CONSTRAINT `FK_Multimedia_Comentario` FOREIGN KEY (`IdComentario`) REFERENCES `comentarios` (`IdComentario`),
  CONSTRAINT `FK_Multimedia_Siniestro` FOREIGN KEY (`IdSiniestro`) REFERENCES `siniestros` (`IdSiniestro`),
  CONSTRAINT `FK_Multimedia_Usuario` FOREIGN KEY (`IdSubidoPor`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Archivos multimedia asociados a los siniestros';

CREATE TABLE `historialsiniestros` (
  `IdHistorial` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador ├║nico del historial',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Referencia al siniestro',
  `IdConsultadoPor` int(11) NOT NULL COMMENT 'Usuario que realiz├│ la acci├│n o consulta',
  `Accion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que ocurri├│ la acci├│n',
  PRIMARY KEY (`IdHistorial`),
  KEY `FK_Historial_Siniestro` (`IdSiniestro`),
  KEY `FK_Historial_Usuario` (`IdConsultadoPor`),
  CONSTRAINT `FK_Historial_Siniestro` FOREIGN KEY (`IdSiniestro`) REFERENCES `siniestros` (`IdSiniestro`),
  CONSTRAINT `FK_Historial_Usuario` FOREIGN KEY (`IdConsultadoPor`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro hist├│rico de acciones sobre siniestros';

CREATE TABLE `reparaciones_historial` (
  `IdHistorial` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id del historial para las reparaciones',
  `IdReparacion` int(11) DEFAULT NULL COMMENT 'Id de la reparaci+on al cual est├í relacionada',
  `EstadoAnterior` varchar(50) DEFAULT NULL COMMENT 'Estado anterior de la reparaci├│n',
  `EstadoActual` varchar(50) DEFAULT NULL COMMENT 'Estado actual de la reparaci├│n',
  `IdUsuario` int(11) DEFAULT NULL COMMENT 'El usuario que hizo la acci├│n',
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se hizo',
  PRIMARY KEY (`IdHistorial`),
  KEY `IdReparacion` (`IdReparacion`),
  KEY `IdUsuario` (`IdUsuario`),
  CONSTRAINT `reparaciones_historial_ibfk_1` FOREIGN KEY (`IdReparacion`) REFERENCES `reparaciones` (`IdReparacion`),
  CONSTRAINT `reparaciones_historial_ibfk_2` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;


CREATE TABLE `comentarios` (
  `IdComentario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del comentario',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Identificador al siniestro asociado',
  `IdUsuario` int(11) NOT NULL COMMENT 'El usuairo que hizo el comentario',
  `Comentario` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'El texto del comentario',
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se hizo',
  `IdComentarioPadre` int(11) DEFAULT NULL COMMENT 'Si este comentario se encuentra dentro de otro',
  PRIMARY KEY (`IdComentario`),
  KEY `FK_Comentario_Siniestro` (`IdSiniestro`),
  KEY `FK_Comentario_Usuario` (`IdUsuario`),
  KEY `FK_Comentario_Padre` (`IdComentarioPadre`),
  CONSTRAINT `FK_Comentario_Padre` FOREIGN KEY (`IdComentarioPadre`) REFERENCES `comentarios` (`IdComentario`),
  CONSTRAINT `FK_Comentario_Siniestro` FOREIGN KEY (`IdSiniestro`) REFERENCES `siniestros` (`IdSiniestro`),
  CONSTRAINT `FK_Comentario_Usuario` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comentarios tipo chat asociados a un siniestro';

--TRIGGERS
BEGIN
 
     INSERT INTO reparaciones_historial(
         IdReparacion,
         EstadoAnterior,
         EstadoActual,
         IdUsuario
     )
     VALUES(
         NEW.IdReparacion,
         NULL,
         NEW.Estado,
         NEW.IdUsuarioCreador
     );
 
 END

 BEGIN
 
     IF OLD.Estado <> NEW.Estado THEN
 
         INSERT INTO reparaciones_historial (
             IdReparacion,
             EstadoAnterior,
             EstadoActual,
             IdUsuario
         )
         VALUES (
             OLD.IdReparacion,
             OLD.Estado,
             NEW.Estado,
             @idUsuario
         );
 
     END IF;
 
 END

 BEGIN
 
     INSERT INTO historialsiniestros (
         IdSiniestro,
         IdConsultadoPor,
         Accion,
         Descripcion
     )
     VALUES (
         NEW.IdSiniestro,
         NEW.IdAjustador,
         'CREACION',
         CONCAT('Siniestro creado con folio: ', NEW.Folio)
     );
 
 END

 BEGIN
 
     DECLARE accion_texto VARCHAR(255);
 
     /* =========================
        DETECTAR CAMBIOS
     ========================= */
 
     IF OLD.Estado <> NEW.Estado THEN
 
         SET accion_texto = CONCAT(
             'Cambio de estado: ', OLD.Estado, ' -> ', NEW.Estado
         );
 
     ELSEIF OLD.MontoEstimado <> NEW.MontoEstimado THEN
 
         SET accion_texto = CONCAT(
             'Cambio de monto estimado: $',
             IFNULL(OLD.MontoEstimado,0),
             ' -> $',
             IFNULL(NEW.MontoEstimado,0)
         );
 
     ELSEIF OLD.MontoAprobado <> NEW.MontoAprobado THEN
 
         SET accion_texto = CONCAT(
             'Cambio de monto aprobado: $',
             IFNULL(OLD.MontoAprobado,0),
             ' -> $',
             IFNULL(NEW.MontoAprobado,0)
         );
 
     ELSE
 
         SET accion_texto = 'Actualización de datos del siniestro';
 
     END IF;
 
     /* =========================
        INSERT HISTORIAL
     ========================= */
 
     INSERT INTO historialsiniestros (
         IdSiniestro,
         IdConsultadoPor,
         Accion,
         Descripcion
     )
     VALUES (
         NEW.IdSiniestro,
         IFNULL(@idUsuario, NEW.IdAjustador),
         'ACTUALIZACION',
         accion_texto
     );
 
 END

--PROCEDURES
--usuarios
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuarios`(

    IN p_opcion INT,

    IN p_idUsuario INT,
    IN p_idRol INT,

    IN p_nombre VARCHAR(100),
    IN p_apellidos VARCHAR(150),
    IN p_alias VARCHAR(50),
    IN p_fechaNacimiento DATE,
    IN p_foto LONGBLOB,
    IN p_genero VARCHAR(10),
    IN p_email VARCHAR(150),
    IN p_contrasena VARCHAR(255),
    IN p_telefono VARCHAR(20)

)
BEGIN

    CASE p_opcion

        /* =======================================================
           1. INSERTAR
        ======================================================= */
        WHEN 1 THEN

            INSERT INTO Usuarios (
                IdRol,
                Nombre,
                Apellidos,
                Alias,
                FechaNacimiento,
                Foto,
                Genero,
                Email,
                Contrasena,
                Telefono
            )
            VALUES (
                p_idRol,
                p_nombre,
                p_apellidos,
                p_alias,
                p_fechaNacimiento,
                p_foto,
                p_genero,
                p_email,
                p_contrasena,
                p_telefono
            );

            SELECT LAST_INSERT_ID() AS id;


        /* =======================================================
           2. ACTUALIZAR
        ======================================================= */
        WHEN 2 THEN

            UPDATE Usuarios
            SET
                IdRol = p_idRol,
                Nombre = p_nombre,
                Apellidos = p_apellidos,
                Alias = p_alias,
                FechaNacimiento = p_fechaNacimiento,
                Foto = p_foto,
                Genero = p_genero,
                Email = p_email,
                Contrasena = p_contrasena,
                Telefono = p_telefono
            WHERE IdUsuario = p_idUsuario;

            SELECT 'Usuario actualizado' AS mensaje;


        /* =======================================================
           3. ELIMINAR
        ======================================================= */
        WHEN 3 THEN

            DELETE FROM Usuarios
            WHERE IdUsuario = p_idUsuario;

            SELECT 'Usuario eliminado' AS mensaje;


        /* =======================================================
           4. CONSULTAR POR ID
        ======================================================= */
        WHEN 4 THEN

            SELECT 
				IdRol,
                Nombre,
                Apellidos,
                Alias,
                FechaNacimiento,
                Foto,
                Genero,
                Email,
                Contrasena,
                Telefono
            FROM Usuarios
            WHERE IdUsuario = p_idUsuario;


        /* =======================================================
           5. LISTAR TODOS
        ======================================================= */
        WHEN 5 THEN

            SELECT *
            FROM Usuarios;


        /* =======================================================
           6. CONSULTAR POR ROL
        ======================================================= */
        WHEN 6 THEN

            SELECT
                u.IdUsuario,
                u.Nombre,
                u.Apellidos,
                r.Nombre AS Rol,
                u.Email
            FROM Usuarios u
            INNER JOIN Roles r
                ON u.IdRol = r.IdRol
            WHERE u.IdRol = p_idRol;


        /* =======================================================
           OPCION INVALIDA
        ======================================================= */
        ELSE

            SELECT 'Opcion no valida' AS error;

    END CASE;

END

--vehiculos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_vehiculos`(

    IN p_opcion INT,
    IN p_idVehiculo INT,
    IN p_idAsegurado INT,
    IN p_tipo VARCHAR(50),
    IN p_marca VARCHAR(50),
    IN p_modelo VARCHAR(50),
    IN p_anio INT,
    IN p_placas VARCHAR(20),
    IN p_numeroPoliza VARCHAR(50),
    IN p_numeroSerie VARCHAR(100)

)
BEGIN

    CASE p_opcion

        /* =========================================
           1. INSERTAR
        ========================================= */
        WHEN 1 THEN

            INSERT INTO vehiculos(
                IdAsegurado,
                Tipo,
                Marca,
                Modelo,
                Anio,
                Placas,
                NumeroPoliza,
                NumeroSerie
            )
            VALUES(
                p_idAsegurado,
                p_tipo,
                p_marca,
                p_modelo,
                p_anio,
                p_placas,
                p_numeroPoliza,
                p_numeroSerie
            );

            SELECT LAST_INSERT_ID() AS id;


        /* =========================================
           2. ACTUALIZAR
        ========================================= */
        WHEN 2 THEN

            UPDATE vehiculos
            SET
                Tipo = p_tipo,
                Marca = p_marca,
                Modelo = p_modelo,
                Anio = p_anio,
                Placas = p_placas,
                NumeroPoliza = p_numeroPoliza,
                NumeroSerie = p_numeroSerie
            WHERE IdVehiculo = p_idVehiculo;

            SELECT 'Vehiculo actualizado' AS mensaje;


        /* =========================================
           3. ELIMINAR
        ========================================= */
        WHEN 3 THEN

            DELETE FROM vehiculos
            WHERE IdVehiculo = p_idVehiculo;

            SELECT 'Vehiculo eliminado' AS mensaje;


        /* =========================================
           4. CONSULTAR POR ID
        ========================================= */
        WHEN 4 THEN

            SELECT IdVehiculo, 
				IdAsegurado, 
				Tipo, 
				Marca, 
				Modelo, 
				Anio, 
				Placas, 
				NumeroPoliza, 
				NumeroSerie 
            FROM vehiculos
            WHERE IdVehiculo = p_idVehiculo;


        /* =========================================
           5. LISTAR POR ASEGURADO
        ========================================= */
        WHEN 5 THEN

            SELECT 
				IdVehiculo, 
				IdAsegurado, 
				Tipo, 
				Marca, 
				Modelo, 
				Anio, 
				Placas, 
				NumeroPoliza, 
				NumeroSerie 
            FROM vehiculos
            WHERE IdAsegurado = p_idAsegurado;


        /* =========================================
           OPCION INVALIDA
        ========================================= */
        ELSE

            SELECT 'Opcion no valida' AS error;

    END CASE;

END

--comentarios
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_comentarios`(
    IN p_opcion INT,
    IN p_idComentario INT,
    IN p_idSiniestro INT,
    IN p_idUsuario INT,
    IN p_comentario TEXT,
    IN p_idComentarioPadre INT
)
BEGIN

    CASE p_opcion

        -- =========================
        -- 1. INSERTAR COMENTARIO
        -- =========================
        WHEN 1 THEN

            INSERT INTO comentarios(
                IdSiniestro,
                IdUsuario,
                Comentario,
                IdComentarioPadre
            )
            VALUES(
                p_idSiniestro,
                p_idUsuario,
                p_comentario,
                p_idComentarioPadre
            );

            SELECT LAST_INSERT_ID() AS idComentario;

        -- =========================
        -- 2. LISTAR POR SINIESTRO
        -- =========================
        WHEN 2 THEN

            SELECT 
                c.IdComentario,
                c.IdSiniestro,
                c.IdUsuario,
                c.Comentario,
                c.Fecha,
                c.IdComentarioPadre,
                u.Nombre,
                u.Apellidos
            FROM comentarios c
            INNER JOIN usuarios u ON u.IdUsuario = c.IdUsuario
            WHERE c.IdSiniestro = p_idSiniestro
            ORDER BY c.Fecha DESC;

    END CASE;

END


--siniestros
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_siniestros`(

    IN p_opcion INT,
    IN p_idSiniestro INT,
    IN p_idVehiculo INT,
    IN p_idAjustador INT,
    IN p_idUsuario INT,
    IN p_tipo VARCHAR(50),
    IN p_estado VARCHAR(50),
    IN p_descripcion TEXT,
    IN p_fecha DATETIME,
    IN p_ubicacion VARCHAR(255),
    IN p_montoEstimado DECIMAL(10,2),
    IN p_montoAprobado DECIMAL(10,2)

)
BEGIN
	DECLARE v_id INT;
	DECLARE v_folio VARCHAR(50);

    CASE p_opcion

        /* =========================
           1. INSERTAR SINIESTRO
        ========================= */
        WHEN 1 THEN
			INSERT INTO siniestros(
				IdVehiculo,
				IdAjustador,
				Tipo,
				Estado,
				Descripcion,
				FechaReporte,
				Ubicacion,
				MontoEstimado,
				MontoAprobado
			)
			VALUES(
				p_idVehiculo,
				p_idAjustador,
				p_tipo,
				'Pendiente',
				p_descripcion,
				p_fecha,
				p_ubicacion,
				p_montoEstimado,
				p_montoAprobado
			);

			/* =========================
			   OBTENER ID
			========================= */
			SET v_id = LAST_INSERT_ID();

			/* =========================
			   GENERAR FOLIO
			========================= */
			SET v_folio = CONCAT(
				'SIN-',
				YEAR(NOW()),
				'-',
				LPAD(v_id, 6, '0')
			);

			/* =========================
			   ACTUALIZAR FOLIO
			========================= */
			UPDATE siniestros
			SET Folio = v_folio
			WHERE IdSiniestro = v_id;

			/* =========================
			   RETORNAR DATOS
			========================= */

			SELECT
				v_id AS id,
				v_folio AS folio;


        /* =========================
           2. ACTUALIZAR
        ========================= */
			 WHEN 2 THEN
			-- AJUSTADOR
			UPDATE siniestros
			SET
				Tipo = p_tipo,
				Descripcion = p_descripcion,
				Ubicacion = p_ubicacion,
				MontoEstimado = p_montoEstimado
			WHERE IdSiniestro = p_idSiniestro;

			SELECT 'Siniestro actualizado por ajustador' AS mensaje;

        /* =========================
           3. ACTUALIZAR estado
        ========================= */
			WHEN 3 THEN
				-- SUPERVISOR
				UPDATE siniestros
				SET
					Estado = p_estado,
					MontoAprobado = p_montoAprobado
				WHERE IdSiniestro = p_idSiniestro;

				SELECT 'Siniestro aprobado' AS mensaje;


        /* =========================
           4. CONSULTAR POR ID
        ========================= */
        WHEN 4 THEN

            SELECT IdSiniestro, 
				IdVehiculo, IdAjustador, 
				Folio, Estado, 
                Descripcion, FechaReporte, 
                FechaAsignacion, FechaCierre, 
                Ubicacion, MontoEstimado, 
                MontoAprobado, Tipo
            FROM siniestros
            WHERE IdSiniestro = p_idSiniestro;


        /* =========================
           5. LISTAR POR USUARIO
        ========================= */
        WHEN 5 THEN

            SELECT 
				s.IdSiniestro, s.IdVehiculo, 
                s.IdAjustador, s.Folio, 
                s.Estado, s.Descripcion, 
                s.FechaReporte, s.FechaAsignacion, 
                s.FechaCierre, s.Ubicacion, 
                s.MontoEstimado, s.MontoAprobado, 
                s.Tipo
            FROM siniestros s
            INNER JOIN vehiculos v
                ON s.IdVehiculo = v.IdVehiculo
            WHERE v.IdAsegurado = p_idUsuario;  /*Recibe ID del asegurado*/
            
		WHEN 6 THEN
				SELECT 
					s.IdSiniestro,
					s.IdVehiculo,
					s.IdAjustador,
					s.Folio,
					s.Estado,
					s.Descripcion,
					s.FechaReporte,
					s.FechaAsignacion,
					s.FechaCierre,
					s.Ubicacion,
					s.MontoEstimado,
					s.MontoAprobado,
					s.Tipo
				FROM siniestros s
				WHERE s.IdAjustador = p_idUsuario;  -- recibe ID del ajustador
                
			/* =========================
				7. LISTAR TODOS LOS SINIESTROS (SUPERVISOR)
			========================= */
			WHEN 7 THEN
					SELECT 
						s.IdSiniestro,
						s.IdVehiculo,
						s.IdAjustador,
						s.Folio,
						s.Estado,
						s.Descripcion,
						s.FechaReporte,
						s.FechaAsignacion,
						s.FechaCierre,
						s.Ubicacion,
						s.MontoEstimado,
						s.MontoAprobado,
						s.Tipo
					FROM siniestros s;


        ELSE
            SELECT 'Opción no válida' AS error;

    END CASE;

END

--reparaciones
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reparaciones`(
    IN p_opcion INT,
    IN p_idReparacion INT,
    IN p_idSiniestro INT,
    IN P_IdUsuarioCreador INT,
    IN p_descripcion TEXT,
    IN p_taller VARCHAR(150),
    IN p_fechaInicio DATETIME,
    IN p_fechaFin DATETIME,
    IN p_costo DECIMAL(10,2),
    IN p_estado VARCHAR(50)
)
BEGIN

    CASE p_opcion

        /* =========================
           1. INSERTAR REPARACION
        ========================= */
        WHEN 1 THEN

            INSERT INTO reparaciones(
                IdSiniestro,
                IdUsuarioCreador,
                DescripcionTrabajo,
                Taller,
                FechaInicio,
                FechaFin,
                CostoFinal,
                Estado
            )
            VALUES(
                p_idSiniestro,
                p_IdUsuarioCreador,
                p_descripcion,
                p_taller,
                p_fechaInicio,
                p_fechaFin,
                p_costo,
                'Pendiente'
            );

            SELECT LAST_INSERT_ID() AS id;

        /* =========================
           2. ACTUALIZAR (ESTADO) SUPERVISOR
        ========================= */
       WHEN 2 THEN
			IF p_estado = 'En proceso' THEN

				UPDATE reparaciones
				SET
					Estado = p_estado,
					FechaInicio = IFNULL(FechaInicio, NOW())
				WHERE IdReparacion = p_idReparacion;

			ELSEIF p_estado = 'Finalizada' THEN

				UPDATE reparaciones
				SET
					Estado = p_estado,
					FechaFin = IFNULL(FechaFin, NOW())
				WHERE IdReparacion = p_idReparacion;

			ELSE

				UPDATE reparaciones
				SET
					Estado = p_estado
				WHERE IdReparacion = p_idReparacion;

			END IF;

        /* =========================
           3. LISTAR POR SINIESTRO
        ========================= */
        WHEN 3 THEN

            SELECT IdReparacion, IdSiniestro, IdUsuarioCreador, DescripcionTrabajo, Taller, FechaInicio, FechaFin, CostoFinal, Estado
            FROM reparaciones
            WHERE IdSiniestro = p_idSiniestro;

        /* =========================
           4. CONSULTAR UNA
        ========================= */
        WHEN 4 THEN

            SELECT IdReparacion, IdSiniestro, IdUsuarioCreador, DescripcionTrabajo, Taller, FechaInicio, FechaFin, CostoFinal, Estado
            FROM reparaciones
            WHERE IdReparacion = p_idReparacion;

		/* =======================
        5.UPDATE AJUSTADOR
        ======================== */
        WHEN 5 THEN
			UPDATE reparaciones
			SET
				DescripcionTrabajo = p_descripcion,
				Taller = p_taller,
				FechaInicio = p_fechaInicio,
				FechaFin = p_fechaFin,
				CostoFinal = p_costo
			WHERE IdReparacion = p_idReparacion;

			SELECT 'Datos de reparación actualizados' AS mensaje;
			END CASE;

END

--historial reparaciones
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reparaciones_historial`(
    IN p_idReparacion INT
)
BEGIN

    SELECT
        IdHistorial,
        IdReparacion,
        EstadoAnterior,
        EstadoActual,
        IdUsuario,
        Fecha
    FROM reparaciones_historial
    WHERE IdReparacion = p_idReparacion
    ORDER BY Fecha DESC;

END

--historial siniestros

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_historial_siniestros`(
    IN p_idSiniestro INT
)
BEGIN
    SELECT 
        h.IdHistorial,
        h.IdSiniestro,
        h.FechaCambio,
        h.Campo,
        h.ValorAnterior,
        h.ValorNuevo,
        u.Nombre AS Usuario
    FROM historial_siniestros h
    JOIN usuarios u ON h.IdUsuario = u.IdUsuario
    WHERE h.IdSiniestro = p_idSiniestro
    ORDER BY h.FechaCambio DESC;
END

--multimedia
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_multimedia`(

    IN p_opcion INT,
    IN p_idArchivo INT,
    IN p_idSiniestro INT,
    IN p_idSubidoPor INT,
    IN p_tipo VARCHAR(50),
    IN p_nombreArchivo VARCHAR(255),
    IN p_tipoMime VARCHAR(100),
    IN p_archivo LONGBLOB,
    IN p_idComentario INT

)
BEGIN

    CASE p_opcion

        /* =========================
           1. INSERTAR ARCHIVO
        ========================= */
        WHEN 1 THEN

            INSERT INTO multimedia(
                IdSiniestro,
                IdSubidoPor,
                Tipo,
                NombreArchivo,
                TipoMime,
                Archivo,
                IdComentario
            )
            VALUES(
                p_idSiniestro,
                p_idSubidoPor,
                p_tipo,
                p_nombreArchivo,
                p_tipoMime,
                p_archivo,
                p_idComentario
            );

            SELECT LAST_INSERT_ID() AS id;

        /* =========================
           2. Consulta
        ========================= */
        WHEN 2 THEN

             -- Actualizar (opcional)
			UPDATE multimedia
			SET NombreArchivo = p_nombreArchivo, TipoMime = p_tipoMime, Archivo = p_archivo
			WHERE IdArchivo = p_idArchivo;

        /* =========================
           3. Eliminar
        ========================= */
        WHEN 3 THEN

            -- Eliminar
			DELETE FROM multimedia WHERE IdArchivo = p_idArchivo;

        /* =========================
           4. CONSULTAR POR ID
        ========================= */
        WHEN 4 THEN

           -- Listar archivos por siniestro
			SELECT IdArchivo, NombreArchivo, TipoMime, IdSiniestro
			FROM multimedia
			WHERE IdSiniestro = p_idSiniestro;

        ELSE

            SELECT 'Opción no válida' AS error;

    END CASE;

END

--vistas

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_usuario_login` AS select `u`.`IdUsuario` AS `IdUsuario`,`u`.`Nombre` AS `Nombre`,`u`.`Email` AS `Email`,`u`.`Contrasena` AS `Contrasena`,`r`.`Nombre` AS `Rol`,`u`.`Activo` AS `Activo` from (`bd_siniestros`.`usuarios` `u` join `bd_siniestros`.`roles` `r` on((`r`.`IdRol` = `u`.`IdRol`)))
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_siniestros_detalle` AS select `s`.`IdSiniestro` AS `IdSiniestro`,`s`.`IdVehiculo` AS `IdVehiculo`,`s`.`IdAjustador` AS `IdAjustador`,`s`.`Folio` AS `Folio`,`s`.`Estado` AS `Estado`,`s`.`Descripcion` AS `Descripcion`,`s`.`FechaReporte` AS `FechaReporte`,`s`.`FechaAsignacion` AS `FechaAsignacion`,`s`.`FechaCierre` AS `FechaCierre`,`s`.`Ubicacion` AS `Ubicacion`,`s`.`MontoEstimado` AS `MontoEstimado`,`s`.`MontoAprobado` AS `MontoAprobado`,`s`.`Tipo` AS `Tipo`,`v`.`IdAsegurado` AS `IdAsegurado`,`v`.`Marca` AS `Marca`,`v`.`Modelo` AS `Modelo`,`v`.`Placas` AS `Placas`,`v`.`NumeroPoliza` AS `NumeroPoliza`,concat(`u`.`Nombre`,' ',`u`.`Apellidos`) AS `NombreAsegurado` from ((`bd_siniestros`.`siniestros` `s` join `bd_siniestros`.`vehiculos` `v` on((`s`.`IdVehiculo` = `v`.`IdVehiculo`))) join `bd_siniestros`.`usuarios` `u` on((`v`.`IdAsegurado` = `u`.`IdUsuario`)))
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_reparaciones_siniestro` AS select `r`.`IdReparacion` AS `IdReparacion`,`r`.`IdSiniestro` AS `IdSiniestro`,`r`.`DescripcionTrabajo` AS `DescripcionTrabajo`,`r`.`Taller` AS `Taller`,`r`.`FechaInicio` AS `FechaInicio`,`r`.`FechaFin` AS `FechaFin`,`r`.`CostoFinal` AS `CostoFinal`,`r`.`Estado` AS `Estado`,`r`.`FechaCreacion` AS `FechaCreacion` from `bd_siniestros`.`reparaciones` `r`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_multimedia_siniestro` AS select `bd_siniestros`.`multimedia`.`IdArchivo` AS `IdArchivo`,`bd_siniestros`.`multimedia`.`IdSiniestro` AS `IdSiniestro`,`bd_siniestros`.`multimedia`.`IdSubidoPor` AS `IdSubidoPor`,`bd_siniestros`.`multimedia`.`Tipo` AS `Tipo`,`bd_siniestros`.`multimedia`.`NombreArchivo` AS `NombreArchivo`,`bd_siniestros`.`multimedia`.`TipoMime` AS `TipoMime`,`bd_siniestros`.`multimedia`.`FechaSubida` AS `FechaSubida`,`bd_siniestros`.`multimedia`.`IdComentario` AS `IdComentario` from `bd_siniestros`.`multimedia`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_historial_siniestros` AS select `h`.`IdHistorial` AS `IdHistorial`,`h`.`IdSiniestro` AS `IdSiniestro`,`h`.`Accion` AS `Accion`,`h`.`Descripcion` AS `Descripcion`,`h`.`Fecha` AS `Fecha`,`s`.`Folio` AS `Folio`,`s`.`Estado` AS `Estado` from (`bd_siniestros`.`historialsiniestros` `h` join `bd_siniestros`.`siniestros` `s` on((`s`.`IdSiniestro` = `h`.`IdSiniestro`)))
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_historial_reparaciones` AS select `h`.`IdHistorial` AS `IdHistorial`,`h`.`IdReparacion` AS `IdReparacion`,`h`.`EstadoAnterior` AS `EstadoAnterior`,`h`.`EstadoActual` AS `EstadoActual`,`h`.`Fecha` AS `Fecha`,`r`.`Taller` AS `Taller`,`s`.`Folio` AS `Folio` from ((`bd_siniestros`.`reparaciones_historial` `h` join `bd_siniestros`.`reparaciones` `r` on((`r`.`IdReparacion` = `h`.`IdReparacion`))) join `bd_siniestros`.`siniestros` `s` on((`s`.`IdSiniestro` = `r`.`IdSiniestro`)))
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_busqueda_siniestros` AS select `s`.`IdSiniestro` AS `IdSiniestro`,`s`.`Folio` AS `Folio`,`s`.`Estado` AS `Estado`,`s`.`Tipo` AS `Tipo`,`s`.`Ubicacion` AS `Ubicacion`,`s`.`FechaReporte` AS `FechaReporte`,`s`.`Descripcion` AS `Descripcion`,`s`.`IdAjustador` AS `IdAjustador`,`v`.`IdAsegurado` AS `IdAsegurado` from (`bd_siniestros`.`siniestros` `s` join `bd_siniestros`.`vehiculos` `v` on((`s`.`IdVehiculo` = `v`.`IdVehiculo`)))
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_busqueda_reparaciones` AS select `r`.`IdReparacion` AS `IdReparacion`,`r`.`IdSiniestro` AS `IdSiniestro`,`r`.`Taller` AS `Taller`,`r`.`Estado` AS `Estado`,`r`.`DescripcionTrabajo` AS `DescripcionTrabajo`,`r`.`FechaInicio` AS `FechaInicio`,`r`.`FechaFin` AS `FechaFin`,`s`.`Folio` AS `Folio`,`s`.`Estado` AS `EstadoSiniestro` from (`bd_siniestros`.`reparaciones` `r` join `bd_siniestros`.`siniestros` `s` on((`s`.`IdSiniestro` = `r`.`IdSiniestro`)))
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_busqueda_global` AS select 'Siniestro' AS `TipoRegistro`,`vw_busqueda_siniestros`.`IdSiniestro` AS `Id`,`vw_busqueda_siniestros`.`Folio` AS `Ref`,`vw_busqueda_siniestros`.`Estado` AS `Estado`,`vw_busqueda_siniestros`.`Tipo` AS `Tipo`,`vw_busqueda_siniestros`.`Ubicacion` AS `Ubicacion`,`vw_busqueda_siniestros`.`FechaReporte` AS `Fecha` from `bd_siniestros`.`vw_busqueda_siniestros` union all select 'Reparacion' AS `TipoRegistro`,`vw_busqueda_reparaciones`.`IdReparacion` AS `Id`,`vw_busqueda_reparaciones`.`Folio` AS `Ref`,`vw_busqueda_reparaciones`.`Estado` AS `Estado`,`vw_busqueda_reparaciones`.`Taller` AS `Tipo`,`vw_busqueda_reparaciones`.`DescripcionTrabajo` AS `Ubicacion`,`vw_busqueda_reparaciones`.`FechaInicio` AS `Fecha` from `bd_siniestros`.`vw_busqueda_reparaciones` union all select 'HistorialSiniestro' AS `TipoRegistro`,`vw_historial_siniestros`.`IdHistorial` AS `Id`,`vw_historial_siniestros`.`Folio` AS `Ref`,`vw_historial_siniestros`.`Estado` AS `Estado`,`vw_historial_siniestros`.`Accion` AS `Tipo`,`vw_historial_siniestros`.`Descripcion` AS `Ubicacion`,`vw_historial_siniestros`.`Fecha` AS `Fecha` from `bd_siniestros`.`vw_historial_siniestros` union all select 'HistorialReparacion' AS `TipoRegistro`,`vw_historial_reparaciones`.`IdHistorial` AS `Id`,`vw_historial_reparaciones`.`Folio` AS `Ref`,`vw_historial_reparaciones`.`EstadoActual` AS `Estado`,`vw_historial_reparaciones`.`Taller` AS `Tipo`,concat('De: ',`vw_historial_reparaciones`.`EstadoAnterior`,' a: ',`vw_historial_reparaciones`.`EstadoActual`) AS `Ubicacion`,`vw_historial_reparaciones`.`Fecha` AS `Fecha` from `bd_siniestros`.`vw_historial_reparaciones`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_siniestros_acceso` AS select `bd_siniestros`.`siniestros`.`IdSiniestro` AS `IdSiniestro`,`bd_siniestros`.`siniestros`.`IdVehiculo` AS `IdVehiculo`,`bd_siniestros`.`siniestros`.`IdAjustador` AS `IdAjustador`,`bd_siniestros`.`siniestros`.`Folio` AS `Folio`,`bd_siniestros`.`siniestros`.`Estado` AS `Estado`,`bd_siniestros`.`siniestros`.`Tipo` AS `Tipo`,`bd_siniestros`.`siniestros`.`Descripcion` AS `Descripcion`,`bd_siniestros`.`siniestros`.`FechaReporte` AS `FechaReporte`,`bd_siniestros`.`siniestros`.`FechaAsignacion` AS `FechaAsignacion`,`bd_siniestros`.`siniestros`.`FechaCierre` AS `FechaCierre`,`bd_siniestros`.`siniestros`.`Ubicacion` AS `Ubicacion`,`bd_siniestros`.`siniestros`.`MontoEstimado` AS `MontoEstimado`,`bd_siniestros`.`siniestros`.`MontoAprobado` AS `MontoAprobado` from `bd_siniestros`.`siniestros`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_usuarios_sesion` AS select `u`.`IdUsuario` AS `IdUsuario`,`u`.`Nombre` AS `Nombre`,`u`.`Apellidos` AS `Apellidos`,`u`.`Foto` AS `Foto`,`u`.`Email` AS `Email`,`u`.`Telefono` AS `Telefono`,`r`.`Nombre` AS `Rol` from (`bd_siniestros`.`usuarios` `u` join `bd_siniestros`.`roles` `r` on((`u`.`IdRol` = `r`.`IdRol`))) where (`u`.`Activo` = 1)
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_vehiculos_panel` AS select `v`.`IdVehiculo` AS `IdVehiculo`,`v`.`IdAsegurado` AS `IdAsegurado`,`v`.`Tipo` AS `Tipo`,`v`.`Marca` AS `Marca`,`v`.`Modelo` AS `Modelo`,`v`.`Anio` AS `Anio`,`v`.`Placas` AS `Placas`,`v`.`NumeroPoliza` AS `NumeroPoliza` from `bd_siniestros`.`vehiculos` `v`
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bd_siniestros`.`vw_vehiculos_usuario` AS select `v`.`IdVehiculo` AS `IdVehiculo`,`v`.`IdAsegurado` AS `IdAsegurado`,`v`.`Tipo` AS `Tipo`,`v`.`Marca` AS `Marca`,`v`.`Modelo` AS `Modelo`,`v`.`Anio` AS `Anio`,`v`.`Placas` AS `Placas`,`v`.`NumeroPoliza` AS `NumeroPoliza`,`v`.`NumeroSerie` AS `NumeroSerie` from `bd_siniestros`.`vehiculos` `v`


--Funciones
DELIMITER $$

CREATE FUNCTION fn_total_comentarios_siniestro(
    pIdSiniestro INT
)
RETURNS INT
READS SQL DATA
BEGIN

    DECLARE vTotal INT;

    SELECT COUNT(*)
    INTO vTotal
    FROM comentarios
    WHERE IdSiniestro = pIdSiniestro;

    RETURN COALESCE(vTotal,0);

END$$

DELIMITER ;

DELIMITER $$

CREATE FUNCTION fn_mensaje_bienvenida(
    pIdUsuario INT
)
RETURNS VARCHAR(255)
READS SQL DATA
BEGIN

    DECLARE vNombre VARCHAR(100);
    DECLARE vFechaNacimiento DATE;

    SELECT Nombre, FechaNacimiento
    INTO vNombre, vFechaNacimiento
    FROM usuarios
    WHERE IdUsuario = pIdUsuario;

    IF DATE_FORMAT(vFechaNacimiento,'%m-%d')
       = DATE_FORMAT(CURDATE(),'%m-%d') THEN

        RETURN CONCAT('¡Feliz cumpleaños ', vNombre, '!');

    END IF;

    RETURN '';

END$$

DELIMITER ;