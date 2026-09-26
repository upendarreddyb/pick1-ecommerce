<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class StoreSettingModel extends Model
{
    public const DEFAULTS = [
        'shipping_charge' => 49.0,
        'free_shipping_minimum' => 349.0,
        'gst_rate' => 4.4,
    ];

    protected $table = 'store_settings';
    protected $allowedFields = ['setting_key', 'setting_value'];
    protected $useTimestamps = true;

    public static function values(): array
    {
        $values = self::DEFAULTS;
        $database = db_connect();
        if (! $database->tableExists('store_settings')) return $values;

        foreach ((new self())->whereIn('setting_key', array_keys($values))->findAll() as $row) {
            $key = (string) ($row['setting_key'] ?? '');
            if (array_key_exists($key, $values)) $values[$key] = max(0, (float) $row['setting_value']);
        }

        return $values;
    }

    public static function ensureTable(): void
    {
        $database = db_connect();
        if (! $database->tableExists('store_settings')) {
            $forge = Database::forge();
            $forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'setting_key' => ['type' => 'VARCHAR', 'constraint' => 80],
                'setting_value' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addUniqueKey('setting_key');
            $forge->createTable('store_settings', true);
        }

        $model = new self();
        foreach (self::DEFAULTS as $key => $value) {
            if (! $model->where('setting_key', $key)->first()) {
                $model->insert(['setting_key' => $key, 'setting_value' => $value]);
            }
        }
    }

    public function saveValues(array $values): void
    {
        foreach (self::DEFAULTS as $key => $default) {
            $value = max(0, (float) ($values[$key] ?? $default));
            $row = $this->where('setting_key', $key)->first();
            $data = ['setting_key' => $key, 'setting_value' => $value];
            $row ? $this->update((int) $row['id'], $data) : $this->insert($data);
        }
    }
}
