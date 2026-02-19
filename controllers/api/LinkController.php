<?php

namespace app\controllers\api;

use app\models\Link;
use app\services\UserIpService;
use app\services\UserIpLinkLogService;
use Exception;
use Yii;
use yii\rest\ActiveController;
use yii\web\Response;

class LinkController extends ActiveController
{
	public $modelClass = Link::class;

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();

		// Enable JSON response format
		$behaviors['contentNegotiator']['formats'] = [
			'application/json' => Response::FORMAT_JSON,
		];

		return $behaviors;
	}

	/**
	 * Creates a new short link by host
	 * @see Link::beforeSave()
	 *
	 * (POST /api/link)
	 */
	public function actionCreate()
	{
	}

	/**
	 * Finds existing link by ID
	 * (GET /api/link/<id>)
	 *
	 * @param $id
	 *
	 * @return void
	 */
	public function actionView($id)
	{
	}

	/**
	 * Finds existing link by host
	 * (GET /api/link/search?host=https://example.com)
	 *
	 * @throws Exception
	 */
	public function actionSearch(): ?Link
	{
		return (new Link())->searchBy(Yii::$app->request->queryParams, 'host');
	}

	/**
	 * Finds existing link by short
	 * (GET /api/link/visit?short=53de59)
	 *
	 * @throws Exception
	 */
	public function actionVisit()
	{
		$link = (new Link())->searchBy(Yii::$app->request->queryParams, 'short');

		if (!$link) {
			return new Response(['data' => "Link Not Found", 'statusCode' => 404]);
		}

		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {

			$userIp = (new UserIpService())->saveUserIp(Yii::$app->request->userIP);
			(new UserIpLinkLogService())->log($userIp, $link);
			$link->incrementCounter();
			$transaction->commit();

			return Yii::$app->response->redirect($link->host, 302);

		} catch (Exception $e) {

			$transaction->rollBack();
			Yii::error('Error processing link visit: ' . $e->getMessage());

			return Yii::$app->response->redirect(array('site/index'));
		}
	}
}
