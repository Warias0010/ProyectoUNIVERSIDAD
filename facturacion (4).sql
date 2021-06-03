-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2021 a las 08:09:29
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `facturacion`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (IN `codigo` INT, IN `cantidad` INT, IN `token_user` VARCHAR(50))  BEGIN

DECLARE precio_actual decimal(10,2);
SELECT precio INTO precio_actual FROM producto WHERE codproducto = codigo;

 INSERT INTO detalle_temp(token_user,codproducto,cantidad,precio_venta) VALUES(token_user,codigo,cantidad,precio_actual);
 
 SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp
 INNER JOIN producto p
 ON tmp.codproducto = p.codproducto
 WHERE tmp.token_user= token_user;
 

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (IN `no_factura` INT)  BEGIN
DECLARE existe_factura int;
DECLARE registros int;
DECLARE a int;
DECLARE cod_producto int;
DECLARE cant_producto int;
DECLARE existencia_actual int;
DECLARE nueva_existencia int;

SET existe_factura = (SELECT COUNT(*)  FROM factura WHERE nofactura = no_factura and estatus = 1);

IF existe_factura > 0 THEN
CREATE TEMPORARY TABLE tbl_tmp (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cod_prod BIGINT,
    cant_prod int);
    SET a = 1;
    SET registros = (SELECT COUNT(*) FROM detallefactura WHERE nofactura= no_factura);
                  IF(registros > 0) THEN
                     INSERT INTO tbl_tmp(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detallefactura WHERE nofactura = no_factura;
                     WHILE a <= registros DO
                     SELECT cod_prod,cant_prod INTO cod_producto,cant_producto FROM tbl_tmp WHERE id = a;
                     SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = cod_producto;
                     SET nueva_existencia = existencia_actual + cant_producto;
                     UPDATE producto SET existencia = nueva_existencia WHERE codproducto = cod_producto;
                     
                     SET a=a+1;
                     
                     END WHILE;
                     UPDATE factura SET estatus = 2 WHERE nofactura = no_factura;
                     DROP TABLE tbl_tmp;
                     SELECT * FROM factura WHERE nofactura = no_factura;
                     
                     END IF;                  
    ELSE
    SELECT 0 factura;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (IN `id_detalle` INT, IN `token` VARCHAR(50))  BEGIN
DELETE FROM detalle_temp WHERE correlativo = id_detalle;
SELECT tmp.correlativo,tmp.codproducto,p.descripcion,tmp.cantidad,tmp.precio_venta FROM detalle_temp tmp INNER JOIN producto p ON tmp.codproducto= p.codproducto WHERE tmp.token_user= token;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `cod_cliente` INT, IN `token` VARCHAR(50))  BEGIN
DECLARE factura INT;

DECLARE registros int;
DECLARE total DECIMAL(10,2);

DECLARE nueva_existencia int;
DECLARE existencia_actual int;

DECLARE tmp_cod_producto int;
DECLARE tmp_cant_producto int;
DECLARE a INT;
SET a = 1;

CREATE TEMPORARY TABLE tbl_tmp_tokenuser (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cod_prod BIGINT,
    cant_prod int);
    
    SET registros = (SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
    IF registros > 0 THEN
    INSERT INTO tbl_tmp_tokenuser(cod_prod,cant_prod) SELECT codproducto,cantidad FROM detalle_temp WHERE token_user = token;
    INSERT INTO factura(usuario,codcliente) VALUES (cod_usuario,cod_cliente);
    SET factura = LAST_INSERT_ID();
    INSERT INTO detallefactura(nofactura,codproducto,cantidad,precio_venta) SELECT (factura) as nofactura, codproducto,cantidad, precio_venta FROM detalle_temp WHERE token_user = token;
    
    WHILE a<= registros DO
    SELECT cod_prod,cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id= a;
    SELECT existencia INTO existencia_actual FROM producto WHERE codproducto = tmp_cod_producto;
    
    SET nueva_existencia = existencia_actual - tmp_cant_producto;
    UPDATE producto SET existencia = nueva_existencia WHERE codproducto = tmp_cod_producto;
    
    SET a=a+1;
    
    
    END WHILE;
    SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp WHERE token_user = token);
                 UPDATE factura SET totalfactura = total WHERE nofactura = factura;
                 
                 DELETE FROM detalle_temp WHERE token_user = token;
                 TRUNCATE TABLE tbl_tmp_tokenuser;
                 SELECT * FROM factura WHERE nofactura = factura;
    ELSE
                 SELECT 0;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `Cod_categoria` int(10) NOT NULL,
  `Nombre` varchar(150) NOT NULL,
  `Descripcion` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`Cod_categoria`, `Nombre`, `Descripcion`) VALUES
