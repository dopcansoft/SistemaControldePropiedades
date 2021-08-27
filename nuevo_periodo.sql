INSERT INTO bien_periodo(fk_id_bien, fk_id_periodo, fk_id_cat_estado_fisico, folio, consecutivo, depreciacion_acumulada, depreciacion_periodo, anios_uso, fk_id_cat_uma, valor_uma, inventario_contable, fecha_cierre, fk_id_cat_estatus_inventario, fecha_impresion, valuacion, fk_id_cat_tipo_valuacion, fk_id_responsable, fk_id_cat_color, valor_anterior, matricula, fecha_avaluo)
SELECT
	bp.fk_id_bien, 
	'13',
	bp.fk_id_cat_estado_fisico, 
	bp.folio, 
	bp.consecutivo, 
	bp.depreciacion_acumulada, 
	bp.depreciacion_periodo, 
	bp.anios_uso, 
	bp.fk_id_cat_uma, 
	bp.valor_uma, 
	bp.inventario_contable, 
	bp.fecha_cierre, 
	bp.fk_id_cat_estatus_inventario, 
	bp.fecha_impresion, 
	bp.valuacion, 
	bp.fk_id_cat_tipo_valuacion, 
	bp.fk_id_responsable, 
	bp.fk_id_cat_color, 
	bp.valor_anterior, 
	bp.matricula, 
	bp.fecha_avaluo
FROM
	bien b
LEFT JOIN
	bien_periodo bp
ON
	b.id = bp.fk_id_bien
WHERE
	b.fk_id_empresa = 11
	AND bp.fk_id_periodo = 8
	AND bp.fk_id_cat_estatus_inventario IN (1,2);