<?php

namespace Kiboko\Bundle\DAMBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class KibokoDAMBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->alterOroIntegrationTransportTable($schema);
        $this->createKibokoDamNodeSlugTable($schema);
        $this->createKibokoDamNodeMetadataTable($schema);
        $this->createKibokoDamNodeNameTable($schema);
        $this->createKibokoDamDocumentNameTable($schema);
        $this->createKibokoDamAuthorizationTable($schema);
        $this->createKibokoDamDocumentSlugTable($schema);
        $this->createKibokoDamNodeAuthorizationTable($schema);
        $this->createKibokoDamNodeTable($schema);
        $this->createKibokoDamDocumentAuthorizationTable($schema);
        $this->createKibokoDamDocumentTable($schema);
        $this->createKibokoDamMetadataTable($schema);
        $this->createKibokoDamDocumentMetadataTable($schema);

        /** Foreign keys generation **/
        $this->addKibokoDamNodeSlugForeignKeys($schema);
        $this->addKibokoDamNodeMetadataForeignKeys($schema);
        $this->addKibokoDamNodeNameForeignKeys($schema);
        $this->addKibokoDamDocumentNameForeignKeys($schema);
        $this->addKibokoDamDocumentSlugForeignKeys($schema);
        $this->addKibokoDamNodeAuthorizationForeignKeys($schema);
        $this->addKibokoDamNodeForeignKeys($schema);
        $this->addKibokoDamDocumentAuthorizationForeignKeys($schema);
        $this->addKibokoDamDocumentForeignKeys($schema);
        $this->addKibokoDamMetadataForeignKeys($schema);
        $this->addKibokoDamDocumentMetadataForeignKeys($schema);
    }

    /**
     * Create oro_integration_transport table
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function alterOroIntegrationTransportTable(Schema $schema)
    {

        if (!$schema->hasTable('oro_integration_transport')) {
            return;
        }

        $table = $schema->getTable('oro_integration_transport');
        $table->addColumn('kbk_dam_path', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('kbk_dam_local_lock', 'boolean', ['notnull' => false]);
        $table->addColumn('kbk_dam_cdp_url', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('kbk_dam_cdp_client', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('kbk_dam_cdp_secret', 'string', ['notnull' => false, 'length' => 255]);
    }

    /**
     * Create kbk_dam_node_slug table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamNodeSlugTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_node_slug');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_e375fb21eb576e89');
        $table->setPrimaryKey(['document_id', 'localized_value_id']);
        $table->addIndex(['document_id'], 'idx_e375fb21c33f7837', []);
    }

    /**
     * Create kbk_dam_node_metadata table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamNodeMetadataTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_node_metadata');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('metadata_id', 'integer', []);
        $table->addUniqueIndex(['metadata_id'], 'uniq_df12d9b7dc9ee959');
        $table->addIndex(['document_id'], 'idx_df12d9b7c33f7837', []);
        $table->setPrimaryKey(['document_id', 'metadata_id']);
    }

    /**
     * Create kbk_dam_node_name table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamNodeNameTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_node_name');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['document_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_25cb1e45eb576e89');
        $table->addIndex(['document_id'], 'idx_25cb1e45c33f7837', []);
    }

    /**
     * Create kbk_dam_document_name table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamDocumentNameTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_document_name');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['document_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_d73c0763eb576e89');
        $table->addIndex(['document_id'], 'idx_d73c0763c33f7837', []);
    }

    /**
     * Create kbk_dam_authorization table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamAuthorizationTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_authorization');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('uuid', 'uuid', ['comment' => '(DC2Type:uuid)']);
        $table->addColumn('authorizations', 'array', ['comment' => '(DC2Type:array)']);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uuid'], 'uniq_54c67e19d17f50a6');
    }

    /**
     * Create kbk_dam_document_slug table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamDocumentSlugTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_document_slug');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_1182e207eb576e89');
        $table->addIndex(['document_id'], 'idx_1182e207c33f7837', []);
        $table->setPrimaryKey(['document_id', 'localized_value_id']);
    }

    /**
     * Create kbk_dam_node_authorization table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamNodeAuthorizationTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_node_authorization');
        $table->addColumn('node_id', 'integer', []);
        $table->addColumn('authorization_id', 'integer', []);
        $table->addIndex(['node_id'], 'idx_1c3301e4460d9fd7', []);
        $table->setPrimaryKey(['node_id', 'authorization_id']);
        $table->addUniqueIndex(['authorization_id'], 'uniq_1c3301e42f8b0eb2');
    }

    /**
     * Create kbk_dam_node table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamNodeTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_node');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('updated_by_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('root_id', 'integer', ['notnull' => false]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('integration_id', 'integer', ['notnull' => false]);
        $table->addColumn('thumbnail_id', 'integer', ['notnull' => false]);
        $table->addColumn('uuid', 'uuid', ['comment' => '(DC2Type:uuid)']);
        $table->addColumn('tree_left', 'integer', []);
        $table->addColumn('tree_right', 'integer', []);
        $table->addColumn('tree_level', 'integer', []);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addIndex(['thumbnail_id'], 'idx_707b560efdff2e92', []);
        $table->addIndex(['owner_user_id'], 'idx_707b560e2b18554a', []);
        $table->addUniqueIndex(['uuid'], 'uniq_707b560ed17f50a6');
        $table->addIndex(['parent_id'], 'idx_707b560e727aca70', []);
        $table->addIndex(['organization_id'], 'idx_707b560e32c8a3de', []);
        $table->addIndex(['integration_id'], 'idx_707b560e9e82ddea', []);
        $table->addIndex(['updated_by_user_id'], 'idx_707b560e2793cc5e', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['root_id'], 'idx_707b560e79066886', []);
    }

    /**
     * Create kbk_dam_document_authorization table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamDocumentAuthorizationTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_document_authorization');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('authorization_id', 'integer', []);
        $table->addIndex(['document_id'], 'idx_77af6c88c33f7837', []);
        $table->addUniqueIndex(['authorization_id'], 'uniq_77af6c882f8b0eb2');
        $table->setPrimaryKey(['document_id', 'authorization_id']);
    }

    /**
     * Create kbk_dam_document table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamDocumentTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_document');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('file_id', 'integer', ['notnull' => false]);
        $table->addColumn('thumbnail_id', 'integer', ['notnull' => false]);
        $table->addColumn('updated_by_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('node_id', 'integer', ['notnull' => false]);
        $table->addColumn('uuid', 'uuid', ['comment' => '(DC2Type:uuid)']);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addIndex(['owner_user_id'], 'idx_f20745c72b18554a', []);
        $table->addIndex(['updated_by_user_id'], 'idx_f20745c72793cc5e', []);
        $table->addUniqueIndex(['uuid'], 'uniq_f20745c7d17f50a6');
        $table->addIndex(['node_id'], 'idx_f20745c7460d9fd7', []);
        $table->addIndex(['organization_id'], 'idx_f20745c732c8a3de', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['file_id'], 'idx_f20745c793cb796c', []);
        $table->addUniqueIndex(['thumbnail_id'], 'uniq_f20745c7fdff2e92');
    }

    /**
     * Create kbk_dam_metadata table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamMetadataTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_metadata');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('localization_id', 'integer', ['notnull' => false]);
        $table->addColumn('uuid', 'uuid', ['comment' => '(DC2Type:uuid)']);
        $table->addColumn('raw', 'json', []);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addIndex(['localization_id'], 'idx_657afba56a2856c7', []);
        $table->addUniqueIndex(['uuid'], 'uniq_657afba5d17f50a6');
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create kbk_dam_document_metadata table
     *
     * @param Schema $schema
     */
    protected function createKibokoDamDocumentMetadataTable(Schema $schema)
    {
        $table = $schema->createTable('kbk_dam_document_metadata');
        $table->addColumn('document_id', 'integer', []);
        $table->addColumn('metadata_id', 'integer', []);
        $table->addIndex(['document_id'], 'idx_41f255e0c33f7837', []);
        $table->setPrimaryKey(['document_id', 'metadata_id']);
        $table->addUniqueIndex(['metadata_id'], 'uniq_41f255e0dc9ee959');
    }

    /**
     * Add kbk_dam_node_slug foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamNodeSlugForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_node_slug');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_node_metadata foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamNodeMetadataForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_node_metadata');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_metadata'),
            ['metadata_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_node_name foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamNodeNameForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_node_name');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_document_name foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamDocumentNameForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_document_name');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_document'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_document_slug foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamDocumentSlugForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_document_slug');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_document'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_node_authorization foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamNodeAuthorizationForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_node_authorization');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['node_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_authorization'),
            ['authorization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_node foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamNodeForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_node');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['updated_by_user_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_user_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['root_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['parent_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_channel'),
            ['integration_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_attachment_file'),
            ['thumbnail_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => null]
        );
    }

    /**
     * Add kbk_dam_document_authorization foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamDocumentAuthorizationForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_document_authorization');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_document'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_authorization'),
            ['authorization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_document foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamDocumentForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_document');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_attachment_file'),
            ['file_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_attachment_file'),
            ['thumbnail_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['updated_by_user_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_user_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_node'),
            ['node_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_metadata foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamMetadataForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_metadata');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_localization'),
            ['localization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add kbk_dam_document_metadata foreign keys.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addKibokoDamDocumentMetadataForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('kbk_dam_document_metadata');
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_document'),
            ['document_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('kbk_dam_metadata'),
            ['metadata_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }
}