(1, 'Juegos de comedor', 'Producto de calidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nit` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `dateadd` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nit`, `nombre`, `telefono`, `direccion`, `dateadd`, `usuario_id`, `estatus`) VALUES
(1, 0, 'Cliente Final', 878766787, 'Granada, Nicaragua', '2018-02-15 21:55:51', 1, 1),
(2, 87654321, 'Pruea555555', 34343434, 'Calzada Buena Vista', '2018-02-15 21:57:03', 1, 0),
(3, 22222, 'Elena HernÃ¡ndez', 987897987, 'nicaragua', '2018-02-15 21:59:20', 2, 0),
(5, 55555, 'Helen', 98789798, 'prueba', '2018-02-18 10:53:53', 1, 0),
(7, 798798798, 'Jorge Maldonado1', 2147483647, 'Colonia la Flores', '2018-02-18 11:10:07', 1, 1),
(8, 203, 'Marta Cabrera888', 987987987, 'Calzada ', '2018-02-18 11:11:40', 2, 1),
(10, 2147483647, 'Roberto Morazan', 2147483647, 'Calzada a', '2018-03-04 19:17:22', 1, 1),
(11, 898798798, 'Rosa Pineda', 987998788, 'Plaza Sésamo', '2018-03-04 19:17:45', 1, 1),
(14, 655555, 'pavon', 12345678, 'monimbo', '2021-04-05 18:25:41', 1, 0),
(16, 2147483647, 'walter', 6166565, 'Granada, Nicaragua', '2021-05-04 14:22:01', 1, 1),
(17, 203031197, 'walter arias', 778888, 'Grandad,Gomper 1/2 C al oeste', '2021-05-14 00:11:29', 1, 1),
(21, 1212, 'ss', 515, '3', '2021-05-14 15:13:27', 1, 1),
(22, 1212, 'ss', 515, '3', '2021-05-14 15:15:03', 1, 1),
(23, 12121, '55', 52, 'sdsdsds', '2021-05-14 15:20:08', 1, 1),
(24, 12122, 'walter arias', 652621, 'granada', '2021-05-14 15:27:01', 1, 1),
(27, 555555, 'Coronavac', 2021, 'China', '2021-05-17 21:51:12', 1, 1),
(28, 555555, 'Coronavac', 2021, 'China', '2021-05-17 21:51:12', 1, 1),
(29, 1234567, '', 0, '', '2021-05-17 22:47:43', 1, 0),
(30, 1234567, '', 0, '', '2021-05-17 22:47:46', 1, 1),
(36, 999999999, 'Test02', 5595, '65', '2021-05-28 00:20:46', 1, 1),
(37, 2147483647, '1122', 9, '7', '2021-05-28 00:22:30', 1, 1),
(38, 2147483647, '1122', 9, '7', '2021-05-28 00:22:30', 1, 1),
(41, 12345679, '01walter', 1223333, 'sdsd', '2021-05-29 23:26:32', 1, 1),
(42, 12345679, '01walter', 1223333, 'sdsd', '2021-05-29 23:26:33', 1, 1),
(43, 12345679, '', 0, '', '2021-05-29 23:26:33', 1, 1),
(44, 12345679, '', 0, '', '2021-05-29 23:26:33', 1, 1),
(45, 2222, 'Walter José Arias Anton', 522556632, 'Granda , de la gomper 1/c al oeste', '2021-05-31 10:40:36', 1, 1),
(46, 2222, 'Walter José Arias Anton', 522556632, 'Granda , de la gomper 1/c al oeste', '2021-05-31 10:40:36', 1, 1),
(47, 555, 'sdsd', 899, 'dsfsdfsdfds', '2021-05-31 10:46:15', 1, 1),
(48, 555, 'sdsd', 899, 'dsfsdfsdfds', '2021-05-31 10:46:15', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `correlativo` bigint(11) NOT NULL,
  `nofactura` bigint(11) DEFAULT NULL,
  `codproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`correlativo`, `nofactura`, `codproducto`, `cantidad`, `precio_venta`) VALUES
(5, 7, 1, 1, '110.00'),
(6, 8, 1, 1, '110.00'),
(7, 9, 1, 1, '110.00'),
(8, 10, 1, 1, '110.00'),
(9, 11, 1, 1, '110.00'),
(10, 11, 2, 1, '1500.00'),
(11, 12, 4, 1, '10000.00'),
(12, 12, 2, 1, '1500.00'),
(14, 13, 2, 1, '1500.00'),
(15, 14, 7, 1, '2200.00'),
(16, 15, 3, 1, '250.00'),
(17, 16, 3, 1, '250.00'),
(18, 17, 1, 3, '110.00'),
(19, 17, 9, 2, '2.00'),
(21, 18, 9, 1, '2.00'),
(22, 19, 9, 1, '2.00'),
(23, 20, 10, 3, '20.36'),
(26, 22, 8, 1, '160.00'),
(27, 22, 9, 1, '2.00'),
(29, 23, 8, 5, '160.00'),
(30, 24, 1, 3, '110.00'),
(31, 25, 2, 1, '1500.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas`
--

CREATE TABLE `entradas` (
  `correlativo` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`correlativo`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(1, 1, '0000-00-00 00:00:00', 150, '110.00', 1),
(2, 2, '2018-04-05 00:12:15', 100, '1500.00', 1),
(3, 3, '2018-04-07 22:48:23', 200, '250.00', 9),
(4, 4, '2018-09-08 22:28:50', 50, '10000.00', 1),
(5, 5, '2018-09-08 22:34:38', 20, '500.00', 1),
(6, 6, '2018-09-08 22:35:27', 8, '2000.00', 1),
(7, 7, '2018-12-02 00:15:09', 75, '2200.00', 1),
(8, 8, '2018-12-02 00:39:42', 75, '160.00', 1),
(9, 5, '2021-04-05 17:10:11', 2, '20.00', 5),
(10, 5, '2021-04-05 17:11:13', 55, '20.00', 1),
(11, 1, '2021-04-13 20:31:54', 2, '20.00', 1),
(12, 1, '2021-04-22 22:14:27', 5, '400.00', 1),
(13, 1, '2021-04-22 22:15:04', 2, '89.00', 1),
(14, 9, '2021-04-23 13:55:22', 3, '2.00', 20),
(15, 10, '2021-05-20 13:02:56', 10, '20.36', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(7, '2021-05-29 00:04:23', 1, 3, '300.00', 1),
(8, '2021-05-29 00:06:33', 1, 3, '500.00', 1),
(9, '2021-05-29 00:06:37', 1, 3, '400.00', 1),
(10, '2021-05-29 00:06:43', 1, 3, '600.00', 1),
(11, '2021-05-29 00:10:01', 1, 3, '1610.00', 1),
(12, '2021-05-30 00:14:10', 1, 27, '11500.00', 1),
(13, '2021-05-30 00:16:45', 1, 1, '1500.00', 1),
(14, '2021-05-30 00:18:02', 1, 1, '2200.00', 1),
(15, '2021-05-30 21:26:15', 1, 1, '250.00', 1),
(16, '2021-05-30 21:27:33', 1, 1, '250.00', 1),
(17, '2021-05-30 23:10:23', 1, 27, '334.00', 1),
(18, '2021-05-30 23:12:52', 1, 1, '2.00', 1),
(19, '2021-05-30 23:14:19', 1, 27, '2.00', 1),
(20, '2021-05-30 23:24:10', 1, 1, '61.08', 1),
(22, '2021-05-31 10:42:26', 1, 45, '162.00', 2),
(23, '2021-05-31 13:19:15', 1, 41, '800.00', 2),
(24, '2021-06-01 16:54:00', 1, 1, '330.00', 2),
(25, '2021-06-02 23:59:20', 9, 41, '1500.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil`
--

CREATE TABLE `perfil` (
  `id` int(11) NOT NULL,
  `nombre_comercial` varchar(255) NOT NULL,
  `propietario` varchar(255) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `web` varchar(100) NOT NULL,
  `tax` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `perfil`
--

INSERT INTO `perfil` (`id`, `nombre_comercial`, `propietario`, `telefono`, `direccion`, `email`, `web`, `tax`) VALUES
(1, 'Super Gangas Conny', 'Conny Robleto', '8380-1088', 'Gancho camino 1 1/2c.Arriba ', 'franchezventa@gmail.com', 'Super gangas Conny', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio`, `existencia`, `date_add`, `usuario_id`, `estatus`) VALUES
(1, 'Ropero', 3, '110.00', 137, '2021-04-05 00:09:34', 1, 1),
(2, 'Ropero2', 3, '1500.00', 95, '2021-04-05 00:12:15', 1, 1),
(3, 'Teclado USB', 3, '250.00', 198, '2021-04-07 22:48:23', 9, 1),
(4, 'Cama', 3, '10000.00', 49, '2021-09-08 22:28:50', 1, 1),
(5, 'Plancha', 3, '500.00', 100, '2018-09-08 22:34:38', 1, 1),
(6, 'Monitor', 3, '2000.00', 8, '2018-09-08 22:35:27', 1, 1),
(7, 'Monitor LCD 17', 3, '2200.00', 74, '2018-12-02 00:15:09', 1, 1),
(8, 'Sofa', 3, '160.00', 69, '2018-12-02 00:39:42', 1, 1),
(9, 'Prueba:Ingresar', 3, '2.00', -2, '2021-04-23 13:55:22', 20, 1),
(10, 'Ropero Premium', 3, '20.36', 7, '2021-05-20 13:02:56', 1, 1);

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `entradas_A_I` AFTER INSERT ON `producto` FOR EACH ROW BEGIN
		INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) 
		VALUES(new.codproducto,new.existencia,new.precio,new.usuario_id);    
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `codproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` bigint(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `date_add`, `usuario_id`, `estatus`) VALUES
(3, 'Mubleria Conny', 'Benlly Vilchez', 98287748, 'Gancho de Camino 1/2 c al sur', '2018-03-24 23:21:10', 1, 1),
(12, 'Walter', 'Ing. Maria', 12345678, 'Managua', '2021-05-04 23:37:58', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `usuario` varchar(15) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`, `estatus`) VALUES
(1, 'Walter José Arias Anton', 'WjDeveloper@gmail.com', 'Admin', 'e10adc3949ba59abbe56e057f20f883e', 1, 1),
(2, 'Julio Estrada', 'julio@gmail.com', 'julio0222', '81dc9bdb52d04dc20036dbd8313ed055', 3, 0),
(9, 'María De los Ángeles  López Leyton', 'alan@gmail.com', 'vendedor', 'e10adc3949ba59abbe56e057f20f883e', 3, 1),
(20, 'José Gabriel Pavón', 'Supervisor@conny.com', 'Supervisor', 'e10adc3949ba59abbe56e057f20f883e', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`Cod_categoria`,`Nombre`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`),
  ADD KEY `nofactura` (`nofactura`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`);

--
-- Indices de la tabla `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codproveedor`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `Cod_categoria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`),
  ADD CONSTRAINT `detallefactura_ibfk_3` FOREIGN KEY (`nofactura`) REFERENCES `factura` (`nofactura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`codproducto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`codcliente`) REFERENCES `cliente` (`idcliente`),
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
