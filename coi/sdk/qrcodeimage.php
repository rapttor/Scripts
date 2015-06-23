<?php
include_once "../../../coi/sdk/qrcode/qrlib.php";
QRcode::png($_GET["QR"]);
