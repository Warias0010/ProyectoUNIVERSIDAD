-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2021 a las 02:20:42
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_precio_producto` (`n_cantidad` INT, `n_precio` DECIMAL(10,2), `codigo` INT)  BEGIN
DECLARE nueva_existencia int;
DECLARE nuevo_total decimal(10,2);
declare nuevo_precio decimal(10,2);

 DECLARE cant_actual int;
 DECLARE pre_actual decimal(10,2);
 
 DECLARE actual_existencia int;
 DECLARE actual_precio decimal(10,2);
 
 SELECT precio, existencia INTO actual_precio,actual_existencia FROM producto WHERE codproducto = codigo;
 SET nueva_existencia = actual_existencia + n_cantidad;
 SET nuevo_total = (actual_existencia * actual_precio) + (n_cantidad * n_precio);
 SET nuevo_precio = nuevo_total / nueva_existencia;
 
 
 UPDATE producto SET existencia = nueva_existencia, precio = nuevo_precio  WHERE codproducto = codigo;
  SELECT nueva_existencia, nuevo_precio;
  
  END$$

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
  `cod_categoria` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`cod_categoria`, `nombre`) VALUES
(1, 'Juegos de Sala'),
(2, 'Otros Muebles');

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
(21, 1212, 'ss', 515, '3', '2021-05-14 15:13:27', 1, 0),
(22, 1212, 'ss', 515, '3', '2021-05-14 15:15:03', 1, 1),
(23, 12121, '55', 52, 'sdsdsds', '2021-05-14 15:20:08', 1, 0),
(24, 12122, 'walter arias', 652621, 'granada', '2021-05-14 15:27:01', 1, 1),
(27, 555555, 'Coronavac', 2021, 'China', '2021-05-17 21:51:12', 1, 0),
(28, 555555, 'Coronavac', 2021, 'China', '2021-05-17 21:51:12', 1, 0),
(29, 1234567, '', 0, '', '2021-05-17 22:47:43', 1, 0),
(30, 1234567, '', 0, '', '2021-05-17 22:47:46', 1, 0),
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
(33, 44, 11, 1, '9857.14'),
(34, 44, 12, 1, '8500.00'),
(36, 45, 11, 1, '9857.14');

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
  `identrada` int(11) NOT NULL,
  `codproducto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `entradas`
--

INSERT INTO `entradas` (`identrada`, `codproducto`, `fecha`, `cantidad`, `precio`, `usuario_id`) VALUES
(23, 14, '2021-06-03 18:41:07', 10, '10500.00', 1),
(24, 17, '2021-06-03 21:52:12', 11, '9000.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `nofactura` bigint(11) NOT NULL,
  `metodopago` int(11) NOT NULL DEFAULT 1,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `totalfactura` decimal(10,2) DEFAULT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`nofactura`, `metodopago`, `fecha`, `usuario`, `codcliente`, `totalfactura`, `estatus`) VALUES
(44, 1, '2021-06-04 17:52:25', 1, 41, '18357.14', 1),
(45, 1, '2021-06-04 17:53:23', 1, 41, '9857.14', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `categoria` int(11) NOT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `existencia` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `categoria`, `precio`, `existencia`, `date_add`, `usuario_id`, `estatus`) VALUES
(11, 'Pieza Sevilla', 3, 1, '9857.14', 19, '2021-06-03 18:13:57', 1, 1),
(12, 'Pieza Porta Vaso', 3, 2, '8500.00', 9, '2021-06-03 18:17:37', 1, 1),
(13, 'Pieza SINAI', 3, 1, '8500.00', 1, '2021-06-03 18:27:51', 1, 1),
(14, 'Comedor 6', 3, 2, '10500.00', 10, '2021-06-03 18:41:07', 1, 1),
(17, 'Pieza Trenza Acustico 2 sillones', 3, 1, '9000.00', 11, '2021-06-03 21:52:12', 1, 1);

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `entrada_producto` AFTER INSERT ON `producto` FOR EACH ROW BEGIN 
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
(12, 'Walter', 'Ing. Maria', 12345678, 'Managua', '2021-05-04 23:37:58', 1, 0),
(13, 'Provedor0120', 'Prueba Ingresar', 87993565, 'Managua, Mercado Oriental', '2021-06-03 17:12:28', 1, 1);

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
-- Estructura de tabla para la tabla `salida_producto`
--

CREATE TABLE `salida_producto` (
  `Cod_salida` int(11) NOT NULL,
  `Id_usuario` int(11) NOT NULL,
  `producto` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `Decripcion` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipopago`
--

CREATE TABLE `tipopago` (
  `Cod_Tipo_pago` int(11) NOT NULL,
  `Nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipopago`
--

INSERT INTO `tipopago` (`Cod_Tipo_pago`, `Nombre`) VALUES
(1, 'Efectivo');

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
(9, 'María De los Ángeles  López Leyton', 'Mlopez@gmail.com', 'vendedor', 'e10adc3949ba59abbe56e057f20f883e', 3, 1),
(20, 'José Gabriel Pavón', 'Supervisor@conny.com', 'Supervisor', 'e10adc3949ba59abbe56e057f20f883e', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`cod_categoria`);

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
  ADD PRIMARY KEY (`identrada`),
  ADD KEY `codproducto` (`codproducto`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`nofactura`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `codcliente` (`codcliente`),
  ADD KEY `metodopago` (`metodopago`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `proveedor` (`proveedor`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `categoria` (`categoria`);

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
-- Indices de la tabla `salida_producto`
--
ALTER TABLE `salida_producto`
  ADD PRIMARY KEY (`Cod_salida`),
  ADD KEY `Id_usuario` (`Id_usuario`,`producto`),
  ADD KEY `producto` (`producto`);

--
-- Indices de la tabla `tipopago`
--
ALTER TABLE `tipopago`
  ADD PRIMARY KEY (`Cod_Tipo_pago`);

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
  MODIFY `cod_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `correlativo` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT de la tabla `entradas`
--
ALTER TABLE `entradas`
  MODIFY `identrada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `nofactura` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salida_producto`
--
ALTER TABLE `salida_producto`
  MODIFY `Cod_salida` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipopago`
--
ALTER TABLE `tipopago`
  MODIFY `Cod_Tipo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_ibfk_4` FOREIGN KEY (`metodopago`) REFERENCES `tipopago` (`Cod_Tipo_pago`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`proveedor`) REFERENCES `proveedor` (`codproveedor`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`categoria`) REFERENCES `categoria` (`Cod_categoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `proveedor_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `salida_producto`
--
ALTER TABLE `salida_producto`
  ADD CONSTRAINT `salida_producto_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `salida_producto_ibfk_2` FOREIGN KEY (`producto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `rol` (`idrol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
