<?php

// = СУЩНОСТЬ СЕРВЕРА =

namespace UX\PersonalArea\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Server extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_servers';
        $structure->shortName = 'UX\PersonalArea:Server';
        $structure->contentType = 'admin_servers';
        $structure->primaryKey = 'id';
        $structure->columns = [
            'id' => ['type' => self::UINT, 'autoIncrement' => true],
            'name' => ['type' => self::STR],
            'title' => ['type' => self::STR],
            'host' => ['type' => self::STR],
            'port' => ['type' => self::STR],
            'passwd' => ['type' => self::STR],
        ];
        $structure->relations = [];
        $structure->defaultWith = [];
        $structure->getters = [];
        $structure->behaviors = [];

        return $structure;
    }
}