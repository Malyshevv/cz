<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Bank';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php  if (Yii::$app->user->isGuest) { echo "Доступ запрещен!"; } else { ?>



<div class="site-about">
    <h1 style="display: none;"><?= Html::encode($this->title) ?></h1>
	<div class="container" style="overflow: hidden;width: 100%;">
	
	<div class="row">
		
	<hr>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th>User Name</th>
				<th>Money</th>
			</tr>
		</thead>
		<tbody>
			<?php

			
			foreach ($bankAll as $bank) {
				print '
			<tr>
				<td>'.$bank['name'].'</td>
				<td>'.$bank['money'].'</td>
			</tr>
				';
			}
			?>
			
		</tbody>
	</table>

	</div>
</div>
<?php } ?>


