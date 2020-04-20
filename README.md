
## 基于 阿里云短信 验证的 业务类


## 安装

安装扩展
```
composer require gemor/fzo
```

发布配置文件
```
php artisan vendor:publish --provider="Fzo\ServiceProvider"
```

数据库创建(手动创建一下吧)
```
CREATE TABLE `verify_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) NOT NULL COMMENT '手机号',
  `code` int(11) NOT NULL DEFAULT '0' COMMENT '验证码',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '类型',
  `status` varchar(255) NOT NULL DEFAULT '' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `response` text,
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`(191)) USING BTREE COMMENT '手机号索引'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
```


### 用法

```
/**
 * 发送短信
 * $phone 手机号
 */
(new \Fzo\VerifyCode())->send($phone)

/**
 * 验证短信
 * $phone 手机号
 * $code 短信验证码
 */
(new \Fzo\VerifyCode())->verify($phone, $code)

```


