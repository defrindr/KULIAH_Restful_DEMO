<?php

/**
 * Query Builder
 * it always need some improvement
 */
class QueryBuilder
{
    /**
     * $db
     * variable to save instance of MySQL PDO from Connection
     */
    protected $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function ExecQuery($query, $binding = [], $with_response = true)
    {
        try {
            $model = $this->db->prepare($query);
            $model->setFetchMode(PDO::FETCH_OBJ);
            $model->execute($binding);
            $total_data = $model->rowCount();

            $response = [];
            $response['message'] = 'Data berhasil didapatkan';

            if ($total_data) {
                while ($row = $model->fetch()) {
                    if (strpos($query, "limit 1") !== false) {
                        $response['data'] = $row;
                    } else {
                        $response['data'][] = $row;
                    }
                }
                if (!$with_response) return $response['data'];
            } else {
                if (!$with_response) return null;
                $response['success'] = false;
                $response['message'] = 'Data kosong';
                $response['code'] = 404;
            }
        } catch (\Throwable $th) {
            $response['message'] = 'Internal Server Error: ' . $th->getMessage();
            $response['code'] = 500;
        }
        return $response;
    }


    function insert($query, $binding)
    {
        $response['message'] = "Data berhasil ditambahkan";
        try {
            $model = $this->db->prepare($query);
            $model->setFetchMode(PDO::FETCH_OBJ);
            $model->execute($binding);
        } catch (\Throwable $th) {
            $response['success'] = false;
            $response['message'] = "Data gagal ditambahkan: " . $th->getMessage();
            $response['code'] = 500;
        }
        return $response;
    }


    function update($query, $binding)
    {
        $response['message'] = "Data berhasil diubah";
        try {
            $model = $this->db->prepare($query);
            $model->setFetchMode(PDO::FETCH_OBJ);
            $model->execute($binding);
        } catch (\Throwable $th) {
            $response['success'] = false;
            $response['message'] = "Data gagal diubah: " . $th->getMessage();
            $response['code'] = 500;
        }

        return $response;
    }

    function delete($query, $binding = [])
    {
        $model = $this->db->prepare($query);
        $model->setFetchMode(PDO::FETCH_OBJ);
        if ($model->execute($binding)) {
            $response['message'] = "Data berhasil dihapus";
        } else {
            $response['success'] = false;
            $response['message'] = "Data gagal dihapus";
            $response['code'] = 400;
        }

        return $response;
    }

    function assignData($keys, $post, $type = "create")
    {
        $params = [];
        foreach ($keys as $key) {
            $is_field_required = in_array($key, $this->required) && $type == "create";
            if ($is_field_required && isset($post[$key]) == false) response_api(['success' => false, 'message' => "Field '$key' tidak boleh kosong", "code" => 400]);
            if ($is_field_required && $post[$key] == "" && $key != $this->primary_key) response_api(['success' => false, 'message' => "Field '$key' tidak boleh kosong", "code" => 400]);
            $params[":" . $key] = $post[$key];
        }
        return $params;
    }

    function createInsertQuery($table, $arrays)
    {
        $template_1 = implode(", ", $arrays);
        foreach ($arrays as $key => $item) $arrays[$key] = ":$item";
        $template_2 = implode(", ", $arrays);
        $query = "INSERT INTO $table ($template_1) values ($template_2)";

        return $query;
    }

    function createUpdateQuery($table, $arrays, $primary)
    {
        foreach ($arrays as $key => $item) $arrays[$key] = "$item=:$item";
        $template_1 = implode(", ", $arrays);
        $query = "UPDATE $table SET $template_1 where $primary=:$primary";

        return $query;
    }
}
