<?php

namespace UX\PersonalArea;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Create;
use XF\Import\Importer\StepPostsTrait;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    //Миграция
    //php cmd.php xf-addon:install-step UX\PersonalArea 1

	public function installStep1() {
        $this->createTable('xf_prices', function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('id', 'int')->autoIncrement();
            $table->addColumn('server_id', 'int')->unsigned(false)->setDefault(0);
            $table->addColumn('name', 'text');
            $table->addColumn('title', 'text');
            $table->addColumn('price_add', 'int')->unsigned(false)->setDefault(0);
            $table->addColumn('price_edit', 'int')->unsigned(false)->setDefault(0);
            $table->addColumn('is_discount', 'tinyint')->setDefault(0);
            $table->addColumn('discount_add', 'int')->unsigned(false)->setDefault(0);
            $table->addColumn('discount_edit', 'int')->unsigned(false)->setDefault(0);
        });
        $this->createTable('xf_servers', function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('id', 'int')->autoIncrement();
            $table->addColumn('name', 'text');
            $table->addColumn('title', 'text');
            $table->addColumn('host', 'text');
            $table->addColumn('port', 'text');
            $table->addColumn('passwd', 'text');
        });
        $this->createTable('xf_premium', function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('user_id', 'int')->autoIncrement();
            $table->addColumn('date', 'int')->unsigned(false);
        });
        $this->createTable('xf_prefix', function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('user_id', 'int');
            $table->addColumn('prefix', 'text');
            $table->addColumn('prefix_color', 'text');
            $table->addColumn('nick_color', 'text');
            $table->addPrimaryKey('user_id');
        });
    }
}