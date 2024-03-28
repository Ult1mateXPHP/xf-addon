<?php

namespace UX\PersonalArea\Pub\Controller;

use Couchbase\Role;
use UX\PersonalArea\Pub\Controller\RCON;
use XF\Pub\Controller\AbstractController;

class Cabinet extends AbstractController
{
    public function actionIndex()
    {
        if (\XF::visitor()->user_id == 0) {
            return $this->redirectPermanently('index.php');
        }
        $this::CheckPremium();
        $db = \XF::db();
        $servers = $db->fetchAll("SELECT * FROM xf_servers");
        $userdata = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
        $userdata_group = $db->fetchRow("SELECT * FROM xf_user_group WHERE user_group_id = ?", $userdata['user_group_id']);
        if($userdata['user_group_id'] == Roles::$premium) {
            $premium = $db->fetchRow("SELECT * FROM xf_premium WHERE user_id = ?", \XF::visitor()->user_id);
            $expired = date('d.m.Y', $premium['date']);
        } else {
            $expired = '-';
        }
        $prices = [
            'player' => $db->fetchRow("SELECT * FROM xf_prices WHERE id = 1"),
            'premium' => $db->fetchRow("SELECT * FROM xf_prices WHERE id = 2"),
        ];
        $params = [
            'style' => Themes::Style($userdata['style_id']),
            'nav' => true,
            'servers' => $servers,
            'role' => $userdata_group['user_title'],
            'tau' => $userdata['tau'],
            'prices' => $prices,
            'expired' => $expired
        ];
        $view = $this->view('PersonalArea:Cabinet', 'personal_area', $params);
        $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
        return $view;
    }

    public static function CheckPremium()
    {
        if (\XF::visitor()->user_group_id == Roles::$premium) {
            $db = \XF::db();
            $premium = $db->fetchRow("SELECT * FROM xf_premium WHERE user_id = ?", \XF::visitor()->user_id);
            $current_date = time();
            if ($current_date > $premium['date']) {
                $db->update("xf_user", ["user_group_id" => Roles::$player], 'user_id = ' . \XF::visitor()->user_id);
                $db->delete('xf_premium', 'user_id = ' . \XF::visitor()->user_id);
            }
        } else {
            return null;
        }
    }

