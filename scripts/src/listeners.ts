const COOKIE_RECENT_EXPANDED = "recent_expanded";

$(document).ready(() => {
    $(".navbar-link").on("click", (evt: JQuery.ClickEvent) => {
        let $target: JQuery<HTMLAnchorElement> = $(evt.target);
        if ($target.closest("a").length === 0) {
            $target.find("a")[0].click();
        }
    });

    $("#recent-control").on("click", (evt: JQuery.ClickEvent) => {
        let root: JQuery<HTMLElement> = $(evt.target).parents(".sidebar-link").find("#post-list");
        let expanded = false;
        if ($(evt.target).attr("id") === "expand-recent") {
            root.addClass("expanded");
            $(evt.target).hide();
            $(evt.target).siblings("#collapse-recent").show();
            expanded = true;
        } else if ($(evt.target).attr("id") === "collapse-recent") {
            root.removeClass("expanded");
            $(evt.target).hide();
            $(evt.target).siblings("#expand-recent").show();
            expanded = false;
        }

        document.cookie = `${COOKIE_RECENT_EXPANDED}=${expanded ? 1 : 0};0;path=/`;
    });

    $(".archive-year-label").on("click", (evt: JQuery.ClickEvent) => {
        let parent: JQuery<HTMLDivElement> = $(evt.target).parents(".archive-year");
        if (parent.hasClass("collapsed")) {
            parent.removeClass("collapsed");
            parent.find(".archive-year-expander").removeClass("fa-caret-right").addClass("fa-caret-down");
        } else {
            parent.addClass("collapsed");
            parent.find(".archive-year-expander").removeClass("fa-caret-down").addClass("fa-caret-right");
        }
    });

    $("#edit-about-input").on("change", (evt: JQuery.ChangeEvent) => {
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
            validatePass(evt.target as HTMLInputElement);
        }
    }).on("input", evt => {
        passChanged = true;
        if (passBlurred) {
            validatePass(evt.target as HTMLInputElement);
        }
    });

    let passVerifyChanged = false;
    let passVerifyBlurred = false;
    $("#register-passwordVerify-input").on("blur", evt => {
        passVerifyBlurred = true;
        if (passVerifyChanged) {
            validatePassVerify(evt.target as HTMLInputElement);
        }
    }).on("input", evt => {
        passVerifyChanged = true;
        if (passVerifyBlurred) {
            validatePassVerify(evt.target as HTMLInputElement);
        }
    });
});

function validatePass(passInput: HTMLInputElement) {
    let pass = passInput.value;
    let error = "";

    if (pass.length < 10) {
        error = "Password must be at least 10 characters";
    } else if (pass.length > 256) {
        error = "Password must not exceed 256 characters";
    } else if (!/[A-Z]/g.test(pass)
            || !/[a-z]/g.test(pass)
            || !/[0-9]/g.test(pass)) {
        error = "Password must contain a lowercase letter, uppercase letter, and a number";
    }

    passInput.setCustomValidity(error);
}

function validatePassVerify(passVerifyInput: HTMLInputElement) {
    if (passVerifyInput.value !== $("#register-password-input").val()) {
        passVerifyInput.setCustomValidity("Passwords must match");
    } else {
        passVerifyInput.setCustomValidity("");
    }
}

function changePost(action: string, id: number) {
    $.ajax({
        url: "/ajax/change_post.php",
        data: {
            action: action,
            id: id
        },
        method: "POST",
        success: (resp: any, msg: string) => {
            if (!resp.success) {
                alert(`Failed to ${action} post: ${resp.message}`);
                return;
            }

            if (!id) {
                id = resp.id;
            }

            window.location.href = "/";
        },
        error: (xhr: JQueryXHR, msg: string) => {
            alert(`Failed to ${action} post: ${msg}`);
        }
    });
}

function confirmHide(id: number): void {
    if (!confirm("Are you sure you want to hide this post?")) {
        return;
    }

    changePost("hide", id);
}

function confirmUnhide(id: number): void {
    if (!confirm("Are you sure you want to unhide this post?")) {
        return;
    }

    changePost("unhide", id);
}

function confirmPermaDelete(id: number): void {
    let title = $(`.post[data-id=${id}]`).attr("data-title");
    
    let promptRes = prompt(`Warning: this action will permanently delete the post from the database.\n`
            + `Please type the post title if you wish to continue ('${title}').`);
    if (promptRes?.toLowerCase() !== title?.toLowerCase()) {
        if (promptRes !== null) {
            alert("The title was entered incorrectly");
        }
        return;
    }

    changePost("delete", id);
}
