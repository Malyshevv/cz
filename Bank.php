<?php

namespace frontend\models;
use Yii;

class Bank extends \yii\db\ActiveRecord{


      public static function getBank() {

      	$query = "SELECT * FROM transaction_bank ORDER BY id";
      	$sql = Yii::$app->db->createCommand($query)->queryAll();

      	return $sql;

      }
}