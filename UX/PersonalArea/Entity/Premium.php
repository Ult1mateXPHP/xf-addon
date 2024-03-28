<?php

// = СУЩНОСТЬ ПРЕМИУМ ПОДПИСКИ =

namespace UX\PersonalArea\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Premium extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_premium';
        $structure->shortName = 'UX\PersonalArea:Premium';
        $structure->contentType = 'user_premium';
        $structure->primaryKey = 'user_id';
        $structure->columns = [
            'user_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'date' => ['type' => self::INT]
        ];
        $structure->relations = [];
        $structure->defaultWith = [];
        $structure->getters = [];
        $structure->behaviors = [];

        return $structure;
    }
}