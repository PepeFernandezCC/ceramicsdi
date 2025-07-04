-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 03-11-2021 a las 16:57:41
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wordpress`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_correos_oficial_customs_description`
--

DROP TABLE IF EXISTS `wp_correos_oficial_customs_description`;
CREATE TABLE IF NOT EXISTS `wp_correos_oficial_customs_description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(10) NOT NULL,
  `description` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `wp_correos_oficial_customs_description`
--

INSERT INTO `wp_correos_oficial_customs_description` (`id`, `code`, `description`) VALUES
(1, 300, 'Adornos de cerámica, estatuillas'),
(2, 301, 'Adornos para fiestas de Navidad'),
(3, 302, 'Agujas de coser, punto, imperdibles'),
(4, 303, 'Aparatos fotografia - Filtro para cámaras fotogr'),
(5, 304, 'Aparatos fotografia - Flashes'),
(6, 305, 'Aparatos fotografia - Lente para cámara'),
(7, 306, 'Aparatos de optica - Binoculares'),
(8, 307, 'Aparatos de optica - Gafas (de ver)'),
(9, 308, 'Aparatos de optica- Gafas de sol'),
(10, 309, 'Aparatos de optica - Monturas gafas'),
(11, 310, 'Aparatos de optica- cristales, lentes gafas'),
(12, 311, 'Aparatos de optica- Lentes de contacto'),
(13, 312, 'Aparatos laboratorio - microscopio'),
(14, 313, 'Aparatos medicoquirúrjicos- Audífono'),
(15, 314, 'Aparatos medicoquirurjicos- Marcapasos'),
(16, 315, 'Aparatos relojería - adornos, piedras'),
(17, 316, 'Aparatos relojería - Despertador'),
(18, 317, 'Aparatos relojeria - Pulsera para reloj'),
(19, 318, 'Aparatos relojeria - Reloj de pulsera, bolsillo'),
(20, 319, 'Aparatos relojería Reloj Muelles,tornillos'),
(21, 320, 'Aperitivos salados'),
(22, 321, 'Armas decorativas Sables, espadas, bayoneta'),
(23, 322, 'Articulos de Bisutería'),
(24, 323, 'Artículos de ceramica, porcelana'),
(25, 324, 'Artículos de cuero - bolso, mochila'),
(26, 325, 'Artículos de cuero - Cinturones, guantes'),
(27, 326, 'Artículos de metal - Cucharas, tenedores, cucharo'),
(28, 327, 'Artículos de metal - Cuchillas, abrecartas, raspa'),
(29, 328, 'Artículos de metal - Cuchillos, navajas'),
(30, 329, 'Artículos de orfebrería'),
(31, 330, 'Artículos de pesca - anzuelos, carretes'),
(32, 331, 'Artículos de piel Billetera, monedero, pitillera'),
(33, 332, 'Azúcar de caña o remolacha'),
(34, 333, 'Bebida - Vermú'),
(35, 334, 'Bebida alcohólicas - Ginebra'),
(36, 335, 'Bebidas - Agua'),
(37, 336, 'Bebidas - Cerveza'),
(38, 337, 'Bebidas - Sidra'),
(39, 338, 'Vinagre'),
(40, 339, 'Bebidas - Vino'),
(41, 340, 'Bebidas - Vino espumoso'),
(42, 341, 'Bebidas - Zumos de frutas alcohóicas'),
(43, 342, 'Bebidas alcohólicas - Vodka'),
(44, 343, 'Bebidas alcohólicas - licores'),
(45, 344, 'Bebidas alcohólicas - Whisky'),
(46, 345, 'Bebidas - Gin y Ginebra'),
(47, 346, 'Bebidas - Mosto'),
(48, 347, 'Bebidas - Ron y aguardiente de caña'),
(49, 348, 'Bebidas sin alcohol'),
(50, 349, 'Betunes, creamas para zapatos o cuero'),
(51, 350, 'Bolígrafos'),
(52, 351, 'Bolsos de mano de piel'),
(53, 352, 'Botones'),
(54, 353, 'Brochas de afeitar'),
(55, 354, 'Bulbos, cebollas de flores'),
(56, 355, 'Cacao en polvo'),
(57, 356, 'Café'),
(58, 357, 'Café descafeinado'),
(59, 358, 'Cajas, bolsas de papel'),
(60, 359, 'Calculadoras'),
(61, 360, 'Calendarios'),
(62, 361, 'Calzado de deporte'),
(63, 362, 'Candados, cerrojos, llaves'),
(64, 363, 'Caña de pescar'),
(65, 364, 'Cepillo para el pelo'),
(66, 365, 'Cepillos de dientes'),
(67, 366, 'Cereales - Arroz'),
(68, 367, 'Cereales - Copos de maiz y similiares'),
(69, 368, 'Cereales Avena'),
(70, 369, 'Cereales Maiz'),
(71, 370, 'Cereales Trigo'),
(72, 371, 'Chicles'),
(73, 372, 'Chocolate, cacao'),
(74, 373, 'Cierres, hebillas, ganchos, ojales de metal común'),
(75, 374, 'Comida - Conservas - Aceitunas'),
(76, 375, 'Comida - Conservas de pescado'),
(77, 376, 'Comida- Caviar y sus sucedáneos'),
(78, 377, 'Comida- Chorizo'),
(79, 378, 'Comida- Conservas de carne'),
(80, 379, 'Comida- Conservas de marisco y moluscos'),
(81, 380, 'Comida- Hígado de oca, pato, Foie grass'),
(82, 381, 'Comida- Jamón'),
(83, 382, 'Comida- Lomo'),
(84, 383, 'Comida- paletilla jamón'),
(85, 384, 'Comida- pata de Jamón'),
(86, 385, 'Comida- Salchichas'),
(87, 386, 'Comida- Salchichón'),
(88, 387, 'Compresas, tampones higiénicos'),
(89, 388, 'Conservas - esparrágos'),
(90, 389, 'Conservas de legumbres'),
(91, 390, 'Conservas de Setas, hongos, trufas'),
(92, 391, 'Conservas de tomate'),
(93, 392, 'Conservas de verduras'),
(94, 393, 'Cosméticos - champú, Gel'),
(95, 394, 'Cosméticos - dentífricos'),
(96, 395, 'Cosméticos - espuma afeitar'),
(97, 396, 'Cosméticos - lacas para el cabello'),
(98, 397, 'Cosméticos - maquillajes, bronceadores'),
(99, 398, 'Cosméticos - pintalabios'),
(100, 399, 'Cosméticos - sombras de ojos, polvos compactos'),
(101, 400, 'Cosméticos- Desodorantes corporales'),
(102, 401, 'Cremalleras'),
(103, 402, 'Cuadernos'),
(104, 403, 'Cuadernos para dibujar o colorear, para niños'),
(105, 404, 'Deportes - Balones de fútbol, balonmano, balonces'),
(106, 405, 'Deportes - Pelotas de Golf'),
(107, 406, 'Deportes - Pelotas de tenis'),
(108, 407, 'Deportes -Patines'),
(109, 408, 'Diccionarios y enciclopedias, fascículos'),
(110, 409, 'Dispositivos electricos - Auriculares'),
(111, 410, 'Dispositivos electricos - Depiladora electrica'),
(112, 411, 'Dispositivos electricos - Linterna'),
(113, 412, 'Dispositivos electricos - Maquina cortar pelo'),
(114, 413, 'Dispositivos electricos - Maquinilla afeitar'),
(115, 414, 'Dispositivos electricos - Radiocasete de bolsillo'),
(116, 415, 'Dispositivos electricos - Reproductor de CD'),
(117, 416, 'Dispositivos electricos - Reproductor de DVD'),
(118, 417, 'Dispositivos electricos - Tarjetascircuito electr'),
(119, 428, 'Dispositivos electricos - Teléfono móvil'),
(120, 419, 'Dispositivos electricos - Videocámara portatil'),
(121, 420, 'Dispositivos electricos - Walkman'),
(122, 421, 'Dispositivos electricos -Reproductor de MP3'),
(123, 423, 'Dispositivos electricos- Secador de pelo'),
(124, 423, 'Edredones, cojines, almohadas'),
(125, 424, 'Encendedores de bolsillo, de gas, no recargables'),
(126, 425, 'Especias Azafrán'),
(127, 426, 'Especias Cúrcuma'),
(128, 427, 'Especias Curri'),
(129, 428, 'Especias Jengibre'),
(130, 429, 'Especias Vainilla'),
(131, 430, 'Estampas, grabados y fotografías'),
(132, 431, 'Fechadores, selladores'),
(133, 432, 'Flores artificiales, plumas, pelucas, barbas, mech'),
(134, 433, 'Flores, capullos, adornos decorativos'),
(135, 434, 'Fruta deshidratada'),
(136, 435, 'Frutos secos almendras'),
(137, 436, 'Frutos secos anacardo'),
(138, 437, 'Frutos secos avellanas'),
(139, 438, 'Frutos secos Dátiles'),
(140, 439, 'Frutos secos Higos'),
(141, 440, 'Frutos secos Nueces'),
(142, 441, 'Frutos secos Pistachos'),
(143, 442, 'Frutos secos Uvas secas - pasas'),
(144, 443, 'Galletas, pastas, pasteles'),
(145, 444, 'Gasas, vendas'),
(146, 445, 'Gofres y obleas'),
(147, 446, 'Goma de Borrar'),
(148, 447, 'Grapas, clips'),
(149, 448, 'Guirnaldas electricas de Navidad'),
(150, 449, 'Harina'),
(151, 450, 'Herramientas - Destornilladores'),
(152, 451, 'Herramientas - Cepillos y herramientas para madera'),
(153, 452, 'Herramientas - limas, alicates, pinzas, cizallas'),
(154, 453, 'Herramientas- Martillos'),
(155, 454, 'Herramientas Llaves de ajuste manuales, llave ingl'),
(156, 455, 'Huevos'),
(157, 456, 'Impresos publicitarios, catálogos comerciales y s'),
(158, 457, 'Instrumento musical - Guitarra'),
(159, 458, 'Instrumento musical - Armónica'),
(160, 459, 'Instrumento musical - Cajas de Música'),
(161, 460, 'Instrumento musical - Clarinetes'),
(162, 461, 'Instrumento musical - piezas de instrumentos'),
(163, 462, 'Instrumento musical -Violin'),
(164, 463, 'Jabón en pastillas'),
(165, 464, 'Joyas - Artículos para joyería'),
(166, 465, 'Joyas - Diamantes'),
(167, 466, 'Joyas - Oro'),
(168, 467, 'Joyas - Plata'),
(169, 468, 'Joyas -Perlas'),
(170, 469, 'Juegos - pin pon'),
(171, 470, 'Juegos - Videojuegos'),
(172, 471, 'Juguetes Cartas de mesa, Naipes'),
(173, 472, 'Juguetes Muñecas'),
(174, 473, 'Juguetes Rompecabezas'),
(175, 474, 'Juguetes ropa y accesorios muñecos'),
(176, 475, 'Juguetes Videojuego'),
(177, 476, 'JuguetesTrenes, coches eléctricos'),
(178, 477, 'Lámpara eléctrica de mesa, oficina'),
(179, 478, 'Lámparas o aparatos eléctricos de alumbrado'),
(180, 479, 'Lápices portaminas'),
(181, 480, 'Leche'),
(182, 481, 'Leche concentrada, condensada'),
(183, 482, 'Leche en polvo'),
(184, 483, 'levadura en polvo para esponjar masas'),
(185, 484, 'Libros registro, libros de contabilidad, talonario'),
(186, 485, 'Libros, folletos, e impresos similares'),
(187, 486, 'Manteles y servilletas de papel'),
(188, 487, 'Mantequilla'),
(189, 488, 'Marcos de madera'),
(190, 489, 'Material cestería - mimbre, rafia'),
(191, 490, 'Medicamento - Antisueros'),
(192, 491, 'Medicamento - Cortisona'),
(193, 492, 'Medicamento - Insulina'),
(194, 493, 'Medicamento - Penicilinas'),
(195, 494, 'Medicamento - Vitaminas'),
(196, 495, 'Medicamento -Vacunas para animales'),
(197, 496, 'Medicamento -Vacunas para uso humano'),
(198, 497, 'Medicamentos'),
(199, 498, 'Medicamentos - Antibióticos'),
(200, 499, 'Mermeladas, Confituras, jaleas'),
(201, 500, 'Miel Natural'),
(202, 501, 'Monedas.'),
(203, 502, 'Muesli y similares'),
(204, 503, 'Objetos arte, colección - pinturas, dibujos a man'),
(205, 504, 'Objetos arte, colección - sellos, estampillas'),
(206, 505, 'Objetos arte, collección - Escultura de arte'),
(207, 506, 'Objetos arte, collección - Grabados, estampas, li'),
(208, 507, 'Objetos arte, collección- Piezas de colección'),
(209, 508, 'Pan y similares'),
(210, 509, 'Pañales de bebés'),
(211, 510, 'Pañuelos, toallitas de desmaquillar y toallas'),
(212, 511, 'Papel higiénico'),
(213, 512, 'Paraguas, sombrillas, quitasoles, fustas y latigos'),
(214, 513, 'Partes o accesorios de bicicletas o motos'),
(215, 514, 'Partes vehículos - Amortiguador vehiculos'),
(216, 515, 'Partes vehículos - Freno de coche'),
(217, 516, 'Partes vehículos - Volante coche'),
(218, 517, 'Partes vehículos -Cinturones de seguridad de coch'),
(219, 518, 'Pasta alimenticia, esapagueti, macarrones'),
(220, 519, 'Pegamentos, colas o adhesivos'),
(221, 520, 'Peines, peinetas'),
(222, 521, 'Películas fotográficas'),
(223, 522, 'Perchas de madera'),
(224, 523, 'Perfume, colonia'),
(225, 524, 'Piedras preciosas o semi - Cuarzo'),
(226, 525, 'Piedras preciosas o semi - Rubíes, zafiros y esme'),
(227, 526, 'Piedras preciosas o semipreciosas'),
(228, 527, 'Pinceles y brochas'),
(229, 528, 'Pintura artística'),
(230, 529, 'Pipas y cachimbas'),
(231, 530, 'Plantas, bulbos'),
(232, 531, 'Plástico - artículos de adorno'),
(233, 532, 'Plástico - Vajilla, servicio de mesa o de cocina'),
(234, 533, 'Plástico -Artículos de oficina y artículos esco'),
(235, 534, 'Portatiles hasta 10 kg'),
(236, 535, 'Pralinés'),
(237, 536, 'Preparados alimenticios para animales'),
(238, 537, 'Preparados alimenticios para bebés - cereales'),
(239, 538, 'Preservativos'),
(240, 539, 'Productos farmaceuticos- Apósitos'),
(241, 540, 'Productos farmaceuticos- Botiquines primeros auxil'),
(242, 541, 'Queso curado'),
(243, 542, 'Queso fresco, de untar'),
(244, 543, 'Raíces o tuberculos de Trufas frescas o refrigera'),
(245, 544, 'Ropa - Calzoncillos'),
(246, 545, 'Ropa - Camisas de punto hombre'),
(247, 546, 'Ropa - Camisas de punto mujer'),
(248, 547, 'Ropa Abrigos, chaquetones, capas, cazadoras'),
(249, 548, 'Ropa Albornoces de baño'),
(250, 549, 'Ropa Batas de casa'),
(251, 550, 'Ropa blanca de baño y de cocina'),
(252, 551, 'Ropa Bragas, saltos de cama'),
(253, 552, 'Ropa Camisetas'),
(254, 553, 'Ropa Camisones, pijamas'),
(255, 554, 'Ropa chales, pañuelos de cuello, bufandas, mantil'),
(256, 555, 'Ropa Combinaciones, enaguas'),
(257, 556, 'Ropa Corbatas o pajaritas'),
(258, 557, 'Ropa de cama, tejida o de ganchillo'),
(259, 558, 'Ropa deportiva (chandal, mono, ropa de esquí, ba'),
(260, 559, 'Ropa Guantes, mitones y manoplas'),
(261, 560, 'Ropa impermeables'),
(262, 561, 'Ropa Jerseys'),
(263, 562, 'Ropa leotardos, medias, calcetines'),
(264, 563, 'Ropa Mantelería, tejida o de ganchillo'),
(265, 564, 'Ropa para bebés'),
(266, 565, 'Ropa Sujetadores, fajas, corsés, tirantes, liguer'),
(267, 566, 'Ropa Trajes , conjuntos, chaquetas hombres'),
(268, 567, 'Ropa Trajes sastre, vestidos, faldas mujer'),
(269, 568, 'Sales perfumadas y demás preparaciones para el ba'),
(270, 569, 'Aceite de oliva'),
(271, 570, 'Semillas de hortalizas'),
(272, 571, 'Sobres de papel'),
(273, 572, 'Sombreros, tocados, redecillas para el pelo'),
(274, 573, 'Sopas y salsas preparadas'),
(275, 574, 'Suplementos alimenticios (multivitaminas/minerales'),
(276, 575, 'Tabaco cigarrillos'),
(277, 576, 'Tabaco puros'),
(278, 577, 'Tapones de corcho'),
(279, 578, 'Té negro'),
(280, 579, 'Té Verde'),
(281, 580, 'Textiles - algodón 581 Textiles - lana'),
(282, 582, 'Textiles - nailon'),
(283, 583, 'Textiles - poliésteres'),
(284, 584, 'Textiles - seda'),
(285, 585, 'Tornillos, pernos, tuercas'),
(286, 586, 'Velas, cirios'),
(287, 587, 'Verduras deshidratadas'),
(288, 588, 'Vidrio vasos, copas jarras'),
(289, 589, 'Discos de música'),
(290, 590, 'Discos de vinilo'),
(291, 591, "DVD\'s"),
(292, 592, 'Fertilizantes'),
(293, 593, 'Calzado con suela de caucho o plástico'),
(294, 594, 'Calzado con suela de cuero'),
(295, 595, 'Resto de calzado'),
(296, 596, 'Partes de calzado'),
(297, 597, 'Utensilios de cocina'),
(298, 598, 'Biberones o artículos de bebés'),
(299, 599, 'Tarjetas de crédito'),
(300, 999, 'Textil y calzado');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
