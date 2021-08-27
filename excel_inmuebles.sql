SELECT
	cbi.cuenta_contable,
	cbi.descr,
	bi.fk_id_cat_tipo_inmueble 'tipo.id',
	cbi.descr 'tipo.descr',
	bi.fk_id_clasificacion_inmueble,
	cbid.descr,
	bi.folio,
	bi.escritura_convenio,	
	IF(bi.valor_capitalizable>0,bi.valor_capitalizable,bi.valor) 'valorHistorico',
	bi.valor,
	'0' AS 'depreciacionAcumulada',
	'0' AS 'valorAnterior',
	bi.id
	#COUNT(bi.id) 'cantidad'
FROM	
	bien_inmueble bi
LEFT JOIN
	cat_bien_inmueble cbi
ON
	bi.fk_id_cat_tipo_inmueble = cbi.id
LEFT JOIN
	cat_bien_inmueble cbid
ON
	bi.fk_id_clasificacion_inmueble = cbid.id	
WHERE
	bi.fk_id_empresa = 15
ORDER BY
	cbi.cuenta_contable