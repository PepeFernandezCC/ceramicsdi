-- Solo lectura. Pégalo en phpMyAdmin y mira si hay una fila con
-- Column_name = id_cart_rule_2 (y otra con id_cart_rule_1).
-- Si falta alguna, es el motivo de la lentitud.
SHOW INDEX FROM ps_cart_rule_combination;
