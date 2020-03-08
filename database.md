## データベース作成

- name: doodle_google_search_clone

```sql
create database doodle_google_search_clone
```

## サイト情報テーブル

```sql
CREATE TABLE sites(
id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
url VARCHAR(512) ,
title VARCHAR(512),
description VARCHAR(512) ,
keywords VARCHAR(512) ,
clicks int NOT NULL DEFAULT 0
)
```

## 画像テーブル

```sql
CREATE TABLE sites(
id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
siteUrl VARCHAR(512) ,
imageUrl VARCHAR(512),
alt VARCHAR(512) ,
title VARCHAR(512) ,
clicks int NOT NULL DEFAULT 0,
broken TINYINT NOT NULL DEFAULT 0
)
```
