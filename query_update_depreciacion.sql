SELECT
	b.id, 
	bp.folio,
	PERIOD_DIFF('201905',DATE_FORMAT(b.fecha_adquisicion, '%Y%m')) 'meses', 
	#PERIOD_DIFF('201904', PERIOD_ADD(DATE_FORMAT(b.fecha_adquisicion, '%Y%m'),1)) 'meses',	
	PERIOD_DIFF('201905',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12 'veces_depreciacion', 
	cd.depreciacion_anual, b.valuacion,	
	DATE_FORMAT(b.fecha_adquisicion,'%Y%m') 'adquisicion',
	b.fk_id_cat_depreciacion,
	bp.depreciacion_acumulada,
	ROUND(((PERIOD_DIFF('201905',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2)
	'depreciacion_acumulada_calc',
	bp.depreciacion_periodo,
	CASE 
		WHEN b.fecha_adquisicion < '2019-01-01 00:00:00' THEN ROUND(((PERIOD_DIFF('201905','201901')/12)*(cd.depreciacion_anual/100))*b.valuacion,2)
		ELSE ROUND(((PERIOD_DIFF('201905',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2) END 'dep_per'
FROM 
	bien b
LEFT JOIN
	bien_periodo bp
ON
	b.id = bp.fk_id_bien
LEFT JOIN
	cat_uma cu
ON
	bp.fk_id_cat_uma = cu.id
LEFT JOIN
	cat_depreciacion cd
ON
	b.fk_id_cat_depreciacion = cd.id
WHERE
	b.valuacion>=(cu.valor_diario*cu.factor)
	AND b.fk_id_empresa = 7
	AND bp.fk_id_periodo = 4
	AND (b.fk_id_cat_depreciacion <> 47 AND b.fk_id_cat_depreciacion <> 0);
	

--  

UPDATE
	bien b
LEFT JOIN
	bien_periodo bp
ON
	b.id = bp.fk_id_bien
LEFT JOIN
	cat_uma cu
ON
	bp.fk_id_cat_uma = cu.id
LEFT JOIN
	cat_depreciacion cd
ON
	b.fk_id_cat_depreciacion = cd.id
SET
	bp.depreciacion_acumulada = ROUND(((PERIOD_DIFF('201910',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2),
	bp.depreciacion_periodo = 
	(CASE 
		WHEN b.fecha_adquisicion < '2019-01-01 00:00:00' THEN ROUND(((PERIOD_DIFF('201910','201901')/12)*(cd.depreciacion_anual/100))*b.valuacion,2)
		ELSE ROUND(((PERIOD_DIFF('201910',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2) END)	
WHERE
	b.valuacion>=(cu.valor_diario*cu.factor)
	AND b.fk_id_empresa = 7
	AND bp.fk_id_periodo = 4
	AND (b.fk_id_cat_depreciacion <> 47 AND b.fk_id_cat_depreciacion <> 0)




## NUEVO QUERY

SELECT
	b.id, 
	bp.folio,
	PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m')) 'meses', 
	#PERIOD_DIFF('201904', PERIOD_ADD(DATE_FORMAT(b.fecha_adquisicion, '%Y%m'),1)) 'meses',	
	PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12 'veces_depreciacion', 
	cd.depreciacion_anual, b.valuacion,	
	DATE_FORMAT(b.fecha_adquisicion,'%Y%m') 'adquisicion',
	b.fk_id_cat_depreciacion,
	bp.depreciacion_acumulada,
	#ROUND(((PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2)
	#'depreciacion_acumulada_calc',
	ROUND(PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))*((b.valuacion/cd.vida_util)/12),3)
	'depreciacion_acumulada_calc',
	bp.depreciacion_periodo,
	cd.vida_util,
	ROUND((b.valuacion/cd.vida_util)/12,2) 'factor_depreciacion',
	CASE
		WHEN b.fecha_adquisicion < '2019-01-01 00:00:00' THEN ROUND((b.valuacion/cd.vida_util),2)
		ELSE ROUND((b.valuacion/cd.vida_util)/12,2)*PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m')) END 'de_per_nvo',
	CASE 
		WHEN b.fecha_adquisicion < '2019-01-01 00:00:00' THEN ROUND(((cd.depreciacion_anual/100))*b.valuacion,2)
		ELSE ROUND(((PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2) END 'dep_per'
FROM 
	bien b
LEFT JOIN
	bien_periodo bp
ON
	b.id = bp.fk_id_bien
LEFT JOIN
	cat_uma cu
ON
	bp.fk_id_cat_uma = cu.id
LEFT JOIN
	cat_depreciacion cd
ON
	b.fk_id_cat_depreciacion = cd.id
WHERE
	b.valuacion>=(cu.valor_diario*cu.factor)
	AND b.fk_id_empresa = 12
	AND bp.fk_id_periodo = 9
	AND (b.fk_id_cat_depreciacion <> 47 AND b.fk_id_cat_depreciacion <> 0)
ORDER BY 
	bp.folio;
	
	
	
	
UPDATE
	bien b
LEFT JOIN
	bien_periodo bp
ON
	b.id = bp.fk_id_bien
LEFT JOIN
	cat_uma cu
ON
	bp.fk_id_cat_uma = cu.id
LEFT JOIN
	cat_depreciacion cd
ON
	b.fk_id_cat_depreciacion = cd.id
SET
	#bp.depreciacion_acumulada = ROUND(((PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2),
	bp.depreciacion_acumulada = ROUND(PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))*((b.valuacion/cd.vida_util)/12),2),
	#bp.depreciacion_periodo = 
	#(CASE 
	#	WHEN b.fecha_adquisicion < '2019-01-01 00:00:00' THEN ROUND(((cd.depreciacion_anual/100))*b.valuacion,2)
	#	ELSE ROUND(((PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m'))/12)*(cd.depreciacion_anual/100))*b.valuacion, 2) END)	
	bp.depreciacion_periodo = 
	(CASE	
	WHEN b.fecha_adquisicion < '2019-01-01 00:00:00' THEN ROUND((b.valuacion/cd.vida_util),2)
		ELSE ROUND((b.valuacion/cd.vida_util)/12,2)*PERIOD_DIFF('201912',DATE_FORMAT(b.fecha_adquisicion, '%Y%m')) END)
WHERE
	b.valuacion>=(cu.valor_diario*cu.factor)
	AND b.fk_id_empresa = 12
	AND bp.fk_id_periodo = 9
	AND (b.fk_id_cat_depreciacion <> 47 AND b.fk_id_cat_depreciacion <> 0);