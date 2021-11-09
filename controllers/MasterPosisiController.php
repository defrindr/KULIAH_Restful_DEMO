<?php

class MasterPosisiController extends Controller
{
    public $table = 'master_posisi';
    public $primary_key = 'id';
    public $columns = ["nama_posisi"];
    public $required = ["nama_posisi"];
}
