<?php

use Phinx\Db\Adapter\MysqlAdapter;

class AddBooks extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('books_bisac_codes', [
            'id' => false,
            'primary_key' => ['books_id', 'bisac_codes_id', 'categories_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('books_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('bisac_codes_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'books_id',
            ])
            ->addColumn('categories_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'bisac_codes_id',
            ])
            ->addIndex(['bisac_codes_id', 'categories_id'], [
                'name' => 'fk_books_has_bisac_codes_bisac_codes1_idx',
                'unique' => false,
            ])
            ->addIndex(['books_id'], [
                'name' => 'fk_books_has_bisac_codes_books1_idx',
                'unique' => false,
            ])
            ->addIndex(['categories_id'], [
                'name' => 'categories_id',
                'unique' => false,
            ])
            ->create();
        $this->table('authors', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('biography', 'text', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::TEXT_MEDIUM,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'name',
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'biography',
            ])
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'created_at',
            ])
            ->addColumn('is_deleted', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'updated_at',
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->addIndex(['created_at'], [
                'name' => 'created_at',
                'unique' => false,
            ])
            ->create();
        $this->table('books', [
            'id' => false,
            'primary_key' => ['id', 'publishers_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('books_series_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('publishers_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'books_series_id',
            ])
            ->addColumn('platform_id', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'publishers_id',
            ])
            ->addColumn('title', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'platform_id',
            ])
            ->addColumn('duration', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'title',
            ])
            ->addColumn('short_title', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 128,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'duration',
            ])
            ->addColumn('description', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'short_title',
            ])
            ->addColumn('excerpt', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'description',
            ])
            ->addColumn('notes', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'excerpt',
            ])
            ->addColumn('pages', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'notes',
            ])
            ->addColumn('release_date', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'pages',
            ])
            ->addColumn('total_chapters', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'release_date',
            ])
            ->addColumn('book_order', 'decimal', [
                'null' => true,
                'default' => null,
                'precision' => '4',
                'scale' => '1',
                'after' => 'total_chapters',
            ])
            ->addColumn('published_date', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'book_order',
            ])
            ->addColumn('status', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'published_date',
            ])
            ->addColumn('isbn', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 17,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'status',
            ])
            ->addColumn('product_id', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'isbn',
            ])
            ->addColumn('is_premium', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'product_id',
            ])
            ->addColumn('rating', 'decimal', [
                'null' => true,
                'default' => null,
                'precision' => '2',
                'scale' => '1',
                'after' => 'is_premium',
            ])
            ->addColumn('narrators_count', 'integer', [
                'null' => true,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'rating',
            ])
            ->addColumn('authors_count', 'integer', [
                'null' => true,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'narrators_count',
            ])
            ->addColumn('series_count', 'integer', [
                'null' => true,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'authors_count',
            ])
            ->addColumn('non_public_link', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'series_count',
            ])
            ->addColumn('family_safe', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'non_public_link',
            ])
            ->addColumn('is_published', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'family_safe',
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'is_published',
            ])
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'created_at',
            ])
            ->addColumn('is_deleted', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'updated_at',
            ])
            ->addIndex(['books_series_id'], [
                'name' => 'fk_books_books_series_idx',
                'unique' => false,
            ])
            ->addIndex(['publishers_id'], [
                'name' => 'fk_books_publishers1_idx',
                'unique' => false,
            ])
            ->addIndex(['created_at'], [
                'name' => 'created_at',
                'unique' => false,
            ])
            ->addIndex(['is_published'], [
                'name' => 'is_published',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->addIndex(['status'], [
                'name' => 'status',
                'unique' => false,
            ])
            ->addIndex(['platform_id'], [
                'name' => 'platform_id',
                'unique' => false,
            ])
            ->create();
        $this->table('bisac_codes', [
            'id' => false,
            'primary_key' => ['id', 'categories_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('categories_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'categories_id',
            ])
            ->addColumn('code', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 12,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'name',
            ])
            ->addColumn('description', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'code',
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'description',
            ])
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'created_at',
            ])
            ->addColumn('is_deleted', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'updated_at',
            ])
            ->addIndex(['categories_id'], [
                'name' => 'fk_bisac_codes_categories1_idx',
                'unique' => false,
            ])
            ->addIndex(['created_at'], [
                'name' => 'created_at',
                'unique' => false,
            ])
            ->addIndex(['code'], [
                'name' => 'code',
                'unique' => false,
            ])
            ->addIndex(['is_deleted'], [
                'name' => 'is_deleted',
                'unique' => false,
            ])
            ->create();
        $this->table('books_authors', [
            'id' => false,
            'primary_key' => ['books_id', 'authors_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
            'collation' => 'utf8_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('books_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('authors_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'books_id',
            ])
            ->addIndex(['authors_id'], [
                'name' => 'fk_books_has_authors_authors1_idx',
                'unique' => false,
            ])
            ->addIndex(['books_id'], [
                'name' => 'fk_books_has_authors_books1_idx',
                'unique' => false,
            ])
            ->create();
    }
}
