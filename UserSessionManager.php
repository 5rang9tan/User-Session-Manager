<?php
// 세션 시간 설정 (15분 = 900초)
ini_set('session.gc_maxlifetime', 1800); // 세션 최대 생존 시간 설정
session_set_cookie_params([
    'lifetime' => 1800, // 세션 쿠키의 생명 주기 설정 (15분)
    'path' => '/',
    'domain' => '.cubes.kr', // 서브도메인 공유를 위한 도메인 설정
    'secure' => true, // HTTPS 사용 시 secure 설정을 true로
    'httponly' => true // HTTP 전용 설정
]);

// 세션 이름 설정
session_name('name_of_session');

// 세션 시작
session_start();

//데이터 확인 후, DB 접속
include("dbcon.php");

if (!isset($_SESSION['lang'])) {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if ($lang == 'ko') {
        $lang = 'kr';
    } else {
        $lang = 'en';
    }
} else {
    $lang = $_SESSION['lang'];
}

$lang = "en"; //임시

$is_set_session = false;

if ($lang == "kr") {
    include("index.php");
} else {
    include("index.php");
}

if (isset($_SESSION['idx'])) {


    $email = $_SESSION['email'];
    $sql = "SELECT * FROM member_table WHERE email ='{$email}'";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);

    if (($_SESSION['idx'] !== $row['idx']) && ($_SESSION['name'] !== $row['name']) && ($_SESSION['email'] !== $row['email']) && ($_SESSION['role'] !== $row['role'])) {
        $is_set_session = false;
        session_start();
        session_destroy();
        echo ("<script>alert('" . $error_session_strong_invaild . "');</script>");
        echo ("<script>location.href='index.php" . $lang . "/index.php?';</script>");
    }

    if ($grant == NULL) {

        $is_set_session = true;

    } else if (($grant == 'member') && (($_SESSION['role'] == 'member') || ($_SESSION['role'] == 'teacher') || ($_SESSION['role'] == 'admin'))) {

        $is_set_session = true;

    } else if (($grant == 'teacher') && (($_SESSION['role'] == 'teacher') || ($_SESSION['role'] == 'admin'))) {

        $is_set_session = true;

    } else if (($grant == 'admin') && ($_SESSION['role'] == 'admin')) {

        $is_set_session = true;

    } else {

        $is_set_session = true;
        echo ("<script>alert('" . $error_session_upper_grant . "');</script>");
        echo ("<script>location.href='index.php" . $lang . "/index.php';</script>");
    }

} else {

    if ($grant == NULL) {
        $is_set_session = false;

    } else {

        echo ("<script>alert('" . $error_session_access_isnot_NULL . "');</script>");
        echo ("<script>location.href='index.php';</script>");
    }

}

if($is_set_session == true){
    // 세션의 유효 기간 설정 (30분)
$session_lifetime = 1800; // 30분

// 마지막 활동 시간 체크 및 초기화
if (!isset($_SESSION['LAST_ACTIVITY'])) {
    $_SESSION['LAST_ACTIVITY'] = time(); // 현재 시간으로 초기화
} else {
    // 마지막 활동 시간과 현재 시간의 차이를 계산
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];

    // 설정한 유효 기간을 초과하면 세션을 종료
    if ($elapsed_time >= $session_lifetime) {
        session_unset(); // 세션 변수 초기화
        session_destroy(); // 세션 종료
        echo "세션이 만료되었습니다. 다시 로그인하세요.";
        exit; // 스크립트 종료
    }
}

// 마지막 활동 시간을 현재 시간으로 업데이트
$_SESSION['LAST_ACTIVITY'] = time();

}

?>