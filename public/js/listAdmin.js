
const listArticles = document.querySelector("#tab-articles");
const listUsers = document.querySelector("#tab-users");
const listComs = document.querySelector("#tab-coms");
const selectForm = document.querySelector("#selectList");
const title = document.querySelector(".titleAdmin");

let selectValue = selectForm.value;

function selectOption() {
    selectValue = selectForm.value;
    console.log(selectValue);
    if (selectValue == 1) {
        listArticles.style.display = "block";
        listUsers.style.display = "none";
        listComs.style.display = "none";

    }
    if (selectValue == 2) {
        listArticles.style.display = "none";
        listUsers.style.display = "block";
        listComs.style.display = "none";
    }
    if (selectValue == 3) {
        listArticles.style.display = "none";
        listUsers.style.display = "none";
        listComs.style.display = "block";
    }
}
