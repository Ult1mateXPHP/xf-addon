<?php

// = СУЩНОСТЬ ЦЕНЫ =

namespace UX\PersonalArea\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Price extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_prices';
        $structure->shortName = 'UX\PersonalArea:Price';
        $structure->contentType = 'admin_prices';
        $structure->primaryKey = 'id';
        $structure->columns = [
            'id' => ['type' => self::UINT, 'autoIncrement' => true],
            'server_id' => ['type' => self::INT, 'default' => 0],
            'name' => ['type' => self::STR],
            'title' => ['type' => self::STR],
            'price_add' => ['type' => self::INT, 'default' => 0],
            'price_edit' => ['type' => self::INT, 'default' => 0],
            'is_discount' => ['type' => self::BOOL, 'default' => false],
            'discount_add' => ['type' => self::INT, 'default' => 0],
            'discount_edit' => ['type' => self::INT, 'default' => 0]
        ];
        $structure->relations = [];
        $structure->defaultWith = [];
        $structure->getters = [];
        $structure->behaviors = [];

        return $structure;
    }
}