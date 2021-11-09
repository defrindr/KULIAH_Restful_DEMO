<?php

class PegawaiController extends Controller
{
    public $table = 'pegawai';
    public $primary_key = 'id';
    public $columns = ["nama_pegawai", "jenis_kelamin", "posisi_pegawai", "alamat_pegawai", "status_pegawai"]; // list column of yout table
    public $required = ["nama_pegawai", "jenis_kelamin", "posisi_pegawai", "alamat_pegawai", "status_pegawai"];
    public $unset_actions = ["coba-aksi"];

    public $verbs = [
        'coba-aksi' => ['POST'] // can multiple value like ["GET","DELETE"]
    ];

    public function actionCobaAksi()
    {
        response_api([
            "data" => ["test" => "berjalan lancar"]
        ]);
    }

    public function querySchema()
    {
        $column = $this->columns;
        array_unshift($column, $this->primary_key); // add primary key
        unset($column["posisi_pegawai"]); // remove relation key
        foreach ($column as $idx => $item) $column[$idx] = "pegawai.$item"; // add suffix
        $selected_column = implode(", ", $column); // join string

        return "select $selected_column, master_posisi.nama_posisi from $this->table left join master_posisi on pegawai.posisi_pegawai = master_posisi.id";
    }
}
