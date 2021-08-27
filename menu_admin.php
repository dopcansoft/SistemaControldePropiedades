<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>General</h3>
        <ul class="nav side-menu">
            <li>
                <a><i class="fa fa-bell"></i> Empresas <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="empresas.php">Catálogo</a></li>
                    <li><a href="empresa.php">Nueva</a></li>
                </ul>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Bienes Inmuebles <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="inmuebles.php">Catálogo</a></li>
                    <li><a href="fichasinmuebles.php">Fichas Tenicas</a></li>
                    <li><a href="inmueble.php">Agregar</a></li>
                </ul>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Bienes Muebles <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="inventario.php">Catálogo</a></li>
                    <li><a href="bien.php">Agregar</a></li>
                </ul>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Conciliación Física<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="historico.php">Historico</a></li>
                    <li><a href="conciliacion.php">Conciliación</a></li>
                </ul>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Reportes <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="cedulas.php">Cédulas de Registro</a></li>
                    <?
                    if(trim($misesion->id)!=9){
                        ?>
                        <li><a href="fichas_cuenta.php">Cédulas por Clasificación Armonizada (Contable)</a></li>
                        <?    
                    }
                    ?>
                    <!-- <li><a href="fichas_cuenta_instrumental.php">Cédulas por Clasificación Armonizada (Instrumental)</a></li> -->
                    <li><a href="reporte_cuenta.php"><!-- Resumen por Cuenta Contable--> Balanza Contable (Bienes Muebles)</a></li>
                    <li><a href="reporte_cuenta_inmuebles.php"><!-- Resumen por Cuenta Contable--> Balanza Contable (Bienes Inmuebles)</a></li>
                    <li><a href="reporte_cuenta_dep.php">Balanza Contable con Depreciación</a></li>
                    <li><a href="reporte_cuenta_instrumental.php"><!-- Resumen por Cuenta Contable Instrumental --> Balanza (Bienes de Control Interno)</a></li>
                    <li><a href="balanzares.php"> Balanza General por Objeto de Gasto </a></li>
                    <li><a href="resumenca.php"><!-- Reporte por Clasificación Armonizada--> Reporte General por Objeto de Gasto </a></li>

                    <li><a href="resumencc_cont.php"><!-- Reporte por Cuenta Contable--> Auxiliar Contable </a></li>
                    <li><a href="resumencc_cont_dep.php">Auxiliar Contable con Depreciación</a></li>
                    <li><a href="resumencc_cont_dep_xls.php">Auxiliar Contable con Depreciación (Resumen)</a></li>
                    <li><a href="resumencc_util.php"><!-- Reporte por Cuenta Contable (Instrumental)--> Auxiliar (Bienes de Control Interno)</a></li>
                    <li><a href="auxiliar_inmuebles.php">Auxiliar (Bienes Inmuebles)</a></li>
                    <li><a href="resguardos.php">Resguardos por Departamento</a></li>
                    <li><a href="repfechas.php">Reporte por Fechas</a></li>
                    <li><a href="resumenbajas.php">Reporte de Bajas</a></li>
                    <li><a href="evidfot.php?departamento=146">Evidencia Fotográfica</a></li>                    
                </ul>
            </li>
            <li>
            	<a href="selestatus.php"><i class="fa fa-exchange"></i> Altas/Bajas </a>
            </li>
            <li>
            	<a href="inventariobajas.php"><i class="fa fa-database"></i> Listado Bajas </a>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Bienes Armonizados <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="editclasificacion.php">Disponibilidad</a></li>
                </ul>
            </li>
            <li>
                <a><i class="fa fa-bell"></i> Comisión de Hacienda <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="planilla.php">Integrantes</a></li>                    
                </ul>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Departamentos <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="departamentos.php">Catálogo</a></li>
                    <li><a href="departamento.php">Nuevo</a></li>
                </ul>
            </li>
            <li>
                <a><i class="fa fa-cog"></i> Responsables <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="responsables.php">Catálogo</a></li>
                    <li><a href="responsable.php">Nuevo</a></li>
                </ul>
            </li>
            <li>
            	<a href="cat_bandera.php"><i class="fa fa-database"></i> Banderas </a>
            </li>
            <li>
                <a><i class="fa fa-user"></i> Sistema <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="report_auxiliar_especial.php">Reporte Especial</a></li>
                    <li><a href="auxiliar_especial_gasto.php">Auxiliar Especial Gasto</a></li>
                    <li><a href="auxiliar_especial_gasto_nocontable.php">Auxiliar Especial Gasto (No contable)</a></li>
                    <li><a href="auxiliar_especial_baja.php">Auxiliar Especial Baja</a></li>
                    <li><a href="auxiliar_especial_baja_nocontable.php">Auxiliar Especial Baja (No contable)</a></li>
                    <li><a href="auxiliar_especial_baja_ej.php">Auxiliar Especial Bajas del Ejercicio</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="usuario.php">Nuevo</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="menu_section">
    </div>
</div>