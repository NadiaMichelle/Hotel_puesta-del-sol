-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2025 a las 18:04:57
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hotelito`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anticipos`
--

CREATE TABLE `anticipos` (
  `id` int(11) NOT NULL,
  `guest` varchar(100) DEFAULT NULL,
  `reserva_id` int(11) DEFAULT NULL,
  `entrada` date DEFAULT NULL,
  `salida` date DEFAULT NULL,
  `tipoHabitacion` varchar(100) DEFAULT NULL,
  `personas` int(11) DEFAULT NULL,
  `tarifa` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `anticipo` decimal(10,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `ticket` varchar(100) DEFAULT NULL,
  `tasa_cambio` decimal(10,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hora_impresion` datetime DEFAULT NULL,
  `selectMoneda` varchar(10) DEFAULT 'MXN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anticipos`
--

INSERT INTO `anticipos` (`id`, `guest`, `reserva_id`, `entrada`, `salida`, `tipoHabitacion`, `personas`, `tarifa`, `total`, `anticipo`, `saldo`, `metodo_pago`, `ticket`, `tasa_cambio`, `observaciones`, `fecha`, `created_at`, `hora_impresion`, `selectMoneda`) VALUES
(3, 'Emiliano', 6, '2025-05-01', '2025-05-03', '2', 6, 8000.00, 19040.00, 10587.50, 19040.00, 'Efectivo', 'ANT-20250604-0003', 19.25, '', '2025-06-04', '2025-06-04 17:36:00', '2025-06-04 11:36:30', 'USD');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `iva` decimal(5,2) DEFAULT 16.00,
  `ish` decimal(5,2) DEFAULT 3.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `iva`, `ish`) VALUES
(1, 16.00, 3.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guests`
--

CREATE TABLE `guests` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `nacionalidad` varchar(255) DEFAULT NULL,
  `calle` varchar(255) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `cp` varchar(20) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `rfc` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `auto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `guests`
--

INSERT INTO `guests` (`id`, `nombre`, `nacionalidad`, `calle`, `ciudad`, `estado`, `cp`, `telefono`, `rfc`, `email`, `auto`) VALUES
(1, 'Julian Almeida Diaz', 'Mexicana', 'Av Siempre Viva 123', 'Springfield', 'SP', '12345', '555-1234', 'ALDJ800101ABC', 'julian.a@example.com', NULL),
(2, 'Shirley R. Cartwright', 'Estadounidense', '1 Infinite Loop', 'Cupertino', 'CA', '95014', '555-5678', 'CART850202XYZ', 'shirley.c@example.com', NULL),
(3, 'Horváth Darda', 'Húngara', 'Santa Carolina XD', 'Budapest', 'Colima', '202246', '555-9988', 'HD381848EN1', 'h.darda@sample.net', NULL),
(4, 'Delia Pineda', 'Mexicana', 'Santa Carolina XD', 'Polaco', 'Colima', '28219', '555-9988', 'HD381848EN1', 'delia.aldaco.m@gmail.com', NULL),
(5, 'Valeria Elizabeth', 'Venezolana', 'Castillo perez', 'Manzanillo', 'Colima', '3422', '31433513', 'fdfdgfdefg12', 'valery23@gmail.com', NULL),
(6, 'Emiliano ', 'Venezolana', 'PORFAVOR', 'Polanco ', 'Mexico', '2344', '3143351365', 'fdfdgfdefg12', 'emy23@gmail.com', NULL),
(7, 'Sabina', 'Europea', 'Alavarez1244', 'Valencia', 'España', '2355', '314134666', 'SANC2394EB1', 'sabi23@gmail.com', NULL),
(8, 'Mauricio Chavez', 'Mexicana', 'Neptuno 16', 'Manzanillo', 'Colima', '', '3143312980', 'MACM1308ENQ10', 'mchavez15@gmail.com', 'AVANZA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantillas_temporada`
--

CREATE TABLE `plantillas_temporada` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plantillas_temporada`
--

INSERT INTO `plantillas_temporada` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`) VALUES
(3, 'Año Nuevo', '2025-01-01', '2025-01-04'),
(4, 'Fin de Enero', '2025-01-05', '2025-01-31'),
(5, 'Febrero', '2025-02-01', '2025-02-28'),
(6, 'Marzo (inicio)', '2025-03-01', '2025-03-14'),
(7, 'Marzo (media)', '2025-03-15', '2025-03-16'),
(8, 'Marzo (fin)', '2025-03-17', '2025-03-31'),
(9, 'Semana Santa previa', '2025-04-13', '2025-04-15'),
(10, 'Semana Santa alta', '2025-04-16', '2025-04-20'),
(11, 'Abril (fin)', '2025-04-21', '2025-04-30'),
(12, 'Mayo (feriado)', '2025-05-01', '2025-05-01'),
(13, 'Mayo (resto)', '2025-05-02', '2025-05-31'),
(14, 'Junio completo', '2025-06-01', '2025-06-30'),
(15, 'Julio (inicio)', '2025-07-01', '2025-07-12'),
(16, 'Julio (alta)', '2025-07-13', '2025-08-15'),
(17, 'Agosto (fin)', '2025-08-16', '2025-08-31'),
(18, 'Septiembre (inicio)', '2025-09-01', '2025-09-14'),
(19, 'Septiembre (feriado)', '2025-09-15', '2025-09-16'),
(20, 'Septiembre (fin)', '2025-09-17', '2025-09-30'),
(21, 'Octubre completo', '2025-10-01', '2025-10-30'),
(22, 'Noviembre (inicio)', '2025-11-01', '2025-11-03'),
(23, 'Noviembre (media)', '2025-11-04', '2025-11-15'),
(24, 'Noviembre (feriado)', '2025-11-16', '2025-11-17'),
(25, 'Noviembre (fin)', '2025-11-18', '2025-11-30'),
(26, 'Diciembre (inicio)', '2025-12-01', '2025-12-14'),
(27, 'Diciembre (alta)', '2025-12-15', '2025-12-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `resourceId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `color` varchar(20) DEFAULT '#FFD700',
  `guestId` int(11) DEFAULT NULL,
  `guestNameManual` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'RESERVACION_PREVIA',
  `rate` decimal(10,2) DEFAULT 0.00,
  `iva` decimal(5,2) DEFAULT 16.00,
  `ish` decimal(5,2) DEFAULT 3.00,
  `inapamDiscount` tinyint(1) DEFAULT 0,
  `inapamCredential` varchar(50) DEFAULT NULL,
  `inapamDiscountValue` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `anticipo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`anticipo`)),
  `pagosHotel` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pagosHotel`)),
  `pagosExtra` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pagosExtra`)),
  `verification` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`verification`)),
  `checkinGuests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checkinGuests`)),
  `checkinItems` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checkinItems`)),
  `receptionistName` varchar(255) DEFAULT NULL,
  `totalReserva` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `inapamDiscountType` varchar(20) DEFAULT 'porcentaje'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `resourceId`, `title`, `start_date`, `end_date`, `color`, `guestId`, `guestNameManual`, `status`, `rate`, `iva`, `ish`, `inapamDiscount`, `inapamCredential`, `inapamDiscountValue`, `notes`, `anticipo`, `pagosHotel`, `pagosExtra`, `verification`, `checkinGuests`, `checkinItems`, `receptionistName`, `totalReserva`, `created_at`, `updated_at`, `inapamDiscountType`) VALUES
