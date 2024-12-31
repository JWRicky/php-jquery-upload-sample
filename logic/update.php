<?php


if (
    !(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    && (!empty($_SERVER['SCRIPT_FILENAME'])
        && 'json.php' === basename($_SERVER['SCRIPT_FILENAME']))
) {
    die('このページは直接ロードしないでください。');
}

require_once('./config/database.php');

const TITLE_LIMIT = 50;
const MIN_PHOTO_ID = 1;
const MAX_PHOTO_ID = 8;



if (empty($_FILES['file'])) {

    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');

    $photos = getPhotos();
    echo json_encode($photos);
    exit;

} else {

    $tmp_file = [];
    $tmp_file[] = $_FILES['file']['name'];
    $destination_path = getcwd() . DIRECTORY_SEPARATOR;


    $title = [];
    $titles_length = count($_POST['title']);
    for ($i = 0; $i < $titles_length; $i++) {
        $title[] = $_POST['title'][$i];
        if (checkValidate($title[$i])) {
            echo "タイトルの長さは50文字以内";
            exit;
        }
    }


    $target_path = [];
    $tmp_file_length = count($tmp_file[0]);

    for ($i = 0; $i < $tmp_file_length; $i++) {
        $target_path[] = $destination_path . 'photos/' . basename($_FILES['file']['name'][$i]);
        $image = setFileExtension($_FILES['file']['tmp_name'][$i]);
    }


    array_map('unlink', glob($destination_path . 'photos/*.*'));

    $target_paths_length = count($target_path);
    for ($i = 0; $i < $target_paths_length; $i++) {
        move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path[$i]);
    }


    for ($i = 0; $i < $target_paths_length; $i++) {
        $target_path[$i] = str_replace("C:\\xampp\\htdocs", "", $target_path[$i]);
    }


    for ($i = 1; $i <= $tmp_file_length; $i++) {
        if (isPhotoExist($i)) {
            updatePhoto($target_path[$i - 1], $title[$i - 1], $i);
        } else {
            storePhoto($target_path[$i - 1], $title[$i - 1], $i);
        }
    }

    $photos = getPhotos();


    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($photos);
    exit;


}



function getConnection()
{

    $dsn = DB_CONNECTION_VALUE['dsn'];
    $username = DB_CONNECTION_VALUE['username'];
    $password = DB_CONNECTION_VALUE['password'];

    $dbh = new PDO($dsn, $username, $password);
    return $dbh;

}

function closeConnection($dbh, $stmt): void
{
    try {
        $dbh = null;
        $stmt = null;
    } catch (PDOException $e) {
        error_log('接続をクローズできませんでした', 0);
        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        exit($e->getMessage());
    }

}

function redirectUrl(): void
{
    $to_url = "http://localhost/index.php";
    header('Location:' . $to_url . '');
    exit;
}


function checkValidate($string)
{
    return mb_strlen($string, 'utf-8') > TITLE_LIMIT;
}

function setFileExtension($tmp_file)
{


    $image_file = uniqid(mt_rand(), false);

    switch (exif_imagetype($tmp_file)) {

        case IMAGETYPE_JPEG:
            $image_file .= '.jpg';
            break;
        case IMAGETYPE_PNG:
            $image_file .= '.png';
            break;
        case IMAGETYPE_GIF:
            $image_file .= '.gif';
            break;
        default:
            echo "拡張子を変更してください";

    }

    return $image_file;
}

function isPhotoExist($id)
{
    try {


        $dbh = getConnection();
        $sql = "SELECT count(*) as photo_count FROM photos WHERE id =" . $id;
        $stmt = $dbh->query($sql);
        $value = $stmt->fetch(PDO::FETCH_ASSOC);

        closeConnection($dbh, $stmt);

        return $value['photo_count'] > 0;

    } catch (PDOException $e) {

        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        exit($e->getMessage());

    }
}

function updatePhoto($target_path, $title, $id)
{

    try {


        $dbh = getConnection();
        $sql = "UPDATE photos SET path = :path, title = :title where id = :id";
        $stmt = $dbh->prepare($sql);


        $stmt->bindValue(':path', $target_path, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        closeConnection($dbh, $stmt);

    } catch (PDOException $e) {

        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        exit($e->getMessage());

    }

}

function storePhoto($target_path, $title, $id)
{
    try {


        $dbh = getConnection();
        $sql = "INSERT INTO photos (path, title, id) VALUES (:path, :title, :id)";
        $stmt = $dbh->prepare($sql);


        $stmt->bindValue(':path', $target_path, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        closeConnection($dbh, $stmt);

    } catch (PDOException $e) {

        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        exit($e->getMessage());

    }

}

function getPhotos()
{


    try {


        $dbh = getConnection();
        $sql = "SELECT * FROM photos";
        $stmt = $dbh->query($sql);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        closeConnection($dbh, $stmt);
        return $photos;

    } catch (PDOException $e) {

        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        exit($e->getMessage());

    }

}


?>