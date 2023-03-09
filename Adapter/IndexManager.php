<?php
declare(strict_types=1);

namespace Monogo\TypesenseCmsPages\Adapter;

use Http\Client\Exception;
use Monogo\TypesenseCore\Adapter\Client;
use Monogo\TypesenseCore\Adapter\IndexManager as IndexManagerCore;
use Monogo\TypesenseCore\Exceptions\ConnectionException;
use Monogo\TypesenseCore\Services\LogService;
use Monogo\TypesenseCmsPages\Services\ConfigService;
use Typesense\Exceptions\ConfigError;

class IndexManager extends IndexManagerCore
{
    /**
     * @var ConfigService
     */
    protected ConfigService $configService;

    /**
     * @param Client $client
     * @param LogService $logService
     * @param ConfigService $configService
     * @throws Exception
     * @throws ConnectionException
     * @throws ConfigError
     */
    public function __construct(Client $client, LogService $logService, ConfigService $configService)
    {
        parent::__construct($client, $logService);
        $this->configService = $configService;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getIndexSchema(string $name): array
    {
        return [
            'name' => $name,
            'fields' => $this->getFormattedFields(),
            'default_sorting_field' => 'page_id'
        ];
    }

    /**
     * @return array
     */
    public function getFormattedFields(): array
    {
        $formattedFields = [];
        $fields = $this->getIndexFields();
        foreach ($fields as $field) {
            $formattedFields[] = $field;
        }
        return $formattedFields;
    }

    /**
     * @return array
     */
    public function getIndexFields(): array
    {
        $defaultSchema = $this->getDefaultSchema();
        $configSchema = $this->configService->getSchema();
        return array_merge($defaultSchema, $configSchema);
    }

    /**
     * @return array[]
     */
    public function getDefaultSchema(): array
    {
        return [
            'page_id' => ['name' => 'page_id', 'type' => 'int32', 'optional' => false, 'index' => true],
            'identifier' => ['name' => 'identifier', 'type' => 'string', 'optional' => false, 'index' => true],
            'title' => ['name' => 'title', 'type' => 'string', 'optional' => false, 'index' => true],
            'meta_keywords' => ['name' => 'meta_keywords', 'type' => 'string', 'optional' => true, 'index' => false],
            'meta_description' => ['name' => 'meta_description', 'type' => 'string', 'optional' => true, 'index' => false],
            'page_layout' => ['name' => 'page_layout', 'type' => 'string', 'optional' => true, 'index' => false],
            'content_heading' => ['name' => 'content_heading', 'type' => 'string', 'optional' => true, 'index' => true],
            'content' => ['name' => 'content', 'type' => 'string', 'optional' => true, 'index' => false],
            'content_stripped' => ['name' => 'content_stripped', 'type' => 'string', 'optional' => false, 'index' => true],
            'url' => ['name' => 'url', 'type' => 'string', 'optional' => false, 'index' => true],
        ];
    }
}

