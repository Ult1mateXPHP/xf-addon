<?php

namespace UX\PersonalArea\Pub\Controller;

class Commands
{
    // СОЗДАНИЕ КОММАНДЫ
    /*
     * case 'player':
     * return ['комманда'];
     *
     * $data (ДАННЫЕ ИЗ FRONTEND)
     * $player (ИГРОК)
     *
     * ИСПОЛЬЗОВАНИЕ ПЕРЕМЕННЫХ:
     * return ['комманда '.$data];    // ВКОНЦЕ
     * return ['комманда '.$data.' продолжение_комманды'];    // В СЕРЕДИНЕ
     *
     * ВЫЗОВ НЕСКОЛЬКИХ КОММАНД:
     * return ['КОММАНДА 1', 'КОММАНДА 2', 'КОММАНДА 3'];
     *
     * ПРИМЕРЫ
     * return ['bc &4ИГРОК '.$player.' купил статус '.$data];
     * return ['time set '.$data];
     */



    public static function Command($name, $player = null, $data = null)
    {
        switch($name) {
            case 'player':
                return ['pex user '.$player.' group set player', 'broadcast Бывший бродяга '.$player.' стал гражданином сервера!'];
            case 'premium':
                return ['pex user '.$player.' group add premium "" 2678400', 'broadcast '.$player.' стал почетным гражданином!'];
            case 'say':
                return ['say '.$data];      // ТЕСТОВАЯ КОММАНДА (МОЖНО ИСПОЛЬЗОВАТЬ)
            case 'nick_color':
                $db = \XF::db();
                $prefix = $db->fetchRow('SELECT * FROM xf_prefix WHERE user_id = ?', \XF::visitor()->user_id);
                if($prefix === false) {
                    $db->insert('xf_prefix', [
                        'user_id' => \XF::visitor()->user_id,
                        'prefix' => '',
                        'prefix_color' => '',
                        'nick_color' => $data,
                    ]);
                    return ['pex user '.$player.' prefix '.$data.' World'];
                } else {
                    $db->update('xf_prefix', ['nick_color' => $data], 'user_id = '.\XF::visitor()->user_id);
                    return ['pex user '.$player.' prefix "&f['.$prefix["prefix_color"].$prefix["prefix"].'&f] '.$data.'" World'];
                }
            case 'prefix':
                $db = \XF::db();
                $prefix = $db->fetchRow('SELECT * FROM xf_prefix WHERE user_id = ?', \XF::visitor()->user_id);
                if($prefix === false) {
                    $db->insert('xf_prefix', [
                        'user_id' => \XF::visitor()->user_id,
                        'prefix' => $data,
                        'prefix_color' => '',
                        'nick_color' => '',
                    ]);
                    return ['pex user '.$player.' prefix "&f['.$data.'&f] &f" World'];
                } else {
                    $db->update('xf_prefix', ['prefix' => $data], 'user_id = '.\XF::visitor()->user_id);
                    return ['pex user '.$player.' prefix &f['.$prefix["prefix_color"].$data.'&f] '.$prefix["nick_color"].'" World'];
                }
            case 'prefix_color':
                $db = \XF::db();
                $prefix = $db->fetchRow('SELECT * FROM xf_prefix WHERE user_id = ?', \XF::visitor()->user_id);
                if($prefix === false) {
                    $db->insert('xf_prefix', [
                        'user_id' => \XF::visitor()->user_id,
                        'prefix' => '',
                        'prefix_color' => $data,
                        'nick_color' => '',
                    ]);
                    return [];
                } else {
                    $db->update('xf_prefix', ['prefix_color' => $data], 'user_id = '.\XF::visitor()->user_id);
                    return ['pex user '.$player.' prefix "&f['.$data.$prefix["prefix"].'&f] '.$prefix["nick_color"].'" World'];
                }
            case 'pay':
                return ['money give '.$player.' '.$data];
        }
        return null;
    }
}