<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class HelloController extends Controller
{

	public $count;

    public function actionIndex($count)
    {

    	$sql = "SELECT * FROM user_has_gift WHERE send = 0 ORDER BY RAND() LIMIT $count";
    	$res = Yii::$app->db->createCommand($sql)->queryAll();

    	foreach ($res as $keyRes) {
    		$id = $keyRes['id'];
    		$id_gift = $keyRes['gift_id'];
    		$id_user = $keyRes['user_id'];
    		$quantity = $keyRes['quantity'];

    		Yii::$app->db->createCommand("UPDATE user_has_gift SET send = '1' WHERE id = $id")->execute();

    		$query = Yii::$app->db->createCommand("SELECT * FROM user WHERE id = $id_user")->queryOne();

    		$cash = $query['cash'];
    		$points = $query['Points'];

    		if($id_gift == 1 || $id_gift == 2) {
    			if($id_gift == 1) {
					Yii::$app->db->createCommand("UPDATE user SET cash = $cash+$quantity WHERE id = $id_user")->execute();
    			}
    			if($id_gift == 2) {
					Yii::$app->db->createCommand("UPDATE user SET points = $points+$quantity WHERE id = $id_user")->execute();
    			}
    		}
    		
    		Yii::$app->db->createCommand("UPDATE user_has_gift SET send = '1' WHERE id = $id")->execute();
    		
    		
    		
    	}

        #echo $this->message . "\n";
        echo '==================== Готово! ====================';
    }
}
