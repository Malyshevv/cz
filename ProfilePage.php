<?php

namespace frontend\models;
use Yii;

class ProfilePage extends \yii\db\ActiveRecord{

      public static function tableName()
      {
             return 'user'; // Имя таблицы в БД в которой хранятся записи блога
      }


      public static function getAll()
      {		
        
        $id = Yii::$app->user->identity->id;
        $data = self::find()->where('id = :id', [':id' => $id])->one();

        return $data;
      }


      public static function getGift() {

      	$id = Yii::$app->user->identity->id;

      	$query = "SELECT user_has_gift.gift_id as gift_id,user_has_gift.quantity as quantity, user_has_gift.send as send, gift.name as name, gift.img as img
      			  FROM user_has_gift

      			  LEFT JOIN gift ON gift.id =  user_has_gift.gift_id WHERE user_id = $id";
      	$sql = Yii::$app->db->createCommand($query)->queryAll();

      	return $sql;

      }
}