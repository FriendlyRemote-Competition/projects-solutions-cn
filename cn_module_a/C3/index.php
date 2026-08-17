<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Table</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<?php

$file = __DIR__ . '/table.json';

$data = json_decode(file_get_contents($file), true);
$fields = array_keys($data[0]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

//    add the actions
    if ($action === 'add') {
        $row = [];
        foreach ($fields as $field) {
            $row[$field] = '';
        }

        $data[] = $row;
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));


    }


//    delete the data
    if (str_starts_with($action, 'delete:')) {
        $index = str_replace('delete:', '', $action);

        if (isset($data[$index])) {
            unset($data[$index]);
            $data = array_values($data);
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        }
        header("Location: index.php");
        exit();
    }


//    save the data
    if ($action === 'save') {
        $newFields = $_POST['fields'] ?? [];
        $rows = $_POST['rows'] ?? [];
        $newFields = array_map('trim', $newFields);
        foreach ($newFields as $index => $field) {
            if ($field === '') {
                $newFields[$index] = 'field_' . ($index + 1);
            }
        }
        $newData = [];
        foreach ($rows as $row) {
            $newRow = [];
            foreach ($newFields as $columnIndex => $field) {

                $newRow[$field] = $row[$columnIndex] ?? '';
            }
            $newData[] = $newRow;
        }

        file_put_contents($file, json_encode($newData, JSON_PRETTY_PRINT));

        header("Location: index.php");
        exit();
    }
}
?>
<body>
<!--table-->
<form method="post" action="">
    <div class="table">
        <table>
            <thead>
            <tr>
                <?php foreach ($fields as $field): ?>

                    <th>
                        <input
                                class="input"
                                name="fields[]"
                                value="<?= htmlspecialchars($field) ?>">
                    </th>
                <?php endforeach; ?>
                <th>
                    <div>== Delete ==</div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $rowIndex => $person): ?>
                <tr>
                    <?php foreach ($fields as $columnIndex => $field): ?>
                        <td>
                            <input
                                    class="input"
                                    name="rows[<?= $rowIndex ?>][<?= $columnIndex ?>]"
                                    value="<?= htmlspecialchars(
                                            $person[$field] ?? ''
                                    ) ?>"
                            >
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button
                                class="danger"
                                name="action"
                                value="delete:<?= $rowIndex ?>"
                        >Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </
    >
    <div class="end">
        <button type="submit" class="secondary" name="action" value="add">Add row</button>
        <button type="submit" class="primary" name="action" value="save">Save</button>
    </div>
</form>
</body>
</html>