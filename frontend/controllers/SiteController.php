<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use app\models\FiltersGroup;
use app\models\Filters;
use app\models\ProductsSearch;
use yii\data\ActiveDataProvider;
use app\models\ProductsFilters;
use yii\web\Session;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
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
     * @inheritdoc
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



        $model = new ProductsSearch();

        $oFiltersGroup = new FiltersGroup();
        $oFilters = new Filters();
        $aFiltersData = [];
        $aDimensions = [];
        $query = $model::find();
        $query->joinWith(['productsFilters']);
        $query->joinWith(['productsAttributes']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    ]
                ]
            ]);

        $query2 = $model::find();
        $query2->joinWith(['productsAttributes']);
        $query2->andFilterWhere(['attributes_id'=>7]);
        $dataProvider2 = new ActiveDataProvider([
            'query' => $query2,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    ]
                ]
            ]);
        $query3 = $model::find();
        $query3->joinWith(['productsAttributes']);
        $query3->andFilterWhere(['attributes_id'=>6]);
        $dataProvider3 = new ActiveDataProvider([
            'query' => $query3,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    ]
                ]
            ]);
        $iMaxX = $query2->max('value');
        $iMinX = $query2->min('value');
        $iMaxY = $query3->max('value');
        $iMinY = $query3->min('value');
        $aDimensions['max_x'] = $query2->max('value');
        $aDimensions['min_x'] = $query2->min('value');
        $aDimensions['max_y'] = $query3->max('value');
        $aDimensions['min_y'] = $query3->min('value');

        $aPostData = Yii::$app->request->post();


        if ($aPostData && count($aPostData)>=2)
        {
            //echo '<pre>' .print_r(Yii::$app->request->post(), TRUE); die();
            $iMaxX = $aPostData['to_x'];
            $iMinX = $aPostData['from_x'];
            $iMaxY = $aPostData['to_y'];
            $iMinY = $aPostData['from_y'];
            $aPostData['from_x']='x';
            $aPostData['to_x']='x';
            $aPostData['from_y']='x';
            $aPostData['to_y']='x';
            foreach ($aPostData  as $Filters)
            {



                if (is_numeric($Filters))
                {
                    $aFiltersData[] .= $Filters;
                }
            }
            $aDimensions['max_x'] = ($iMaxX != $query2->max('value') ? $iMaxX : $query2->max('value'));
            $aDimensions['min_x'] = ($iMinX != $query2->min('value') ? $iMinX : $query2->min('value'));
            $aDimensions['max_y'] = $query3->max('value');
            $aDimensions['min_y'] = $query3->min('value');
            $query->andFilterWhere(['IN', 'products_filters.filters_id',$aFiltersData]);
            $query->groupBy('id');
            $query->having('COUNT(*)='.count($aFiltersData) );

            if ($iMaxX != $query2->max('value'))
            {


            }


        }
        $sProjectCount = $dataProvider->count;


        //echo '<pre>' . print_r($aDimensions, true); die();
        $aFiltersGroup = $oFiltersGroup::find()->where(['is_active'=> 1])->orderBy('sort_order')->all();
        foreach ($aFiltersGroup as $_aFiltersGroup)
        {
            $aFilters = $oFilters::find()->where(['filters_group_id' => $_aFiltersGroup->id, 'is_active'=> 1])->all();
            $aData[$_aFiltersGroup->id] = ['question'=>$_aFiltersGroup, 'answer' => $aFilters];
        }

        $aSession = new Session();
        $aSession['FiltersSession'] = [];
        $aSession['FiltersSession'] = $aFiltersData;

        return $this->render('index', ['model' => $model,'sProjectCount' => $sProjectCount, 'aFilters'=>$aData, 'aFiltersData' => $aFiltersData, 'dataProvider'=>$dataProvider, 'aDimensions'=> $aDimensions]);
    }
    public function actionProjekty()
    {
        $model = new ProductsSearch();
        $aSession = new Session();
        $FiltersSession = $aSession->get('FiltersSession');
        //echo 'Filter'.print_r($FiltersSession , TRUE).'<br>'; die();

        $oFiltersGroup = new FiltersGroup();
        $oFilters = new Filters();
        $aFiltersGroup = $oFiltersGroup::find()->where(['is_active'=> 1])->orderBy('sort_order')->all();

        foreach ($aFiltersGroup as $_aFiltersGroup)
        {
            $aFilters = $oFilters::find()->where(['filters_group_id' => $_aFiltersGroup->id, 'is_active'=> 1])->all();
            $aData[$_aFiltersGroup->id] = ['question'=>$_aFiltersGroup, 'answer' => $aFilters];
        }
        $query = $model::find();

        $query->joinWith(['productsDescriptons']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize' => 25],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    ]
                ]
            ]);

        if (Yii::$app->request->post() && count(Yii::$app->request->post())>=2)
        {
            $FiltersSession = '';
            foreach (Yii::$app->request->post() as $Filters)
            {
                if (is_numeric($Filters))
                {
                    $FiltersSession[] .= $Filters;
                }
            }
        }
        $aSession['FiltersSession'] = $FiltersSession;
        if ($FiltersSession)
        {
            $query->joinWith(['productsFilters']);
            $query->andFilterWhere(['IN', 'products_filters.filters_id',$aSession['FiltersSession']]);
            $query->groupBy('id');
            $query->having('COUNT(*)='.count($FiltersSession) );
        }

        if (Yii::$app->request->isAjax)
        {
            return $this->renderAjax('products', ['dataProvider'=>$dataProvider]);
        }
        else
        {
             return $this->render('projekty',['aChooseFilters'=>$FiltersSession, 'aFilters'=>$aData, 'dataProvider'=>$dataProvider]);
        }



    }
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionReset()
    {
        $aSession = new Session();
        $aSession->removeAll();

    }
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
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
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
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
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
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
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}
