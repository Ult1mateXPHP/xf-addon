<?php

namespace UX\PersonalArea\Pub\Controller;

use XF\Pub\Controller\AbstractController;

class Replenish extends AbstractController
{
    public function actionIndex()
    {
        $db = \XF::db();
        $user = $db->fetchAll("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
        $servers = $db->fetchAll("SELECT * FROM xf_servers");
        $params = [
            'user' => $user,
            'style' => 'default',
            'nav' => true,
            'servers' => $servers     // Для навигационного бара
        ];
        $view = $this->view('PersonalArea:Cabinet', 'replenish', $params);
        $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
        return $view;
    }

    public function actionPayment() {
        $db = \XF::db();
        $user = $db->fetchAll("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
        //$servers = $db->fetchAll("SELECT * FROM xf_servers");      // Для навигационного бара
        $params = [
            'user' => $user,
            'style' => 'default',
            'nav' => false,
            'username' => \XF::visitor()->username,
            'number' => str_pad(\XF::visitor()->user_id, 6, '0', STR_PAD_LEFT),
            'tau' => $this->request()->get('tau'),
            'rub' => ($this->request()->get('tau')/100),
            //'servers' => $servers     // Для навигационного бара
        ];
        $view = $this->view('PersonalArea:Cabinet', 'payment', $params);
        $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
        return $view;
    }
}