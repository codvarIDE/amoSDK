
---

```markdown
# 🧠 AmoSDK - PHP API Wrapper

AmoSDK is a lightweight, extensible PHP SDK that makes HTTP requests dead simple. Built with dev-friendly features like method chaining, retry logic, caching, logging, and more.

## ⚡ Features

- 🔁 Retries with delay
- 🕓 Timeout control
- 📁 Caching (with TTL)
- 📜 Logging (file-based)
- 📦 Supports GET, POST, PUT, DELETE, PATCH
- 🔗 Method chaining for clean code
- ✅ Easy to extend or customize

## 📦 Installation

Just clone the repo or copy `AmoSDK.php` into your project:

```bash
git clone https://github.com/codvarIDE/amosdk.git
```

Or manually drop `AmoSDK.php` into your project and require it:

```php
require_once 'path/to/AmoSDK.php';
```

_Composer support coming soon!_

---

## 🚀 Basic Usage

### 🔹 Simple Request



```php
require_once 'AmoSDK.php';

<?php
// 1. Simple GET Request
$response = AmoSDK::get('https://api.example.com/users')
    ->pull();
print_r($response);

// 2. GET Request with Headers
$headers = ['Authorization: Bearer your_token'];
$response = AmoSDK::get('https://api.example.com/protected-data', $headers)
    ->pull();
print_r($response);

// 3. POST Request with Data
$data = [
    'username' => 'john_doe',
    'email' => 'john@example.com'
];
$response = AmoSDK::post('https://api.example.com/users', $data)
    ->pull();
print_r($response);

// 4. PUT Request for Updating
$update_data = ['status' => 'active'];
$response = AmoSDK::put('https://api.example.com/users/123', $update_data)
    ->pull();
print_r($response);


// 5. DELETE Request
$response = AmoSDK::delete('https://api.example.com/users/123')
    ->pull();
print_r($response);
```

---


---

## ⚙️ Advanced Usage with Features



```php
<?php
// 1. Using Caching
$response = AmoSDK::get('https://api.example.com/data')
    ->enableCache(1800) // Cache for 30 minutes
    ->pull();

// 2. With Logging
$response = AmoSDK::post('https://api.example.com/orders', $data)
    ->enableLogging()
    ->pull();

// 3. Custom Timeout and Retry
$response = AmoSDK::get('https://api.example.com/large-data')
    ->setTimeout(60)        // 60 seconds timeout
    ->setRetry(5, 2)       // 5 attempts, 2 seconds delay
    ->pull();

// 4. Combining Multiple Features
$response = AmoSDK::post('https://api.example.com/important-data', $data)
    ->enableCache(3600)    // 1 hour cache
    ->enableLogging()
    ->setTimeout(30)
    ->setRetry(3, 1)
    ->pull();
```

### 🔸 DELETE Request

```php
$response = AmoSDK::delete('https://api.example.com/user/123')
    ->pull();
```

---

## 🧪 Features Explained

| Feature         | Description                                                                 |
|----------------|-----------------------------------------------------------------------------|
| `enableCache()` | Caches responses to `cache/` dir using a hash key and TTL (default 3600s)  |
| `enableLogging()` | Logs requests/responses/errors to `logs/api_logs.txt`                     |
| `setTimeout($sec)` | Sets max request duration in seconds                                     |
| `setRetry($attempts, $delay)` | Retry logic for failed requests                                |

---



## 📜 License

MIT License. Do whatever you want—just don't forget to star the repo ⭐

