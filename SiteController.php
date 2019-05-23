<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\ProfilePage;
use frontend\models\Managergift;
use frontend\models\Bank;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }


    public function actionProfile() {
    	
    	$getAll = ProfilePage::getAll();
    	$getGift = ProfilePage::getGift();


    	return $this->render('profile',[
            'getAll' => $getAll,
            'getGift' => $getGift,
        ]);
    }


    public function actionGetcoin() {
    	$rnd_quantity_get_coint = rand(1,1000);
    	$select = "SELECT * FROM gift WHERE id = 2";
    	$res = Yii::$app->db->createCommand($select)->queryOne();

    	$resName = $res['name'];
    	$resImg = $res['img'];

    	return '
    		<script>
    			localStorage.clear();
    			localStorage.setItem("id_gift", "'.$res['id'].'");
    			localStorage.setItem("quantity", "'.$rnd_quantity_get_coint.'");
    		</script>
	    	<center>
	    		<h1>Congratulations, you get free </h1><br> <br>
	    		<img style="width: 150px;" src="'.$resImg.'"> 
	    		<br><br>
	    		<h3>'.$resName.' ('.$rnd_quantity_get_coint.')</h3><br>
				<span class="GetFinalGift btn btn-info">Get gift</span>
				<span class="gift_btn btn btn-danger">Refuse and try luck again</span>
	    	</center>
    	';    	
    }

    public function actionGiftajax() {
    	if(\Yii::$app->request->isAjax) {
    		if(isset($_POST['gift_id'])) {
    			$id_user = Yii::$app->user->identity->id;
    			$gift_id = (int)$_POST['gift_id'];

    			$sql = "SELECT * FROM gift WHERE id = $gift_id";
    			$query = Yii::$app->db->createCommand($sql)->queryOne();

    			$quantity = (int)$query['quantity'];

    			if($quantity == 0 && $gift_id == 2) {
    				return $this->actionGetcoin();
    			}
    			else {
    				if($quantity != 0 && $gift_id == 1) {
    					$rnd_quantity_money = rand(1,30);

	    				$select = "SELECT * FROM gift WHERE id = $gift_id";
	    				$res = Yii::$app->db->createCommand($select)->queryOne();

	    				$resName = $res['name'];
	    				$resImg = $res['img'];

	    				return '
					    	<script>
				    			localStorage.clear();
				    			localStorage.setItem("id_gift", "'.$res['id'].'");
				    			localStorage.setItem("quantity", "'.$rnd_quantity_money.'");
				    		</script>

		    				<center>
		    					<h1>Congratulations, you get free </h1><br> <br>
		    					<img style="width: 150px;" src="'.$resImg.'"> 
		    					<br><br>
		    					<h3>'.$resName.' ('.$rnd_quantity_money.' $)</h3><br>
								<span class="GetFinalGift btn btn-info">Get gift</span>
								<span class="gift_btn btn btn-danger">Refuse and try luck again</span>
		    				</center>
	    				';

    				}
    				else if($quantity != 0 && $gift_id == 3){

	    				$select = "SELECT * FROM gift WHERE id = $gift_id";
	    				$res = Yii::$app->db->createCommand($select)->queryOne();

	    				$resName = $res['name'];
	    				$resImg = $res['img'];

	    				return '

							<!-- Modal -->
							<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog" role="document">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h5 class="modal-title" id="exampleModalLabel">WOW GIFT!!!!</h5>
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							          <span aria-hidden="true">&times;</span>
							        </button>
							      </div>
							      <div class="modal-body">
							      <center>
							       	<h1>Congratulations, you get free </h1><br> <br>
		    						<img style="width: 150px;" src="'.$resImg.'"> 	
		    						<br><br>
		    						<h3>'.$resName.'</h3><br>
		    						<form>
		    							<label for="adress">Enter your address</label>
										<input type="text" name="adress" class="form-control" id="location" required>
										<br>
										<span class="sedngiftwow btn btn-info" style="margin-top:10px;">Send!</span>
		    						</form>
		    					  </center>
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							      </div>
							    </div>
							  </div>
							</div>


	    					<script>
				    			localStorage.clear();
				    			localStorage.setItem("id_gift", "'.$res['id'].'");
				    		</script>
		    				<center>
		    					<h1>Congratulations, you get free </h1><br> <br>
		    					<img style="width: 150px;" src="'.$resImg.'"> 
		    					<br><br>
		    					<h3>'.$resName.'</h3><br>
								<span class="btn btn-info" data-toggle="modal" data-target="#exampleModal">Get gift</span>
								<span class="gift_btn btn btn-danger">Refuse and try luck again</span>
		    				</center>
							
							<script>
								var placesAutocomplete = places({
								    appId: "plY7RVZO7INN",
								    apiKey: "b4f778c77e5f84515f5d40a28ce7ee71",
								    container: document.querySelector("#location")
								  });
							</script>

	    				';
    				}
    				else {
    					return $this->actionGetcoin();
    				}
    			}
    			
    			
    		}
    		else {
    			return 'error!';
    		}
    	}
    }


    public function actionSendusergift() {
    	if(\Yii::$app->request->isAjax) {
    		if(isset($_POST['id_gift']) && isset($_POST['quantity'])) {
    			$id_gift = (int)$_POST['id_gift'];
    			$quantity = (int)$_POST['quantity'];
				$id_user = (int)Yii::$app->user->identity->id;

				$sql_get_gift = "SELECT quantity FROM gift WHERE id = $id_gift";
				$res = Yii::$app->db->createCommand($sql_get_gift)->queryOne();
				$res_quantity = (int)$res['quantity'];

				if($id_gift == 1) {
					$sum = $res_quantity - 1;
					$sql_update_gift = "UPDATE gift SET quantity = '$sum' WHERE id = '$id_gift'";
	    			Yii::$app->db->createCommand($sql_update_gift)->execute();
				}

	    		$addGift = Yii::$app->db->createCommand()->insert('user_has_gift', [
																		'gift_id' => $id_gift,
																		'user_id' =>  $id_user,
																		'send' => 0,
																		'quantity' => $quantity
																		])->execute();

				return 'Gift have been sent and are being processed!';
    		}
    		else {
    			return 'error';
    		}
    	}
    }


    public function actionSendusergiftwow() {
    	if(\Yii::$app->request->isAjax) {
    		if(isset($_POST['id_gift']) && isset($_POST['adress'])) {
    			$id_gift = (int)$_POST['id_gift'];
    			$adress = $_POST['adress'];
				$id_user = (int)Yii::$app->user->identity->id;

				$sql_get_gift = "SELECT quantity FROM gift WHERE id = $id_gift";
				$res = Yii::$app->db->createCommand($sql_get_gift)->queryOne();
				$res_quantity = (int)$res['quantity'];

				if($id_gift == 3) {
					$sum = $res_quantity - 1;
					$sql_update_gift = "UPDATE gift SET quantity = '$sum' WHERE id = '$id_gift'";
	    			Yii::$app->db->createCommand($sql_update_gift)->execute();
				}

	    		$addGift = Yii::$app->db->createCommand()->insert('user_has_gift', [
																		'gift_id' => $id_gift,
																		'user_id' =>  $id_user,
																		'send' => 0,
																		'quantity' => 1
																		])->execute();

	    		$addAdressGift = Yii::$app->db->createCommand()->insert('adress_user', [
																		'id_gift' => $id_gift,
																		'id_user' =>  $id_user,
																		'address' => "$adress"
																		])->execute();

				return 'Gift have been sent and are being processed!';
    		}
    		else {
    			return 'error';
    		}
    	}
    }

    public function actionConvertpoint() {
    	if(\Yii::$app->request->isAjax) {
    		if(isset($_POST['sum_point']) && isset($_POST['val_point'])) {

    			$id_user = Yii::$app->user->identity->id;
    			$user_points = Yii::$app->user->identity->Points;
    			$user_money = Yii::$app->user->identity->cash;

    			$val_point = $_POST['val_point'];
    			$point = $_POST['sum_point'];

    			if($val_point > $user_points) {
    				return 'Enter to correct value!';
    			}
    			else {
    				$sum_point = $user_points - $val_point;
    				$sum_cash = $user_money + $point;

    				$sql = "UPDATE user SET cash = '$sum_cash', Points = '$sum_point' WHERE id = '$id_user'";
	    			Yii::$app->db->createCommand($sql)->execute();

	    			return 'Operation completed successfully';
    			}

    			
    		}
    	} 
    }

    public function actionConvertmoney() {
    	if(\Yii::$app->request->isAjax) {
    		if(isset($_POST['sum_money']) && isset($_POST['val_money'])) {
    			$id_user = Yii::$app->user->identity->id;
    			$user_points = Yii::$app->user->identity->Points;
    			$user_money = Yii::$app->user->identity->cash;

    			$val_money = $_POST['val_money'];
    			$money = $_POST['sum_money'];

    			if($val_money > $user_money) {
    				return 'Enter to correct value!';
    			}
    			else {
    				$sum_point = $user_points + $money;
    				$sum_cash = $user_money - $val_money;

    				$sql = "UPDATE user SET cash = '$sum_cash', Points = '$sum_point' WHERE id = '$id_user'";
	    			Yii::$app->db->createCommand($sql)->execute();

	    			return 'Operation completed successfully';
    			}
    		}
    		else {
    			return 'ajax error';
    		}
    	}
    }

    public function actionBanksend() {
    	if(\Yii::$app->request->isAjax) {
    		if(isset($_POST['bank_money'])) {

    			$bank_money = $_POST['bank_money'];
    			$id_user = (int)Yii::$app->user->identity->id;
    			$username = Yii::$app->user->identity->username;
    			$user_money = Yii::$app->user->identity->cash;

    			if($user_money < $bank_money) {
    				return 'Enter to correct value!';
    			}
    			else {
    				$sum_cash = $user_money - $bank_money;

    				$sql = "UPDATE user SET cash = '$sum_cash' WHERE id = '$id_user'";
	    			Yii::$app->db->createCommand($sql)->execute();

	    			$select = Yii::$app->db->createCommand("SELECT money,id FROM transaction_bank WHERE id_user = $id_user")->queryOne();

	    			if(!empty($select)) {
	    				$id_bank_user = $select['id'];
	    				$bank_user_money = $select['money'];

	    				$sum = $bank_user_money + $bank_money;

	    				$sql = "UPDATE transaction_bank SET money = '$sum' WHERE id = '$id_bank_user'";
	    				Yii::$app->db->createCommand($sql)->execute();

	    				return 'Operation completed successfully';
	    			}
	    			else {


	    				$addMoneyBank = Yii::$app->db->createCommand()->insert('transaction_bank', [
																		'id_user' => $id_user,
																		'name' =>  $username,
																		'money' => $bank_money
																		])->execute();

	    				return 'Operation completed successfully';
	    			}
    			}
    		}
    		else {
    			return 'ajax error';
    		}
    	}
    }

    public function actionManagergift() {

    	$getGift = Managergift::getGift();


    	return $this->render('managergift',[
            'getGift' => $getGift,
        ]);
    }

    public function actionBank() {

    	$bankAll = Bank::getBank();


    	return $this->render('bank',[
            'bankAll' => $bankAll,
        ]);
    }

}
