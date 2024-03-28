<?php

// = СУЩНОСТЬ ЦЕН =

namespace UX\PersonalArea\Admin\Controller;

use UX\PersonalArea\Entity\Price;
use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;
use XF\Mvc\Entity\Entity;

class Prices extends AbstractController
{
    protected function preDispatchController($action, ParameterBag $params)
    {
        $this->assertSuperAdmin();
    }

    public function actionIndex() {

        $tagFinder = $this->finder('UX\PersonalArea:Price');

        $params = [
            'prices' => $tagFinder->fetch(),
        ];
        return $this->view('UX\PersonalArea:Prices', 'admin_prices', $params);
    }

    public function actionCreate() {
        return $this->view('UX\PersonalArea:Prices', 'admin_price_create');
    }

    public function actionAdd(ParameterBag $parameterBag) {
        $create = $this->em()->create('UX\PersonalArea:Price');
        $create->name = $parameterBag->name;
        //$create->title = $parameterBag->title;
        $create->server_id = $parameterBag->server_id;
        $create->price_add = $parameterBag->price_add;
        $create->price_edit = $parameterBag->price_edit;
        $create->is_discount = $parameterBag->is_discount;
        $create->discount_add = $parameterBag->discount_add;
        $create->discount_edit = $parameterBag->discount_edit;
        $create->save();
    }

    public function actionEdit(ParameterBag $parameterBag)
    {
        $price = $this->assertPriceExists($parameterBag->id);
        return $this->PriceAddEdit($price);
    }

    public function actionSave(ParameterBag $parameterBag) {
        if($parameterBag->id) {
            $price = $this->assertPriceExists($parameterBag->id);
        }
        else {
            $price = $this->em()->create('UX\PersonalArea:Price');
        }
        $this->PriceSaveProcess($price)->run();
        return $this->redirect('admin.php?prices');
    }

    protected function PriceSaveProcess(Price $price) {
        $input = $this->filter([
            'name' => 'str',
            //'title' => 'str',
            'server_id' => 'int',
            'price_add' => 'int',
            'price_edit' => 'int',
            'is_discount' => 'bool',
            'discount_add' => 'int',
            'discount_edit' => 'int'
        ]);
        $form = $this->formAction();
        $form->basicEntitySave($price, $input);

        return $form;
    }

    protected function assertPriceExists($id, $with = null, $phraseKey = null) {
        return $this->assertRecordExists('UX\PersonalArea:Price', $id);
    }

    public function PriceAddEdit(Price $price) {
        $params = [
            'price' => $price
        ];
        return $this->view('UX\PersonalArea:Price\Edit', 'admin_price_edit', $params);
    }

    public function actionDelete(ParameterBag $parameterBag) {
        $price = $this->assertPriceExists($parameterBag->id);

        /** @var \XF\ControllerPlugin\Delete $plugin */
        $plugin = $this->plugin('XF:Delete');
        return $plugin->actionDelete(
            $price,
            $this->buildLink('prices/delete', $price),
            $this->buildLink('prices/edit', $price),
            $this->buildLink('prices'),
            $price->title
        );
    }
}