<?php
    function log_error(string $category, string $ip, string $content, string $details): bool
    {
        try {
            global $pdo;

            $db_query = $pdo->prepare('INSERT INTO LOGS (category, ip, content, details) VALUES (:category, :ip, :content, :details)');
            $db_query->execute(['category' => $category, 
                                'ip' => $ip, 
                                'content' => $content, 
                                'details' => $details]);

            return True;
        } catch (Throwable) {
            return False;
        }
    }

    function get_error_name(int $errno): string
    {
        return match ($errno) {
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            default => "UNKNOWN ($errno)",
        };
    }

    function emergency_display(): bool
    {
        echo("<b>Sorry, an unexpected error occured:(</b> Try again.");
        return True;
    }

    function extended_fatal_error_handler(): void
    {
        try {
            $error = error_get_last();
            $ip = get_real_client_ip();

            if ($error === null) {
                return;
            }

            $fatal = [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_COMPILE_ERROR,
            ];

            if (!in_array($error['type'], $fatal, true)) {
                return;
            }

            $error_type = get_error_name($error['type']);
            $content = "Logged fatal error <code>".htmlentities($error_type)."</code> in <code>".htmlentities($error['file']).":".htmlentities($error['line'])."</code> triggered by <code>".htmlentities($ip)."</code>.";
            $details = "[".htmlentities($ip)."; ".htmlentities($error_type)."; ".htmlentities($error['file']).":".htmlentities($error['line'])."]\n".htmlentities($error['message']);

            log_error("fatal", $ip, $content, $details);
        } catch (Throwable) {
            emergency_display();
            return;
        }
    }

    function extended_error_handler(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        try {
            $error_type = get_error_name($errno);
            $ip = get_real_client_ip();
            $content = "Logged an <b>error</b> <code>".htmlentities($error_type)."</code> in <code>".htmlentities($errfile).":".htmlentities($errline)."</code> triggered by <code>".htmlentities($ip)."</code>.";
            $details = "[".htmlentities($ip)."; ".htmlentities($error_type)."; ".htmlentities($errfile).":".htmlentities($errline)."]\n".htmlentities($errstr);
            return log_error("error", $ip, $content, $details);
        } catch (Throwable) {
            emergency_display();
            return True;
        }
    }

    function extended_exception_handler(Throwable $e): bool
    {
        try {
            $ip = get_real_client_ip();
            $content = "Logged an <b>exception</b> in <code>".htmlentities($e->getFile()).":".htmlentities($e->getLine())."</code> triggered by <code>".htmlentities($ip)."</code>.";
            $details = "[".htmlentities($ip)."; EXCEPTION (".htmlentities(get_class($e))."); ".htmlentities($e->getFile()).":".htmlentities($e->getLine())."]\n".htmlentities($e->getMessage());
            return log_error("exception", $ip, $content, $details);
        } catch (Throwable) {
            emergency_display();
            return True;
        }
    }

    register_shutdown_function("extended_fatal_error_handler");
    set_exception_handler("extended_exception_handler");
    set_error_handler("extended_error_handler");

?>