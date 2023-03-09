<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Services;

use Magento\Store\Model\ScopeInterface as ScopeConfig;
use Monogo\TypesenseCore\Services\ConfigService as CoreConfigService;

class ConfigService extends CoreConfigService
{
    /**
     * Config paths
     */
    const TYPESENSE_CMS_PAGES_ENABLED = 'typesense_cms_pages/settings/enabled';
    const TYPESENSE_EXCLUDED_PAGES = 'typesense_cms_pages/settings/excluded_pages';
    const TYPESENSE_CMS_PAGES_SCHEMA = 'typesense_cms_pages/settings/schema';

    /**
     * @param $storeId
     * @return bool|null
     */
    public function isEnabled($storeId = null): ?bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::TYPESENSE_CMS_PAGES_ENABLED,
            ScopeConfig::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getExcludedPages($storeId = null): array
    {
        $attrs = $this->unserialize($this->scopeConfig->getValue(
            self::TYPESENSE_EXCLUDED_PAGES,
            ScopeConfig::SCOPE_STORE,
            $storeId
        ));
        if (is_array($attrs)) {
            return $attrs;
        }
        return [];
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getSchema($storeId = null): array
    {
        $attributes = [];
        $booleanProperties = ['facet', 'optional', 'index', 'infix', 'sort'];
        $attrs = $this->unserialize($this->scopeConfig->getValue(
            self::TYPESENSE_CMS_PAGES_SCHEMA,
            ScopeConfig::SCOPE_STORE,
            $storeId
        ));
        if (is_array($attrs)) {

            foreach ($attrs as $attr) {
                foreach ($attr as $key => $item) {
                    if (in_array($key, $booleanProperties)) {
                        $attr[$key] = (bool)$item;
                    }
                }
                if (!$attr['index']) {
                    $attr['optional'] = true;
                }
                $attributes[$attr['name']] = $attr;
            }
            return $attributes;
        }
        return [];
    }
}
