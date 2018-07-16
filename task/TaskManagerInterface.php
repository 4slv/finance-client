<?php

namespace ApiClient\Task;

interface TaskManagerInterface
{
    /**
     * Добавить задачу
     * @param TaskInterface $task
     * @return mixed
     */
    public function add(TaskInterface $task): TaskManagerInterface;

    /**
     * Удалить задачу
     * @param TaskInterface $task
     * @return TaskManagerInterface
     */
    public function remove(TaskInterface $task): TaskManagerInterface;

    /** создание запроса */
    public function createRequest();

    /** отправка запроса, получение ответа */
    public function sendRequest();

    /** действие после получения ответа */
    public function afterRequest();
}