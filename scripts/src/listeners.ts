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
});
