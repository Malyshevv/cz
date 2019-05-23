<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>


<div class="site-index">

    <div class="jumbotron">
      <?php   if (Yii::$app->user->isGuest) { ?>
        <h1>Welcome!</h1>

        <p class="lead">Hi,on this site you can get gift .</p>

        <p><a class="btn btn-lg btn-success" href="./signup">Signup NOW!</a></p>
       <?php } else { ?>
       	<h1>Try your luck NOW!</h1>
       	<span class="gift_btn btn btn-danger">CLICK!</span>
       <?php } ?>
    </div>

    <div class="body-content">

        <div class="row">
            <?php   if (Yii::$app->user->isGuest) { ?>

           	<?php } else { ?>
           		
				<div id="gift">
					
				</div>
				<center><img src="https://kadonation.com/assets/images/loading.gif" alt="" style="display: none;" id="loading"></center>
			<?php } ?>
        </div>

    </div>
</div>


      
<?php

$script = <<< JS

$(function() {
	
	

	function randomInteger(min, max) {
	  var rand = min + Math.random() * (max - min)
	  rand = Math.round(rand);
	  return rand;
	}

    $("body").on("click", '.gift_btn',function() {
      	
      	$('.jumbotron').fadeOut('fast');
      	$('#loading').fadeIn('fast');

		$('#gift').empty();

    	var gift =  randomInteger(1, 3);

	    $.ajax({
	        type:'post',
	        url: './giftajax',
	        data: {'gift_id': gift},

	        success:function(data){
	        	$('#loading').fadeOut('fast');
	            $('#gift').append(data);
	            console.log(gift);
	        }
	    }); 

    });

    $("body").on("click", '.GetFinalGift',function() {
		
		var id_gift = localStorage.getItem('id_gift');
		var quantity = localStorage.getItem('quantity');
		console.log(id_gift);
		console.log(quantity);
		 $.ajax({
	        type:'post',
	        url: './sendusergift',
	        data: {'id_gift': id_gift, 'quantity': quantity},

	        success:function(data){
				localStorage.clear();
				alert(data);
				location.reload();
	        }
	    }); 

	});

	
	$("body").on("click", '.sedngiftwow',function() {
		
		var id_gift = localStorage.getItem('id_gift');
		var adress = document.getElementById('location').value;

		console.log(adress);
		 $.ajax({
	        type:'post',
	        url: './sendusergiftwow',
	        data: {'id_gift': id_gift, 'adress': adress},

	        success:function(data){
				localStorage.clear();
				alert(data);
				location.reload();
	        }
	    });  

	});


});



JS;
$this->registerJs($script);


?>