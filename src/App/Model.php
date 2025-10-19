<?php

/**
 * Laika PHP MVC Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Laika\Core\App;

use Laika\Model\Model as BaseModel;

// Forbidden Access
defined('APP_PATH') || http_response_code(403) . die('403 Forbidden Access!');

class Model extends BaseModel
{
    // Status Table Name
    public string $status_table;

    // List
    private array $list = [];

    /**
     * Get Limit
     * @param int|string $page Page Number
     * @param array<string,int|string|null> $where Example: ['id'=>1, 'status'=>'active']
     * @return array<int,array>
     */
    public function limit(int|string $page = 1, array $where = []): array
    {
        return $this->db
            ->table($this->table)
            ->where($where)
            ->limit((int) option('app.limit', 20))
            ->offset($page)
            ->get();
    }


    // Get Statuses
    /**
     * @param string $column Optional Parameter. Default is null.
     * @return array<string,string>
     */
    public function statuses(?string $column = null): array
    {
        $statuses = [];
        $column = $column ?: 'status';
        $data = $this->db->table($this->status_table)->select($column)->get();
        foreach ($data as $val) {
            $statuses[strtolower($val[$column])] = ucwords($val[$column]);
        }
        return $statuses;
    }

    /**
     * Get Selected Column
     * @param string $columns Example 'id,title'
     * @param array<string,int|string|null> $where Example ['id'=>1].
     */
    public function getColumns(string $columns, array $where = []): array
    {
        return $this->db->table($this->table)->select($columns)->where($where)->get();
    }

    /**
     * Get List
     * @param string $column1 Optional Parameter
     * @param string $column2 Required Parameter
     * @param array<int|string,int|string|null> $where Optiona Argument. Example ['id'=>1].
     */
    public function list(string $column1, string $column2, array $where = []): array
    {
        $data = call_user_func([$this, 'getColumns'], "{$column1}, {$column2}", $where);
        foreach ($data as $val) {
            $this->list[$val[$column1]] = $val[$column2];
        }
        return $this->list;
    }
}
