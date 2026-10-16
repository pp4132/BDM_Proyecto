CREATE DATABASE bd_siniestros
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

-- TABLAS
CREATE TABLE `roles` (
  `IdRol` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del rol',
  `Nombre` varchar(50) NOT NULL COMMENT 'Nombre del rol dentro del sistema (Supervisor, Ajustador, Asegurado)',
  PRIMARY KEY (`IdRol`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Cataílogo de roles de usuarios';

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del usuario',
  `IdRol` int(11) NOT NULL COMMENT 'Referencia al rol del usuario',
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre(s) del usuario',
  `Apellidos` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Apellidos del usuario',
  `Alias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre de usuario o alias para login',
  `FechaNacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento del usuario',
  `Foto` longblob COMMENT 'Fotograf├¡a del usuario en formato binario',
  `Genero` enum('M','F','Otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'G├®nero del usuario',
  `Email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Correo electrónico del usuario',
  `Contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contrase├▒a del usuario encriptada',
  `Telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número telefónico del usuario',
  `Activo` tinyint(1) DEFAULT '1' COMMENT 'Indica si el usuario está activo en el sistema',
  `FechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
  PRIMARY KEY (`IdUsuario`),
  UNIQUE KEY `Email` (`Email`),
  KEY `FK_Usuario_Rol` (`IdRol`),
  CONSTRAINT `FK_Usuario_Rol` FOREIGN KEY (`IdRol`) REFERENCES `roles` (`IdRol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla que almacena la información de los usuarios del sistema';

CREATE TABLE `siniestros` (
  `IdSiniestro` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del siniestro',
  `IdVehiculo` int(11) NOT NULL COMMENT 'Referencia al veh├¡culo involucrado',
  `IdAjustador` int(11) DEFAULT NULL COMMENT 'Referencia al usuario ajustador asignado',
  `Folio` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Folio único del siniestro',
  `Estado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Estado actual del siniestro (pendiente, en proceso, cerrado, etc.)',
  `Descripcion` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'Descripción detallada del siniestro',
  `FechaReporte` datetime DEFAULT NULL COMMENT 'Fecha en que se reportó el siniestro',
  `FechaAsignacion` datetime DEFAULT NULL COMMENT 'Fecha en que se asignó el ajustador',
  `FechaCierre` datetime DEFAULT NULL COMMENT 'Fecha en que se cerró el siniestro',
  `Ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ubicaci├│n donde ocurri├│ el siniestro',
  `MontoEstimado` decimal(10,2) DEFAULT NULL COMMENT 'Monto estimado de los datos',
  `MontoAprobado` decimal(10,2) DEFAULT NULL COMMENT 'Monto aprobado para reparación',
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
  `IdVehiculo` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del veh├¡culo',
  `IdAsegurado` int(11) NOT NULL COMMENT 'Referencia al usuario asegurado propietario del vehículo',
  `Tipo` varchar(50) DEFAULT NULL COMMENT 'Tipo de vehículo (auto, moto, cami├│n, etc.)',
  `Marca` varchar(50) DEFAULT NULL COMMENT 'Marca del vehículo',
  `Modelo` varchar(50) DEFAULT NULL COMMENT 'Modelo del vehículo',
  `Anio` int(11) DEFAULT NULL COMMENT 'Año de fabricación del vehículo',
  `Placas` varchar(20) DEFAULT NULL COMMENT 'Número de placas del veh├¡culo',
  `NumeroPoliza` varchar(50) DEFAULT NULL COMMENT 'Número de póliza de seguro',
  `NumeroSerie` varchar(100) DEFAULT NULL COMMENT 'Número de serie (VIN) del veh├¡culo',
  PRIMARY KEY (`IdVehiculo`),
  KEY `FK_Vehiculo_Asegurado` (`IdAsegurado`),
  CONSTRAINT `FK_Vehiculo_Asegurado` FOREIGN KEY (`IdAsegurado`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Tabla que almacena los veh├¡culos asegurados';


CREATE TABLE `reparaciones` (
  `IdReparacion` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la reparaci├│n',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Referencia al siniestro asociado',
  `IdUsuarioCreador` int(11) NOT NULL,
  `FechaCreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `DescripcionTrabajo` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'Descripción del trabajo realizado',
  `Taller` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del taller que realizó la reparación',
  `FechaInicio` datetime DEFAULT NULL COMMENT 'Fecha de inicio de la reparaci├│n',
  `FechaFin` datetime DEFAULT NULL COMMENT 'Fecha de finalización de la reparación',
  `CostoFinal` decimal(10,2) DEFAULT NULL COMMENT 'Costo final de la reparaci├│n',
  `Estado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Estado de la reparación',
  PRIMARY KEY (`IdReparacion`),
  KEY `FK_Reparacion_Siniestro` (`IdSiniestro`),
  KEY `idx_reparacion_taller` (`Taller`),
  CONSTRAINT `FK_Reparacion_Siniestro` FOREIGN KEY (`IdSiniestro`) REFERENCES `siniestros` (`IdSiniestro`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla que almacena las reparaciones de los siniestros';

CREATE TABLE `multimedia` (
  `IdArchivo` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del archivo',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Referencia al siniestro asociado',
  `IdSubidoPor` int(11) NOT NULL COMMENT 'Usuario que subió el archivo',
  `Tipo` varchar(50) DEFAULT NULL COMMENT 'Tipo de archivo (imagen, video, documento)',
  `NombreArchivo` varchar(255) DEFAULT NULL COMMENT 'Nombre original del archivo',
  `TipoMime` varchar(100) DEFAULT NULL COMMENT 'Tipo MIME del archivo (image/jpeg, etc.)',
  `Archivo` longblob COMMENT 'Contenido binario del archivo',
  `FechaSubida` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se subió el archivo',
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
  `IdHistorial` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del historial',
  `IdSiniestro` int(11) NOT NULL COMMENT 'Referencia al siniestro',
  `IdConsultadoPor` int(11) NOT NULL COMMENT 'Usuario que realizó la acción o consulta',
  `Accion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que ocurrió la acción',
  PRIMARY KEY (`IdHistorial`),
  KEY `FK_Historial_Siniestro` (`IdSiniestro`),
  KEY `FK_Historial_Usuario` (`IdConsultadoPor`),
  CONSTRAINT `FK_Historial_Siniestro` FOREIGN KEY (`IdSiniestro`) REFERENCES `siniestros` (`IdSiniestro`),
  CONSTRAINT `FK_Historial_Usuario` FOREIGN KEY (`IdConsultadoPor`) REFERENCES `usuarios` (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro histórico de acciones sobre siniestros';

CREATE TABLE `reparaciones_historial` (
  `IdHistorial` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id del historial para las reparaciones',
  `IdReparacion` int(11) DEFAULT NULL COMMENT 'Id de la reparaci+on al cual est├í relacionada',
  `EstadoAnterior` varchar(50) DEFAULT NULL COMMENT 'Estado anterior de la reparación',
  `EstadoActual` varchar(50) DEFAULT NULL COMMENT 'Estado actual de la reparación',
  `IdUsuario` int(11) DEFAULT NULL COMMENT 'El usuario que hizo la acción',
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
DELIMITER $$
-- =========================================================
-- 1. TRIGGER: INSERT en reparaciones
-- =========================================================
DROP TRIGGER IF EXISTS trg_rep_insert$$
CREATE TRIGGER trg_rep_insert
AFTER INSERT ON reparaciones
FOR EACH ROW
BEGIN

    INSERT INTO reparaciones_historial (
        IdReparacion,
        EstadoAnterior,
        EstadoActual,
        IdUsuario
    )
    VALUES (
        NEW.IdReparacion,
        NULL,
        NEW.Estado,
        NEW.IdUsuarioCreador
    );

END$$

-- =========================================================
-- 2. TRIGGER: UPDATE en reparaciones
-- =========================================================
DROP TRIGGER IF EXISTS trg_reparaciones_update$$
CREATE TRIGGER trg_reparaciones_update
BEFORE UPDATE ON reparaciones
FOR EACH ROW
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

END$$

-- =========================================================
-- 3. TRIGGER: INSERT en siniestros
-- =========================================================
DROP TRIGGER IF EXISTS trg_siniestros_insert$$
CREATE TRIGGER trg_siniestros_insert
AFTER INSERT ON siniestros
FOR EACH ROW
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
        CONCAT(
            'Siniestro creado con folio: ',
            NEW.IdSiniestro
        )
    );

END$$


-- =========================================================
-- 4. TRIGGER: UPDATE en siniestros
-- =========================================================
DROP TRIGGER IF EXISTS trg_siniestros_update$$
CREATE TRIGGER trg_siniestros_update
AFTER UPDATE ON siniestros
FOR EACH ROW
BEGIN
    DECLARE accion_texto VARCHAR(255);
    IF OLD.Estado <> NEW.Estado THEN
        SET accion_texto = CONCAT('Cambio de estado: ', OLD.Estado, ' -> ', NEW.Estado);
    ELSEIF OLD.MontoEstimado <> NEW.MontoEstimado THEN
        SET accion_texto = CONCAT('Cambio de monto estimado: $', IFNULL(OLD.MontoEstimado, 0), ' -> $', IFNULL(NEW.MontoEstimado, 0));
    ELSEIF OLD.MontoAprobado <> NEW.MontoAprobado THEN
        SET accion_texto = CONCAT('Cambio de monto aprobado: $', IFNULL(OLD.MontoAprobado, 0), ' -> $', IFNULL(NEW.MontoAprobado, 0));
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
END$$
DELIMITER ;


-- PROCEDURES
-- PROCEDURE: sp_usuarios¿
DROP PROCEDURE IF EXISTS sp_usuarios;
DELIMITER //
CREATE PROCEDURE sp_usuarios(
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
    -- Manejo de errores de la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    CASE p_opcion

        /* =======================================================
           1. INSERTAR
        ======================================================= */
        WHEN 1 THEN
            START TRANSACTION;

            INSERT INTO Usuarios (
                IdRol, Nombre,
                Apellidos, Alias,
                FechaNacimiento,
                Foto, Genero,
                Email, Contrasena, Telefono
            )
            VALUES (
                p_idRol, p_nombre,
                p_apellidos, p_alias,
                p_fechaNacimiento,
                p_foto, p_genero,
                p_email, p_contrasena, p_telefono
            );

            COMMIT;

            SELECT LAST_INSERT_ID() AS id;

        /* =======================================================
           2. ACTUALIZAR
        ======================================================= */
        WHEN 2 THEN
            START TRANSACTION;

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

            COMMIT;

            SELECT 'Usuario actualizado' AS mensaje;

        /* =======================================================
           3. ELIMINAR
        ======================================================= */
        WHEN 3 THEN
            START TRANSACTION;

            DELETE FROM Usuarios
            WHERE IdUsuario = p_idUsuario;

            COMMIT;

            SELECT 'Usuario eliminado' AS mensaje;

        /* =======================================================
           4. CONSULTAR POR ID
        ======================================================= */
        WHEN 4 THEN
            SELECT
                IdRol, Nombre,
                Apellidos, Alias,
                FechaNacimiento,
                Foto, Genero,
                Email, Contrasena, Telefono
            FROM Usuarios
            WHERE IdUsuario = p_idUsuario;

        /* =======================================================
           5. LISTAR TODOS
        ======================================================= */
        WHEN 5 THEN
            SELECT
                IdRol, Nombre,
                Apellidos, Alias,
                FechaNacimiento,
                Foto, Genero,
                Email, Telefono
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

END //
DELIMITER ;

-- PROCEDURE: sp_vehiculos
DROP PROCEDURE IF EXISTS sp_vehiculos;
DELIMITER //
CREATE PROCEDURE sp_vehiculos(
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
    -- Manejo de errores de la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    CASE p_opcion

        /* =========================================
           1. INSERTAR
        ========================================= */
        WHEN 1 THEN
            START TRANSACTION;

            INSERT INTO vehiculos (
                IdAsegurado, Tipo,
                Marca, Modelo,
                Anio, Placas,
                NumeroPoliza, NumeroSerie
            )
            VALUES (
                p_idAsegurado, p_tipo,
                p_marca, p_modelo,
                p_anio, p_placas,
                p_numeroPoliza, p_numeroSerie
            );

            COMMIT;

            SELECT LAST_INSERT_ID() AS id;

        /* =========================================
           2. ACTUALIZAR
        ========================================= */
        WHEN 2 THEN
            START TRANSACTION;

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

            COMMIT;

            SELECT 'Vehiculo actualizado' AS mensaje;

        /* =========================================
           3. ELIMINAR
        ========================================= */
        WHEN 3 THEN
            START TRANSACTION;

            DELETE FROM vehiculos
            WHERE IdVehiculo = p_idVehiculo;

            COMMIT;

            SELECT 'Vehiculo eliminado' AS mensaje;

        /* =========================================
           4. CONSULTAR POR ID
        ========================================= */
        WHEN 4 THEN
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

END //
DELIMITER ;

-- PROCEDURE: sp_comentarios
DROP PROCEDURE IF EXISTS sp_comentarios;
DELIMITER //
CREATE PROCEDURE sp_comentarios(
    IN p_opcion INT,
    IN p_idComentario INT,
    IN p_idSiniestro INT,
    IN p_idUsuario INT,
    IN p_comentario TEXT,
    IN p_idComentarioPadre INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    CASE p_opcion

        -- =========================
        -- 1. INSERTAR COMENTARIO
        -- =========================
        WHEN 1 THEN
            START TRANSACTION;

            INSERT INTO comentarios (
                IdSiniestro,
                IdUsuario,
                Comentario,
                IdComentarioPadre
            )
            VALUES (
                p_idSiniestro,
                p_idUsuario,
                p_comentario,
                p_idComentarioPadre
            );

            COMMIT;

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
            INNER JOIN usuarios u
                ON u.IdUsuario = c.IdUsuario
            WHERE c.IdSiniestro = p_idSiniestro
            ORDER BY c.Fecha DESC;

        ELSE
            SELECT 'Opcion no valida' AS error;

    END CASE;

END //
DELIMITER ;

-- PROCEDURE: sp_siniestros
DROP PROCEDURE IF EXISTS sp_siniestros;
DELIMITER //
CREATE PROCEDURE sp_siniestros(
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

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    CASE p_opcion

        /* =========================
           1. INSERTAR SINIESTRO
        ========================= */
        WHEN 1 THEN
            START TRANSACTION;

            INSERT INTO siniestros (
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
            VALUES (
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
               CONFIRMAR TRANSACCIÓN
            ========================= */
            COMMIT;

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
            START TRANSACTION;

            -- AJUSTADOR
            UPDATE siniestros
            SET
                Tipo = p_tipo,
                Descripcion = p_descripcion,
                Ubicacion = p_ubicacion,
                MontoEstimado = p_montoEstimado
            WHERE IdSiniestro = p_idSiniestro;

            COMMIT;

            SELECT 'Siniestro actualizado por ajustador' AS mensaje;

        /* =========================
           3. ACTUALIZAR ESTADO
        ========================= */
        WHEN 3 THEN
            START TRANSACTION;

            -- SUPERVISOR
            UPDATE siniestros
            SET
                Estado = p_estado,
                MontoAprobado = p_montoAprobado
            WHERE IdSiniestro = p_idSiniestro;

            COMMIT;

            SELECT 'Siniestro aprobado' AS mensaje;

        /* =========================
           4. CONSULTAR POR ID
        ========================= */
        WHEN 4 THEN
            SELECT
                IdSiniestro,
                IdVehiculo,
                IdAjustador,
                Folio,
                Estado,
                Descripcion,
                FechaReporte,
                FechaAsignacion,
                FechaCierre,
                Ubicacion,
                MontoEstimado,
                MontoAprobado,
                Tipo
            FROM siniestros
            WHERE IdSiniestro = p_idSiniestro;

        /* =========================
           5. LISTAR POR USUARIO
        ========================= */
        WHEN 5 THEN
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
            INNER JOIN vehiculos v
                ON s.IdVehiculo = v.IdVehiculo
            WHERE v.IdAsegurado = p_idUsuario;

        /* =========================
           6. LISTAR POR AJUSTADOR
        ========================= */
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
            WHERE s.IdAjustador = p_idUsuario;

        /* =========================
           7. LISTAR TODOS LOS SINIESTROS
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

END //
DELIMITER ;

-- PROCEDURE: sp_reparaciones
DROP PROCEDURE IF EXISTS sp_reparaciones;
DELIMITER //
CREATE PROCEDURE sp_reparaciones(
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
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    CASE p_opcion
        /* =========================
           1. INSERTAR REPARACIÓN
        ========================= */
        WHEN 1 THEN
            START TRANSACTION;

            INSERT INTO reparaciones (
                IdSiniestro,
                IdUsuarioCreador,
                DescripcionTrabajo,
                Taller,
                FechaInicio,
                FechaFin,
                CostoFinal,
                Estado
            )
            VALUES (
                p_idSiniestro,
                P_IdUsuarioCreador,
                p_descripcion,
                p_taller,
                p_fechaInicio,
                p_fechaFin,
                p_costo,
                'Pendiente'
            );

            COMMIT;

            SELECT LAST_INSERT_ID() AS id;

        /* =========================
           2. ACTUALIZAR ESTADO
           SUPERVISOR
        ========================= */
        WHEN 2 THEN
            START TRANSACTION;

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
                SET Estado = p_estado
                WHERE IdReparacion = p_idReparacion;

            END IF;

            COMMIT;

        /* =========================
           3. LISTAR POR SINIESTRO
        ========================= */
        WHEN 3 THEN
            SELECT
                IdReparacion,
                IdSiniestro,
                IdUsuarioCreador,
                DescripcionTrabajo,
                Taller,
                FechaInicio,
                FechaFin,
                CostoFinal,
                Estado
            FROM reparaciones
            WHERE IdSiniestro = p_idSiniestro;

        /* =========================
           4. CONSULTAR UNA
        ========================= */
        WHEN 4 THEN
            SELECT
                IdReparacion,
                IdSiniestro,
                IdUsuarioCreador,
                DescripcionTrabajo,
                Taller,
                FechaInicio,
                FechaFin,
                CostoFinal,
                Estado
            FROM reparaciones
            WHERE IdReparacion = p_idReparacion;

        /* =========================
           5. UPDATE AJUSTADOR
        ========================= */
        WHEN 5 THEN
            START TRANSACTION;

            UPDATE reparaciones
            SET
                DescripcionTrabajo = p_descripcion,
                Taller = p_taller,
                FechaInicio = p_fechaInicio,
                FechaFin = p_fechaFin,
                CostoFinal = p_costo
            WHERE IdReparacion = p_idReparacion;

            COMMIT;

            SELECT 'Datos de reparación actualizados' AS mensaje;

        ELSE
            SELECT 'Opción no válida' AS error;

    END CASE;

END //
DELIMITER ;

-- PROCEDURE: sp_multimedia
DROP PROCEDURE IF EXISTS sp_multimedia;
DELIMITER //
CREATE PROCEDURE sp_multimedia(
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
    -- Manejo de errores de la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    CASE p_opcion

        /* =========================
           1. INSERTAR ARCHIVO
        ========================= */
        WHEN 1 THEN
            START TRANSACTION;

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

            COMMIT;

            SELECT LAST_INSERT_ID() AS id;

        /* =========================
           2. ACTUALIZAR
        ========================= */
        WHEN 2 THEN
            START TRANSACTION;

            UPDATE multimedia
            SET
                NombreArchivo = p_nombreArchivo,
                TipoMime = p_tipoMime,
                Archivo = p_archivo
            WHERE IdArchivo = p_idArchivo;

            COMMIT;

        /* =========================
           3. ELIMINAR
        ========================= */
        WHEN 3 THEN
            START TRANSACTION;

            DELETE FROM multimedia
            WHERE IdArchivo = p_idArchivo;

            COMMIT;

        /* =========================
           4. CONSULTAR POR ID
        ========================= */
        WHEN 4 THEN
            SELECT
                IdArchivo,
                NombreArchivo,
                TipoMime,
                IdSiniestro
            FROM multimedia
            WHERE IdSiniestro = p_idSiniestro;

        ELSE
            SELECT 'Opción no válida' AS error;

    END CASE;

END //
DELIMITER ;

-- PROCEDURE: sp_reparaciones_historial
DROP PROCEDURE IF EXISTS sp_reparaciones_historial;
DELIMITER //
CREATE PROCEDURE sp_reparaciones_historial(
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
END //
DELIMITER ;

-- PROCEDURE: sp_historial_siniestros
DROP PROCEDURE IF EXISTS sp_historial_siniestros;
DELIMITER //
CREATE PROCEDURE sp_historial_siniestros(
    IN p_idSiniestro INT
)
BEGIN
    SELECT
        h.IdHistorial,
        h.IdSiniestro,
        h.Accion,
        h.Descripcion,
        h.Fecha,
        u.Nombre AS Usuario
    FROM historialsiniestros h
    LEFT JOIN usuarios u
        ON h.IdConsultadoPor = u.IdUsuario
    WHERE h.IdSiniestro = p_idSiniestro
    ORDER BY h.Fecha DESC;
END //
DELIMITER ;


-- vistas
-- =========================================================
-- 1. Vista: vw_usuario_login
-- =========================================================
DROP VIEW IF EXISTS `vw_usuario_login`;
CREATE VIEW `vw_usuario_login` AS
SELECT
    `u`.`IdUsuario` AS `IdUsuario`,
    `u`.`Nombre` AS `Nombre`,
    `u`.`Email` AS `Email`,
    `u`.`Contrasena` AS `Contrasena`,
    `r`.`Nombre` AS `Rol`,
    `u`.`Activo` AS `Activo`
FROM `usuarios` `u`
INNER JOIN `roles` `r`
    ON `r`.`IdRol` = `u`.`IdRol`;


-- =========================================================
-- 2. Vista: vw_siniestros_detalle
-- =========================================================
DROP VIEW IF EXISTS `vw_siniestros_detalle`;
CREATE VIEW `vw_siniestros_detalle` AS
SELECT
    `s`.`IdSiniestro` AS `IdSiniestro`,
    `s`.`IdVehiculo` AS `IdVehiculo`,
    `s`.`IdAjustador` AS `IdAjustador`,
    `s`.`Folio` AS `Folio`,
    `s`.`Estado` AS `Estado`,
    `s`.`Descripcion` AS `Descripcion`,
    `s`.`FechaReporte` AS `FechaReporte`,
    `s`.`FechaAsignacion` AS `FechaAsignacion`,
    `s`.`FechaCierre` AS `FechaCierre`,
    `s`.`Ubicacion` AS `Ubicacion`,
    `s`.`MontoEstimado` AS `MontoEstimado`,
    `s`.`MontoAprobado` AS `MontoAprobado`,
    `s`.`Tipo` AS `Tipo`,
    `v`.`IdAsegurado` AS `IdAsegurado`,
    `v`.`Marca` AS `Marca`,
    `v`.`Modelo` AS `Modelo`,
    `v`.`Placas` AS `Placas`,
    `v`.`NumeroPoliza` AS `NumeroPoliza`,
    CONCAT(`u`.`Nombre`, ' ', `u`.`Apellidos`) AS `NombreAsegurado`
FROM `siniestros` `s`
INNER JOIN `vehiculos` `v`
    ON `s`.`IdVehiculo` = `v`.`IdVehiculo`
INNER JOIN `usuarios` `u`
    ON `v`.`IdAsegurado` = `u`.`IdUsuario`;


-- =========================================================
-- 3. Vista: vw_reparaciones_siniestro
-- =========================================================
CREATE VIEW `vw_reparaciones_siniestro` AS
SELECT
    `r`.`IdReparacion` AS `IdReparacion`,
    `r`.`IdSiniestro` AS `IdSiniestro`,
    `r`.`DescripcionTrabajo` AS `DescripcionTrabajo`,
    `r`.`Taller` AS `Taller`,
    `r`.`FechaInicio` AS `FechaInicio`,
    `r`.`FechaFin` AS `FechaFin`,
    `r`.`CostoFinal` AS `CostoFinal`,
    `r`.`Estado` AS `Estado`,
    `r`.`FechaCreacion` AS `FechaCreacion`
FROM `reparaciones` `r`;


-- =========================================================
-- 4. Vista: vw_multimedia_siniestro
-- =========================================================
CREATE VIEW `vw_multimedia_siniestro` AS
SELECT
    `m`.`IdArchivo` AS `IdArchivo`,
    `m`.`IdSiniestro` AS `IdSiniestro`,
    `m`.`IdSubidoPor` AS `IdSubidoPor`,
    `m`.`Tipo` AS `Tipo`,
    `m`.`NombreArchivo` AS `NombreArchivo`,
    `m`.`TipoMime` AS `TipoMime`,
    `m`.`FechaSubida` AS `FechaSubida`,
    `m`.`IdComentario` AS `IdComentario`
FROM `multimedia` `m`;


-- =========================================================
-- 5. Vista: vw_historial_siniestros
-- =========================================================
CREATE VIEW `vw_historial_siniestros` AS
SELECT
    `h`.`IdHistorial` AS `IdHistorial`,
    `h`.`IdSiniestro` AS `IdSiniestro`,
    `h`.`Accion` AS `Accion`,
    `h`.`Descripcion` AS `Descripcion`,
    `h`.`Fecha` AS `Fecha`,
    `s`.`Folio` AS `Folio`,
    `s`.`Estado` AS `Estado`
FROM `historialsiniestros` `h`
INNER JOIN `siniestros` `s`
    ON `s`.`IdSiniestro` = `h`.`IdSiniestro`;


-- =========================================================
-- 6. Vista: vw_historial_reparaciones
-- =========================================================
CREATE VIEW `vw_historial_reparaciones` AS
SELECT
    `h`.`IdHistorial` AS `IdHistorial`,
    `h`.`IdReparacion` AS `IdReparacion`,
    `h`.`EstadoAnterior` AS `EstadoAnterior`,
    `h`.`EstadoActual` AS `EstadoActual`,
    `h`.`Fecha` AS `Fecha`,
    `r`.`Taller` AS `Taller`,
    `s`.`Folio` AS `Folio`
FROM `reparaciones_historial` `h`
INNER JOIN `reparaciones` `r`
    ON `r`.`IdReparacion` = `h`.`IdReparacion`
INNER JOIN `siniestros` `s`
    ON `s`.`IdSiniestro` = `r`.`IdSiniestro`;


-- =========================================================
-- 7. Vista: vw_busqueda_siniestros
-- =========================================================
CREATE VIEW `vw_busqueda_siniestros` AS
SELECT
    `s`.`IdSiniestro` AS `IdSiniestro`,
    `s`.`Folio` AS `Folio`,
    `s`.`Estado` AS `Estado`,
    `s`.`Tipo` AS `Tipo`,
    `s`.`Ubicacion` AS `Ubicacion`,
    `s`.`FechaReporte` AS `FechaReporte`,
    `s`.`Descripcion` AS `Descripcion`,
    `s`.`IdAjustador` AS `IdAjustador`,
    `v`.`IdAsegurado` AS `IdAsegurado`
FROM `siniestros` `s`
INNER JOIN `vehiculos` `v`
    ON `s`.`IdVehiculo` = `v`.`IdVehiculo`;


-- =========================================================
-- 8. Vista: vw_busqueda_reparaciones
-- =========================================================
CREATE VIEW `vw_busqueda_reparaciones` AS
SELECT
    `r`.`IdReparacion` AS `IdReparacion`,
    `r`.`IdSiniestro` AS `IdSiniestro`,
    `r`.`Taller` AS `Taller`,
    `r`.`Estado` AS `Estado`,
    `r`.`DescripcionTrabajo` AS `DescripcionTrabajo`,
    `r`.`FechaInicio` AS `FechaInicio`,
    `r`.`FechaFin` AS `FechaFin`,
    `s`.`Folio` AS `Folio`,
    `s`.`Estado` AS `EstadoSiniestro`
FROM `reparaciones` `r`
INNER JOIN `siniestros` `s`
    ON `s`.`IdSiniestro` = `r`.`IdSiniestro`;


-- =========================================================
-- 9. Vista: vw_busqueda_global (Depende de otras vistas, por eso se crea después)
-- =========================================================
CREATE VIEW `vw_busqueda_global` AS
SELECT
    'Siniestro' AS `TipoRegistro`,
    `v`.`IdSiniestro` AS `Id`,
    `v`.`Folio` AS `Ref`,
    `v`.`Estado` AS `Estado`,
    `v`.`Tipo` AS `Tipo`,
    `v`.`Ubicacion` AS `Ubicacion`,
    `v`.`FechaReporte` AS `Fecha`
FROM `vw_busqueda_siniestros` `v`
UNION ALL
SELECT
    'Reparacion' AS `TipoRegistro`,
    `v`.`IdReparacion` AS `Id`,
    `v`.`Folio` AS `Ref`,
    `v`.`Estado` AS `Estado`,
    `v`.`Taller` AS `Tipo`,
    `v`.`DescripcionTrabajo` AS `Ubicacion`,
    `v`.`FechaInicio` AS `Fecha`
FROM `vw_busqueda_reparaciones` `v`
UNION ALL
SELECT
    'HistorialSiniestro' AS `TipoRegistro`,
    `v`.`IdHistorial` AS `Id`,
    `v`.`Folio` AS `Ref`,
    `v`.`Estado` AS `Estado`,
    `v`.`Accion` AS `Tipo`,
    `v`.`Descripcion` AS `Ubicacion`,
    `v`.`Fecha` AS `Fecha`
FROM `vw_historial_siniestros` `v`
UNION ALL
SELECT
    'HistorialReparacion' AS `TipoRegistro`,
    `v`.`IdHistorial` AS `Id`,
    `v`.`Folio` AS `Ref`,
    `v`.`EstadoActual` AS `Estado`,
    `v`.`Taller` AS `Tipo`,
    CONCAT(
        'De: ',
        `v`.`EstadoAnterior`,
        ' a: ',
        `v`.`EstadoActual`
    ) AS `Ubicacion`,
    `v`.`Fecha` AS `Fecha`
FROM `vw_historial_reparaciones` `v`;


-- =========================================================
-- 10. Vista: vw_siniestros_acceso
-- =========================================================
DROP VIEW IF EXISTS `vw_siniestros_acceso`;
CREATE VIEW `vw_siniestros_acceso` AS
SELECT
    `s`.`IdSiniestro` AS `IdSiniestro`,
    `s`.`IdVehiculo` AS `IdVehiculo`,
    `s`.`IdAjustador` AS `IdAjustador`,
    `s`.`Folio` AS `Folio`,
    `s`.`Estado` AS `Estado`,
    `s`.`Tipo` AS `Tipo`,
    `s`.`Descripcion` AS `Descripcion`,
    `s`.`FechaReporte` AS `FechaReporte`,
    `s`.`FechaAsignacion` AS `FechaAsignacion`,
    `s`.`FechaCierre` AS `FechaCierre`,
    `s`.`Ubicacion` AS `Ubicacion`,
    `s`.`MontoEstimado` AS `MontoEstimado`,
    `s`.`MontoAprobado` AS `MontoAprobado`
FROM `siniestros` `s`;


-- =========================================================
-- 11. Vista: vw_usuarios_sesion
-- =========================================================
DROP VIEW IF EXISTS `vw_usuarios_sesion`;
CREATE VIEW `vw_usuarios_sesion` AS
SELECT
    `u`.`IdUsuario` AS `IdUsuario`,
    `u`.`Nombre` AS `Nombre`,
    `u`.`Apellidos` AS `Apellidos`,
    `u`.`Foto` AS `Foto`,
    `u`.`Email` AS `Email`,
    `u`.`Telefono` AS `Telefono`,
    `r`.`Nombre` AS `Rol`
FROM `usuarios` `u`
INNER JOIN `roles` `r`
    ON `u`.`IdRol` = `r`.`IdRol`
WHERE `u`.`Activo` = 1;


-- =========================================================
-- 12. Vista: vw_vehiculos_panel
-- =========================================================

DROP VIEW IF EXISTS `vw_vehiculos_panel`;

CREATE VIEW `vw_vehiculos_panel` AS
SELECT
    `v`.`IdVehiculo` AS `IdVehiculo`,
    `v`.`IdAsegurado` AS `IdAsegurado`,
    `v`.`Tipo` AS `Tipo`,
    `v`.`Marca` AS `Marca`,
    `v`.`Modelo` AS `Modelo`,
    `v`.`Anio` AS `Anio`,
    `v`.`Placas` AS `Placas`,
    `v`.`NumeroPoliza` AS `NumeroPoliza`
FROM `vehiculos` `v`;


-- =========================================================
-- 13. Vista: vw_vehiculos_usuario
-- =========================================================
DROP VIEW IF EXISTS `vw_vehiculos_usuario`;
CREATE VIEW `vw_vehiculos_usuario` AS
SELECT
    `v`.`IdVehiculo` AS `IdVehiculo`,
    `v`.`IdAsegurado` AS `IdAsegurado`,
    `v`.`Tipo` AS `Tipo`,
    `v`.`Marca` AS `Marca`,
    `v`.`Modelo` AS `Modelo`,
    `v`.`Anio` AS `Anio`,
    `v`.`Placas` AS `Placas`,
    `v`.`NumeroPoliza` AS `NumeroPoliza`,
    `v`.`NumeroSerie` AS `NumeroSerie`
FROM `vehiculos` `v`;

-- Funciones
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