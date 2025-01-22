DROP DATABASE IF EXISTS cursoscp;
CREATE DATABASE cursoscp DEFAULT CHARACTER SET UTF8; 
USE cursoscp;

# modelo físico en mysql


# Estructura de tabla para la tabla curso

CREATE TABLE IF NOT EXISTS cursos (
  codigo smallint(6) NOT NULL default 0,
  nombre varchar(50) NOT NULL default '',
  abierto boolean NOT NULL default true,
  numeroplazas smallint(2) not null default 20,
  plazoinscripcion date not null,
  PRIMARY KEY  (codigo)
) engine=innodb;


-- Volcar la base de datos para la tabla `cursos`

INSERT INTO cursos VALUES (1, 'Instalacion y uso de Apache',true,20,'2015-05-20');
INSERT INTO cursos VALUES (2, 'Administracion avanzada de Apache',true,30,'2015-05-20');
INSERT INTO cursos VALUES (3, 'Elaboracion de recursos didacticos',true,20,'2015-05-20');
INSERT INTO cursos VALUES (4, 'Uso didactico de Moodle en primaria',true,10,'2015-05-20');
INSERT INTO cursos VALUES (5, 'Uso didactico de Moodle en secundaria', false,20,'2015-01-20');
INSERT INTO cursos VALUES (6, 'Moodle y el aula de musica',true,20,'2015-05-25');
INSERT INTO cursos VALUES (7, 'Tratamiento de imagenes',false,20,'2015-02-20');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla solicitudes

CREATE TABLE IF NOT EXISTS solicitudes (
  dni varchar(9) NOT NULL default '',
  codigocurso smallint(6) NOT NULL default 0,
  fechasolicitud date not null,
  admitido boolean not null default false,
  PRIMARY KEY  (dni,codigocurso)
) engine=innodb;

 
-- Estructura de tabla para la tabla solicitantes
 

CREATE TABLE IF NOT EXISTS solicitantes (
  dni varchar(9) NOT NULL default '',
  apellidos varchar(40) NOT NULL default '',
  nombre varchar(20) NOT NULL default '',
  telefono varchar(12) NOT NULL default '',
  correo varchar(50) NOT NULL default '',
  codcen varchar(8) NOT NULL default '',
  coordinadortic boolean NOT NULL default false,
  grupotic boolean NOT NULL default false,
  nomgrupo varchar(25) NOT NULL default '',
  pbilin boolean NOT NULL default false,
  cargo boolean not null default false,
  nombrecargo varchar(15) NOT NULL default '',
  situacion enum ('activo','inactivo') NOT NULL default 'activo',
  fechanac date,
  especialidad varchar(50) NOT NULL default '',
  puntos tinyint(3) unsigned default '0',
  PRIMARY KEY  (dni)
) engine=innodb;


