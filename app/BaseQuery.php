<?php


namespace app;


use databases\DBConnection;
use Exception;
use PDO;
use PDOException;

abstract class BaseQuery
{
    public function __construct()
    {
        $this->db = DBConnection::get_connection();
    }

    /**
     * The db being used
     * @var
     */
    protected $db;
    /**
     * The table of the calling model class
     * @var
     */
    protected $table;

    /**
     * This value helps us create relationships of current model in other tables
     * ensure the name you give your relationship is constant on all tables
     * to ensure you can fetch your relationship correctly
     * @var
     */
    protected $relationship_id;

    /**
     * This is used to specify the type of relationship the current model has with
     * the other classes it is related to e.g belongsTo, hasMany
     * @var string[]
     */
    protected $relationship_with = [];
    /**
     * The default Column to be used for reference as primary key
     * @var string
     */
    protected $key = 'id';
    /**
     * The query to be executed
     * @var
     */

    protected $query;

    protected $select = 'SELECT * FROM ';

    protected $select_count = 'SELECT COUNT(*) FROM ';

    protected $insert = 'INSERT INTO ';

    protected $where = ' WHERE ';

    protected $set = ' SET ';

    protected $unique_columns = []; // set some columns to be unique to prevent inserting duplicates

    protected $result;

    /** To provide the table in the database we are connecting to */
    abstract public function set_table();


    /**
     * Get data from database
     * @return object
     */
    public function get()
    {
        $result = $this->query_db($this->query);
        return (object)$result->fetchAll();
    }

    /**
     * get all record
     * @return object
     */
    public function get_all(array $with = null, $select = '')
    {

        if ($with) {
            return $this->left_join_query($with, null, $select);
        }

        return $this->select_from_table();
    }

    /**
     * find a record using id
     * @param $id
     * @return object
     */
    public function find($id)
    {
        $query = ['id' => $id];
        $this->where($query, '*', true);
        return $this->first();
    }

    public function find_by_slug($slug, $table = null)
    {
        $query = ['WHERE' => ['name', 'LIKE', $slug]];
        $this->where($query, '*', false, $table);
        return $this->first();
    }

    /**
     * select specific columns
     * @param string $query
     */
    public function select(string $query)
    {
        $this->select = $query;
    }

    /**
     *
     * @return object
     */
    public function first()
    {
        return $this->query->fetch();
    }

    /**
     * Delete data using Id
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $sql = $this->db->query('DELETE FROM ' . $this->table . ' WHERE id=' . $id);

        return $sql->query($sql);
    }

    /**
     * Delete some records using their id
     * @param array $ids
     */
    public function mass_delete(array $ids)
    {
        $list = implode(",", $ids);

        $this->query_db('DELETE FROM ' . $this->table . ' WHERE id IN (' . $list . ')');
    }

    /**
     * @param array $query
     * @param string $columns
     * @param bool $select
     * @param null $table
     * @return $this
     */
    public function where(array $query, $columns = '*', $select = false, $table = null)
    {
        $this->query = $this->select_from_table($columns, $query, $select, $table);
        return $this;
    }

    /**
     *
     */
    public function with()
    {

    }


    /**
     * @param array $data
     * @param string|null $where
     * @return object
     */
    public function create(array $data, string $where = null)
    {
        $query = $this->insert . $this->table . ' ' . $this->prepare_insert_query($data);
        if ($where) {
            $query .= $where;
        }
        $id = $this->insert_query_and_return_result($query, $data);
        return $this->find($id);
    }

    /**
     * @param array $data
     * @param string|null $where
     * @return $this|object
     */
    public function first_or_create(array $data, $table = null)
    {
        try {
            $check = $this->select_from_table('COUNT(*)', $data, true, $table);

            if ($check->fetchColumn() > 0) {
                return $this->where($data, null, true, $table)->first();
            }
            return $this->find($this->insert_into_table($data, $table));
        } catch (PDOException $e) {
            $existing = "Integrity constraint violation: 1062 Duplicate entry";
            if (strpos($e->getMessage(), $existing) !== FALSE) {
                // Take some action if there is a key constraint violation, i.e. duplicate name
                return $this->where($data)->first();
            }

            throw $e;
        }
    }

