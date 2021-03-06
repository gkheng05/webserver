<?php
require_once("lib/dbManager.php");
require_once("lib/components.php");
$dbManager->init();

if ($dbManager->isAdmin()) {
    //echo var_dump($dbManager->isPeserta());
    //one action at once
    $export = false;
    $status = null;
    $message = null;


    if (isset($_POST["action"]))
        switch ($_POST["action"]) {
            case "search": {

                    break;
                }
            case "daftar": {
                    $res = handleDaftarPenggunaRequest(6);
                    $message = $res["message"];
                    $status = $res["status"];

                    break;
                }
            case "delete": {
                    if (isset($_POST["deletePengguna"])) {
                        try {
                            $status = $dbManager->deletePengguna($_POST["deletePengguna"]);
                        } catch (Exception $e) {
                            $status = false;
                        }
                        $message = ($status) ? "Berjaya menghapuskan pengguna ini" : "Tidak boleh menghapuskan pengguna";
                    }
                    break;
                }
            case "import/export": {
                    if (
                        isset($_POST["jenisData"], $_POST["importExport"])
                        && ($_POST["jenisData"] === "peserta" || $_POST["jenisData"] === "hakim" || $_POST["jenisData"] === "admin")
                    ) {
                        if ($_POST["importExport"] == "export") {
                            $export = true;

                            header("Content-disposition: attachment; filename=" . $_POST["jenisData"] . ".csv");
                            header("Content-Type: text/csv");

                            switch ($_POST["jenisData"]) {
                                case "peserta":
                                    $dbManager->exportPeserta();
                                    break;
                                case "hakim":
                                    $dbManager->exportHakimAtauAdmin(false);
                                    break;
                                case "admin":
                                    $dbManager->exportHakimAtauAdmin(true);
                                    break;
                            }
                        }
                        if ($_POST["importExport"] == "import") {
                            switch ($_POST["jenisData"]) {
                                case "peserta":
                                    $status = $dbManager->importByCSV($_FILES["exportFileUpload"]['tmp_name'], 1);
                                    break;
                                case "hakim":
                                    $status = $dbManager->importByCSV($_FILES["exportFileUpload"]['tmp_name'], 2);
                                    break;
                                case "admin":
                                    $status = $dbManager->importByCSV($_FILES["exportFileUpload"]['tmp_name'], 6);
                                    break;
                            }
                            $message = (($status) ? "Berjaya muat naik csv " : "Salah info csv ") . $_POST["jenisData"];
                        }
                    }
                    break;
                }
        }
    if (isset($_POST["cariNamaPengguna"]) || isset($_POST["cariPerananPengguna"]))
        $dataSet = $dbManager->searchBy($_POST["cariNamaPengguna"], $_POST["cariPerananPengguna"]);
    else
        $dataSet = $dbManager->getAllUsers();

    /*
    if (isset($_POST["deletePengguna"])) {
        try {
            $status = $dbManager->deletePengguna($_POST["deletePengguna"]);
        } catch (Exception $e) {
            $status = false;
        }
        $message = ($status) ? "Berjaya menghapuskan pengguna ini" : "Tidak boleh menghapuskan pengguna";
    } else if (isset($_POST["idPengguna"]) && $_POST["idPengguna"] != -1) {
        try {
            $status = $dbManager->editPengguna($_POST["idPengguna"], $_POST["emelPengguna"], $_POST["kataLaluanPengguna"]);
            switch ($_POST["perananPengguna"]) {
                case 1:
                    $status &= $dbManager->editPeserta($_POST["idPengguna"], $_POST["namaPengguna"], $_POST["alamatPeserta"], $_POST["icPeserta"], $_POST["telefonPeserta"]);
                    break;
                case 2:
                    $status &= $dbManager->editHakim($_POST["idPengguna"], $_POST["namaPengguna"]);
                    break;
                case 6:
                    $status &= $dbManager->editAdmin($_POST["idPengguna"], $_POST["namaPengguna"]);
                    break;
            }
        } catch (Exception $e) {
            $status = false;
        }

        $message = ($status) ? "Berjaya edit pengguna ini" : "Tidak boleh edit pengguna ini";
    } else if (isset($_POST["emelPengguna"], $_POST["kataLaluanPengguna"], $_POST["namaPengguna"], $_POST["perananPengguna"])) {
        try {
            switch ($_POST["perananPengguna"]) {
                case 1:
                    $status = $dbManager->createPeserta($_POST["emelPengguna"], $_POST["kataLaluanPengguna"], $_POST["namaPengguna"], $_POST["alamatPeserta"], $_POST["icPeserta"], $_POST["telefonPeserta"]);
                    break;
                case 2:
                    $status = $dbManager->createHakim($_POST["emelPengguna"], $_POST["kataLaluanPengguna"], $_POST["namaPengguna"]);
                    break;
                case 6:
                    $status = $dbManager->createAdmin($_POST["emelPengguna"], $_POST["kataLaluanPengguna"], $_POST["namaPengguna"]);
                    break;
            }
        } catch (Exception $e) {
            $status = false;
        }

        $message = ($status) ? "Berjaya membuat pengguna baharu" : "Tidak boleh membuat pengguna baharu";
    } else if (isset($_POST["jenisData"], $_POST["importExport"]) && ($_POST["jenisData"] === "peserta" || $_POST["jenisData"] === "hakim" || $_POST["jenisData"] === "admin")) {
        if ($_POST["importExport"] == "export") {
            $export = true;

            header("Content-disposition: attachment; filename=" . $_POST["jenisData"] . ".csv");
            header("Content-Type: text/csv");

            switch ($_POST["jenisData"]) {
                case "peserta":
                    $dbManager->exportPeserta();
                    break;
                case "hakim":
                    $dbManager->exportHakimAtauAdmin(false);
                    break;
                case "admin":
                    $dbManager->exportHakimAtauAdmin(true);
                    break;
            }
        }
        if ($_POST["importExport"] == "import") {
            switch ($_POST["jenisData"]) {
                case "peserta":
                    $status = $dbManager->importByCSV($_FILES["exportFileUpload"]['tmp_name'], 1);
                    break;
                case "hakim":
                    $status = $dbManager->importByCSV($_FILES["exportFileUpload"]['tmp_name'], 2);
                    break;
                case "admin":
                    $status = $dbManager->importByCSV($_FILES["exportFileUpload"]['tmp_name'], 6);
                    break;
            }
            $message = (($status) ? "Berjaya muat naik csv " : "Salah info csv ") . $_POST["jenisData"];
        }
    }

    if (isset($_POST["cariNamaPengguna"]) || isset($_POST["cariPerananPengguna"])) {
        $dataSet = $dbManager->searchBy($_POST["cariNamaPengguna"], $_POST["cariPerananPengguna"]);
    } else {
        $dataSet = $dbManager->getAllUsers();
    }
    */
} else {
    redirect("login.php");
}

