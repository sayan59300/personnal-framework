<?php

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data[0] = [
            'nom' => 'Administrateur',
            'prenom' => 'Administrateur',
            'email' => 'admin@admin.fr',
            'username' => 'administrateur',
            'password' => hash('sha512', md5(sha1('admin'))),
            'confirmation_token' => '',
            'confirmed' => 1,
            'registered_at' => date("Y-m-d H:i:s")
        ];
        $table = $this->table('users');
        $table->insert($data)
            ->save();
    }
}
