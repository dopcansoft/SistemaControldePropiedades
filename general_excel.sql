SELECT 	
			bn.id, 	 
			#bn.imagen, 
			bn.descripcion, 
			bn.marca, 
			bn.modelo, 
			bp.folio,
			bp.consecutivo,
			bn.serie, 
			bn.motor, 
			bn.factura, 
			bn.archivo_factura,						
			bn.notas,
			bn.fecha_adquisicion 'fechaAdquisicion', 
			bn.fk_id_cat_tipo_valuacion 'tipoValuacion.id',
			ctv.descr 'tipoValuacion.descr',
			bn.valuacion 'valor',
			bp.valor_anterior 'valorAnterior', 
			bn.fecha_insert 'fechaInsert',
			-- empresa
			#bn.fk_id_empresa 'empresa.id',	
			#e.nombre 'empresa.nombre',
			#e.descr 'empresa.descr',
			#e.direccion 'empresa.direccion',
			#e.logo_mpio 'empresa.logoMpio',
			#e.logo_ayuto 'empresa.logoAyuto',
			#e.logo_periodo 'empresa.logoPeriodo',
			#e.fk_id_cat_estatus 'empresa.status.id',
			-- tipoClasificacion
			bn.fk_id_cat_tipo_clasificacion_bien 'tipoClasificacion.id', 
			ctcb.descr 'tipoClasificacion.descr',		
			-- clasificacion
			bn.fk_id_clasificacion_bien 'clasificacion.id',	
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.descr WHEN 2 THEN cbi.descr ELSE 'Desconocido' END 'clasificacion.descr',	 
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.grupo ELSE '' END 'clasificacion.grupo',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.subgrupo ELSE '' END 'clasificacion.subgrupo',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.clase ELSE '' END 'clasificacion.clase',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN '' ELSE '' END 'clasificacion.subclase',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN '' ELSE cbi.consecutivo END 'clasificacion.consecutivo',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.cuenta_contable ELSE cbi.cuenta_contable END 'clasificacion.cuentaContable',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.cuenta_depreciacion ELSE '' END 'clasificacion.cuentaDepreciacion',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.cuenta_contable ELSE cbi.cuenta_contable END 'cuentaContable',
			CASE bn.fk_id_cat_tipo_clasificacion_bien WHEN 1 THEN cbm.cuenta_depreciacion ELSE '' END 'cuentaDepreciacion',
			-- departamento
			bn.fk_id_departamento 'departamento.id',
			d.descr 'departamento.descr',
			-- depreciacion
			bn.fk_id_cat_depreciacion 'depreciacion.id', 
			cd.cuenta 'depreciacion.cuenta',	
			cd.descr 'depreciacion.descr',
			cd.vida_util 'depreciacion.vidaUtil',
			cd.depreciacion_anual 'depreciacion.depreciacionAnual',
			cd.fecha_insert 'depreciacion.fechaInsert',
			-- origen
			bn.fk_id_cat_origen_fondo_adquisicion 'origen.id',
			cofa.descr 'origen.descr',
			bp.fk_id_periodo 'periodo.id',
			per.descr 'periodo.descr',
			per.fecha_inicio 'periodo.fechaInicio',
			per.fecha_fin 'periodo.fechaFin',
			per.fecha_insert 'periodo.fechaInsert',
			bp.fk_id_cat_estado_fisico 'estadoFisico.id',
			cef.descr 'estadoFisico.descr',
			bp.depreciacion_acumulada 'depreciacionAcumulada',
			bp.depreciacion_periodo 'depreciacionPeriodo',
			bp.anios_uso 'aniosUso',
			bp.fk_id_cat_uma 'uma.id',
			cu.anio 'uma.anio',
			cu.valor_diario 'uma.valorDiario',
			cu.valor_mensual 'uma.valorMensual',
			cu.valor_anual 'uma.valorAnual',
			cu.factor 'uma.factor',
			(cu.valor_diario*cu.factor) 'valorUma',
			#bp.valor_uma 'valorUma',
			bp.fk_id_cat_estatus_inventario 'estatusInventario.id',
			cei.descr 'estatusInventario.descr',
			bp.inventario_contable 'inventarioContable'
			#bp.fk_id_responsable 'responsable.id',
			#resp.titulo 'responsable.titulo',
			#resp.nombre 'responsable.nombre',
			#resp.apellido 'responsable.apellido',
			#resp.email 'responsable.email'
			-- imagenes 
			#GROUP_CONCAT(bi.path) 'images'
		FROM 
			bien bn
		LEFT JOIN
			bien_periodo bp
		ON
			bn.id = bp.fk_id_bien
		-- empresa
		LEFT JOIN
			empresa e
		ON
			bn.fk_id_empresa = e.id
		-- tipoClasificacion
		LEFT JOIN
			cat_tipo_clasificacion_bien ctcb
		ON
			bn.fk_id_cat_tipo_clasificacion_bien = ctcb.id
		-- tipoValuacion
		LEFT JOIN
			cat_tipo_valuacion ctv
		ON
			ctv.id = bn.fk_id_cat_tipo_valuacion
		-- bien_mueble
		LEFT JOIN
			cat_bien_mueble cbm
		ON
			cbm.id = bn.fk_id_clasificacion_bien
		-- bien_inmueble
		LEFT JOIN
			cat_bien_inmueble cbi
		ON
			cbi.id = bn.fk_id_clasificacion_bien	
		-- departamento
		LEFT JOIN
			departamento d
		ON
			bn.fk_id_departamento = d.id
		-- depreciacion
		LEFT JOIN
			cat_depreciacion cd
		ON
			bn.fk_id_cat_depreciacion = cd.id
		-- origen
		LEFT JOIN
			cat_origen_fondo_adquisicion cofa
		ON
			bn.fk_id_cat_origen_fondo_adquisicion = cofa.id	
		LEFT JOIN
			periodo per
		ON
			per.id = bp.fk_id_periodo
		LEFT JOIN
			cat_estado_fisico cef
		ON
			cef.id = bp.fk_id_cat_estado_fisico
		LEFT JOIN
			cat_uma cu
		ON
			cu.id = bp.fk_id_cat_uma
		LEFT JOIN
			cat_estatus_inventario cei
		ON
			bp.fk_id_cat_estatus_inventario = cei.id
		LEFT JOIN
			bien_imagen bi
		ON
			bn.id = bi.fk_id_bien
		LEFT JOIN
			responsable resp
		ON
			bp.fk_id_responsable = resp.id
		WHERE
			bn.fk_id_empresa = 13
			AND bp.fk_id_periodo = 10
			#AND IF(LENGTH(TRIM(:tipo_inventario))>0 AND :tipo_inventario = 'CONTABLE', bn.valuacion >= (cu.valor_diario*cu.factor), TRUE)
			#AND IF(LENGTH(TRIM(:tipo_inventario))>0 AND :tipo_inventario = 'UTILITARIO', bn.valuacion <![CDATA[ < ]]> (cu.valor_diario*cu.factor), TRUE)
			#AND IF(LENGTH(TRIM(:fecha_inicio))>0 <![CDATA[ && ]]> LENGTH(TRIM(:fecha_fin))>0 <![CDATA[ && ]]> TRIM(:tipo_fecha) = 'FECHA_INSERT', bn.fecha_insert BETWEEN :fecha_inicio AND :fecha_fin, true)
			#AND IF(LENGTH(TRIM(:fecha_inicio))>0 <![CDATA[ && ]]> LENGTH(TRIM(:fecha_fin))>0 <![CDATA[ && ]]> TRIM(:tipo_fecha) = 'FECHA_ADQUISICION', bn.fecha_adquisicion BETWEEN :fecha_inicio AND :fecha_fin, true)
			#AND IF(LENGTH(TRIM(:clasificacion_bi))>0, cbi.id = :clasificacion_bi, true)
			#AND IF(LENGTH(TRIM(:clasificacion_bm))>0, cbm.id = :clasificacion_bm, true)
			#AND IF(LENGTH(TRIM(:fk_id_departamento))>0, bn.fk_id_departamento = :fk_id_departamento, true)
			#AND IF(LENGTH(TRIM(:fk_id_cat_estado_fisico))>0, bp.fk_id_cat_estado_fisico  = :fk_id_cat_estado_fisico, true)
			#AND IF(LENGTH(TRIM(:fk_id_cat_estatus_inventario))>0, FIND_IN_SET(bp.fk_id_cat_estatus_inventario, :fk_id_cat_estatus_inventario), true)
		GROUP BY
			bn.id
		ORDER BY
			cbm.grupo, cbm.subgrupo, cbm.clase, d.clave, bp.consecutivo, bn.fecha_insert