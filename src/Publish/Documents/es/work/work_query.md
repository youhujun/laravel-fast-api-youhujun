# es 工作常用

## 认证

### 查询所有认证
```json
GET /youhu_auth_services_index/_search
{ "query": { "match_all": {} } }
```

## 用户

### 查询所有用户

```json
GET /youhu_users_index/_search
{ "query": { "match_all": {} } }
```

### 批量恢复用户 (调试用)

```json
POST /youhu_users_index/_update_by_query 
{
  "query": {
    "terms": {
      "_id": [
          261071290206934,
          262212111510254
        ]
    }
  },
  "script": {
    "source": "ctx._source.deleted_at = null"
  }
}
```

## 管理员

## 相册

### 查询所有相册
```json
GET /youhu_albums_index/_search
{ "query": { "match_all": {} } }
```

### 查询所有相册图片
```json
GET /youhu_album_pictures_index/_search
{ "query": { "match_all": {} } }
```

## 日志

### 查询所有api事件日志
```json
GET /youhu_api_event_logs_index/_search
{ "query": { "match_all": {} } }
```

### 查询所有管理员事件日志

```json
GET /youhu_admin_event_logs_index/_search
{ "query": { "match_all": {} } }
```

### 查询所有用户事件日志

```json
GET /youhu_user_event_logs_index/_search
{ "query": { "match_all": {} } }
```

### 查询所有管理员登录日志

```json
GET /youhu_admin_login_logs_index/_search
{ "query": { "match_all": {} } }
```

### 查询所有用户登录日志

```json
GET /youhu_user_login_logs_index/_search
{ "query": { "match_all": {} } }
```