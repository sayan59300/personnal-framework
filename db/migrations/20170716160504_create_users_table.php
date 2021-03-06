<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('users')
            ->addColumn("nom", "string")
            ->addColumn("prenom", "string")
            ->addColumn("email", "string")
            ->addColumn("username", "string")
            ->addColumn("password", "string")
            ->addColumn("confirmation_token", "string", ['null' => true])
            ->addColumn("confirmed", "integer")
            ->addColumn("registered_at", "datetime")
            ->addColumn("reset_password_token", "string", ['null' => true])
            ->addColumn("reseted_at", "string", ['null' => true])
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}