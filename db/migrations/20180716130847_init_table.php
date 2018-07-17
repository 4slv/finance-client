<?php


use Phinx\Migration\AbstractMigration;

class InitTable extends AbstractMigration
{
    public function change()
    {
        /**
         * TASK
         */
        $table = $this->table(
            'Task',
            ['comment' => 'Задача']
        );
        $table
            ->addColumn(
                'datetime',
                'datetime',
                ['comment' => 'Время создания']
            )
            ->addColumn(
                'processPull',
                'integer',
                ['comment' => 'Идентификатор пула запросов']
            )
            ->addColumn(
                'status',
                'enum',
                ['comment' => 'Статус выполнения']
            )
            ->addForeignKey(
                'processPull',
                'ProcessPull',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->addForeignKey(
                'status',
                'Status',
                'status',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();

        /**
         * QUEUE
         */
        $table = $this->table(
            'Queue',
            ['comment' => 'Очередь задач']
        );
        $table
            ->addColumn(
                'task',
                'integer',
                ['comment' => 'Идентификатор задачи']
            )
            ->addColumn(
                'createDatetime',
                'createDatetime',
                ['comment' => 'Время постановки в очередь']
            )
            ->addColumn(
                'lastRunDatetime',
                'lastRunDatetime',
                ['comment' => 'Время последнего выполнения']
            )
            ->addColumn(
                'attempt',
                'integer',
                ['comment' => 'Количество попыток выполнения задачи']
            )
            ->addColumn(
                'status',
                'enum',
                ['comment' => 'Статус выполнения']
            )
            ->addForeignKey(
                'task',
                'Task',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->addForeignKey(
                'status',
                'Status',
                'status',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();

        /**
         * PROCESS
         */
        $table = $this->table(
            'Process',
            ['comment' => 'Процесс']
        );
        $table
            ->addColumn(
                'processPull',
                'integer',
                ['comment' => 'Идентификатор пула процессов']
            )
            ->addColumn(
                'parameters',
                'text',
                ['comment' => 'Параметры процесса в формате JSON']
            )
            ->addForeignKey(
                'processPull',
                'ProcessPull',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();

        /**
         * PROCESS_POOL
         */
        $table = $this->table(
            'ProcessPool',
            ['comment' => 'Пул процессов']
        );
        $table->create();

        /**
         * HISTORY
         */
        $table = $this->table(
            'History',
            ['comment' => 'История задач']
        );
        $table
            ->addColumn(
                'task',
                'integer',
                ['comment' => 'Идентификатор задачи']
            )
            ->addColumn(
                'datetime',
                'datetime',
                ['comment' => 'Время выполнения задачи']
            )
            ->addColumn(
                'status',
                'enum',
                ['comment' => 'Статус выполнения']
            )
            ->addColumn(
                'description',
                'string',
                ['comment' => 'Текст ошибки, если запрос выполнен с ошибкой']
            )
            ->addForeignKey(
                'task',
                'Task',
                'id',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->addForeignKey(
                'status',
                'Status',
                'status',
                ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION']
            )
            ->create();

        /**
         * STATUS
         */
        $table = $this->table(
            'Status',
            ['comment' => 'Статусы задач']
        );
        $table
            ->addColumn(
                'status',
                'enum',
                ['values' => ['success', 'error', 'reject'],
                    'comment' => 'Статусы выполнения задачи'])
            ->create();
    }
}
