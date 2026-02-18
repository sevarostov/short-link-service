<?php

namespace app\controllers\api;

use app\models\Link;
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
		return (new Link())->searchByHost(Yii::$app->request->queryParams);
	}
}
