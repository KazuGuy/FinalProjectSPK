namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMabacResultsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'alternative_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'skor_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,6',
            ],
            'ranking' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('alternative_id', 'alternatives', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mabac_results');
    }

    public function down()
    {
        $this->forge->dropTable('mabac_results');
    }
}