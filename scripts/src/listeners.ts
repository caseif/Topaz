$(document).ready(() => {
    $(".navbar-link").on("click", (evt: JQuery.ClickEvent) => {
        let $target: JQuery<HTMLAnchorElement> = $(evt.target);
        console.log("click");
        if ($target.closest("a").length === 0) {
            console.log("no parent");
            console.log($target.find("a"));
            $target.find("a")[0].click();
        }
    });

    $("#recent-control").on("click", (evt: JQuery.ClickEvent) => {
        let root: JQuery<HTMLElement> = $(evt.target).parents(".sidebar-link").find("#post-list");
        if ($(evt.target).attr("id") === "expand-recent") {
            root.find(".older").each((index: number, el: HTMLElement) => {
                $(el).addClass("expanded");
            });
            $(evt.target).hide();
            $(evt.target).siblings("#collapse-recent").show();
        } else if ($(evt.target).attr("id") === "collapse-recent") {
            root.find(".older").each((index: number, el: HTMLElement) => {
                $(el).removeClass("expanded");
            });
            $(evt.target).hide();
            $(evt.target).siblings("#expand-recent").show();
        }
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

    $("#edit-submit").on("click", (evt: JQuery.ClickEvent) => {
        let btn: JQuery<HTMLButtonElement> = $(evt.target);
        btn.attr("disabled", "disabled");

        let data: any = {
            title: $("#edit-title-input").val(),
            content: $("#edit-content-input").val(),
            about: $("#edit-about-input").prop("checked") ? 1 : 0
        };
        
        let id = $("#edit-id-input").val() as string;
        if (id) {
            data["id"] = parseInt(id);
        }

        $.ajax({
            url: "/ajax/change_post.php",
            data: data,
            method: id ? "POST" : "PUT",
            success: (resp: any, msg: string) => {
                if (!resp.success) {
                    alert(`Failed to save post: ${resp.message}`);
                    return;
                }

                if (!id) {
                    id = resp.id;
                }

                if (data.about) {
                    window.location.href = `/about.php`;
                } else {
                    window.location.href = `/post.php?id=${id}`;
                }
            },
            error: (xhr: JQueryXHR, msg: string) => {
                alert(`Failed to save post: ${msg}`);
            },
            complete: (resp: any) => {
                $("#edit-submit").prop("disabled", null);
            }
        });
    });
});

function deletePost(id: number, perma: boolean) {
    $.ajax({
        url: "/ajax/change_post.php",
        data: {
            id: id,
            perma: perma
        },
        method: "DELETE",
        success: (resp: any, msg: string) => {
            if (!resp.success) {
                alert(`Failed to delete post: ${resp.message}`);
                return;
            }

            if (!id) {
                id = resp.id;
            }

            window.location.href = "/";
        },
        error: (xhr: JQueryXHR, msg: string) => {
            alert(`Failed to delete post: ${msg}`);
        }
    });
}

function confirmDelete(id: number): void {
    if (!confirm("Are you sure you want to delete this post?")) {
        return;
    }

    deletePost(id, false);
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

    deletePost(id, true);
}
