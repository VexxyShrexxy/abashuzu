<?php
$request = urldecode($_SERVER["REQUEST_URI"]);

require_once("../include/phpheader.php");
require_once("include/func.php");

$request = explode("?", $request)[0];

if ($request == "/") {
	require_once("include/header.php");
	require_once("pages/home.php");
	require_once("include/footer.php");
} else if ($request == "/admintool") {
	require_once("include/header.php");
	require_once("pages/admintool.php");
	require_once("include/footer.php");
} else if (str_starts_with($request, "/admintools")) {
	require_once("include/header.php");
	require_once("./api/admintools/verify.php");
	if ($request == "/admintools") {
		require_once("pages/admintools/index.php");
	} else {
		require_once("pages/admintools/" . substr($request, 12) . ".php");
	}
	require_once("include/footer.php");
} else if ($request == "/rules") {
	require_once("include/header.php");
	require_once("pages/rules.php");
	require_once("include/footer.php");
} else if ($request == "/banned") {
	require_once("include/header.php");
	require_once("pages/banned.php");
	require_once("include/footer.php");
} else {
	require_once("include/header.php");

    $stmt = $db->prepare("SELECT * FROM boards WHERE url = ?");
    $stmt->execute([$splitRequest[1]]);
    $board = $stmt->fetch();

	if ($board == null) {
		http_response_code(404);
		require_once("pages/error.php");
	} elseif ($splitRequest[1] == $board["url"] && !isset($splitRequest[2])) {
		require_once("pages/board.php");
	} else if ($splitRequest[2] == "thread") {
		if (!empty($splitRequest[3])) {
			require_once("pages/thread.php");
		} else {
			http_response_code(404);
			require_once("pages/error.php");
		}
	} else {
		http_response_code(404);
		require_once("pages/error.php");
	}
	require_once("include/footer.php");
}
