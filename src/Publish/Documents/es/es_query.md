# es 查询手册

>must/filter = AND
>should = OR
>minimum_should_match = 至少满足一个 OR

## 查询所有

```json
GET /index_name/_search
{ "query": { "match_all": {} } }
```

## 精准查询某一个文档

### 根据物文档id查询

```json
GET /index_name/_doc/doc_id
```
### term 用于不分词、完全精准匹配
```json
GET /index_name/_search
{
  "query": {
    "term": { "field_name": "field_value" }
  }
}
```

## 高级

## 批量条件更新（调试神器）
作用：WHERE IN + 条件 + 批量更新，恢复数据、批量修改专用

```json
POST /index_name/_update_by_query 
{
  "query": {
    "bool": {
      "must": [
        { "terms": { "_id": [id1, id2] } },
        { "exists": { "field": "deleted_at" } }
      ]
    }
  },
  "script": {
    "source": "ctx._source.deleted_at = null"
  }
}
```

