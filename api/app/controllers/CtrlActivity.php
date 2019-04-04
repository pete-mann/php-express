<?php

namespace ExpressPHP\controllers;
use ExpressPHP\models\Activity;
use ExpressPHP\core\Request;
use ExpressPHP\core\Response;

/**
 * The CtrlActivity class is used handle logic associated with activities
 * @author Pete Mann - peter.mann.design@gmail.com
 */
class CtrlActivity extends Activity {

    /**
     * CtrlUser constructor
     */
    public function __construct() {}

    /**
     * The index method is used to return all activities
     * @param Request $req [Accepts a Request object]
     * @param Response $res [Accepts a Response object]
     */
    public function index(Request $req, Response $res) {
        $res->json(['activities' => $this->findAll()]);
    }

    /**
     * @param Request $req [Accepts a Request object]
     * @param Response $res [Accepts a Response object]
     */
    public function update(Request $req, Response $res) {
        $activity = null;
        try  {
            $activity = new Activity(
                $req->getBody()['activity']['activityId'],
                $req->getBody()['activity']['name'],
                $req->getBody()['activity']['clientId']
            );
        } catch(\InvalidArgumentException $e) {
            $res->send(400, ['title' => 'Client error', 'message' => $e->getMessage()]);
        }

        $this->updateOne($activity);
        $res->json(['activity' => $this->findOne($activity->getActivityId())]);
    }

    /**
     * @param Request $req
     * @param Response $res
     */
    public function getClientProjectWorkCodeActivities(Request $req, Response $res) {
        $res->json(['activities' => $this->findActivity(
            $req->getParams()["clientId"],
            $req->getParams()["projectId"],
            $req->getParams()["workCodeId"])
        ]);
    }

    /**
     * The show method is used to return the activity specified in the url using a named param: clientId
     * @param Request $req [Accepts a Request object]
     * @param Response $res [Accepts a Response object]
     */
    public function show(Request $req, Response $res) {
        $res->json(['activity' => $this->findOne($req->getParams()["activityId"])]);
    }

}