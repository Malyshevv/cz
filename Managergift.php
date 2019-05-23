<?php

namespace frontend\models;
use Yii;

class Managergift extends \yii\db\ActiveRecord{


      public static function getGift() {

      	$query = "SELECT adress_user.id_gift as id_gift, user.username as name,
              adress_user.address as adress
      			  FROM adress_user
              LEFT JOIN user ON user.id = adress_user.id_user
      			  WHERE id_gift = 3";
      	$sql = Yii::$app->db->createCommand($query)->queryAll();

      	return $sql;

      }
}