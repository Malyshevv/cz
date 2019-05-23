<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php  if (Yii::$app->user->isGuest) { echo "Доступ запрещен!"; } else { ?>

<!-- Modal -->

<div class="modal fade" id="exampleModalBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
	    <div class="modal-header">
	      <h5 class="modal-title" id="exampleModalLabel">Withdraw money on visa/MasterCard</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        <span aria-hidden="true">&times;</span>
	      </button>
	    </div>
	    <div class="modal-body">
			<form>
				<p>Your can  withdraw money: <?=$getAll['cash']?> $</p>
				<input class="form-control"  type="text" value="" id="bank_send" name="bank_send" placeholder="Enter how much withdraw" required>
				<br>
				<span class="bank_send_btn btn btn-info">Send!</span>
			</form>
		</div>
		 <div class="modal-footer">
		    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		  </div>
	  </div>
	</div>
</div>

<div class="modal fade" id="exampleModalConvert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
	    <div class="modal-header">
	      <h5 class="modal-title" id="exampleModalLabel">Convert Money to Points / Convert Points to Money</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        <span aria-hidden="true">&times;</span>
	      </button>
	    </div>
	    <div class="modal-body">
			<form>
				<p><input type="checkbox" name="cash" balance="<?=$getAll['cash']?> "> I want convert money to points: <?=$getAll['cash']?> $</p>
				<p><input type="checkbox" name="Points" balance="<?=$getAll['Points']?>"> I want  convert ponts to money: <?=$getAll['Points']?> p.</p>

				<input type="text" class="form-control" id="convert" name="convert" placeholder="Enter how much withdraw" required>
				<br>
				
				<p>You get on your blanace: <span id="your-get"><b></b></span></p>
				
				<br>
				<span class="send_money btn btn-info" id="">Send!</span>
			</form>
		</div>
		 <div class="modal-footer">
		    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		  </div>
	  </div>
	</div>
</div>

<div class="site-about">
    <h1 style="display: none;"><?= Html::encode($this->title) ?></h1>
	<div class="container" style="overflow: hidden;width: 100%;">
	
	<div class="row">
		
		<img src="<?=$getAll['avatar']?>" style="width: 150px;margin-right: 10px;" align="left">	
		<h2><?=$getAll['username']?></h2>
		<p>User type: <?=$getAll['type']?></p>
		<?php
			if($getAll['type'] == 'admin') {
				print '<a href="./managergift" class="btn btn-primary" style="float: right;">Manager WOW Gift</a>  
				<a href="./bank" class="btn btn-primary" style="float: right; margin-right: 15px;">Bank</a> 
				';
			}
		?>
		<p>Total gifts: <?=count($getGift);?></p>
		<p>Money: <?=$getAll['cash']?> $ <span style="color: blue; cursor: pointer;" data-toggle="modal" data-target="#exampleModalBank"><u><i>Withdraw money on visa/MasterCard</i></u></span>
		<span style="color: orange; cursor: pointer;" data-toggle="modal" data-target="#exampleModalConvert"><u><i>Convert to points</i></u></span>
		</p>
		<p>Ponts: <?=$getAll['Points']?> p. <span style="color: blue; cursor: pointer;" data-toggle="modal" data-target="#exampleModalConvert"><u><i>Convert to money</i></u></p>
	</div>
	<hr>
	
	<?php
		if(!empty($getGift)) {
		
			foreach ($getGift as $keyGift) {
			

				if($keyGift['send'] == 0) {
					$stauts = '<b style="color:red;">waitng send..</b>';
				}
				if($keyGift['send'] == 1) {
					$stauts = '<b style="color:green;">Received</b>';
				}

					print '<div class="alert alert-info" style="font-size:17pt;"><img src="'.$keyGift['img'].'" width="40" align="left">'.$keyGift['name'].'('.$keyGift['quantity'].') - Status ('.$stauts.')</div>';	
				

			
			}
		
		}
		else {
			echo "<i>Gifts have not yet been received</i>";
		}
		
	?>

	</div>
</div>
<?php } ?>


<?php
$script = <<< JS


$('input[type="checkbox"]').on('change', function() {
   $('input[type="checkbox"]').not(this).prop('checked', false);
	
	$('#convert').empty();
	$('#your-get').empty();

	var name = $(this).attr('name');
	var balance = $(this).attr('balance');
	
	if(name == 'cash') {
	
		$('#convert').on('keyup',function(){
			var val = $(this).val();
		 	var sum = val * 10;
		 	$('#your-get').empty();
		 	$('#your-get').append(sum);
			$('.send_money').attr('id','cash-convert');
			
			$('body').on('click','#cash-convert',function(){

				$.ajax({
			        type:'post',
			        url: './convertmoney',
			        data: {'sum_money': sum, 'val_money': val},

			        success:function(data){
			        	console.log(data);
						localStorage.clear();
						document.location.reload(true);
			        }
	   			}); 
			});
			

		});

	}
	if(name == 'Points') {
		$('#convert').on('keyup',function(){
			var val = $(this).val();
		 	var sum = val * 0.10;
		 	$('#your-get').empty();
		 	$('#your-get').append(sum);

		 	$('.send_money').attr('id','point-convert');
			
			$('body').on('click','#point-convert', function(){
				$.ajax({
			        type:'post',
			        url: './convertpoint',
			        data: {'sum_point': sum, 'val_point': val},

			        success:function(data){
			        	console.log(data);
					
						document.location.reload(true);
			        }
		   		 }); 
			});


		});

		
	}

});
			


$('body').on('click','.bank_send_btn', function(){
	
	var bank_money = $('#bank_send').val();

	$.ajax({
		type:'post',
		url: './banksend',
		data: {'bank_money': bank_money },

		success:function(data){
			console.log(data);
			alert(data);
			document.location.reload(true);
		}
	}); 
});
			

JS;
$this->registerJs($script);
?>


