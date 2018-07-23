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
                'code',
                'integer',
                ['comment' => 'Статус передачи']
            )
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
                'status',
                'enum',
                ['values' => ['open', 'close', 'error', 'reject', 'block'],
                    'comment' => 'Статусы выполнения задачи']
            )
            ->addColumn(
                'attempt',
                'integer',
                ['comment' => 'Количество выполнений']
            )
            ->addColumn(
                'inWork',
                'boolean',
                ['comment' => 'Задача находится в работе']
            )
            ->addColumn(
                'description',
                'string',
                ['null' => true, 'comment' => 'Комментарий к задаче от сервера']
            )
            ->addForeignKey(
                'action',
                'ApiClientAction',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();

        /**
         * TASK_TRANSFER
         */
        $table = $this->table(
            'ApiClientTaskTransfer',
            ['comment' => 'История передач']
        );
        $table
            ->addColumn(
                'task_id',
                'integer',
                ['comment' => 'Идентификатор задачи']
            )
            ->addColumn(
                'transfer_id',
                'integer',
                ['comment' => 'Идентификатор передачи']
            )
            ->addForeignKey(
                'task_id',
                'ApiClientTask',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->addForeignKey(
                'transfer_id',
                'ApiClientTransfer',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();
    }
}