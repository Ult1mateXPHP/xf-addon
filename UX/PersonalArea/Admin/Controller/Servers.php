<?php

// СУЩНОСТЬ СЕРВЕРОВ

namespace UX\PersonalArea\Admin\Controller;

use UX\PersonalArea\Entity\Server;
use XF\Admin\Controller\AbstractController;
use XF\Entity\Route;
use XF\Mvc\ParameterBag;
use XF\Mvc\Entity\Entity;

class Servers extends AbstractController
{
    protected function preDispatchController($action, ParameterBag $params)
    {
        $this->assertSuperAdmin();
    }

    public function actionIndex() {

        $tagFinder = $this->finder('UX\PersonalArea:Server');

        $params = [
            'servers' => $tagFinder->fetch(),
        ];
        return $this->view('UX\PersonalArea:Servers', 'admin_servers', $params);
    }

    public function actionCreate() {
        return $this->view('UX\PersonalArea:Servers', 'admin_server_create');
    }

    public function actionAdd(ParameterBag $parameterBag) {
        $create_server = $this->em()->create('UX\PersonalArea:Server');
        $create_server->name = $parameterBag->name;
        $create_server->title = $parameterBag->title;
        $create_server->save();
    }

    public function actionEdit(ParameterBag $parameterBag)
    {
        $price = $this->assertServerExists($parameterBag->id);
        return $this->ServerAddEdit($price);
    }

    public function actionSave(ParameterBag $parameterBag) {
        if($parameterBag->id) {
            $server = $this->assertServerExists($parameterBag->id);
        }
        else {
            $server = $this->em()->create('UX\PersonalArea:Server');
        }
        $this->ServerSaveProcess($server)->run();
        return $this->redirect('admin.php?servers');
    }

    protected function ServerSaveProcess(Server $server) {
        $input = $this->filter([
            'name' => 'str',
            'title' => 'str',
            'host' => 'str',
            'port' => 'str',
            'passwd' => 'str'
        ]);
        $form = $this->formAction();
        $form->basicEntitySave($server, $input);
        return $form;
    }

    protected function assertServerExists($id, $with = null, $phraseKey = null) {
        return $this->assertRecordExists('UX\PersonalArea:Server', $id);
    }

    public function ServerAddEdit(Server $server) {
        $params = [
            'server' => $server
        ];
        return $this->view('UX\PersonalArea:Server\Edit', 'admin_server_edit', $params);
    }

    public function actionDelete(ParameterBag $parameterBag) {
        $server = $this->assertServerExists($parameterBag->id);

        /** @var \XF\ControllerPlugin\Delete $plugin */
        $plugin = $this->plugin('XF:Delete');
        return $plugin->actionDelete(
            $server,
            $this->buildLink('servers/delete', $server),
            $this->buildLink('servers/edit', $server),
            $this->buildLink('servers'),
            $server->title
        );
    }
}