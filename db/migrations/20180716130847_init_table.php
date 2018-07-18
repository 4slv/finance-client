<?php


use Phinx\Migration\AbstractMigration;

class ApiClientInitTable extends AbstractMigration
{
    public function change()
    {
        /**
         * ACTION
         */
        $table = $this->table(
            'ApiClientAction',
            ['comment' => 'Очередь задач']
        );
        $table
            ->addColumn(
                'name',
                'string',
                ['comment' => 'Название действия']
            )
            ->addColumn(
                'parameters',
                'json',
                ['comment' => 'Внешние параметры действия в формате JSON']
            )
            ->create();

        /**
         * TRANSFER
         */
        $table = $this->table(
            'ApiClientTransfer',
            ['comment' => 'Передача']
        );
        $table
            ->addColumn(
                'datetime',
                'datetime',
                ['comment' => 'Время передачи']
            )
            ->create();

        /**
         * TASK
         */
        $table = $this->table(
            'ApiClientTask',
            ['comment' => 'Задача']
        );
        $table
            ->addColumn(
                'createDatetime',
                'datetime',
                ['comment' => 'Время создания']
            )
            ->addColumn(
                'action',
                'integer',
                ['comment' => 'Идентификатор действия']
            )
            ->addColumn(
                'parameters',
                'json',
                ['comment' => 'Параметры задачи в формате JSON']
            )
            ->addColumn(
                'creditId',
                'integer',
                ['comment' => 'Идентификатор кредита']
            )
            ->addColumn(
                'transfer',
                'integer',
                ['null' => true, 'comment' => 'Идентификатор передачи']
            )
            ->addColumn(
                'status',
                'enum',
                ['values' => ['new', 'success', 'error', 'reject', 'inWork'],
                    'comment' => 'Статусы выполнения задачи', ['default' => 'new']]
            )
            ->addColumn(
                'attempt',
                'integer',
                ['comment' => 'Количество выполнений', ['default' => 0]]
            )
            ->addColumn(
                'inWork',
                'bool',
                ['comment' => 'Задача находится в работе', ['default' => false]]
            )
            ->addForeignKey(
                'action',
                'ApiClientAction',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->addForeignKey(
                'transfer',
                'ApiClientTransfer',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();

        /**
         * TRANSFER_HISTORY
         */
        $table = $this->table(
            'ApiClientTransferHistory',
            ['comment' => 'История передач']
        );
        $table
            ->addColumn(
                'task',
                'integer',
                ['comment' => 'Идентификатор задачи']
            )
            ->addColumn(
                'transfer',
                'integer',
                ['comment' => 'Идентификатор передачи']
            )
            ->addColumn(
                'datetime',
                'datetime',
                ['comment' => 'Время выполнения передачи']
            )
            ->addColumn(
                'status',
                'enum',
                ['values' => ['new', 'success', 'error', 'reject', 'inWork'],
                    'comment' => 'Статусы выполнения задачи']
            )
            ->addColumn(
                'description',
                'string',
                ['comment' => 'Текст ошибки, если задача выполнена с ошибкой']
            )
            ->addForeignKey(
                'task',
                'ApiClientTask',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->addForeignKey(
                'transfer',
                'ApiClientTransfer',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();
    }
}
