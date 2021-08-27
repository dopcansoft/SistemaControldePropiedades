<ul class="nav navbar-nav navbar-right">
  <li class="">
    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      <img src="<?=isset($misesion)&&$misesion->avatar!=null&&trim($misesion->avatar)!=''?$misesion->avatar:"images/user.png"?>" alt=""><?=isset($misesion)&&$misesion->email!=null?$misesion->email:''?>
      <span class=" fa fa-angle-down"></span>
    </a>
    <ul class="dropdown-menu dropdown-usermenu pull-right">
      <!-- <li><a href="javascript:;"> Profile</a></li>
      <li>
        <a href="javascript:;">
          <span class="badge bg-red pull-right">50%</span>
          <span>Settings</span>
        </a>
      </li>
      <li><a href="javascript:;">Help</a></li> -->
      <li><a href="src/services/logout.php"><i class="fa fa-sign-out pull-right"></i> Salir</a></li>
    </ul>
  </li>

  <li>
    <a href="javascript:;" class="user-profile dropdown-toggle" title="cliente" alt="cliente" data-toggle="dropdown" aria-expanded="false">
      <i class="fa fa-bank"></i> <?=isset($empresa)&&$empresa->id!=null&&$empresa->id!=""?$empresa->nombre:"Desconocido"?>
      <span class=" fa fa-angle-down"></span>
    </a>
    <ul class="dropdown-menu pull-right">
      <li>
        <a href="selempre.php">Cambiar</a>
      </li>
    </ul>   
  </li>
</ul>