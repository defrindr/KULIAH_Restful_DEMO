<?php

/**
 * Controller
 * 
 */
class Controller extends QueryBuilder
{
    protected $default_verbs = [
        'index' => ['GET', 'HEAD'],
        'view' => ['GET', 'HEAD'],
        'create' => ['POST'],
        'update' => ['PUT', 'PATCH'],
        'delete' => ['DELETE'],
    ];

    public $primary_key;
    public $columns;
    public $required;
    public $table;
    public $verbs = [];
    public $unset_actions = [];
    // public $unset_from_schema = ["flag"];

    /**
     * Configuration vers
     */
    public function getVerbs()
    {
        return array_merge($this->default_verbs, $this->verbs);
    }

    protected function checkDataExist($kode)
    {
        if ($kode[":" . $this->primary_key] == null) response_api(["success" => false, "message" => "Parameter '$this->primary_key' required", "code" => 400]);

        $data = $this->ExecQuery("select * from $this->table where {$this->table}.{$this->primary_key}=:{$this->primary_key}", $kode);
        if ($data['success'] == "Data kosong") response_api(["success" => false, "message" => "data tidak ditemukan", "code" => 404]);
        return $data;
    }

    public function querySchema()
    {
        $column = $this->getVisibleColumns();
        $column_str = implode(",", $column);
        return "select $column_str from $this->table";
    }

    protected function getVisibleColumns()
    {
        $column = $this->columns;
        array_unshift($column, $this->primary_key);
        return $column;
    }

    public function actionIndex()
    {
        $response = $this->ExecQuery($this->querySchema());
        response_api($response);
    }

    public function actionView()
    {
        try {
            $keys = [$this->primary_key];
            $kode = $this->assignData($keys, [$this->primary_key => $_GET['id']]);
            if ($kode[":" . $this->primary_key] == null) response_api(["success" => false, "message" => "Parameter '$this->primary_key' required", "code" => 400]);

            $data = $this->ExecQuery("select * from $this->table where {$this->table}.{$this->primary_key}=:{$this->primary_key}", $kode);
            response_api($data);
        } catch (\Exception $e) {
            response_api(['success' => false, 'message' => 'Error: ' . $e->getMessage(), "code" => 500]);
        }
    }

    public function actionCreate($post)
    {
        try {
            $keys = $this->columns;
            if (array_search($this->primary_key, $this->columns) !== false) unset($keys[array_search($this->primary_key, $this->columns)]);
            $params = $this->assignData($keys, $post);
            $query = $this->createInsertQuery($this->table, $keys);
            $response = $this->insert($query, $params);
            response_api($response);
        } catch (\Exception $e) {
            response_api(['success' => false, 'message' => 'Error: ' . $e->getMessage(), "code" => 500]);
        }
    }

    public function actionUpdate($post)
    {
        try {
            $keys = [$this->primary_key];
            $kode = $this->assignData($keys,  [$this->primary_key => $_GET['id']]);
            $data = $this->checkDataExist($kode);
            $data = $data['data'][0];

            $keys = $this->columns;
            $column = $this->getVisibleColumns();

            $data = array_merge((array)$data, $post);
            $data = array_merge($data, [$this->primary_key => $_GET[$this->primary_key]]);
            $params = $this->assignData($column, $data, 'update');

            $query = $this->createUpdateQuery($this->table, $keys, $this->primary_key);
            // dd($query);
            $response = $this->update($query, $params);
            response_api($response);
        } catch (\Exception $e) {
            response_api(['success' => false, 'message' => 'Error: ' . $e->getMessage(), "code" => 500]);
        }
    }

    public function actionDelete($post)
    {
        try {
            $keys = [$this->primary_key];
            $kode = $this->assignData($keys,  [$this->primary_key => $_GET['id']]);
            $this->checkDataExist($kode);

            $response = $this->delete("delete from $this->table where {$this->primary_key}=:{$this->primary_key}", $kode);
            response_api($response);
        } catch (\Exception $e) {
            response_api(['success' => false, 'message' => 'Error: ' . $e->getMessage(), "code" => 500]);
        }
    }
}