    public function actionBuyConfirm()
    {
        $db = \XF::db();
        $user = $db->fetchAll("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
        if (\XF::visitor()->user_group_id == Roles::$newb) {
            $role = "Player";
        } elseif (\XF::visitor()->user_group_id == Roles::$player) {
            $role = "Premium";
        } else {
            return $this->redirectPermanently('index.php?forum/cabinet');
        }
        $params = [
            'user' => $user,
            'style' => 'default',
            'nav' => true,
            'title' => 'Информационное сообщение',
            'role' => $role
        ];
        $view = $this->view('PersonalArea:Info', 'cabinet_buy', $params);
        $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
        return $view;
    }

    public function actionSuccess()
    {
        if(\XF::visitor()->user_group_id == Roles::$player) {
            $db = \XF::db();
            $user = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
            $params = [
                'user' => $user,
                'style' => 'default',
                'nav' => true,
                'title' => 'Информационное сообщение',
                'message' => 'Вы успешно приобрели статус “Player” на свой аккаунт!',
            ];
            $view = $this->view('PersonalArea:Info', 'info', $params);
            $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
            return $view;
        } elseif(\XF::visitor()->user_group_id == Roles::$premium) {
            $db = \XF::db();
            $user = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
            $premium = $db->fetchRow("SELECT * FROM xf_premium WHERE user_id = ?", \XF::visitor()->user_id);
            $expired = date('d.m.Y', $premium['date']);
            $params = [
                'user' => $user,
                'style' => 'default',
                'nav' => true,
                'title' => 'Информационное сообщение',
                'message' => 'Вы успешно приобрели статус “Premium” на свой аккаунт!',
                'expired' => 'Окончание премиума: ' . $expired,
            ];
            $view = $this->view('PersonalArea:Info', 'info', $params);
            $view->setPageParam('template', 'PERSONAL_AREA_PAGE_CONTAINER');
            return $view;
        } else {
            return $this->redirect('index.php?forum/cabinet');
        }
    }

    public function actionPlayerBuy()
    {
        if (\XF::visitor()->user_group_id == Roles::$newb) {
            $db = \XF::db();
            $user = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
            $price = $db->fetchRow("SELECT * FROM xf_prices WHERE id = ?", 1);
            $servers = $db->fetchAll("SELECT * FROM xf_servers");
            if ($price['is_discount'] == 1) {
                if ($user['tau'] > $price['discount_add']) {
                    $db->update('xf_user', ['user_group_id' => 5, 'tau' => ($user['tau'] - $price['discount_add'])], 'user_id = ' . \XF::visitor()->user_id);
                    foreach($servers as $server) {
                        $rcon = new Rcon($server['host'], $server['port'], $server['passwd'], '5');
                        $rcon->connect();
                        $commands = Commands::Command('player', \XF::visitor()->username, $this->request()->get('data'));
                        foreach ($commands as $command) {
                            $rcon->sendCommand($command);
                        }
                        $rcon->disconnect();
                    }
                    return $this->redirect('index.php?forum/cabinet/success');
                } else {
                    return $this->redirectPermanently('index.php?forum/cabinet');
                }
            } else {
                if ($user['tau'] > $price['price_add']) {
                    $db->update('xf_user', ['user_group_id' => 5, 'tau' => ($user['tau'] - $price['price_add'])], 'user_id = ' . \XF::visitor()->user_id);
                    foreach($servers as $server) {
                        $rcon = new Rcon($server['host'], $server['port'], $server['passwd'], '5');
                        $rcon->connect();
                        $commands = Commands::Command('player', \XF::visitor()->username, $this->request()->get('data'));
                        foreach ($commands as $command) {
                            $rcon->sendCommand($command);
                        }
                        $rcon->disconnect();
                    }
                    return $this->redirect('index.php?forum/cabinet/success');
                } else {
                    return $this->redirectPermanently('index.php?forum/cabinet');
                }
            }
        } else {
            return $this->redirectPermanently('index.php?forum/cabinet');
        }
    }

    public function actionPremiumBuy()
    {
        if (\XF::visitor()->user_group_id == Roles::$player) {
            $db = \XF::db();
            $user = $db->fetchRow("SELECT * FROM xf_user WHERE user_id = ?", \XF::visitor()->user_id);
            $price = $db->fetchRow("SELECT * FROM xf_prices WHERE id = ?", 2);
            $servers = $db->fetchAll("SELECT * FROM xf_servers");
            if ($price['is_discount'] == 1) {
                if ($user['tau'] > $price['discount_add']) {
                    $db->update('xf_user', ['user_group_id' => 6, 'tau' => ($user['tau'] - $price['discount_add'])], 'user_id = ' . \XF::visitor()->user_id);
                    $db->insert('xf_premium', ['user_id' => \XF::visitor()->user_id, 'date' => (time()+2505600)]);
                    foreach($servers as $server) {
                        $rcon = new Rcon($server['host'], $server['port'], $server['passwd'], '5');
                        $rcon->connect();
                        $commands = Commands::Command('premium', \XF::visitor()->username, $this->request()->get('data'));
                        foreach ($commands as $command) {
                            $rcon->sendCommand($command);
                        }
                        $rcon->disconnect();
                    }
                    return $this->redirect('index.php?forum/cabinet/success');
                } else {
                    return $this->redirectPermanently('index.php?forum/cabinet');
                }
            } else {
                if ($user['tau'] > $price['price_add']) {
                    $db->update('xf_user', ['user_group_id' => 6, 'tau' => ($user['tau'] - $price['price_add'])], 'user_id = ' . \XF::visitor()->user_id);
                    $db->insert('xf_premium', ['user_id' => \XF::visitor()->user_id, 'date' => (time()+2505600)]);
                    foreach($servers as $server) {
                        $rcon = new Rcon($server['host'], $server['port'], $server['passwd'], '5');
                        $rcon->connect();
                        $commands = Commands::Command('premium', \XF::visitor()->username, $this->request()->get('data'));
                        foreach ($commands as $command) {
                            $rcon->sendCommand($command);
                        }
                        $rcon->disconnect();
                    }
                    return $this->redirect('index.php?forum/cabinet/success');
                } else {
                    return $this->redirectPermanently('index.php?forum/cabinet');
                }
            }
        } else {
            return $this->redirectPermanently('index.php?forum/cabinet');
        }
    }
}