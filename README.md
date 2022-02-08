Validation
==========

Validation是从Laravel5.2框架提取出来，让你更方便的在非Laravel项目中完成数据验证。

更多验证规则请阅读：https://laravel.com/docs/5.2/validation

# Usage

composer require zatxm\validation

```php
<?php

use Zatxm\Validation\Factory as ValidatorFactory;

// 初始化工厂对象
$factory = new ValidatorFactory();

// 验证
$rules = [
    'username' => 'required|min:5',
    'password' => 'confirmed',
    ///...
];

$validator = $factory->make($_POST, $rules);

// 判断验证是否通过
if ($validator->fails()) {
    // 失败
    print_r($validator->errors()->all());
}

```

## 自定义消息语言：

> 语言列表可以从这里拿：https://github.com/caouecs/Laravel-lang

以中文为例：

```php
use Zatxm\Validation\Factory as ValidatorFactory;
use Zatxm\Validation\Translator;

$messages = [
    'accepted'             => ':attribute 必须接受。',
    'active_url'           => ':attribute 不是一个有效的网址。',
    'after'                => ':attribute 必须是一个在 :date 之后的日期。',
    'alpha'                => ':attribute 只能由字母组成。',
    'alpha_dash'           => ':attribute 只能由字母、数字和斜杠组成。',
    'alpha_num'            => ':attribute 只能由字母和数字组成。',
    // ...
];

//初始化工厂对象
$factory = new ValidatorFactory(new Translator($messages));

```

## 设置属性名称

```php
$attributes = [
    'username' => '用户名',
    'password' => '密码',
];

$rules = [
    'username' => 'required|min:5',
    'password' => 'confirmed',
    ///...
];

$messages = [...]; // 自定义消息，如果你在初始化factory的时候已经设置了消息，则留空即可

$validator = $factory->make($input, $rules, $messages, $attributes);
```

# License

MIT
