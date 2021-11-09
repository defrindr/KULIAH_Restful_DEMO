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
}
