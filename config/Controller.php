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
        'update' => ['PUT'],
        'delete' => ['DELETE'],
    ];
    public $primary_key;
    public $columns;
    public $required;
    public $table;
    public $verbs = [];
    public $unset_actions = [];

    /**
     * Configuration vers
     */
    public function getVerbs()
    {
        return array_merge($this->default_verbs, $this->verbs);
    }

    public function actionIndex()
    {
        $response = $this->ExecQuery("select * from $this->table");
        response_api($response);
    }

    public function actionView($post)
    {
        try {
            $keys = [$this->primary_key];
            $kode = $this->assignData($keys, $post);
            $response = $this->ExecQuery("select * from $this->table where {$this->primary_key}=:{$this->primary_key} limit 1", $kode);
            response_api($response);
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
            $kode = $this->assignData($keys, $post);
            $data = $this->ExecQuery("select * from $this->table where {$this->primary_key}=:{$this->primary_key}", $kode);

            $keys = $this->columns;
            if (array_search($this->primary_key, $this->columns) !== false) unset($keys[array_search($this->primary_key, $this->columns)]);
            $params = $this->assignData($this->columns, $post);
            if ($data['message'] == "Data kosong") {
                response_api(["success" => false, "message" => "data tidak ditemukan"]);
            }

            $query = $this->createUpdateQuery($this->table, $keys, $this->primary_key);
            $response = $this->insert($query, $params);
            response_api($response);
        } catch (\Exception $e) {
            response_api(['success' => false, 'message' => 'Error: ' . $e->getMessage(), "code" => 500]);
        }
    }

    public function actionDelete($post)
    {
        try {
            $keys = [$this->primary_key];
            $kode = $this->assignData($keys, $post);
            $response = $this->delete("delete from $this->table where {$this->primary_key}=:{$this->primary_key}", $kode);
            response_api($response);
        } catch (\Exception $e) {
            response_api(['success' => false, 'message' => 'Error: ' . $e->getMessage(), "code" => 500]);
        }
    }
}
