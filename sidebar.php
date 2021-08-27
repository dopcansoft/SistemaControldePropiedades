<?
	//$log->debug($misesion->perfil->id." == ".Usuario::PERFIL_ADMIN);
    if(trim($misesion->perfil->id)==Usuario::PERFIL_ADMIN){
        include("menu_admin.php");
    }else if(trim($misesion->perfil->id)==Usuario::PERFIL_CAPTURISTA){
        include("menu_capturista.php");
    }else{
        
    }

?>