<?php
require_once("lib/dbManager.php");
$dbManager->init();

if ($dbManager->checkLoggedIn()) {

    if ($dbManager->isAdmin() && isset($_POST["deletePeserta"])) {
        $status = $dbManager->deletePengguna($_POST["deletePeserta"]);
        $message = ($status) ? "Berjaya menghapuskan peserta" : "Tidak Boleh menghapuskan peserta";
    } else if ($dbManager->isHakim() && isset($_POST["bahagianA"], $_POST["bahagianB"], $_POST["bahagianC"], $_POST["idPeserta"])) {
        $status = $dbManager->editMarkah($_POST["idPeserta"], $_POST["bahagianA"], $_POST["bahagianB"], $_POST["bahagianC"]);
        $message = ($status) ? "Berjaya edit markah" : "Salah info markah";
    }

    if (isset($_POST['namaPeserta']))
        $dataSet = $dbManager->getTempatPesertaByName($_POST["namaPeserta"]);
    else
        $dataSet = $dbManager->getAllTempatPeserta();
}
?>

<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/7cd63795dc.js" crossorigin="anonymous"></script>

    <title>Laman Utama</title>
</head>

<body style="background-color: #CFD8DC;">
    <?php require "lib/navBar.php" ?>

    <div class="container my-5">
        <form action="/skor.php" method="POST">
            <div class="modal fade" id="editMarkahModal" tabindex="1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Markah</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="bahagianA">Bahagian A (maksimum 20)</label>
                                <input type="number" class="form-control" id="bahagianA" name="bahagianA" placeholder="20" required>
                            </div>
                            <div class="form-group">
                                <label for="bahagianB">Bahagian B (maksimum 30)</label>
                                <input type="number" class="form-control" id="bahagianB" name="bahagianB" placeholder="30" required>
                            </div>
                            <div class="form-group">
                                <label for="bahagianC">Bahagian C (maksimum 50)</label>
                                <input type="number" class="form-control" id="bahagianC" name="bahagianC" placeholder="50" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="btnDaftarPengguna" type="submit" class="btn btn-primary">Kemas Kini</button>
                        </div>
                        <input type="hidden" id="idPeserta" name="idPeserta">
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col">
                <?php if (isset($status)) { ?>
                    <div class="alert <?= ($status) ? "alert-success" : "alert-danger" ?>" role="alert">
                        <?= $message ?>
                    </div>
                <?php } ?>

                <form class="row justify-content-between align-content-center my-3" action="/skor.php" method="POST">
                    <div class="col-4 mx-3">
                        <h4>Skor Peserta</h4>
                    </div>
                    <div class="col-4 input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari Peserta" name="namaPeserta">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </div>
                </form>

                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Tempat Peserta</th>
                            <th scope="col">Nama Peserta</th>
                            <th scope="col">Bahagian A</th>
                            <th scope="col">Bahagian B</th>
                            <th scope="col">Bahagian C</th>
                            <th scope="col">Jumlah Markah</th>
                            <?php if ($dbManager->isAdmin() || $dbManager->isHakim()) { ?>
                                <th scope="col"></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($dataSet)
                            foreach ($dataSet as $index => $row) {
                        ?>
                            <tr>
                                <th scope="row"><?= $index + 1 ?></th>
                                <td><?= $row["nama"] ?></td>
                                <td><?= $row["a"] ?></td>
                                <td><?= $row["b"] ?></td>
                                <td><?= $row["c"] ?></td>
                                <td><?= $row["jumlah"] ?></td>
                                <?php if ($dbManager->isAdmin() || $dbManager->isHakim()) { ?>
                                    <td>
                                        <div class="row justify-content-around">
                                            <?php if ($dbManager->isHakim()) {
                                                $allValues = array($row['id'], $row['a'], $row['b'], $row['c']);
                                            ?>
                                                <button class="btn btn-secondary" type="button" onclick="editMarkah(<?= implode(',', $allValues) ?>);">
                                                    <span class="fa fa fa-pencil fa-lg" aria-hidden="true"></span>
                                                </button>
                                            <?php } ?>

                                            <?php if ($dbManager->isAdmin()) { ?>
                                                <form action="/skor.php" method="POST">
                                                    <button class="btn btn-secondary" type="submit">
                                                        <span class="fa fa-trash-o fa-lg" aria-hidden="true"></span>
                                                    </button>
                                                    <input type="hidden" name="deletePeserta" value="<?= $row[0] ?>" />
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        function editMarkah($id, $a, $b, $c) {
            $("#idPeserta").val($id);

            $("#bahagianA").val($a);
            $("#bahagianB").val($b);
            $("#bahagianC").val($c);

            $("#editMarkahModal").modal("show");
        }
    </script>
</body>