(4, 1, 'Reserva Delia Pineda', '2025-05-02', '2025-05-05', '#000000', 4, 'Delia Pineda', 'CANCELADA', 5000.00, 16.00, 3.00, 1, '223820121', 10.00, '', '{\"monto\":\"\",\"metodo\":\"\",\"ticket\":\"\"}', '[{\"monto\":\"7425\",\"metodo\":\"Efectivo\",\"fecha\":\"2025-05-06\"}]', '[{\"monto\":\"1500\",\"metodo\":\"Transferencia\",\"clave\":\"46929\",\"autorizacion\":\"3554\",\"fecha\":\"2025-05-08\"}]', '{\"dateTime\":\"2025-04-29T11:59\",\"whatsAppVerified\":\"Si\",\"senderName\":\"Mariana\"}', '[]', '{\"loza\":{\"name\":\"Loza (Utensils)\",\"delivered\":false,\"price\":200},\"licuadora\":{\"name\":\"Licuadora (Blender)\",\"delivered\":false,\"price\":200},\"cafetera\":{\"name\":\"Cafetera (Coffee Maker)\",\"delivered\":false,\"price\":200},\"controltv\":{\"name\":\"Control TV\",\"delivered\":false,\"price\":0},\"controlaa\":{\"name\":\"Control AA\",\"delivered\":false,\"price\":0},\"toallashab\":{\"name\":\"Toallas Habitaci\\u00f3n\",\"delivered\":false,\"price\":0},\"toallasalb\":{\"name\":\"Toallas Alberca\",\"delivered\":false,\"price\":0}}', 'Ignacio', 17838.10, '2025-05-27 18:00:10', '2025-05-29 22:37:29', 'monto'),
(5, 2, 'Reserva Mauricio Chavez', '2025-05-29', '2025-05-30', '#ffd700', 8, 'Mauricio Chavez', 'RESERVACION_PREVIA', 8000.00, 16.00, 3.00, 1, '2y372392310', 50.00, '', NULL, '[{\"monto\":\"720.50\",\"metodo\":\"Credito\",\"fecha\":\"2025-05-14\"}]', '[{\"monto\":\"6000\",\"metodo\":\"Transferencia\",\"clave\":\"46929\",\"autorizacion\":\"3554\",\"fecha\":\"2025-05-15\"}]', '{\"dateTime\":\"2025-05-08T11:22\",\"whatsAppVerified\":\"Si\",\"senderName\":\"Andrea\"}', '[]', '{\"loza\":{\"name\":\"Loza (Utensils)\",\"delivered\":false,\"price\":200},\"licuadora\":{\"name\":\"Licuadora (Blender)\",\"delivered\":false,\"price\":200},\"cafetera\":{\"name\":\"Cafetera (Coffee Maker)\",\"delivered\":false,\"price\":200},\"controltv\":{\"name\":\"Control TV\",\"delivered\":false,\"price\":0},\"controlaa\":{\"name\":\"Control AA\",\"delivered\":false,\"price\":0},\"toallashab\":{\"name\":\"Toallas Habitaci\\u00f3n\",\"delivered\":false,\"price\":0},\"toallasalb\":{\"name\":\"Toallas Alberca\",\"delivered\":false,\"price\":0}}', 'Ignacio', 9460.50, '2025-05-28 15:21:17', '2025-06-04 17:34:40', 'monto'),
(6, 2, 'Reserva Emiliano', '2025-05-01', '2025-05-03', '#ffd700', 6, 'Emiliano', 'RESERVACION_PREVIA', 8000.00, 16.00, 3.00, 0, '', 0.00, '', NULL, '[]', '[]', '{\"dateTime\":\"\",\"whatsAppVerified\":\"No\",\"senderName\":\"\"}', '[]', '{\"loza\":{\"name\":\"Loza (Utensils)\",\"delivered\":false,\"price\":200},\"licuadora\":{\"name\":\"Licuadora (Blender)\",\"delivered\":false,\"price\":200},\"cafetera\":{\"name\":\"Cafetera (Coffee Maker)\",\"delivered\":false,\"price\":200},\"controltv\":{\"name\":\"Control TV\",\"delivered\":false,\"price\":0},\"controlaa\":{\"name\":\"Control AA\",\"delivered\":false,\"price\":0},\"toallashab\":{\"name\":\"Toallas Habitaci\\u00f3n\",\"delivered\":false,\"price\":0},\"toallasalb\":{\"name\":\"Toallas Alberca\",\"delivered\":false,\"price\":0}}', 'Ignacio', 19040.00, '2025-05-29 23:04:00', '2025-06-04 17:35:22', 'porcentaje'),
(7, 1, 'Reserva Sabina', '2025-05-05', '2025-05-09', '#ffd700', 7, 'Sabina', 'RESERVACION_PREVIA', 5000.00, 16.00, 3.00, 0, '', 0.00, '', '{\"monto\":\"\",\"metodo\":\"\",\"ticket\":\"\"}', '[]', '[]', '{\"dateTime\":\"\",\"whatsAppVerified\":\"No\",\"senderName\":\"\"}', '[]', '{\"loza\":{\"name\":\"Loza (Utensils)\",\"delivered\":false,\"price\":200},\"licuadora\":{\"name\":\"Licuadora (Blender)\",\"delivered\":false,\"price\":200},\"cafetera\":{\"name\":\"Cafetera (Coffee Maker)\",\"delivered\":false,\"price\":200},\"controltv\":{\"name\":\"Control TV\",\"delivered\":false,\"price\":0},\"controlaa\":{\"name\":\"Control AA\",\"delivered\":false,\"price\":0},\"toallashab\":{\"name\":\"Toallas Habitaci\\u00f3n\",\"delivered\":false,\"price\":0},\"toallasalb\":{\"name\":\"Toallas Alberca\",\"delivered\":false,\"price\":0}}', 'Ignacio', 23800.00, '2025-06-03 16:15:34', '2025-06-03 16:15:34', 'porcentaje');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rooms`
--

CREATE TABLE `rooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `number` varchar(50) NOT NULL,
  `beds` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `inapam` tinyint(1) DEFAULT 0,
  `status` varchar(50) DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rooms`
--

INSERT INTO `rooms` (`id`, `type`, `number`, `beds`, `capacity`, `price`, `inapam`, `status`) VALUES
(1, 'Bungalow', 'B2', 4, 6, 5000.00, 1, 'disponible'),
(2, 'SUITE', '404', 5, 10, 8000.00, 1, 'disponible'),
(3, 'Estandar', '403', 2, 4, 600.00, 0, 'disponible'),
(4, 'Estandar', '101', 5, 2, 2000.00, 1, 'disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas_habitacion`
--

