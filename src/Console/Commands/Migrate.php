<?php

/**
 * Laika Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MMC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Laika\Core\Console\Commands;

use Laika\Core\Console\Command;
use Laika\Core\Helper\Config;
use Laika\App\Model\Options;
use Laika\Model\Blueprint;
use Laika\Model\Schema;
use Laika\Model\DB;
use Exception;

class Migrate extends Command
{
    /**
     * Default Options Keys
     * @return array<string,string>
     */
    private function defaulKeys(): array
    {
        return [
            'app.name'      =>  'CBM Framework',
            'time.zone'     =>  'Europe/London',
            'time.format'   =>  'Y-M-d H:i:s',
            'dbsession'     =>  'yes',
            'debug'         =>  'yes',
            'app.path'      =>  realpath(APP_PATH ?? __DIR__ . '/../../../../../../'),
            'admin.icon'    =>  'favicon.ico',
            'admin.logo'    =>  'logo.png',
            'csrf.lifetime' =>  '300',
        ];
    }

    /**
     * Run the command to create a new controller.
     * @param array $params
     * @return void
     */
    public function run(array $params): void
    {
        try {
            // Make Table
            $model = new Options();
            $model->migrate();

            // Insert Default Data
            $db = DB::getInstance();

            // Check Option Table Doesn't Exists
            if (!empty($model->all())) {
                throw new Exception("Database Table '{$model->table}' Already Exists. Please Remove Old Table First");
            }

            $rows = [];
            foreach ($this->defaulKeys() as $key => $val) {
                $rows[] = [$model->name => $key, $model->value => $val, $model->default => 'yes'];
            }
            // Insert Options
            $model->insertMany($rows);

            // Create Secret Config File if Not Exist
            if (!Config::has('secret')) {
                Config::create('secret', ['key' => bin2hex(random_bytes(64))]);
            }
            // Create Secret Key Value Not Exist or Empty
            if (!Config::has('secret', 'key')) {
                Config::set('secret', 'key', bin2hex(random_bytes(64)));
            }
            // Success Message
            $this->info("App Migrated Successfully");
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            return;
        }
    }
}
