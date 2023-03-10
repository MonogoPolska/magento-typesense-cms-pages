# Typesense Magento integration - CMS Pages indexer

Indexer for Magento CMS Pages

## Configuration
As the first step, Go to Magento Admin &rarr; Configuration &rarr; Typesense &rarr; Catalog Categories


## Indexers

| Indexer                                               | Description                                                                                                              |
|-------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------|
| ```bin/magento indexer:reindex typesense_cms_pages``` | Typesense CMS Pages indexer. To enable this, configure <br/>Stores &rarr; Configuration &rarr;Typesense &rarr; CMS Pages |

## Initial schema
```
'name' => $prefix . '_cms_pages' . $suffix,
'fields' => [
    ['name' => 'page_id', 'type' => 'int32', 'optional' => false, 'index' => true],
    ['name' => 'identifier', 'type' => 'string', 'optional' => false, 'index' => true],
    ['name' => 'title', 'type' => 'string', 'optional' => false, 'index' => true],
    ['name' => 'meta_keywords', 'type' => 'string', 'optional' => true, 'index' => false],
    ['name' => 'meta_description', 'type' => 'string', 'optional' => true, 'index' => false],
    ['name' => 'page_layout', 'type' => 'string', 'optional' => true, 'index' => false],
    ['name' => 'content_heading', 'type' => 'string', 'optional' => true, 'index' => true],
    ['name' => 'content', 'type' => 'string', 'optional' => true, 'index' => false],
    ['name' => 'content_stripped', 'type' => 'string', 'optional' => true, 'index' => true],
    ['name' => 'url', 'type' => 'string', 'optional' => false, 'index' => true],
],
'default_sorting_field' => 'page_id'
```

# Credits
- [Monogo](https://monogo.pl/en)
- [Typesense](https://typesense.org)
- [Official Algolia magento module](https://github.com/algolia/algoliasearch-magento-2)