CREATE TABLE `tarifas_habitacion` (
  `id` int(11) NOT NULL,
  `habitacion_id` int(10) UNSIGNED NOT NULL,
  `temporada_id` int(11) UNSIGNED NOT NULL,
  `tarifa` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `fullName` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `fullName`, `email`, `role`, `password_hash`) VALUES
(2, 'recepcion', 'Reception User', 'recepcion@example.com', 'receptionist', '$2y$10$L5fG0oP3qW9rS7uV1xZ0ie.D8fE6gH4iJ2kL9mN0oP1qR2sT3uV4'),
(3, 'nadia', 'Nadia admin', 'nnava1@ucol.mx', 'admin', '$2y$10$OmD9bfp/mv3QboLeADTXvOm8NaR8PVESYglwz5ThMdGWKEQSnN7na'),
(4, 'admin', 'Admin User', 'admin@hotel.com', 'admin', '$2y$10$YYxuiot1kEMx5IHBLEVMcOMbmR1iyNcyHdcPJartyqxm2iGOzWiI.'),
(5, 'Mariana', 'Mariana lepez', 'matiana@gmail.com', 'user', '$2y$10$zx0qjPtNa4zAJXBNL8tZk.tytN/jc37nD/AEllEOfO794yJtP2wMm'),
(6, 'SARAH', 'SARAH SOFIA', 'nnadiamichelle@gmail.com', 'admin', '$2y$10$qVRpG1K.Aszd8AMVKKVzb.fx03zGwW7U8EBhVBlC7eMT3j16gDfIW'),
(7, 'JACINTO', 'Juancito lepez', 'heriberto1@ucol.mx', 'admin', '$2y$10$MbuuKB.4Yo1j.LHD63xrJOh2xHHO1SIZ901vvRlstLc38VO7haBT2');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anticipos`
--
ALTER TABLE `anticipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plantillas_temporada`
--
ALTER TABLE `plantillas_temporada`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarifas_habitacion`
--
ALTER TABLE `tarifas_habitacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habitacion_id` (`habitacion_id`),
  ADD KEY `temporada_id` (`temporada_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anticipos`
--
ALTER TABLE `anticipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `guests`
--
ALTER TABLE `guests`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `plantillas_temporada`
--
ALTER TABLE `plantillas_temporada`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tarifas_habitacion`
--
ALTER TABLE `tarifas_habitacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tarifas_habitacion`
--
ALTER TABLE `tarifas_habitacion`
  ADD CONSTRAINT `tarifas_habitacion_ibfk_1` FOREIGN KEY (`habitacion_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `tarifas_habitacion_ibfk_2` FOREIGN KEY (`temporada_id`) REFERENCES `plantillas_temporada` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