    /**
     * relationship id should always tally with their implementation e.g product
     * table should have a product_id relationship and product_attributes
     * should have a product_attribute_id relationship
     * @param $model_object
     * @param $related_model
     * @param array $paras
     * @return object
     */
    public function add_related_details($model_object, $related_model, array $paras)
    {
        $this->store_relationship($model_object, $related_model, $paras);
        // Fetch all
        return $this->left_join_query([$related_model]); // create the join query

    }

    /**
     * @param $object
     * @param string $related_model
     * @param array $related_model_values
     * @return mixed
     */
    private function store_relationship($object, string $related_model, array $related_model_values)
    {

        $related_model_class = new $related_model();
        $related_model_table = $related_model_class->table;
        $related_model_relationship_type = $related_model_class->relationship_with;
        if ($related_model_relationship_type) {
            $set_relationship = array_search($this->table, $related_model_relationship_type, false);
        } else {
            $set_relationship = 'belongsTo'; //DEFAULT
        }
        switch ($set_relationship) {
            case "hasMany":
                return $this->insert_into_table($related_model_values, $related_model_table);

            default:
                return $this->insert_into_table(array_merge([$this->relationship_id => $object['id']], $related_model_values), $related_model_table);

        }
    }

    public function left_join_query(array $models, $where = null, $columns = '*')
    {
        $query2 = '';
        foreach ($models as $model) {
            $model_class = new $model();
            $model_table = $model_class->table;
            $model_relationship_type = $model_class->relationship_with;
            if ($model_relationship_type) {
                $set_relationship = array_search($this->table, $model_relationship_type);
            } else {
                $set_relationship = 'belongsTo'; //DEFAULT
            }
            switch ($set_relationship) {
                case "hasMany":
                    $query2 .= ' LEFT JOIN ' . $model_table . ' ON ' . $this->table . '.' . $model_class->relationship_id . ' = ' . $model_table . '.' . $this->key;
                    break;
                default:
                    $query2 .= ' LEFT JOIN ' . $model_table . ' ON ' . $this->table . '.' . $this->key . ' = ' . $model_table . '.' . $this->relationship_id;
                    break;
            }
        }

        return $this->select_from_join_table($columns, $query2, null, true);

    }


    /**
     * Count the number of records that exists in database
     * @return mixed
     */
    public function count()
    {
        return $this->db->query($this->query)->fetchColumn();
    }

