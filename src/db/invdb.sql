-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-02-2018 a las 10:47:11
-- Versión del servidor: 10.1.21-MariaDB
-- Versión de PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `invdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bien`
--

CREATE TABLE `bien` (
  `id` int(11) NOT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `fk_id_cat_tipo_clasificacion_bien` int(11) DEFAULT NULL,
  `fk_id_clasificacion_bien` int(11) DEFAULT NULL,
  `fk_id_departamento` int(11) DEFAULT NULL,
  `fk_id_cat_depreciacion` int(11) DEFAULT NULL,
  `fk_id_cat_origen_fondo_adquisicion` int(11) DEFAULT NULL,
  `imagen` varchar(220) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8_spanish_ci,
  `marca` varchar(140) COLLATE utf8_spanish_ci DEFAULT NULL,
  `modelo` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `serie` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `motor` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `factura` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_adquisicion` timestamp NULL DEFAULT NULL,
  `fk_id_cat_tipo_valuacion` int(11) DEFAULT NULL,
  `valuacion` double DEFAULT NULL,
  `fecha_insert` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bien`
--

INSERT INTO `bien` (`id`, `fk_id_empresa`, `fk_id_cat_tipo_clasificacion_bien`, `fk_id_clasificacion_bien`, `fk_id_departamento`, `fk_id_cat_depreciacion`, `fk_id_cat_origen_fondo_adquisicion`, `imagen`, `descripcion`, `marca`, `modelo`, `serie`, `motor`, `factura`, `fecha_adquisicion`, `fk_id_cat_tipo_valuacion`, `valuacion`, `fecha_insert`) VALUES
(2, 1, 1, 1, 2, 3, 4, '/files/2018-02-20/cat.png', 'jkljkjkjljkljl edit', 'NO LEGIBLE', 'NO APLICA', 'serie 3', 'NO APLICA', 'factura 2', '2009-06-26 05:00:00', 1, 6000, '2018-02-18 02:52:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bien_periodo`
--

CREATE TABLE `bien_periodo` (
  `id` int(11) NOT NULL,
  `fk_id_bien` int(11) DEFAULT NULL,
  `fk_id_periodo` int(11) DEFAULT NULL,
  `fk_id_cat_estado_fisico` int(11) DEFAULT NULL,
  `depreciacion_acumulada` double DEFAULT NULL,
  `depreciacion_periodo` double DEFAULT NULL,
  `anios_uso` int(11) DEFAULT NULL,
  `fk_id_cat_uma` int(11) DEFAULT NULL,
  `valor_uma` double DEFAULT NULL,
  `inventario_contable` char(1) COLLATE utf8_spanish_ci DEFAULT '1',
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bien_periodo`
--

INSERT INTO `bien_periodo` (`id`, `fk_id_bien`, `fk_id_periodo`, `fk_id_cat_estado_fisico`, `depreciacion_acumulada`, `depreciacion_periodo`, `anios_uso`, `fk_id_cat_uma`, `valor_uma`, `inventario_contable`, `fecha_insert`) VALUES
(1, 1, 1, 1, 473.28, 157.76, 3, 3, 0, '1', '2018-02-18 02:48:36'),
(2, 2, 1, 2, 1584, 198, 8, 3, 5642, '1', '2018-02-18 02:52:42'),
(3, 3, 1, 1, 0, 0, 7, 3, 2833.25, '0', '2018-02-18 03:11:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_bien_inmueble`
--

CREATE TABLE `cat_bien_inmueble` (
  `id` int(11) NOT NULL,
  `grupo` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `subgrupo` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `clase` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `subclase` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `consecutivo` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descr` text COLLATE utf8_spanish_ci,
  `cuenta_contable` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cuenta_depreciacion` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fk_id_cat_tipo_clasificacion_bien` int(11) DEFAULT '2',
  `fecha_insert` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_bien_inmueble`
--

INSERT INTO `cat_bien_inmueble` (`id`, `grupo`, `subgrupo`, `clase`, `subclase`, `consecutivo`, `descr`, `cuenta_contable`, `cuenta_depreciacion`, `fk_id_cat_tipo_clasificacion_bien`, `fecha_insert`) VALUES
(1, '01', '01', '01', '01', '01', 'Clasificacion de prueba', '1.5.1', '1.5.2', 2, '2018-02-17 22:48:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_bien_mueble`
--

CREATE TABLE `cat_bien_mueble` (
  `id` int(11) NOT NULL,
  `grupo` int(11) DEFAULT NULL,
  `subgrupo` int(11) DEFAULT NULL,
  `clase` int(11) DEFAULT NULL,
  `descr` varchar(256) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cuenta_contable` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cuenta_depreciacion` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fk_id_cat_tipo_clasificacion_bien` int(11) DEFAULT '1',
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_bien_mueble`
--

INSERT INTO `cat_bien_mueble` (`id`, `grupo`, `subgrupo`, `clase`, `descr`, `cuenta_contable`, `cuenta_depreciacion`, `fk_id_cat_tipo_clasificacion_bien`, `fecha_insert`) VALUES
(1, 2, 0, 0, 'Materiales y suministros', '1.1.1', '5.1.1', 1, '2018-02-17 22:51:47'),
(2, 2, 1, 0, 'Materiales de administración, emisión de documentos y artículos oficiales', '1.2.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(3, 2, 1, 1, 'Materiales, útiles y equipos menores de oficina', '1.1.1', '5.1.1', 1, '2018-02-17 22:51:47'),
(4, 2, 1, 2, 'Materiales y útiles de impresión y reproducción', '1.0.1', '5.1.1', 1, '2018-02-17 22:51:47'),
(5, 2, 1, 3, 'Material estadístico y geográfico', '2.1.1', '5.1.1', 1, '2018-02-17 22:51:47'),
(6, 2, 1, 4, 'Materiales, útiles y equipos menores de tecnologías de la información y comunicaciones', '2.1.4', '5.1.1', 1, '2018-02-17 22:51:47'),
(7, 2, 1, 5, 'Material impreso e información digital', '1.2.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(8, 2, 1, 6, 'Material de limpieza', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(9, 2, 1, 7, 'Materiales y útiles de enseñanza', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(10, 2, 1, 8, 'Materiales para el registro e identificación de bienes y personas', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(11, 2, 2, 0, 'Alimentos y utensilios', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(12, 2, 2, 1, 'Productos alimenticios para personas', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(13, 2, 2, 2, 'Productos alimenticios para animales', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(14, 2, 2, 3, 'Utensilios para el servicio de alimentación', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(15, 2, 3, 0, 'Materias primas y materiales de producción y comercialización', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(16, 2, 3, 1, 'Productos alimenticios, agropecuarios y forestales adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(17, 2, 3, 2, 'Insumos textiles adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(18, 2, 3, 3, 'Productos de papel, cartón e impresos adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(19, 2, 3, 4, 'Combustibles, lubricantes, aditivos, carbón y sus derivados adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(20, 2, 3, 5, 'Productos químicos, farmacéuticos y de laboratorio adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(21, 2, 3, 6, 'Productos metálicos y a base de minerales no metálicos adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(22, 2, 3, 7, 'Productos de cuero, piel, plástico y hule adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(23, 2, 3, 8, 'Mercancías adquiridas para su comercialización', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(24, 2, 3, 9, 'Otros productos adquiridos como materia prima', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(25, 2, 4, 0, 'Materiales y artículos de construcción y de reparación', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(26, 2, 4, 1, 'Productos minerales no metálicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(27, 2, 4, 2, 'Cemento y productos de concreto', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(28, 2, 4, 3, 'Cal, yeso y productos de yeso', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(29, 2, 4, 4, 'Madera y productos de madera', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(30, 2, 4, 5, 'Vidrio y productos de vidrio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(31, 2, 4, 6, 'Material eléctrico y electrónico', '2.1.3', '5.1.1', 1, '2018-02-17 22:57:37'),
(32, 2, 4, 7, 'Artículos metálicos para la construcción', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(33, 2, 4, 8, 'Materiales complementarios', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(34, 2, 4, 9, 'Otros materiales y artículos de construcción y reparación', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(35, 2, 5, 0, 'Productos químicos, farmacéuticos y de laboratorio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(36, 2, 5, 1, 'Productos químicos básicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(37, 2, 5, 2, 'Fertilizantes, pesticidas y otros agroquímicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(38, 2, 5, 3, 'Medicinas y productos farmacéuticos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(39, 2, 5, 4, 'Materiales, accesorios y suministros médicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(40, 2, 5, 5, 'Materiales, accesorios y suministros de laboratorio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(41, 2, 5, 6, 'Fibras sintéticas, hules, plásticos y derivados', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(42, 2, 5, 9, 'Otros productos químicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(43, 2, 6, 0, 'Combustibles, lubricantes y aditivos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(44, 2, 6, 1, 'Combustibles, lubricantes y aditivos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(45, 2, 6, 2, 'Carbón y sus derivados', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(46, 2, 7, 0, 'Vestuario, blancos, prendas de protección y artículos deportivos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(47, 2, 7, 1, 'Vestuario y uniformes', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(48, 2, 7, 2, 'Prendas de seguridad y protección personal', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(49, 2, 7, 3, 'Artículos deportivos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(50, 2, 7, 4, 'Productos textiles', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(51, 2, 7, 5, 'Blancos y otros productos textiles, excepto prendas de vestir', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(52, 2, 8, 0, 'Materiales y suministros para seguridad', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(53, 2, 8, 1, 'Sustancias y materiales explosivos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(54, 2, 8, 2, 'Materiales de seguridad publica', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(55, 2, 8, 3, 'Prendas de protección para seguridad pública y nacional', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(56, 2, 9, 0, 'Herramientas, refacciones y accesorios menores', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(57, 2, 9, 1, 'Herramientas menores', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(58, 2, 9, 2, 'Refacciones y accesorios menores de edificios', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(59, 2, 9, 3, 'Refacciones y accesorios menores de mobiliario y equipo de administración, educacional y recreativo', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(60, 2, 9, 4, 'Refacciones y accesorios menores de equipo de cómputo y tecnologías de la información', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(61, 2, 9, 5, 'Refacciones y accesorios menores de equipo e instrumental médico y de laboratorio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(62, 2, 9, 6, 'Refacciones y accesorios menores de equipo de transporte', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(63, 2, 9, 7, 'Refacciones y accesorios menores de equipo de defensa y seguridad', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(64, 2, 9, 8, 'Refacciones y accesorios menores de maquinaria y otros equipos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(65, 2, 9, 9, 'Refacciones y accesorios menores otros bienes muebles', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(66, 5, 0, 0, 'Bienes muebles, inmuebles e intangibles', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(67, 5, 1, 0, 'Mobiliario y equipo de administración', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(68, 5, 1, 1, 'Muebles de oficina y estantería', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(69, 5, 1, 2, 'Muebles, excepto de oficina y estantería', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(70, 5, 1, 3, 'Bienes artísticos, culturales y científicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(71, 5, 1, 4, 'Objetos de valor', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(72, 5, 1, 5, 'Equipo de cómputo y de tecnologías de la información', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(73, 5, 1, 9, 'Otros mobiliarios y equipos de administración', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(74, 5, 2, 0, 'Mobiliario y equipo educacional y recreativo', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(75, 5, 2, 1, 'Equipos y aparatos audiovisuales', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(76, 5, 2, 2, 'Aparatos deportivos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(77, 5, 2, 3, 'Cámaras fotográficas y de video', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(78, 5, 2, 9, 'Otro mobiliario y equipo educacional y recreativo', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(79, 5, 3, 0, 'Equipo e instrumental médico y de laboratorio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(80, 5, 3, 1, 'Equipo médico y de laboratorio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(81, 5, 3, 2, 'Instrumental médico y de laboratorio', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(82, 5, 4, 0, 'Vehículos y equipo de transporte', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(83, 5, 4, 1, 'Vehículos y equipo terrestre', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(84, 5, 4, 2, 'Carrocerías y remolques', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(85, 5, 4, 3, 'Equipo aeroespacial', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(86, 5, 4, 4, 'Equipo ferroviario', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(87, 5, 4, 5, 'Embarcaciones', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(88, 5, 4, 9, 'Otros equipos de transporte', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(89, 5, 5, 0, 'Equipo de defensa y seguridad', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(90, 5, 5, 1, 'Equipo de defensa y seguridad', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(91, 5, 6, 0, 'Maquinaria, otros equipos y herramientas', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(92, 5, 6, 1, 'Maquinaria y equipo agropecuario', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(93, 5, 6, 2, 'Maquinaria y equipo industrial', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(94, 5, 6, 3, 'Maquinaria y equipo de construcción', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(95, 5, 6, 4, 'Sistemas de aire acondicionado, calefacción y de refrigeración industrial y comercial', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(96, 5, 6, 5, 'Equipo de comunicación y telecomunicación', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(97, 5, 6, 6, 'Equipos de generación eléctrica, aparatos y accesorios eléctricos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(98, 5, 6, 7, 'Herramientas y máquinas-herramienta', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(99, 5, 6, 9, 'Otros equipos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(100, 5, 7, 0, 'Activos biológicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(101, 5, 7, 1, 'Bovinos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(102, 5, 7, 2, 'Porcinos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(103, 5, 7, 3, 'Aves', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(104, 5, 7, 4, 'Ovinos y caprinos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(105, 5, 7, 5, 'Peces y acuicultura', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(106, 5, 7, 6, 'Equinos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(107, 5, 7, 7, 'Especies menores y de zoológico', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(108, 5, 7, 8, 'Arboles y plantas', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47'),
(109, 5, 7, 9, 'Otros activos biológicos', '2.1.3', '5.1.1', 1, '2018-02-17 22:51:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_depreciacion`
--

CREATE TABLE `cat_depreciacion` (
  `id` int(11) NOT NULL,
  `cuenta` varchar(14) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descr` varchar(256) COLLATE utf8_spanish_ci DEFAULT NULL,
  `vida_util` double DEFAULT NULL,
  `depreciacion_anual` double DEFAULT NULL,
  `fk_id_tipo_bien` int(11) DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_depreciacion`
--

INSERT INTO `cat_depreciacion` (`id`, `cuenta`, `descr`, `vida_util`, `depreciacion_anual`, `fk_id_tipo_bien`, `fecha_insert`) VALUES
(1, '1.2.3', 'BIENES INMUEBLES, INFRAESTRUCTURA Y CONSTRUCCIONES EN PROCESO', 0, 0, 2, '2018-01-24 05:55:17'),
(2, '1.2.3.2', 'Viviendas', 50, 2, 2, '2018-01-24 05:55:49'),
(3, '1.2.3.3', 'Edificios No Habitacionales', 30, 3.3, 2, '2018-01-24 05:56:21'),
(4, '1.2.3.4', 'INFRAESTRUCTURA', 25, 4, 2, '2018-01-24 05:57:01'),
(5, '1.2.3.9', 'Otros Bienes INMUEBLES', 20, 5, 2, '2018-01-24 05:57:25'),
(6, '1.2.4', 'BIENES MUEBLES', 0, 0, 1, '2018-02-20 08:35:20'),
(7, '1.2.4.1', 'Mobiliario y Equipo de Administración', 0, 0, 1, '2018-02-20 08:35:20'),
(8, '1.2.4.1.1', 'Muebles de Oficina y Estantería', 10, 10, 1, '2018-02-20 08:35:20'),
(9, '1.2.4.1.2', 'Muebles, Excepto De Oficina Y Estantería', 10, 10, 1, '2018-02-20 08:35:20'),
(10, '1.2.4.1.3', 'Equipo de Cómputo y de Tecnologías de la Información', 3, 33.3, 1, '2018-02-20 08:35:20'),
(11, '1.2.4.1.9', 'Otros Mobiliarios y Equipos de Administración', 10, 10, 1, '2018-02-20 08:35:20'),
(12, '1.2.4.2', 'Mobiliario y Equipo Educacional y Recreativo', 0, 0, 1, '2018-02-20 08:35:20'),
(13, '1.2.4.2.1', 'Equipos y Aparatos Audiovisuales', 3, 33.3, 1, '2018-02-20 08:35:20'),
(14, '1.2.4.2.2', 'Aparatos Deportivos', 5, 20, 1, '2018-02-20 08:35:20'),
(15, '1.2.4.2.3', 'Cámaras Fotográficas y de Video', 3, 33.3, 1, '2018-02-20 08:35:20'),
(16, '1.2.4.2.9', 'Otro Mobiliario y Equipo Educacional y Recreativo', 5, 20, 1, '2018-02-20 08:35:20'),
(17, '1.2.4.3', 'Equipo e Instrumental Médico y de Laboratorio', 0, 0, 1, '2018-02-20 08:35:20'),
(18, '1.2.4.3.1', 'Equipo Médico y de Laboratorio', 5, 20, 1, '2018-02-20 08:35:20'),
(19, '1.2.4.3.2', 'Instrumental Médico y de Laboratorio', 5, 20, 1, '2018-02-20 08:35:20'),
(20, '1.2.4.4', 'Equipo de Transporte', 0, 0, 1, '2018-02-20 08:35:20'),
(21, '1.2.4.4.1', 'Automóviles y Equipo Terrestre', 5, 20, 1, '2018-02-20 08:35:20'),
(22, '1.2.4.4.2', 'Carrocerías y Remolques', 5, 20, 1, '2018-02-20 08:35:20'),
(23, '1.2.4.4.3', 'Equipo Aeroespacial', 5, 20, 1, '2018-02-20 08:35:20'),
(24, '1.2.4.4.4', 'Equipo Ferroviario', 5, 20, 1, '2018-02-20 08:35:20'),
(25, '1.2.4.4.5', 'Embarcaciones', 5, 20, 1, '2018-02-20 08:35:20'),
(26, '1.2.4.4.9', 'Otros Equipos de Transporte', 5, 20, 1, '2018-02-20 08:35:20'),
(27, '1.2.4.5', 'Equipo de Defensa y Seguridad1', 0, 0, 1, '2018-02-20 08:35:20'),
(28, '1.2.4.6', 'Maquinaria, Otros Equipos y Herramientas', 0, 0, 1, '2018-02-20 08:35:20'),
(29, '1.2.4.6.1', 'Maquinaria y Equipo Agropecuario', 10, 10, 1, '2018-02-20 08:35:20'),
(30, '1.2.4.6.2', 'Maquinaria y Equipo Industrial ', 10, 10, 1, '2018-02-20 08:35:20'),
(31, '1.2.4.6.3', 'Maquinaria y Equipo de Construcción', 10, 10, 1, '2018-02-20 08:35:20'),
(32, '1.2.4.6.4', 'Sistemas de Aire Acondicionado, Calefacción y de Refrigeración Industrial y Comercial', 10, 10, 1, '2018-02-20 08:35:20'),
(33, '1.2.4.6.5', 'Equipo de Comunicación y Telecomunicación', 10, 10, 1, '2018-02-20 08:35:20'),
(34, '1.2.4.6.6', 'Equipos de Generación Eléctrica, Aparatos y Accesorios Eléctricos ', 10, 10, 1, '2018-02-20 08:35:20'),
(35, '1.2.4.6.7', 'Herramientas y Máquinas-Herramienta ', 10, 10, 1, '2018-02-20 08:35:20'),
(36, '1.2.4.6.9', 'Otros Equipos', 10, 10, 1, '2018-02-20 08:35:20'),
(37, '1.2.4.8', 'Activos Biológicos', 0, 0, 1, '2018-02-20 08:35:20'),
(38, '1.2.4.8.1', 'Bovinos', 5, 20, 1, '2018-02-20 08:35:20'),
(39, '1.2.4.8.2', 'Porcinos', 5, 20, 1, '2018-02-20 08:35:20'),
(40, '1.2.4.8.3', 'Aves', 5, 20, 1, '2018-02-20 08:35:20'),
(41, '1.2.4.8.4', 'Ovinos y Caprinos', 5, 20, 1, '2018-02-20 08:35:20'),
(42, '1.2.4.8.5', 'Peces y Acuicultura', 5, 20, 1, '2018-02-20 08:35:20'),
(43, '1.2.4.8.6', 'Equinos', 5, 20, 1, '2018-02-20 08:35:20'),
(44, '1.2.4.8.7', 'Especies Menores y de Zoológico', 5, 20, 1, '2018-02-20 08:35:20'),
(45, '1.2.4.8.8 ', 'Arboles y Plantas', 5, 20, 1, '2018-02-20 08:35:20'),
(46, '1.2.4.8.9', 'Otros Activos Biológicos', 5, 20, 1, '2018-02-20 08:35:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_estado_fisico`
--

CREATE TABLE `cat_estado_fisico` (
  `id` int(11) NOT NULL,
  `descr` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_estado_fisico`
--

INSERT INTO `cat_estado_fisico` (`id`, `descr`, `fecha_insert`) VALUES
(1, 'NUEVO', '2018-01-18 05:32:21'),
(2, 'BUEN ESTADO', '2018-01-18 05:32:28'),
(3, 'SEMI NUEVO', '2018-01-18 05:32:57'),
(4, 'FUNCIONAL', '2018-01-18 05:33:16'),
(5, 'MAL ESTADO', '2018-01-18 05:33:20'),
(6, 'INSERVIBLE', '2018-01-18 05:33:22'),
(7, 'REQUIERE MANTENIMIENTO', '2018-01-18 05:34:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_estatus_empresa`
--

CREATE TABLE `cat_estatus_empresa` (
  `id` int(11) NOT NULL,
  `descr` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_estatus_empresa`
--

INSERT INTO `cat_estatus_empresa` (`id`, `descr`) VALUES
(1, 'ACTIVO'),
(2, 'INACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_origen_fondo_adquisicion`
--

CREATE TABLE `cat_origen_fondo_adquisicion` (
  `id` int(11) NOT NULL,
  `descr` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_origen_fondo_adquisicion`
--

INSERT INTO `cat_origen_fondo_adquisicion` (`id`, `descr`) VALUES
(1, 'INGRESOS PROPIOS'),
(2, 'PARTICIPACIÓN ARBITRIOS'),
(3, 'FORTAMUN DF'),
(4, 'FISM'),
(5, 'PORTAFIN'),
(6, 'FORTASEG'),
(7, 'CONTINVER'),
(8, 'HIDROCARBUROS'),
(9, 'HABITAT'),
(10, 'VIVIENDA'),
(11, 'FORTELECE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_tipo_bien`
--

CREATE TABLE `cat_tipo_bien` (
  `id` int(11) NOT NULL,
  `descr` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_tipo_bien`
--

INSERT INTO `cat_tipo_bien` (`id`, `descr`) VALUES
(1, 'BIEN INMUEBLE'),
(2, 'BIEN MUEBLE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_tipo_clasificacion_bien`
--

CREATE TABLE `cat_tipo_clasificacion_bien` (
  `id` int(11) NOT NULL,
  `descr` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_tipo_clasificacion_bien`
--

INSERT INTO `cat_tipo_clasificacion_bien` (`id`, `descr`) VALUES
(1, 'BIEN MUEBLE'),
(2, 'BIEN INMUEBLE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_tipo_responsable_departamento`
--

CREATE TABLE `cat_tipo_responsable_departamento` (
  `id` int(11) NOT NULL,
  `descr` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_tipo_responsable_departamento`
--

INSERT INTO `cat_tipo_responsable_departamento` (`id`, `descr`) VALUES
(1, 'ADMINISTRATIVO'),
(2, 'CONTABLE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_tipo_valuacion`
--

CREATE TABLE `cat_tipo_valuacion` (
  `id` int(11) NOT NULL,
  `descr` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_tipo_valuacion`
--

INSERT INTO `cat_tipo_valuacion` (`id`, `descr`) VALUES
(1, 'IMPORTE'),
(2, 'VALOR REPOSICION'),
(3, 'VALOR REEMPLAZO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_uma`
--

CREATE TABLE `cat_uma` (
  `id` int(11) NOT NULL,
  `anio` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valor_diario` double DEFAULT NULL,
  `valor_mensual` double DEFAULT NULL,
  `valor_anual` double DEFAULT NULL,
  `factor` int(11) DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cat_uma`
--

INSERT INTO `cat_uma` (`id`, `anio`, `valor_diario`, `valor_mensual`, `valor_anual`, `factor`, `fecha_insert`) VALUES
(1, '2016', 75, 2420.75, 28299.8, 35, '2018-02-12 05:09:19'),
(2, '2017', 80, 2430, 29300, 35, '2018-02-12 05:09:24'),
(3, '2018', 80.6, 2450.54, 29402.88, 70, '2018-02-12 05:09:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id` int(11) NOT NULL,
  `descr` varchar(220) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `departamento`
--

INSERT INTO `departamento` (`id`, `descr`, `fecha_insert`) VALUES
(1, 'TESORERIA', '2018-01-24 02:33:45'),
(2, 'DIRECCIÓN DE INGRESOS', '2018-01-24 02:48:11'),
(3, 'LIMPIA PÚBLICA', '2018-01-24 02:48:33'),
(4, 'EJECUCIÓN FISCAL', '2018-01-24 02:48:47'),
(5, 'DIRECCIÓN DE EGRESOS', '2018-01-24 02:49:01'),
(6, 'CONTROL FINANCIERO', '2018-01-24 02:49:25'),
(7, 'DIRECCIÓN DE CONTABILIDAD Y CONTROL PRESUPUESTAL', '2018-01-24 02:49:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento_asignacion`
--

CREATE TABLE `departamento_asignacion` (
  `id` int(11) NOT NULL,
  `fk_id_departamento` int(11) DEFAULT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `fk_id_periodo` int(11) DEFAULT NULL,
  `fk_id_cat_tipo_responsable_departamento` int(11) DEFAULT NULL,
  `fk_id_personal` int(11) DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `nombre` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descr` text COLLATE utf8_spanish_ci,
  `direccion` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `logo` varchar(220) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fk_id_cat_estatus` int(11) DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `nombre`, `descr`, `direccion`, `logo`, `fk_id_cat_estatus`, `fecha_insert`) VALUES
(1, 'Ayuntamiento de Jilotepec', '', '', '', NULL, '2018-01-21 06:25:25'),
(2, 'Ayuntamiento de Jilotepec', '', '', '', NULL, '2018-01-21 06:27:55'),
(3, 'Nuevo', '', '', '', NULL, '2018-01-21 06:30:37'),
(4, 'Nueva sierra', '', '', '', NULL, '2018-01-21 07:48:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodo`
--

CREATE TABLE `periodo` (
  `id` int(11) NOT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `descr` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_fin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `periodo`
--

INSERT INTO `periodo` (`id`, `fk_id_empresa`, `descr`, `fecha_inicio`, `fecha_fin`, `fecha_insert`) VALUES
(1, 1, 'Ejercicio 2018', '2018-01-29 05:56:48', '2018-01-01 06:00:00', '2018-01-22 05:03:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidos` varchar(180) COLLATE utf8_spanish_ci DEFAULT NULL,
  `avatar` varchar(256) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `password`, `nombre`, `apellidos`, `avatar`, `fecha_insert`) VALUES
(1, 'zero@mail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'Marco', 'Lozada', NULL, '2018-01-10 06:33:48');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bien`
--
ALTER TABLE `bien`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `bien_periodo`
--
ALTER TABLE `bien_periodo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_bien_inmueble`
--
ALTER TABLE `cat_bien_inmueble`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_bien_mueble`
--
ALTER TABLE `cat_bien_mueble`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_depreciacion`
--
ALTER TABLE `cat_depreciacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_estado_fisico`
--
ALTER TABLE `cat_estado_fisico`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_estatus_empresa`
--
ALTER TABLE `cat_estatus_empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_origen_fondo_adquisicion`
--
ALTER TABLE `cat_origen_fondo_adquisicion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_tipo_bien`
--
ALTER TABLE `cat_tipo_bien`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_tipo_clasificacion_bien`
--
ALTER TABLE `cat_tipo_clasificacion_bien`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_tipo_responsable_departamento`
--
ALTER TABLE `cat_tipo_responsable_departamento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_tipo_valuacion`
--
ALTER TABLE `cat_tipo_valuacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_uma`
--
ALTER TABLE `cat_uma`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `departamento_asignacion`
--
ALTER TABLE `departamento_asignacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_idx` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bien`
--
ALTER TABLE `bien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `bien_periodo`
--
ALTER TABLE `bien_periodo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `cat_bien_inmueble`
--
ALTER TABLE `cat_bien_inmueble`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `cat_bien_mueble`
--
ALTER TABLE `cat_bien_mueble`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;
--
-- AUTO_INCREMENT de la tabla `cat_depreciacion`
--
ALTER TABLE `cat_depreciacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT de la tabla `cat_estado_fisico`
--
ALTER TABLE `cat_estado_fisico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `cat_estatus_empresa`
--
ALTER TABLE `cat_estatus_empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `cat_origen_fondo_adquisicion`
--
ALTER TABLE `cat_origen_fondo_adquisicion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `cat_tipo_bien`
--
ALTER TABLE `cat_tipo_bien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `cat_tipo_clasificacion_bien`
--
ALTER TABLE `cat_tipo_clasificacion_bien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `cat_tipo_responsable_departamento`
--
ALTER TABLE `cat_tipo_responsable_departamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `cat_tipo_valuacion`
--
ALTER TABLE `cat_tipo_valuacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `cat_uma`
--
ALTER TABLE `cat_uma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `departamento_asignacion`
--
ALTER TABLE `departamento_asignacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `periodo`
--
ALTER TABLE `periodo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
