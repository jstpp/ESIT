<?php

    # get_misc_value() & net_check_if_trusted():
try {
    $temp = get_misc_value('general_url');
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "get_misc_value() doesn't work as expected!"
    ]);
}

    # get_roles():
try {
    $temp = get_roles();
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "get_roles() doesn't work as expected!"
    ]);
}

    # has_permission():
try {
    if (!has_permission('main.display.admin.diagnostics')) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "has_permission() doesn't work as expected!"
    ]);
}

    # check_session_timeout():
try {
    check_session_timeout();
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "check_session_timeout() doesn't work as expected!"
    ]);
}

    # net_check_if_trusted() & get_real_client_ip():
try {
    $temp = net_check_if_trusted();

    $allowed = json_decode(
        get_misc_value('general_workers_allowed_addr'),
        true
    );
    $ip = get_real_client_ip();
    $status = false;

    foreach ($allowed as $pattern) {
        if ($ip === $pattern) {
            $status = true;
        }

        if (fnmatch($pattern, $ip)) {
            $status = true;
        }
    }

    if (in_array($ip, $allowed, true)) {
        $status = true;
    }

    if ($temp !== $status) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "net_check_if_trusted() or get_real_client_ip() doesn't work as expected!"
    ]);
}

    # copy_directory():
try {
    if (!copy_directory(__DIR__ . "/../tests_temp/dir_A", __DIR__ . "/../tests_temp/dir_B/copied")) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "copy_directory() doesn't work as expected!"
    ]);
}

    # delete_directory():
try {
    if (!delete_directory(__DIR__ . "/../tests_temp/dir_B/copied")) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "delete_directory() doesn't work as expected!"
    ]);
}

    # content_type_identification():
try {
    $temp = content_type_identification('unk')['icon'];
    if ($temp != "fas fa-bug") {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "content_type_identification() doesn't work as expected!"
    ]);
}

    # api_rate_limit_tick():
try {
    $temp = api_rate_limit_tick(1, 1, "diagnostics");
    $temp = api_rate_limit_tick(1, 1, "diagnostics");
    $temp = api_rate_limit_tick(1, 1, "diagnostics");
    if ($temp !== false) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "api_rate_limit_tick() doesn't work as expected!"
    ]);
}

    # import_user_settings():
try {
    $temp = import_user_settings();
    if ($temp['USER_ID'] != $_SESSION['AUTH_ID']) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "content_type_identification() doesn't work as expected!"
    ]);
}

    # i18n functions:
try {
    $temp = isset($_GET['lang'], $_SESSION['lang']) ? init_i18n('messages', $_GET['lang']) : init_i18n('messages');
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "i18n functions doesn't work as expected!"
    ]);
}