?>
<?php if ($export != true) { ?>
    <!DOCTYPE html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/7cd63795dc.js" crossorigin="anonymous"></script>


        <title>Pengurusan Pengguna</title>

        <style>
            .disabledbutton {
                pointer-events: none;
                opacity: 0.4;
            }
        </style>
    </head>

    <body style="background-color: <?= $_SESSION["colour"] ?>;">

        <?php require("lib/navBar.php"); ?>

        <div class="container my-5">
            <!-- create/update user -->
            <form action="/pengguna.php" method="POST">
                <?= createDaftarModal() ?>
                <input type="hidden" name="action" value="daftar" />
            </form>


            <!-- import/export data -->
            <form action="/pengguna.php" method="POST" enctype="multipart/form-data">
                <div class="modal fade" id="importExportDataModal" tabindex="1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pengguna</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="formDaftarPengguna">
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="importExport" id="pilihanExport" value="export" checked>
                                            <label class="form-check-label" for="pilihanExport">Eksport</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="importExport" id="pilihanImport" value="import">
                                            <label class="form-check-label" for="pilihanImport">Import</label>
                                        </div>
                                    </div>
                                    <div id="exportFileUploadDiv" class="form-group disabledbutton">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="exportFileUploadAddon">Muat Naik</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="exportFileUpload" name="exportFileUpload" aria-describedby="exportFileUploadAddon">
                                                <label class="custom-file-label" for="exportFileUpload">Pilih Dokumen...</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenisData" id="jenisDataPeserta" value="peserta" checked>
                                            <label class="form-check-label" for="jenisDataPeserta">Peserta</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenisData" id="jenisDataHakim" value="hakim">
                                            <label class="form-check-label" for="jenisDataHakim">Hakim</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenisData" id="jenisDataAdmin" value="admin">
                                            <label class="form-check-label" for="jenisDataAdmin">Admin</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Pasti</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="action" value="import/export" />
            </form>

            <div class="row">
                <div class="col">

                    <?= displayMessage($message, $status) ?>

                    <form class="row justify-content-between align-content-center my-3" action="/pengguna.php" method="POST">
                        <div class="col-4 mx-3">
                            <h4>Pengguna</h4>
                        </div>

                        <div class="col-4 input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari Pengguna" name="cariNamaPengguna">
                            <div class="input-group-append">
                                <select class="custom-select" name="cariPerananPengguna">
                                    <option value="0" selected>Pilihan</option>
                                    <option value="1">Peserta</option>
                                    <option value="2">Hakim</option>
                                    <option value="6">Admin</option>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                            </div>
                        </div>

                        <input type="hidden" name="action" value="search" />
                    </form>

                    <div class="row align-content-center justify-content-end mx-auto my-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="Menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="Menu">
                            <a class="dropdown-item" data-toggle="modal" data-target="#importExportDataModal">
                                Import / Eksport
                            </a>
                            <a class="dropdown-item" onclick="showForm(true, '1')">
                                Daftar Pengguna Baharu
                            </a>
                        </div>
                    </div>

                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Pengguna</th>
                                <th scope="col">Emel Pengguna</th>
                                <th scope="col">Peranan Pengguna</th>
                                <th scope="col"></th>
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
                                    <td><?= $row["emel"] ?></td>
                                    <td><?= $dbManager->getPeranan($row["peranan"]) ?></td>
                                    <td>
                                        <div class="row justify-content-around mx-1">
                                            <form action="/pengguna.php" method="POST">
                                                <?php
                                                $args = array($row['peranan'], $row['id'], $row['emel'], $row['nama']);
                                                if ($row['peranan'] == 1)
                                                    array_push($args, $row['alamatPeserta'], $row['telefonPeserta'], $row['noicPeserta']);
                                                ?>
                                                <button class="btn btn-secondary" type="button" onclick="showForm(false, '<?= implode('\',\'', $args) ?>')">
                                                    <i class="fa fa fa-pencil fa-lg" aria-hidden="true"></i>
                                                </button>

                                                <button class="btn btn-secondary" 
                                                data-toggle="confirmation" 
                                                data-singleton="true" 
                                                data-popout="true" 
                                                data-title="Penghapusan Pengguna"
                                                data-content="Adakah anda pasti?"
                                                data-btn-ok-label="Ya"
                                                data-btn-ok-class="btn btn-danger"
                                                data-btn-cancel-label="Tidak"
                                                data-btn-cancel-class="btn btn-secondary"
                                                onclick="$(this).confirmation('toggle');">
                                                    <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                                </button>


                                                <input type="hidden" name="deletePengguna" value="<?= $row["id"] ?>" />
                                                <input type="hidden" name="action" value="delete" />
                                            </form>

                                        </div>
                                    </td>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-confirmation2/dist/bootstrap-confirmation.min.js"></script>
        <script>
            $("[data-toggle=confirmation]").confirmation({
                rootSelector: '[data-toggle=confirmation]',
                onConfirm: function(value) {
                    console.log(value);
                }
            });

            $('#exportFileUpload').change(function() {
                //get the file name
                var fileName = $(this).val().split(/(\\|\/)/g).pop();
                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fileName);
            })
            $('input[name="perananPengguna"]').change(function() {
                if (this.value !== '1') {
                    $("#infoPeserta").attr("hidden", true);
                    $("#telefonPeserta").val("");
                    $("#alamatPeserta").val("");
                    $("#icPeserta").val("");
                    $("#infoPeserta input").removeAttr("required");
                } else {
                    $("#infoPeserta").removeAttr("hidden");
                    $("#infoPeserta input").attr("required", true)
                }
            });

            $('input[name="importExport"]').change(function() {
                if (this.value == "export")
                    $("#exportFileUploadDiv").addClass("disabledbutton");
                else
                    $("#exportFileUploadDiv").removeClass("disabledbutton");
            });

            function showForm(showPeranan, peranan, id = "", emel = "", nama = "", alamat = "", telefon = "", nric = "") {
                if (showPeranan === true)
                    $("#pilihanPeranan").removeAttr("hidden");
                else
                    $("#pilihanPeranan").attr("hidden", true);

                switch (peranan) {
                    case "1":
                        $('input[name="perananPengguna"]')[0].click();
                        break;
                    case "2":
                        $('input[name="perananPengguna"]')[1].click();
                        break;
                    case "6":
                        $('input[name="perananPengguna"]')[2].click();
                        break;
                }
                //$('input[name="perananPengguna"]').val(peranan);
                if (id)
                    $("#idPengguna").val(id);
                else
                    $("#idPengguna").val(-1);
                $("#emelPengguna").val(emel);
                $("#namaPengguna").val(nama);
                $("#telefonPeserta").val(telefon);
                $("#alamatPeserta").val(alamat);
                $("#icPeserta").val(nric);

                $('#daftarPengguna').modal("show");
            }
        </script>
    </body>
<?php } ?>