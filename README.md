Say less 😎—here’s a full-on **Markdown README.md** doc you can drop straight into your GitHub repo for your SDK.

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
git clone https://github.com/yourusername/amosdk.git
```

Or manually drop `AmoSDK.php` into your project and require it:

```php
require_once 'path/to/AmoSDK.php';
```

_Composer support coming soon!_

---

## 🚀 Basic Usage

### 🔹 Simple GET Request



```php
require_once 'AmoSDK.php';

$response = AmoSDK::get('https://api.example.com/data')
    ->enableCache(3600)           // optional: enable 1 hour caching
    ->enableLogging()             // optional: enable logging to logs/api_logs.txt
    ->setTimeout(10)              // optional: set timeout to 10 seconds
    ->setRetry(3, 2)              // optional: retry 3 times with 2s delay
    ->pull();

print_r($response);
```

---

### 🔹 POST Request with Data

```php
$data = [
    'name' => 'Jane Doe',
    'email' => 'jane@example.com'
];

$headers = [
    'Authorization: Bearer YOUR_API_TOKEN'
];

$response = AmoSDK::post('https://api.example.com/users', $data, $headers)
    ->enableLogging()
    ->pull();

print_r($response);
```

---

## ⚙️ Advanced Usage

### 🔸 PUT Request

```php
$data = ['status' => 'active'];

$response = AmoSDK::put('https://api.example.com/user/123', $data)
    ->setTimeout(20)
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

