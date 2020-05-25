"use strict";
const COOKIE_RECENT_EXPANDED = "recent_expanded";
$(document).ready(() => {
    $(".navbar-link").on("click", (evt) => {
        let $target = $(evt.target);
        if ($target.closest("a").length === 0) {
            $target.find("a")[0].click();
        }
    });
    $("#recent-control").on("click", (evt) => {
        let root = $(evt.target).parents(".sidebar-link").find("#post-list");
        let expanded = false;
        if ($(evt.target).attr("id") === "expand-recent") {
            root.addClass("expanded");
            $(evt.target).hide();
            $(evt.target).siblings("#collapse-recent").show();
            expanded = true;
        }
        else if ($(evt.target).attr("id") === "collapse-recent") {
            root.removeClass("expanded");
            $(evt.target).hide();
            $(evt.target).siblings("#expand-recent").show();
            expanded = false;
        }
        document.cookie = `${COOKIE_RECENT_EXPANDED}=${expanded ? 1 : 0};0;path=/`;
    });
    $(".archive-year-label").on("click", (evt) => {
        let parent = $(evt.target).parents(".archive-year");
        if (parent.hasClass("collapsed")) {
            parent.removeClass("collapsed");
            parent.find(".archive-year-expander").removeClass("fa-caret-right").addClass("fa-caret-down");
        }
        else {
            parent.addClass("collapsed");
            parent.find(".archive-year-expander").removeClass("fa-caret-down").addClass("fa-caret-right");
        }
    });
    $("#edit-about-input").on("change", (evt) => {
        if ($(evt.target).prop("checked")) {
            if (!confirm("Enabling this option will assign the post's content to the About page and prevent it from "
                + "appearing in the post feed. Any existing post assigned to the About page will be returned to "
                + "the post feed. Do you wish to continue?")) {
                $(evt.target).prop("checked", null);
            }
        }
    });
    let passChanged = false;
    let passBlurred = false;
    $("#register-password-input").on("blur", evt => {
        passBlurred = true;
        if (passChanged) {
            validatePass(evt.target);
        }
    }).on("input", evt => {
        passChanged = true;
        if (passBlurred) {
            validatePass(evt.target);
        }
    });
    let passVerifyChanged = false;
    let passVerifyBlurred = false;
    $("#register-passwordVerify-input").on("blur", evt => {
        passVerifyBlurred = true;
        if (passVerifyChanged) {
            validatePassVerify(evt.target);
        }
    }).on("input", evt => {
        passVerifyChanged = true;
        if (passVerifyBlurred) {
            validatePassVerify(evt.target);
        }
    });
});
function validatePass(passInput) {
    let pass = passInput.value;
    let error = "";
    if (pass.length < 10) {
        error = "Password must be at least 10 characters";
    }
    else if (pass.length > 256) {
        error = "Password must not exceed 256 characters";
    }
    else if (!/[A-Z]/g.test(pass)
        || !/[a-z]/g.test(pass)
        || !/[0-9]/g.test(pass)) {
        error = "Password must contain a lowercase letter, uppercase letter, and a number";
    }
    passInput.setCustomValidity(error);
}
function validatePassVerify(passVerifyInput) {
    if (passVerifyInput.value !== $("#register-password-input").val()) {
        passVerifyInput.setCustomValidity("Passwords must match");
    }
    else {
        passVerifyInput.setCustomValidity("");
    }
}
function changePost(action, id) {
    $.ajax({
        url: "/ajax/change_post.php",
        data: {
            action: action,
            id: id
        },
        method: "POST",
        success: (resp, msg) => {
            if (!resp.success) {
                alert(`Failed to ${action} post: ${resp.message}`);
                return;
            }
            if (!id) {
                id = resp.id;
            }
            window.location.reload();
            console.log('attempted reload');
        },
        error: (xhr, msg) => {
            alert(`Failed to ${action} post: ${msg}`);
        }
    });
}
function confirmHide(id) {
    if (!confirm("Are you sure you want to hide this post?")) {
        return;
    }
    changePost("hide", id);
}
function confirmUnhide(id) {
    if (!confirm("Are you sure you want to unhide this post?")) {
        return;
    }
    changePost("unhide", id);
}
function confirmPermaDelete(id) {
    let title = $(`.post[data-id=${id}]`).attr("data-title");
    let promptRes = prompt(`Warning: this action will permanently delete the post from the database.\n`
        + `Please type the post title if you wish to continue ('${title}').`);
    if ((promptRes === null || promptRes === void 0 ? void 0 : promptRes.toLowerCase()) !== (title === null || title === void 0 ? void 0 : title.toLowerCase())) {
        if (promptRes !== null) {
            alert("The title was entered incorrectly");
        }
        return;
    }
    changePost("delete", id);
}
//# sourceMappingURL=listeners.js.map