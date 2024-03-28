<?php

// = СУЩНОСТЬ ПРЕФИКСОВ (ИГРОКОВ) =

namespace UX\PersonalArea\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Prefix extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_prefix';
        $structure->shortName = 'UX\PersonalArea:Prefix';
        $structure->contentType = 'prefix';
        $structure->primaryKey = 'player_id';
        $structure->columns = [
            'user_id' => ['type' => self::UINT],
            'prefix' => ['type' => self::STR],
            'prefix_color' => ['type' => self::STR],
            'nick_color' => ['type' => self::STR],
        ];
        $structure->relations = [];
        $structure->defaultWith = [];
        $structure->getters = [];
        $structure->behaviors = [];

        return $structure;
    }
}