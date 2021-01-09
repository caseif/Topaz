<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/user_session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/db/db_users.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_internal/util/page_config.php';

$form_error = null;

$_created_user_id = null;

if ($current_user !== null) {
    redirect_back();
}

function handle_submit(): ?string {
    global $_created_user_id;

    if (!isset($_POST['username']) || !isset($_POST['fullname'])
            || !isset($_POST['password']) || !isset($_POST['ticket'])) {
        return 'All required fields must be supplied';
    }

    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = $_POST['password'];
    $ticket = $_POST['ticket'];

    if (strlen($username) < 1) {
        return 'Username must be at least 1 character';
    } else if (strlen($username) > 64) {
        return 'Username must not exceed 64 characters';
    } else if (strlen($fullname) < 1) {
        return 'Full name must be at least 1 character';
    } else if (strlen($fullname) > 64) {
        return 'Full name must not exceed 64 characters';
    } else if (strlen($password) < 10 || strlen($password) > 256) {
        return 'Password must be between 10 and 256 characters';
    } else if (!preg_match('/[A-Z]/', $password)
            || !preg_match('/[a-z]/', $password)
            || !preg_match('/[0-9]/', $password)) {
        return 'Password must contain a lowercase letter, uppercase letter, and a number';
    }

    try {
        $_created_user_id = create_user($username, $fullname, $password, $ticket);
    } catch (UnexpectedValueException $ex) {
        return $ex->getMessage();
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_error = handle_submit();

    if ($form_error === null) {
        $_SESSION['user_id'] = $_created_user_id;

        redirect_back();
    }
}

PageConfig::$title = 'Register';

include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/header.php';
?>

<main id="main-content">
    <header class="header">
        Register an Account
    </header>
    <form id="register-form" method="POST">
        <input type="hidden" name="back" value="<?php echo strip_tags($_GET['back'] ?? ''); ?>" />
        <div class="form-section form-error">
            <?php echo $form_error ?? ''; ?>
        </div>
        <div id="register-username" class="form-section">
            <label class="form-label required" for="register-username-input">Username</label>
            <input type="text" id="register-username-input" name="username" required="required" maxlength="64"
                    value="<?php echo get_post_val('username'); ?>" />
        </div>
        <div id="register-fullname" class="form-section">
            <label class="form-label required" for="register-fullname-input">Full Name</label>
            <input type="text" id="register-fullname-input" name="fullname" required="required" maxlength="64"
                    value="<?php echo get_post_val('fullname'); ?>" />
        </div>
        <div id="register-password" class="form-section">
            <label class="form-label required" for="register-password-input">Password</label>
            <input type="password" id="register-password-input" name="password" required="required" minlength="10"
                    maxlength="256" />
        </div>
        <div id="register-passwordVerify" class="form-section">
            <label class="form-label required" for="register-passwordVerify-input">Verify Password</label>
            <input type="password" id="register-passwordVerify-input" name="passwordVerify" required="required"
                    minlength="10" maxlength="256" />
        </div>
        <div id="register-ticket" class="form-section">
            <?php
            $ticket_required = !GlobalConfig\get_config()->user_perms->open_registration;
            ?>
            <label class="form-label <?php echo $ticket_required ? "required" : ""; ?>" for="register-ticket-input">Registration ticket</label>
            <input type="password" id="register-ticket-input" name="ticket"
                    <?php echo $ticket_required ? "required=\"required\"" : ""; ?>
                    value="<?php echo get_post_val('ticket'); ?>" />
        </div>

        <div class="form-section">
            <button type="submit" id="register-submit" class="btn-primary">
                Submit
            </button>
        </div>
    </form>
</main>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/sidebar.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/_internal/template/footer.php';
?>
