<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="./style.css">
    <?php
    $pdo = new PDO(
            'mysql:host=localhost;dbname=module-a',
            'root',
            'root'
    );


    $year = ($_GET['year'] ?? date('Y'));
    $month = ($_GET['month'] ?? date('m'));


    //    post data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title = trim($_POST['title'] ?? '');
        $taskDate = $_POST['task_date'] ?? '';

        $isRecurring = isset($_POST['is_recurring']);

        $type = $_POST['type'] ?? null;
        $cycle = $_POST['cycle'] ?? null;
        $endDate = $_POST['end_date'] ?? null;


        if ($title !== '' && $taskDate !== '') {

            if (!$isRecurring) {
                $type = null;
                $cycle = null;
                $endDate = null;
            }


            $stmt = $pdo->prepare("
            INSERT INTO c2_calander
            (
                title,
                task_date,
                is_recurring,
                recurrence_type,
                cycle,
                end_date
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

            $stmt->execute([
                    $title,
                    $taskDate,
                    $isRecurring ? 1 : 0,
                    $type,
                    $cycle,
                    $endDate
            ]);
            header('Location: index.php');
            exit;
        }
    }


    $firstDay =
            new DateTimeImmutable(
                    sprintf(
                            '%04d-%02d-01',
                            $year,
                            $month
                    )
            );


    $weekday =
            (int)$firstDay->format('w');
    $calendarStart =
            $firstDay->modify(
                    "-{$weekday} days"
            );

    ?>
</head>
<body>

<div class="container">
    <form
            class="change-date"
            method="get"
    >
        <label>
            Year:
            <input
                    type="number"
                    name="year"
                    value="<?= $year ?>"
            >
        </label>


        <label>
            Month:

            <input
                    type="number"
                    name="month"
                    min="1"
                    max="12"
                    value="<?= $month ?>"
            >
        </label>
        <button type="submit">
            Change
        </button>
    </form>
    <table class="calendar">

        <thead>

        <tr>

            <th>S</th>

            <th>M</th>

            <th>T</th>

            <th>W</th>

            <th>T</th>

            <th>F</th>

            <th>S</th>

        </tr>

        </thead>


        <tbody>

        <?php

        $current =
                $calendarStart;
        for (
                $week = 0;
                $week < 6;
                $week++
        ):

            ?>

            <tr>

                <?php

                for (
                        $day = 0;
                        $day < 7;
                        $day++
                ):

                    $date =
                            $current
                                    ->format('Y-m-d');


                    $isCurrentMonth =
                            (int)$current
                                    ->format('m')
                            ===
                            $month;

                    ?>

                    <td
                            class="<?=
                            !$isCurrentMonth
                                    ?
                                    'other-month'
                                    :
                                    ''
                            ?>"
                    >

                        <div class="day">

                            <?= $current->format('j') ?>

                        </div>


                        <?php
                        foreach (
                                $events[$date] ?? []
                                as
                                $event
                        ):?>

                            <div class="event">

                                <?= htmlspecialchars(
                                        $event['title']
                                ) ?>

                            </div>

                        <?php endforeach; ?>


                    </td>

                    <?php

                    $current =
                            $current->modify(
                                    '+1 day'
                            );

                endfor;

                ?>

            </tr>

        <?php endfor; ?>

        </tbody>

    </table>
    <form
            method="post"
            class="add-event"
    >
        <div class="field">
            <label>
                *Title
            </label>
            <input
                    type="text"
                    name="title"
            >

        </div>
        <div class="field">
            <label>
                *Task date
            </label>
            <input
                    type="date"
                    name="task_date"
            >
        </div>
        <div class="recurring-wrapper">
            <label class="checkbox-field">
                <input
                        id="is_recurring"
                        type="checkbox"
                        name="is_recurring"
                >
                Recurring task
            </label>
            <div class="recurring-options">
                <div class="field">
                    <label>
                        Type
                    </label>
                    <select name="recurrence_type">
                        <option value="day">
                            Day
                        </option>
                        <option value="week">
                            Week
                        </option>
                        <option value="month">
                            Month
                        </option>
                        <option value="year">
                            Year
                        </option>
                    </select>
                </div>
                <div class="field">
                    <label>
                        Cycle
                    </label>
                    <input
                            type="number"
                            name="cycle"
                            min="1"
                            value="1"
                    >
                </div>
                <div class="field">
                    <label>End Date</label>
                    <input type="date" name="end_date">
                </div>
            </div>
        </div>
        <button type="submit">
            Create
        </button>
</div>
</body>
</html>