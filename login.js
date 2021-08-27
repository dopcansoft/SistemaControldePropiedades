$(document).ready(function(){
	
	$("#btn-login").click(function(event){
		if($('#Datos').parsley().validate()){
			event.preventDefault();
			var $trigger = $(this);
			var ajax_data = {
				"email":$("#txt-email").val(),
				"password":$("#txt-password").val(),
				"session":"ON"
			};

			$.ajax({
	            url:'src/services/login.php',
	            type:'POST',
	            data:ajax_data,
	            contentType:'application/x-www-form-urlencoded; charset=UTF-8',
	            dataType:'json', //json
	            beforeSend:function(){
	                $trigger.attr("disabled",true);
	                $trigger.html("<i class='fa fa-refresh fa-spin'></i>&nbsp;Espere ..."); 
	            },
	            success:function(data){
	            	if($.trim(data.result)=="SUCCESS"){
	            		window.location.href = data.link;
	            	}else if($.trim(data.result)=="FAIL"){
	            		new PNotify({
							title: 'Error',
							text: data.desc,
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
						$trigger.html("Iniciar sesión");
						$trigger.removeAttr("disabled",true); 		
					}else{
						new PNotify({
							title: 'Error',
							text: 'Error desconocido',
							type: 'error',
							hide: false,
							styling: 'bootstrap3',
						});
						$trigger.html("Iniciar sesión");
	            		$trigger.removeAttr("disabled",true); 		
	            	}	            	
	            },
	            error:function(obj,quepaso,otro){
					new PNotify({
						title: 'Error',
						text: quepaso,
						type: 'error',
						hide: false,
						styling: 'bootstrap3',
					});
	            	$trigger.removeAttr("disabled",true);
	            	$trigger.html("Iniciar sesión");
	            }
	      	});
		}else{
			new PNotify({
				title: 'Error',
				text: 'Debe especificar email/password',
				type: 'error',
				hide: false,
				styling: 'bootstrap3',
			});
		}
	});
});