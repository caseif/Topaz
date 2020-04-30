$(document).ready(() => {
    $(".navbar-link").on("click", (evt: JQuery.ClickEvent) => {
        let $target = $(evt.target);
        console.log("click");
        if ($target.closest("a").length === 0) {
            console.log("no parent");
            console.log($target.find("a"));
            $target.find("a")[0].click();
        }
    });

    $("#recent-control").on("click", (evt: JQuery.ClickEvent) => {
        let root = $(evt.target).parents(".sidebar-link").find("#post-list");
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
        let parent = $(evt.target).parents(".archive-year");
        if (parent.hasClass("collapsed")) {
            parent.removeClass("collapsed");
            parent.find(".archive-year-expander").removeClass("fa-caret-right").addClass("fa-caret-down");
        } else {
            parent.addClass("collapsed");
            parent.find(".archive-year-expander").removeClass("fa-caret-down").addClass("fa-caret-right");
        }
    });
});
