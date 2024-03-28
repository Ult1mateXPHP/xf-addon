<?php

namespace UX\PersonalArea\Pub\Controller;

use UX\PersonalArea\Entity\Price;
use UX\PersonalArea\Pub\Controller\Rcon;
use UX\PersonalArea\Pub\Controller\Commands;
use XF\Error;
use XF\Mvc\ParameterBag;
use XF\Pub\Controller\AbstractController;

class Server extends AbstractController
{
    protected function getServer()
    {
        $db = \XF::db();
        $path = explode('/', $this->request()->getRoutePath());
        $route = $path[1];
        $server = $db->fetchRow("SELECT * FROM xf_servers WHERE name = ?", $route);
        return $server;
    }

    protected function getPrices($id)
    {
        $db = \XF::db();
        return $db->fetchAll("SELECT * FROM xf_prices WHERE server_id = ?", $id);
    }

    public function actionIndex()
    {
        Cabinet::CheckPremium();
        $db = \XF::db();
        $userdata = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
        $userdata_group = $db->fetchRow("SELECT * FROM xf_user_group WHERE user_group_id = ?", $userdata['user_group_id']);
        $servers = $db->fetchAll("SELECT * FROM xf_servers");
        $this_server = $this->getServer();
        $params = [
            'style' => Themes::Style($userdata['style_id']),
            'background' => Themes::Background($userdata['style_id']),
            'servers' => $servers,         // ДАННЫЕ ДЛЯ ГЕНЕРАЦИИ nav-bar.html
            'this_server' => $this_server,   // ДАННЫЕ О ТЕКУЩЕМ СЕРВЕРЕ
            'nav' => true,
            'role' => $userdata_group['user_title'],
            'prices' => $this->getPrices($this_server['id'])
        ];
        $view = $this->view('PersonalArea:Server', 'server', $params);
        $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
        return $view;
    }

    public function actionSkin()
    {
        $server = $this->getServer();
        $user_role = \XF::visitor()->user_group_id;
        $dataDir = \XF::app()->config('externalDataPath');
        $skin = $this->request->getFile('skin', false, false);
        if ($this->isPost()) {
            if ($user_role == Roles::$newb) {
                if (!isset($skin)) {
                    return $this->redirectPermanently('index.php?servers/'.$server['name']);
                }
                if (($skin->getImageWidth() == 64) and ($skin->getImageHeight() == 32)) {
                    $dataDir .= '://cabinet/skins/' . \XF::visitor()->username . '_skin.png';
                    \XF\Util\File::copyFileToAbstractedPath($skin->getTempFile(), $dataDir);
                    return $this->redirectPermanently('index.php?servers/'.$server['name']);
                } else {
                    return $this->error('Файл не подходит для загрузки');
                }
            } elseif ($user_role == Roles::$premium) {
                if (!isset($skin)) {
                    return $this->redirectPermanently('index.php?servers/'.$server['name']);
                }
                if (($skin->getImageWidth() <= 512) and ($skin->getImageHeight() <= 256)) {
                    $dataDir .= '://cabinet/skins/' . \XF::visitor()->username . '_skin.png';
                    \XF\Util\File::copyFileToAbstractedPath($skin->getTempFile(), $dataDir);
                    return $this->redirectPermanently('index.php?servers/'.$server['name']);
                } else {
                    return $this->error('Файл не подходит для загрузки');
                }
            } elseif ($user_role == Roles::$player) {
                if (!isset($skin)) {
                    return $this->redirectPermanently('index.php?servers/'.$server['name']);
                }
                if (($skin->getImageWidth() == 64) and ($skin->getImageHeight() == 32)) {
                    $dataDir .= '://cabinet/skins/' . \XF::visitor()->username . '_skin.png';
                    \XF\Util\File::copyFileToAbstractedPath($skin->getTempFile(), $dataDir);
                    return $this->redirectPermanently('index.php?servers/'.$server['name']);
                } else {
                    return $this->error('Файл не подходит для загрузки');
                }
            } else {
                $this->redirectPermanently('index.php');
            }
        }
        return $this->redirectPermanently('index.php?servers/'.$server['name']);
    }

    public function actionBuy()
    {
        $server = $this->getServer();
        $db = \XF::db();
        $user = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
        $buy_isset = $db->fetchRow("SELECT * FROM xf_buy_isset WHERE user_id = ?", \XF::visitor()->user_id);
        $price = $db->fetchRow("SELECT * FROM xf_prices WHERE name = ? and server_id = ?",[$this->request()->get('buy'), $server['id']]);
        if (is_array($buy_isset)) {
            if ($user['tau'] >= $price['price_edit']) {
                $rcon = new Rcon($server['host'], $server['port'], $server['passwd'], '5');
                $rcon->connect();
                $commands = Commands::Command($price['name'], \XF::visitor()->username, $this->request()->get('data'));
                foreach($commands as $command) {
                    $rcon->sendCommand($command);
                }
                $rcon->disconnect();
                $db->update('xf_user', ['tau' => ($user['tau'] - $price['price_edit'])], 'user_id = '.$user['user_id']);
                return $this->redirectPermanently('index.php?servers/' . $server['name'], 'Куплено!');
            } else {
                return $this->redirectPermanently('index.php?servers/' . $server['name'], 'Недостаточно Tau');
            }
        } else {
            if ($user['tau'] >= $price['price_add']) {
                $rcon = new Rcon($server['host'], $server['port'], $server['passwd'], '5');
                $rcon->connect();
                $commands = Commands::Command($price['name'], \XF::visitor()->username, $this->request()->get('data'));
                foreach($commands as $command) {
                    $rcon->sendCommand($command);
                }
                $rcon->disconnect();
                $db->update('xf_user', ['tau' => ($user['tau'] - $price['price_add'])], 'user_id = '.$user['user_id']);
                if($buy_isset === false) {
                    $db->insert('xf_buy_isset', [
                        'user_id' => $user['user_id'],
                        'name' => $price['name'],
                        'isset' => 1,
                    ]);
                } else {
                    $db->update('xf_buy_isset', ['isset' => 1], 'user_id = '.$user['user_id']);
                }
                return $this->redirectPermanently('index.php?servers/' . $server['name'], 'Куплено!');
            } else {
                return $this->redirectPermanently('index.php?servers/' . $server['name'], 'Недостаточно Tau');
            }
        }
    }
}