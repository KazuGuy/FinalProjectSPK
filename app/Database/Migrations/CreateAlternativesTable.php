namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlternativesTable extends Migration
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
            'kode_alternatif' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'nama_lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'deskripsi' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('alternatives');
    }

    public function down()
    {
        $this->forge->dropTable('alternatives');
    }
}