    /**
     * Takes a prepared query, executes it and returns the record
     *
     * @param $query
     * @return object
     */
    private function insert_query_and_return_result($query): object
    {
        $this->insert_into_table($query);
        $id = $this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * @param $model
     * @param $related_model
     * @param array $data
     */
    private function merge_with_relationship($model, $table_name, array $data)
    {
        // var_dump($model, $table_name, $data);
    }


    public function insert_into_table(array $param, $table = null)
    {
        $columns = $this->stringify_columns($param);
        $values = $this->stringify_values($param);

        $query = $this->insert . ($table ?? $this->table) . '(' . $columns . ')values(' . $values . ')';

        return $this->prepare_statement_and_execute($query, $param);
    }

    public function select_from_table($columns = null, array $conditions = null, bool $simple_select = false, $table = null)
    {
        // SELECT * FROM TABLE NAME
        $query_string = 'SELECT ' . ($columns ? trim($columns) : '*') . ' FROM ' . trim($table ?? $this->table);

        if (!empty($conditions)) {
            $conditions_array = $simple_select ? $this->stringify_simple_select($conditions, true) : $this->stringify_select_statement($conditions);
            $query_string .= ' ' . $conditions_array['string'];

            return $this->prepare_statement_and_execute($query_string, $conditions_array['values']);
        }
        return $this->query_db($query_string)->fetchAll();
    }


    /**
     * @param null $columns
     * @param string $join
     * @param null $conditions
     * @param false $simple_select
     * @param null $table
     * @return mixed
     */
    public function select_from_join_table($columns = null, $join = '', $conditions = null, $simple_select = false, $table = null, $order_by = null)
    {
        $order_by = $order_by ?? ' ORDER BY ' . $this->table . '.id DESC';
        $query_string = 'SELECT ' . ($columns ? trim($columns) : '*') . ' FROM ' . trim($table ?? $this->table . ' ' . $join) . $order_by;
        if (!empty($conditions)) {
            $conditions_array = $simple_select ? $this->stringify_simple_select($conditions, true) : $this->stringify_select_statement($conditions);
            $query_string .= ' ' . $conditions_array['string'];

            return $this->prepare_statement_and_execute($query_string, $conditions_array['values']);
        }
        return $this->query_db($query_string)->fetchAll();
    }

    private function stringify_simple_select(array $conditions, $select = false)
    {
        $stringify_select_statement = '';
        $param = [];
        $count = 1;
        foreach ($conditions as $key => $inner_array) {
            $stringify_select_statement .= ' ' . $key . ' ';
            if (is_iterable($inner_array)) {
                foreach ($inner_array as $column => $value) {
                    if ($column == 2) {
                        $stringify_select_statement .= ':' . $inner_array[0];

                        $param[$inner_array[0]] = ' ' . $value . ' ';
                    } else {
                        $param[$inner_array[0]] = ' ' . $value . ' ';
                        $stringify_select_statement .= ' ' . $value . ' ';
                    }
                }
            } else {
                $end = $count === count($conditions); // if loop count is equal to number of arrays
                $param[$key] = trim($inner_array);
                $stringify_select_statement .= '=:';
                $stringify_select_statement .= trim($key) . ($end ? '' : ' AND ');

            }
            $count++;
        }

        $final_conditions['string'] = $select ? 'WHERE ' . $stringify_select_statement : $stringify_select_statement;
        $final_conditions['values'] = $param;
        return $final_conditions;
    }

    private function stringify_select_statement(array $conditions)
    {
        $stringify_select_statement = null;
        $param = [];
        foreach ($conditions as $key => $inner_array) {

            $stringify_select_statement .= ' ' . $key . ' ';

            foreach ($inner_array as $column => $value) {
                if ($column == 2) {

                    $stringify_select_statement .= ' :' . $inner_array[0];

                    $param[$inner_array[0]] = $value;

                } else {
                    $param[$inner_array[0]] = $value;
                    $stringify_select_statement .= ' ' . $value . ' ';
                }
            }

        }

        $final_conditions['string'] = $stringify_select_statement;
        $final_conditions['values'] = $param;

        return $final_conditions;
    }

    private function stringify_columns(array $query)
    {
        $stringify_columns = null;
        foreach ($query as $column => $value) {
            $stringify_columns .= $column . ', ';
        }
        return rtrim($stringify_columns, ', ');
    }


    private function stringify_values(array $query)
    {
        $stringify_values = null;
        foreach ($query as $column => $value) {
            $stringify_values .= ' :' . $column . ', ';
        }
        return rtrim($stringify_values, ', ');
    }

    private function prepare_statement_and_execute(string $query, array $param)
    {
        try {
            $prepared_statement = $this->prepare_db($query);

            $query_type = $this->strip_first_word_from_query($query);
            $prepared_statement->execute($param);

            switch ($query_type) {
                case 'SELECT':
                    return $prepared_statement;
                case 'INSERT':
                    return $this->db->lastInsertId();
            }

        } catch (Exception $e) {

            echo $e->getMessage();

        }
    }

    public function sanitize($value, $filter = FILTER_SANITIZE_STRING)
    {
        return filter_var(strip_tags(trim($value)), $filter);
    }

    private function prepare_db($query)
    {
        return $this->db->prepare($query);
    }

    private function query_db($query, $fetch = false)
    {
        $stmt = $this->db->query($query);
        if ($fetch) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
        }
        return $stmt;
    }

    private function strip_first_word_from_query(string $query)
    {
        $query = explode(' ', trim($query));
        return trim($query[0]);
    }

    public function __destruct()
    {
        DBConnection::close_connection();
    }
}

