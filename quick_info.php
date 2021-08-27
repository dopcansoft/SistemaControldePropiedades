<div class="profile clearfix">
	<div class="profile_pic">
		<img src="<?=isset($misesion)&&$misesion->avatar!=null&&trim($misesion->avatar)!=''?$misesion->avatar:"images/user.png"?>" alt="..." class="img-circle profile_img">
	</div>
	<div class="profile_info">
		<span>Bienvenido</span>
		<h2><?=isset($misesion)&&$misesion->nombre!=null?$misesion->nombre:''?></h2>
	</div>
</div>