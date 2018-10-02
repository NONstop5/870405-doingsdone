            <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.php" method="post">
                    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                        <a href="/" class="tasks-switch__item">Повестка дня</a>
                        <a href="/" class="tasks-switch__item">Завтра</a>
                        <a href="/" class="tasks-switch__item">Просроченные</a>
                    </nav>

                    <label class="checkbox">
                        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
                        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if ($show_complete_tasks) { print("checked"); } ?>>
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <table class="tasks">
                    <?php
                    date_default_timezone_set("Europe/Moscow");
                    foreach ($tasks as $task) {
                        $taskCompletedClass = "";
                        $taskImportantClass = "";

                        if (!is_null($task['task_deadline'])) {
                            $taskDate = strtotime($task['task_deadline'] . '00:00:00');
                            $timeToOver = floor(($taskDate - time()) / 3600);

                            if ($timeToOver <= 24) {
                                $taskImportantClass = " task--important";
                            }
                        }
                        if ($task['task_complete_status']) {
                            if (!$show_complete_tasks) { continue; }
                            $taskCompletedClass = " task--completed";
                            $taskImportantClass = "";
                        }
                    ?>
                    <tr class="tasks__item task<?= $taskCompletedClass . $taskImportantClass ?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                                <span class="checkbox__text"><?= $task['task_name'] ?></span>
                            </label>
                        </td>

                        <td class="task__file">
                            <a class="download-link" href="<?= $task['task_file'] ?>"><?= basename($task['task_file']) ?></a>
                        </td>

                        <td class="task__date"><?= $task['task_deadline'] ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </main>
