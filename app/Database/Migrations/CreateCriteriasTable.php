namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCriteriasTable extends Migration
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
            'kode_kriteria' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'nama_kriteria' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['benefit', 'cost'],
            ],
            'bobot' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,4',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('criterias');
    }

    public function down()
    {
        $this->forge->dropTable('criterias');
    }
}