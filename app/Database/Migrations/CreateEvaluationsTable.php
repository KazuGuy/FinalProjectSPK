namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvaluationsTable extends Migration
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
            'criteria_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nilai' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        
        // Relasi antar tabel
        $this->forge->addForeignKey('alternative_id', 'alternatives', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('criteria_id', 'criterias', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('evaluations');
    }

    public function down()
    {
        $this->forge->dropTable('evaluations');
    }
}