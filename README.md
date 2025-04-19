

---

# 📦 amoSDK - PHP PDO Wrapper

A simple and powerful PHP SDK built on top of PDO for easy interaction with MySQL databases.

> Author: Amanuel Ayele  
> Version: 1.0  
> License: MIT  

---

## 📌 Features

- Easy CRUD (Create, Read, Update, Delete) operations
- Safe and secure prepared statements
- Fluent interface for chaining
- Supports transactions
- Executes raw SQL queries
- Counts records with conditions

---

## 🚀 Getting Started

### ✅ Requirements

- PHP >= 7.1
- MySQL
- PDO extension enabled

---

## 🏗️ Initialization

```php
require_once 'amoSDK.php';

$db = new amoSDK('localhost', 'mydatabase', 'username', 'password');
```

---

## 🔧 Methods

### ➕ `table(string $tableName): self`

Sets the active table for subsequent operations.

```php
$db->table('users');
```

---

### 🆕 `create(array $data): int`

Inserts a new record into the current table.

#### Parameters:
- `array $data`: Associative array of column names and values

#### Returns:
- `int`: ID of the inserted record

#### Example:
```php
$userId = $db->table('users')->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => date('Y-m-d H:i:s')
]);
```

---

### 📖 `read(array $conditions = [], string $fields = '*', string $orderBy = '', int|null $limit = null): array`

Fetches records from the current table.

#### Parameters:
- `array $conditions`: Key-value conditions
- `string $fields`: Columns to select (default: `*`)
- `string $orderBy`: SQL `ORDER BY` clause
- `int|null $limit`: Max number of records

#### Returns:
- `array`: List of matched rows

#### Example:
```php
$users = $db->table('users')->read(
    ['status' => 'active'],
    '*',
    'created_at DESC',
    10
);
```

---

### 🛠️ `update(array $data, array $conditions): bool`

Updates records in the current table.

#### Parameters:
- `array $data`: Columns and values to update
- `array $conditions`: Key-value conditions

#### Returns:
- `bool`: Success status

#### Example:
```php
$db->table('users')->update(
    ['status' => 'inactive'],
    ['id' => 1]
);
```

---

### ❌ `delete(array $conditions): bool`

Deletes records from the current table.

#### Parameters:
- `array $conditions`: Key-value conditions

#### Returns:
- `bool`: Success status

#### Example:
```php
$db->table('users')->delete(['id' => 1]);
```

---

### 📊 `count(array $conditions = []): int`

Counts the number of records in the current table that match given conditions.

#### Parameters:
- `array $conditions`: Key-value conditions

#### Returns:
- `int`: Number of records

#### Example:
```php
$activeCount = $db->table('users')->count(['status' => 'active']);
```

---

### 🔍 `query(string $sql, array $params = []): array`

Executes a raw SQL query with optional parameter binding.

#### Parameters:
- `string $sql`: Raw SQL query
- `array $params`: Bound parameters for prepared statement

#### Returns:
- `array`: Query result set

#### Example:
```php
$results = $db->query(
    "SELECT * FROM users WHERE email LIKE ?",
    ['%@example.com']
);
```

---

### 🧾 Transaction Support

#### `beginTransaction(): bool`

Starts a database transaction.

#### `commit(): bool`

Commits the current transaction.

#### `rollback(): bool`

Rolls back the current transaction.

#### Example:
```php
$db->beginTransaction();
try {
    $db->table('users')->create(['name' => 'User 1']);
    $db->table('users')->create(['name' => 'User 2']);
    $db->commit();
} catch (\Exception $e) {
    $db->rollback();
    throw $e;
}
```

---

## 🧪 Full Usage Example

```php
try {
    $db = new DatabaseSDK('localhost', 'mydatabase', 'user', 'pass');

    // Insert new record
    $userId = $db->table('users')->create([
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // Read records
    $users = $db->table('users')->read(['status' => 'active'], '*', 'created_at DESC', 5);

    // Update record
    $db->table('users')->update(['status' => 'inactive'], ['id' => $userId]);

    // Delete record
    $db->table('users')->delete(['id' => $userId]);

    // Count active users
    $count = $db->table('users')->count(['status' => 'active']);

    // Raw query
    $emails = $db->query("SELECT email FROM users WHERE email LIKE ?", ['%@example.com']);

    // Transactions
    $db->beginTransaction();
    try {
        $db->table('users')->create(['name' => 'Transaction User 1']);
        $db->table('users')->create(['name' => 'Transaction User 2']);
        $db->commit();
    } catch (\Exception $e) {
        $db->rollback();
        echo "Transaction failed: " . $e->getMessage();
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## 📌 Notes

- All methods use **prepared statements** to prevent SQL injection.
- Always set the table using `table()` before executing CRUD operations.
- Make sure your MySQL user has proper privileges for the database and table.

---

## ✅ Best Practices

- Catch exceptions to handle errors gracefully.
- Use transactions when inserting/updating multiple related rows.
- Avoid raw SQL unless necessary for flexibility or performance.

---

## 📬 Contributing

Pull requests and suggestions are welcome. Open an issue or fork the repo!

---
