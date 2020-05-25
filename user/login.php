<?php
require_once '../util/_user_session.php';
require_once '../util/_page_config.php';
require_once '../util/db/_db_users.php';

if ($current_user !== null) {
    redirect_back();
}

$form_error = null;

$_login_user_id = null;

function redirect_back() {
    $redir_page = $_POST['back'] ?? '/';
    if (empty($redir_page)) {
        $redir_page = '/';
    }
    header("Location: {$redir_page}");
    die();
}

function handle_submit(): ?string {
    global $_login_user_id;

    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        return 'All required fields must be supplied';
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $_login_user_id = validate_login($username, $password);
    } catch (UnexpectedValueException $ex) {
        return $ex->getMessage();
    }
    
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_error = handle_submit();

    if ($form_error === null) {
        $_SESSION['user_id'] = $_login_user_id;

        redirect_back();
    }
}

PageConfig::$title = 'Log In';

include_once "../template/_header.php";
?>

<main id="main-content">
    <header class="header">
        Log In
    </header>
    <form id="login-form" method="POST">
        <div class="form-section form-error">
            <?php echo $form_error ?? ''; ?>
        </div>
        <div id="login-username" class="form-section">
            <label class="form-label required" for="login-username-input">Username</label>
            <input type="text" id="login-username-input" name="username" value="<?php echo getPostVal('username') ?>"
                    required="required" maxlength="64" />
        </div>
        <div id="login-password" class="form-section">
            <label class="form-label required" for="login-password-input">Password</label>
            <input type="password" id="login-password-input" name="password" required="required" maxlength="256" />
        </div>

        <div class="form-section">
            <button type="submit" id="register-submit" class="btn-primary">
                Submit
            </button>
        </div>
    </form>
</main>

<?php
include_once "../template/_sidebar.php";
include_once "../template/_footer.php";
?>
